<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class DetJurnalTrans_model extends Conf{
    // public $incrementing = false;

    protected $table = 'det_jurnal_trans';
    protected $primaryKey = 'id';

    public function jurnal_trans()
    {
      	return $this->hasOne('\Model\Storage\JurnalTrans_model', 'id', 'id_header');
    }
}
