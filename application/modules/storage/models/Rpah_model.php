<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Rpah_model extends Conf {
	protected $table = 'rpah';
	protected $primaryKey = 'id';
	protected $status = 'g_status';
    public $timestamps = false;

    public function det_rpah_without_konfir()
	{
		return $this->hasMany('\Model\Storage\DetRpah_model', 'id_rpah', 'id');
	}

    public function det_rpah()
	{
		return $this->hasMany('\Model\Storage\DetRpah_model', 'id_rpah', 'id')->with(['data_konfir']);
	}

	public function det_rpah_real_sj()
	{
		return $this->hasMany('\Model\Storage\DetRpah_model', 'id_rpah', 'id')->with(['data_real_sj']);
	}

	public function logs()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->select('tbl_name', 'tbl_id', 'user_id', 'waktu', 'deskripsi', '_action')->where('tbl_name', $this->table);
	}

	public function getDashboard($status)
	{
    	$table_name = $this->table;
		$column_name_key = $this->primaryKey;
		$column_name_status = $this->status;
		$sql = <<<QUERY
					select
						count(m.id) jumlah,
						case
							when(m.$column_name_status = 1) then 'Submit'
							when(m.$column_name_status = 4) then 'Reject'
							else 'Finish'
						end as status_data,
						case
							when(m.$column_name_status = 1) then 'Approve'
							when(m.$column_name_status = 4) then 'Resubmit'
							else 'Finish'
						end as next_state,
						lt.nama_detuser aktor
					from
						$table_name m
					join
						(select
							log.id,
							log.tbl_id,
							d_usr.nama_detuser,
							log.deskripsi,
							log.waktu
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
						m.$column_name_status = $status
					group by
						m.$column_name_status,
						lt.nama_detuser
QUERY;

		return $this->hydrateRaw ( $sql );
  	}
}