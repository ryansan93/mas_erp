<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Perusahaan_model extends Conf {
	public $incrementing = false;

	protected $table = 'perusahaan';
	protected $primaryKey = 'id';
	protected $status = 'status';
	protected $kode = 'kode';
	protected $kode_jenis = 'P';

	public function getNextNomor()
	{
		$id = $this->selectRaw("'".$this->kode_jenis."'+replace(str(substring(coalesce(max(".$this->kode."),'000'),4,3)+1,3), ' ', '0') as nextId")->first();
		return $id->nextId;
	}

	public function d_kota()
	{
		return $this->hasOne('\Model\Storage\Lokasi_model', 'id', 'kota');
	}

	public function logs()
  	{
    	return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->where('tbl_name', $this->table);
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
	   m.tipe = 'pelanggan'
	join (
		select max(z.id) as id from pelanggan z where tipe = 'pelanggan' group by z.nomor
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