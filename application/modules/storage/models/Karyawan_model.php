<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Karyawan_model extends Conf {
	protected $table = 'karyawan';
	protected $primaryKey = 'id';
	protected $nik = 'nik';

	public function getNextNomor($kode_jenis)
	{
		$id = $this->selectRaw("'".$kode_jenis."'+right(year(current_timestamp),2)+replace(str(substring(coalesce(max(".$this->nik."),'000'),4,3)+1,3), ' ', '0') as nextId")->first();
		return $id->nextId;
	}

	public function unit()
	{
		return $this->hasMany('\Model\Storage\UnitKaryawan_model', 'id_karyawan', 'id');
	}

	public function dWilayah()
	{
		return $this->hasMany('\Model\Storage\WilayahKaryawan_model', 'id_karyawan', 'id');
	}

	public function logs()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->where('tbl_name', $this->table);
	}
}