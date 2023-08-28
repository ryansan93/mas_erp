<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class DetJurnal_model extends Conf{
    // public $incrementing = false;

    protected $table = 'det_jurnal';
    protected $primaryKey = 'id';

    public function jurnal_trans_detail()
    {
        return $this->hasOne('\Model\Storage\DetJurnalTrans_model', 'id', 'det_jurnal_trans_id');
    }

    public function jurnal_trans_sumber_tujuan()
    {
        return $this->hasOne('\Model\Storage\JurnalTransSumberTujuan_model', 'id', 'jurnal_trans_sumber_tujuan_id');
    }

    public function d_supplier()
    {
        return $this->hasOne('\Model\Storage\Supplier_model', 'nomor', 'supplier')->orderBy('id', 'desc');
    }

    public function d_perusahaan()
    {
        return $this->hasOne('\Model\Storage\Perusahaan_model', 'kode', 'perusahaan')->orderBy('id', 'desc');
    }

    public function d_unit()
    {
        return $this->hasOne('\Model\Storage\Wilayah_model', 'kode', 'unit')->orderBy('id', 'desc');
    }
}
