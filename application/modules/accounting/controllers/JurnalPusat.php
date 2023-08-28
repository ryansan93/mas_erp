<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JurnalPusat extends Public_Controller
{
    private $pathView = 'accounting/jurnal_pusat/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                'assets/select2/js/select2.min.js',
                'assets/accounting/jurnal_pusat/js/jurnal-pusat.js'
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                'assets/accounting/jurnal_pusat/css/jurnal-pusat.css'
            ));

            $data = $this->includes;

            $data['title_menu'] = 'Jurnal Pusat';

            $content['add_form'] = $this->addForm();
            $content['riwayat'] = $this->riwayat();

            $content['akses'] = $this->hakAkses;
            $data['view'] = $this->load->view($this->pathView . 'index', $content, true);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $jurnal_trans_detail = $params['jurnal_trans_detail'];
        $perusahaan = $params['perusahaan'];

        $sql_jurnal_trans_detail = null;
        if ( $jurnal_trans_detail != 'all' ) {
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select * from det_jurnal_trans djt
                where
                    id = ".$jurnal_trans_detail."
            ";
            $d_djt = $m_conf->hydrateRaw( $sql );

            if ( $d_djt ) {
                $d_djt = $d_djt->toArray()[0];

                $sql_jurnal_trans_detail = "and dj.coa_asal = '".$d_djt['sumber_coa']."' and dj.coa_tujuan = '".$d_djt['tujuan_coa']."'";
            }
        }
        $sql_perusahaan = "and dj.perusahaan = '".$perusahaan."'";
        if ( $perusahaan == 'all' ) {
            $sql_perusahaan = null;
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                dj.id,
                dj.id_header,
                dj.tanggal,
                dj.det_jurnal_trans_id,
                dj.jurnal_trans_sumber_tujuan_id,
                dj.supplier,
                dj.perusahaan,
                max(cast(dj.keterangan as varchar(500))) as keterangan,
                dj.nominal,
                dj.saldo,
                dj.ref_id,
                dj.asal,
                dj.coa_asal,
                dj.tujuan,
                dj.coa_tujuan,
                dj.unit,
                dj.pic,
                dj.tbl_name,
                dj.tbl_id,
                djt.nama as jurnal_trans_detail_nama,
                p.perusahaan as nama_perusahaan,
                w.nama as nama_unit
            from det_jurnal dj
            left join
                det_jurnal_trans djt
                on
                    dj.det_jurnal_trans_id = djt.id
            left join
                jurnal_trans jt
                on
                    djt.id_header = jt.id
            left join
                (
                    select p1.* from perusahaan p1
                    right join
                        (select max(id) as id, kode from perusahaan group by kode) p2
                        on 
                            p1.id = p2.id
                ) p
                on
                    p.kode = dj.perusahaan
            left join
                (
                    select
                        REPLACE(REPLACE(w1.nama, 'Kab', ''), 'Kota', '') as nama, w1.kode
                    from wilayah w1
                    where
                        w1.kode is not null
                    group by
                        w1.nama,
                        w1.kode
                ) w
                on
                    w.kode = dj.unit
            left join
                jurnal j
                on
                    dj.id_header = j.id
            where
                dj.tanggal between '".$start_date."' and '".$end_date."' and
                (jt.unit is null or jt.unit = '0')
                ".$sql_jurnal_trans_detail."
                ".$sql_perusahaan."
            group by
                dj.id,
                dj.id_header,
                dj.tanggal,
                dj.det_jurnal_trans_id,
                dj.jurnal_trans_sumber_tujuan_id,
                dj.supplier,
                dj.perusahaan,
                dj.nominal,
                dj.saldo,
                dj.ref_id,
                dj.asal,
                dj.coa_asal,
                dj.tujuan,
                dj.coa_tujuan,
                dj.unit,
                dj.pic,
                dj.tbl_name,
                dj.tbl_id,
                djt.nama,
                p.perusahaan,
                w.nama
            order by
                dj.tanggal desc
        ";
        $d_jurnal = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jurnal->count() > 0 ) {
            $data = $d_jurnal->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function loadForm()
    {
        $params = $this->input->get('params');
        $edit = $this->input->get('edit');

        $id = $params['id'];

        $content = array();
        $html = "url not found";
        
        if ( !empty($id) && $edit != 'edit' ) {
            // NOTE: view data BASTTB (ajax)
            $html = $this->viewForm($id);
        } else if ( !empty($id) && $edit == 'edit' ) {
            // NOTE: edit data BASTTB (ajax)
            $html = $this->editForm($id);
        }else{
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getUnit()
    {
        $m_wilayah = new \Model\Storage\Wilayah_model();
        $d_wilayah = $m_wilayah->where('jenis', 'UN')->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_wilayah->count() > 0 ) {
            $d_wilayah = $d_wilayah->toArray();

            foreach ($d_wilayah as $k_wil => $v_wil) {
                $nama = trim(str_replace('KAB ', '', str_replace('KOTA ', '', strtoupper($v_wil['nama']))));
                $data[ $nama.' - '.$v_wil['kode'] ] = array(
                    'nama' => $nama,
                    'kode' => $v_wil['kode']
                );
            }

            ksort($data);
        }

        return $data;
    }

    public function getJurnalTrans()
    {
        $m_jt = new \Model\Storage\JurnalTrans_model();
        $d_jt = $m_jt->where(function ($query) {
                                $query->where('unit', 0)
                                      ->orWhereNull('unit');
                            })->where('mstatus', 1)->orderBy('nama', 'asc')->with(['detail', 'sumber_tujuan'])->get();

        $data = null;
        if ( $d_jt->count() > 0 ) {
            $data = $d_jt->toArray();
        }

        return $data;
    }

    public function getPerusahaan()
    {
        $m_perusahaan = new \Model\Storage\Perusahaan_model();
        $kode_perusahaan = $m_perusahaan->select('kode')->distinct('kode')->get();

        $data = null;
        if ( $kode_perusahaan->count() > 0 ) {
            $kode_perusahaan = $kode_perusahaan->toArray();

            foreach ($kode_perusahaan as $k => $val) {
                $m_perusahaan = new \Model\Storage\Perusahaan_model();
                $d_perusahaan = $m_perusahaan->where('kode', $val['kode'])->orderBy('version', 'desc')->first();

                $key = strtoupper($d_perusahaan->perusahaan).' - '.$d_perusahaan['kode'];
                $data[ $key ] = array(
                    'nama' => strtoupper($d_perusahaan->perusahaan),
                    'kode' => $d_perusahaan->kode
                );
            }

            ksort($data);
        }

        return $data;
    }

    public function getSumberTujuanCoa()
    {
        $params = $this->input->post('params');

        // cetak_r( $params, 1 );
        try {
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select * from det_jurnal_trans djt
                where
                    id = ".$params."
            ";
            $d_djt = $m_conf->hydrateRaw( $sql );

            $data = null;
            if ( $d_djt->count() > 0 ) {
                $d_djt = $d_djt->toArray()[0];

                $data = array(
                    'sumber' => $d_djt['sumber'],
                    'sumber_coa' => $d_djt['sumber_coa'],
                    'tujuan' => $d_djt['tujuan'],
                    'tujuan_coa' => $d_djt['tujuan_coa'],
                );
            }

            $this->result['status'] = 1;
            $this->result['content'] = $data;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function riwayat()
    {
        $data = null;

        $m_jt = new \Model\Storage\JurnalTrans_model();
        $d_jt = $m_jt->orderBy('nama', 'asc')->with(['detail'])->get();

        if ( $d_jt->count() > 0 ) {
            $data = $d_jt->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'riwayat', $content, true);

        return $html;
    }

    public function getSupplier()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select p.* from pelanggan p
            right join
                (select max(id) as id, nomor from pelanggan p where tipe = 'supplier' and jenis <> 'ekspedisi' and mstatus = 1 group by nomor) p2
                on
                    p.id = p2.id
            order by
                p.nama asc
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function addForm()
    {
        $content['unit'] = $this->getUnit();
        $content['jurnal_trans'] = $this->getJurnalTrans();
        $content['perusahaan'] = $this->getPerusahaan();
        $content['supplier'] = $this->getSupplier();

        $html = $this->load->view($this->pathView . 'addForm', $content, true);

        return $html;
    }

    public function viewForm($id)
    {
        $m_jurnal = new \Model\Storage\Jurnal_model();
        $d_jurnal = $m_jurnal->where('id', $id)->with(['jurnal_trans', 'detail'])->first();

        $data = null;
        if ( $d_jurnal ) {
            $data = $d_jurnal->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'viewForm', $content, true);

        return $html;
    }

    public function editForm($id)
    {
        $m_jurnal = new \Model\Storage\Jurnal_model();
        $d_jurnal = $m_jurnal->where('id', $id)->with(['jurnal_trans', 'detail'])->first();

        $data = null;
        if ( $d_jurnal ) {
            $d_jurnal = $d_jurnal->toArray();

            $data = $d_jurnal;
        }

        $content['unit'] = $this->getUnit();
        $content['jurnal_trans'] = $this->getJurnalTrans();
        $content['perusahaan'] = $this->getPerusahaan();
        $content['supplier'] = $this->getSupplier();
        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'editForm', $content, true);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_jurnal = new \Model\Storage\Jurnal_model();

            $m_jurnal->tanggal = $params['tanggal'];
            $m_jurnal->jurnal_trans_id = $params['jurnal_trans_id'];
            $m_jurnal->save();

            $id = $m_jurnal->id;
            foreach ($params['detail'] as $k_det => $v_det) {
                $m_dj = new \Model\Storage\DetJurnal_model();

                $m_dj->id_header = $id;
                $m_dj->tanggal = $v_det['tanggal'];
                $m_dj->det_jurnal_trans_id = $v_det['det_jurnal_trans_id'];
                $m_dj->supplier = (isset($v_det['supplier']) && !empty($v_det['supplier'])) ? $v_det['supplier'] : null;
                $m_dj->perusahaan = $v_det['perusahaan'];
                $m_dj->keterangan = $v_det['keterangan'];
                $m_dj->nominal = $v_det['nominal'];
                $m_dj->saldo = (isset($v_det['supplier']) && !empty($v_det['supplier'])) ? $v_det['nominal'] : null;
                $m_dj->asal = $v_det['sumber'];
                $m_dj->coa_asal = $v_det['sumber_coa'];
                $m_dj->tujuan = $v_det['tujuan'];
                $m_dj->coa_tujuan = $v_det['tujuan_coa'];
                $m_dj->unit = $v_det['unit'];
                $m_dj->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jurnal, $deskripsi_log);

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $id);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $id = $params['id'];

            $m_jurnal = new \Model\Storage\Jurnal_model();
            $m_jurnal->where('id', $id)->update(
                array(
                    'tanggal' => $params['tanggal'],
                    'jurnal_trans_id' => $params['jurnal_trans_id']
                )
            );

            $m_dj = new \Model\Storage\DetJurnal_model();
            $m_dj->where('id_header', $id)->delete();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_dj = new \Model\Storage\DetJurnal_model();

                $m_dj->id_header = $id;
                $m_dj->tanggal = $v_det['tanggal'];
                $m_dj->det_jurnal_trans_id = $v_det['det_jurnal_trans_id'];
                $m_dj->supplier = (isset($v_det['supplier']) && !empty($v_det['supplier'])) ? $v_det['supplier'] : null;
                $m_dj->perusahaan = $v_det['perusahaan'];
                $m_dj->keterangan = $v_det['keterangan'];
                $m_dj->nominal = $v_det['nominal'];
                $m_dj->saldo = (isset($v_det['supplier']) && !empty($v_det['supplier'])) ? $v_det['nominal'] : null;
                $m_dj->asal = $v_det['sumber'];
                $m_dj->coa_asal = $v_det['sumber_coa'];
                $m_dj->tujuan = $v_det['tujuan'];
                $m_dj->coa_tujuan = $v_det['tujuan_coa'];
                $m_dj->unit = $v_det['unit'];
                $m_dj->save();
            }

            $d_jurnal = $m_jurnal->where('id', $id)->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jurnal, $deskripsi_log);

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $id);
            $this->result['message'] = 'Data berhasil di update.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $id = $params['id'];

            $m_jurnal = new \Model\Storage\Jurnal_model();
            $d_jurnal = $m_jurnal->where('id', $id)->first();

            $m_dj = new \Model\Storage\DetJurnal_model();
            $m_dj->where('id_header', $id)->delete();

            $m_jurnal->where('id', $id)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_jurnal, $deskripsi_log);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}