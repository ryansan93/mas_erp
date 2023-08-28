<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Lhk_model extends Conf {
	protected $table = 'lhk';

    public function lhk_sekat()
	{
		return $this->hasMany('\Model\Storage\LhkSekat_model', 'id_header', 'id');
	}

	public function lhk_nekropsi()
	{
		return $this->hasMany('\Model\Storage\LhkNekropsi_model', 'id_header', 'id')->with(['d_nekropsi', 'foto_nekropsi']);
	}

	public function lhk_solusi()
	{
		return $this->hasMany('\Model\Storage\LhkSolusi_model', 'id_header', 'id')->with(['d_solusi']);
	}

	public function foto_sisa_pakan()
	{
		return $this->hasMany('\Model\Storage\LhkFotoSisaPakan_model', 'id_header', 'id');
	}

	public function foto_ekor_mati()
	{
		return $this->hasMany('\Model\Storage\LhkFotoEkorMati_model', 'id_header', 'id');
	}
}