<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Supplier_model extends Conf {
	public $incrementing = false;

	protected $table = 'pelanggan';
	protected $primaryKey = 'id';
	protected $status = 'status';
	protected $nomor = 'nomor';

	public function getNextNomor($kode_jenis)
	{
		$id = $this->selectRaw("right(year(current_timestamp),2)+". "'".$kode_jenis."'+replace(str(substring(coalesce(max(".$this->nomor."),'000'),4,3)+1,3), ' ', '0') as nextId")->where('tipe', 'supplier')->first();
		return $id->nextId;
	}

	public function telepons()
	{
		return $this->hasMany('\Model\Storage\TelpPelanggan_model', 'pelanggan', 'id');
	}

	public function kecamatan()
	{
		return $this->hasOne('\Model\Storage\Lokasi_model', 'id', 'alamat_kecamatan')->with('dKota');
	}

	public function logs()
  	{
    	return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->where('tbl_name', 'pelanggan');
  	}

  	public function lampiran()
	{
		return $this->hasOne('\Model\Storage\Lampiran_model', 'tabel_id', 'id')->where('tabel', 'pelanggan')->with('d_nama_lampiran');
	}

  	public function banks() {
  		return $this->hasMany('\Model\Storage\BankPelanggan_model', 'pelanggan', 'id')->with(['lampiran']);
  	}

  	public function aktif() {
  		return $this->hasOne('\Model\Storage\AktifPelanggan_model', 'pelanggan', 'id');
  	}

  	public function getDashboard($status)
	{
    	$table_name = $this->table;
		$column_name_key = $this->primaryKey;
		$column_name_status = $this->status;
		$sql = <<<QUERY
					select
	count( distinct(nomor) ) jumlah,
	m.$column_name_status status_data,
	case
		when(m.$column_name_status = 'submit') then 'Ack'
		else 'Finish'
	end as next_state,
	lt.nama_detuser aktor
	from
	$table_name m
	join
		(select
			log.id
		, log.tbl_id
		, d_usr.nama_detuser
		, log.deskripsi
		, log.waktu
		from ( select
					l.tbl_id
				, max(l.id) as id
				from
				log_tables l
				where l.tbl_name = '$table_name'
				group by
				l.tbl_id
				) mx
		join log_tables log
			on log.id = mx.id
		join ms_user usr
			on usr.id_user = log.user_id
		join detail_user d_usr
			on d_usr.id_user = usr.id_user and d_usr.nonaktif_detuser is null
	) lt
	on lt.tbl_id = m.id and 
	   m.$column_name_status = 'submit' and
	   m.tipe = 'supplier'
	join (
		select max(z.id) as id from pelanggan z where tipe = 'supplier' group by z.nomor
	) x
	on
		lt.tbl_id = x.id
	group by
	m.$column_name_status,
	lt.nama_detuser
QUERY;

		return $this->hydrateRaw ( $sql );
  	}
}