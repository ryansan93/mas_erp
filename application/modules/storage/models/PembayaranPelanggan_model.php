<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PembayaranPelanggan_model extends Conf{
	protected $table = 'pembayaran_pelanggan';

	public function detail()
	{
		return $this->hasMany('\Model\Storage\DetPembayaranPelanggan_model', 'id_header', 'id')->with(['data_do']);
	}

	public function perusahaan()
	{
		return $this->hasOne('\Model\Storage\Perusahaan_model', 'kode', 'perusahaan')->orderBy('id', 'desc');
	}

	public function pelanggan()
	{
		return $this->hasOne('\Model\Storage\Pelanggan_model', 'nomor', 'no_pelanggan')->with(['kecamatan'])->orderBy('version', 'desc');
	}

	public function logs()
  	{
    	return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->where('tbl_name', $this->table);
  	}
}
