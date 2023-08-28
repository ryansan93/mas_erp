<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Rhpp_model extends Conf{
	protected $table = 'rhpp';

	public function doc()
	{
		return $this->hasOne('\Model\Storage\RhppDoc_model', 'id_header', 'id');
	}

	public function pakan()
	{
		return $this->hasMany('\Model\Storage\RhppPakan_model', 'id_header', 'id');
	}

	public function oa_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppOaPakan_model', 'id_header', 'id');
	}

	public function pindah_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppPindahPakan_model', 'id_header', 'id');
	}

	public function oa_pindah_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppOaPindahPakan_model', 'id_header', 'id');
	}

	public function retur_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppReturPakan_model', 'id_header', 'id');
	}

	public function oa_retur_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppOaReturPakan_model', 'id_header', 'id');
	}

	public function voadip()
	{
		return $this->hasMany('\Model\Storage\RhppVoadip_model', 'id_header', 'id');
	}

	public function retur_voadip()
	{
		return $this->hasMany('\Model\Storage\RhppReturVoadip_model', 'id_header', 'id');
	}

	public function penjualan()
	{
		return $this->hasMany('\Model\Storage\RhppPenjualan_model', 'id_header', 'id')->orderBy('nota', 'asc');
	}

	public function potongan()
	{
		return $this->hasMany('\Model\Storage\RhppPotongan_model', 'id_header', 'id');
	}

	public function bonus()
	{
		return $this->hasMany('\Model\Storage\RhppBonus_model', 'id_header', 'id');
	}
}
