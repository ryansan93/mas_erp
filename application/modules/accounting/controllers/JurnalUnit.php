<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JurnalUnit extends Public_Controller
{
    private $pathView = 'accounting/jurnal_unit/';
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
                'assets/accounting/jurnal_unit/js/jurnal-unit.js'
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                'assets/accounting/jurnal_unit/css/jurnal-unit.css'
            ));

            $data = $this->includes;

            $data['title_menu'] = 'Jurnal Unit';

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
        $unit = $params['unit'];

        $m_jurnal = new \Model\Storage\Jurnal_model();
        
        $d_jurnal = null;
        if ( $unit != 'all' ) {
            $d_jurnal = $m_jurnal->whereBetween('tanggal', [$start_date, $end_date])->where('unit', $unit)->orderBy('tanggal', 'desc')->with(['jurnal_trans', 'detail'])->get();
        } else {
            $d_jurnal = $m_jurnal->whereBetween('tanggal', [$start_date, $end_date])->orderBy('tanggal', 'desc')->with(['jurnal_trans', 'detail'])->get();
        }

        $data = null;
        if ( $d_jurnal->count() > 0 ) {
            $d_jurnal = $d_jurnal->toArray();

            foreach ($d_jurnal as $k_jurnal => $v_jurnal) {
                if ( $v_jurnal['jurnal_trans']['unit'] == 1 ) {
                    $data[] = $v_jurnal;
                }
            }
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
        $d_jt = $m_jt->where('unit', 1)->where('mstatus', 1)->orderBy('nama', 'asc')->with(['detail', 'sumber_tujuan'])->get();

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

    public function getNoreg()
    {
        $params = $this->input->post('params');

        try {
            $m_conf = new \Model\Storage\Conf();
            $now = $m_conf->getDate();

            $unit = $params['unit'];

            $sql_unit = "";
            if ( $unit != 'all' ) {
                $sql_unit = "and w.kode = '".$unit."'";
            }

            $tgl_min = prev_date( $now['tanggal'], 90 );

            $sql = "
                select 
                    rs.noreg,
                    CONVERT(VARCHAR(10), td.datang, 103) as tgl_terima,
                    cast(SUBSTRING(rs.noreg, LEN(rs.noreg)-1, 2) as int) as kandang,
                    m.nama as nama_mitra
                from rdim_submit rs
                right join
                    kandang k
                    on
                        k.id = rs.kandang
                right join
                    wilayah w
                    on
                        k.unit = w.id
                right join
                    order_doc od
                    on
                        rs.noreg = od.noreg
                right join
                    terima_doc td
                    on
                        od.no_order = td.no_order
                right join
                    (
                        select mm1.* from mitra_mapping mm1
                        right join
                            (select max(id) as id, nim from mitra_mapping group by nim) mm2
                            on
                                mm1.id = mm2.id
                    ) mm
                    on
                        rs.nim = mm.nim
                right join
                    mitra m
                    on
                        mm.mitra = m.id
                where
                    not exists (select * from tutup_siklus ts where ts.noreg = rs.noreg) and
                    rs.noreg is not null and
                    td.datang is not null and
                    td.datang > '".$tgl_min.' 00:00:00'."'
                    ".$sql_unit."
                group by
                    rs.noreg,
                    td.datang,
                    m.nama
                order by
                    td.datang desc
            ";
            $d_djt = $m_conf->hydrateRaw( $sql );

            if ( $d_djt->count() > 0 ) {
                $d_djt = $d_djt->toArray();
            }

            $this->result['status'] = 1;
            $this->result['content'] = $d_djt;
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

        $content['unit'] = $this->getUnit();
        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'riwayat', $content, true);

        return $html;
    }

    public function addForm()
    {
        $content['unit'] = $this->getUnit();
        $content['jurnal_trans'] = $this->getJurnalTrans();
        $content['perusahaan'] = $this->getPerusahaan();

        $html = $this->load->view($this->pathView . 'addForm', $content, true);

        return $html;
    }

    public function viewForm($id)
    {
        $m_jurnal = new \Model\Storage\Jurnal_model();
        $d_jurnal = $m_jurnal->where('id', $id)->with(['d_unit', 'jurnal_trans', 'detail'])->first();

        $data = null;
        if ( $d_jurnal ) {
            $d_jurnal = $d_jurnal->toArray();

            $m_dj = new \Model\Storage\DetJurnal_model();
            $d_dj = $m_dj->where('id_header', $d_jurnal['id'])->with(['d_perusahaan'])->first();

            $perusahaan = null;
            $plasma = null;
            if ( $d_dj ) {
                $d_dj = $d_dj->toArray();
                $perusahaan = $d_dj['d_perusahaan']['perusahaan'];
                $noreg = $d_dj['noreg'];

                if ( !empty($noreg) ) {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select 
                            rs.noreg,
                            CONVERT(VARCHAR(10), td.datang, 103) as tgl_terima,
                            cast(SUBSTRING(rs.noreg, LEN(rs.noreg)-1, 2) as int) as kandang,
                            m.nama as nama_mitra
                        from rdim_submit rs
                        right join
                            order_doc od
                            on
                                rs.noreg = od.noreg
                        right join
                            terima_doc td
                            on
                                od.no_order = td.no_order
                        right join
                            (
                                select mm1.* from mitra_mapping mm1
                                right join
                                    (select max(id) as id, nim from mitra_mapping group by nim) mm2
                                    on
                                        mm1.id = mm2.id
                            ) mm
                            on
                                rs.nim = mm.nim
                        right join
                            mitra m
                            on
                                mm.mitra = m.id
                        where
                            rs.noreg = '".$noreg."'
                        group by
                            rs.noreg,
                            td.datang,
                            m.nama
                        order by
                            td.datang desc
                    ";
                    $d_rs = $m_conf->hydrateRaw( $sql );

                    if ( $d_rs->count() > 0 ) {
                        $d_rs = $d_rs->toArray()[0];

                        $plasma = $d_rs;
                    }
                }
            }

            $data = $d_jurnal;
            $data['perusahaan'] = $perusahaan;
            $data['plasma'] = $plasma;
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

            $m_dj = new \Model\Storage\DetJurnal_model();
            $d_dj = $m_dj->where('id_header', $d_jurnal['id'])->with(['d_perusahaan'])->first();

            $perusahaan = null;
            $plasma = null;
            if ( $d_dj ) {
                $d_dj = $d_dj->toArray();
                $perusahaan = $d_dj['perusahaan'];
                $noreg = $d_dj['noreg'];

                if ( !empty($noreg) ) {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select 
                            rs.noreg,
                            CONVERT(VARCHAR(10), td.datang, 103) as tgl_terima,
                            cast(SUBSTRING(rs.noreg, LEN(rs.noreg)-1, 2) as int) as kandang,
                            m.nama as nama_mitra
                        from rdim_submit rs
                        right join
                            order_doc od
                            on
                                rs.noreg = od.noreg
                        right join
                            terima_doc td
                            on
                                od.no_order = td.no_order
                        right join
                            (
                                select mm1.* from mitra_mapping mm1
                                right join
                                    (select max(id) as id, nim from mitra_mapping group by nim) mm2
                                    on
                                        mm1.id = mm2.id
                            ) mm
                            on
                                rs.nim = mm.nim
                        right join
                            mitra m
                            on
                                mm.mitra = m.id
                        where
                            rs.noreg = '".$noreg."'
                        group by
                            rs.noreg,
                            td.datang,
                            m.nama
                        order by
                            td.datang desc
                    ";
                    $d_rs = $m_conf->hydrateRaw( $sql );

                    if ( $d_rs->count() > 0 ) {
                        $d_rs = $d_rs->toArray()[0];

                        $plasma = $d_rs;
                    }
                }
            }

            $data = $d_jurnal;
            $data['perusahaan'] = $perusahaan;
            $data['plasma'] = $plasma;
        }

        $content['unit'] = $this->getUnit();
        $content['jurnal_trans'] = $this->getJurnalTrans();
        $content['perusahaan'] = $this->getPerusahaan();
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
            $m_jurnal->unit = $params['unit'];
            $m_jurnal->save();

            $id = $m_jurnal->id;
            foreach ($params['detail'] as $k_det => $v_det) {
                $m_dj = new \Model\Storage\DetJurnal_model();

                $m_dj->id_header = $id;
                $m_dj->tanggal = $v_det['tanggal'];
                $m_dj->det_jurnal_trans_id = $v_det['det_jurnal_trans_id'];
                $m_dj->perusahaan = $params['perusahaan'];
                $m_dj->keterangan = $v_det['keterangan'];
                $m_dj->nominal = $v_det['nominal'];
                $m_dj->asal = $v_det['sumber'];
                $m_dj->coa_asal = $v_det['sumber_coa'];
                $m_dj->tujuan = $v_det['tujuan'];
                $m_dj->coa_tujuan = $v_det['tujuan_coa'];
                $m_dj->unit = $params['unit'];
                $m_dj->pic = $v_det['pic'];
                $m_dj->noreg = (isset($params['noreg']) && !empty($params['noreg'])) ? $params['noreg'] : null;
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
                    'jurnal_trans_id' => $params['jurnal_trans_id'],
                    'unit' => $params['unit']
                )
            );

            $m_dj = new \Model\Storage\DetJurnal_model();
            $m_dj->where('id_header', $id)->delete();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_dj = new \Model\Storage\DetJurnal_model();

                $m_dj->id_header = $id;
                $m_dj->tanggal = $v_det['tanggal'];
                $m_dj->det_jurnal_trans_id = $v_det['det_jurnal_trans_id'];
                $m_dj->perusahaan = $params['perusahaan'];
                $m_dj->keterangan = $v_det['keterangan'];
                $m_dj->nominal = $v_det['nominal'];
                $m_dj->asal = $v_det['sumber'];
                $m_dj->coa_asal = $v_det['sumber_coa'];
                $m_dj->tujuan = $v_det['tujuan'];
                $m_dj->coa_tujuan = $v_det['tujuan_coa'];
                $m_dj->unit = $params['unit'];
                $m_dj->pic = $v_det['pic'];
                $m_dj->noreg = (isset($params['noreg']) && !empty($params['noreg'])) ? $params['noreg'] : null;
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