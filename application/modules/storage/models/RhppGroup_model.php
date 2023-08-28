<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class RhppGroup_model extends Conf{
	protected $table = 'rhpp_group';

	public function doc()
	{
		return $this->hasMany('\Model\Storage\RhppGroupDoc_model', 'id_header', 'id');
	}

	public function list_noreg()
	{
		return $this->hasMany('\Model\Storage\RhppGroupNoreg_model', 'id_header', 'id');
	}

	public function pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupPakan_model', 'id_header', 'id');
	}

	public function oa_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupOaPakan_model', 'id_header', 'id');
	}

	public function pindah_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupPindahPakan_model', 'id_header', 'id');
	}

	public function oa_pindah_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupOaPindahPakan_model', 'id_header', 'id');
	}

	public function retur_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupReturPakan_model', 'id_header', 'id');
	}

	public function oa_retur_pakan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupOaReturPakan_model', 'id_header', 'id');
	}

	public function voadip()
	{
		return $this->hasMany('\Model\Storage\RhppGroupVoadip_model', 'id_header', 'id');
	}

	public function retur_voadip()
	{
		return $this->hasMany('\Model\Storage\RhppGroupReturVoadip_model', 'id_header', 'id');
	}

	public function penjualan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupPenjualan_model', 'id_header', 'id');
	}

	public function potongan()
	{
		return $this->hasMany('\Model\Storage\RhppGroupPotongan_model', 'id_header', 'id');
	}

	public function bonus()
	{
		return $this->hasMany('\Model\Storage\RhppGroupBonus_model', 'id_header', 'id');
	}
}
