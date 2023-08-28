<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends Public_Controller {

    private $pathView = 'report/pembelian/';
    private $url;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index($segment=0)
    {
        $akses = hakAkses($this->url);
        if ( $akses['a_view'] == 1 ) {
            $this->add_external_js(array(
                'assets/select2/js/select2.min.js',
                "assets/report/pembelian/js/pembelian.js",
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                "assets/report/pembelian/css/pembelian.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;
            $content['unit'] = $this->getUnit();
            $content['perusahaan'] = $this->getPerusahaan();

            $content['title_menu'] = 'Pembelian';

            // Load Indexx
            $data['view'] = $this->load->view($this->pathView.'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getUnit()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                REPLACE(REPLACE(w.nama, 'Kota ', ''), 'Kab ', '') as nama,
                w.kode
            from wilayah w
            where
                w.jenis = 'UN'
            group by
                REPLACE(REPLACE(w.nama, 'Kota ', ''), 'Kab ', ''),
                w.kode
            order by
                REPLACE(REPLACE(w.nama, 'Kota ', ''), 'Kab ', '') asc
        ";
        $d_unit = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_unit->count() > 0 ) {
            $data = $d_unit->toArray();
        }

        return $data;
    }

    public function getPerusahaan()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select p.id, p.kode, p.perusahaan as nama from perusahaan p
            right join
                (select max(id) as id, kode from perusahaan group by kode) _p
                on
                    _p.id = p.id
            order by
                p.perusahaan asc
        ";
        $d_perusahaan = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_perusahaan->count() > 0 ) {
            $data = $d_perusahaan->toArray();
        }

        return $data;
    }

    public function getDataDoc( $start_date, $end_date, $unit, $perusahaan )
    {
        $sql_unit = "and data.unit in ('".implode("', '", $unit)."')";
        if ( in_array('all', $unit) ) {
            $sql_unit = null;
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                data.noreg,
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            from
            (
                select
                    od.noreg,
                    od.no_order,
                    cast(od.tgl_submit as date) as datang,
                    rs.nama,
                    rs.kandang,
                    SUBSTRING(od.no_order, 5, 3)  as unit,
                    supl.nama as supplier,
                    (brg.nama + ' BOX ' + isnull(od.jns_box, '')) as barang,
                    prs.nama as nama_perusahaan,
                    prs.kode as kode_perusahaan,
                    td.jml_ekor as jumlah,
                    od.harga,
                    (td.jml_ekor * od.harga) as total
                from 
                    ( 
                        select od1.* from order_doc od1
                        right join
                            (select max(id) as id, no_order from order_doc group by no_order) od2
                            on
                                od1.id = od2.id
                    ) od
                right join
                    (
                        select td1.* from terima_doc td1
                        right join
                            (select max(id) as id, no_order from terima_doc group by no_order) td2
                            on
                                td1.id = td2.id
                    ) td 
                    on
                        od.no_order = td.no_order 
                right join
                    (
                        select 
                            rs.noreg, 
                            m.nama,
                            SUBSTRING(rs.noreg, 10, 2) as kandang
                        from rdim_submit rs 
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
                    ) rs
                    on
                        rs.noreg = od.noreg
                right join
                    (
                        select p1.* from pelanggan p1
                        right join
                            (select max(id) as id, nomor from pelanggan where tipe = 'supplier' and jenis <> 'ekspedisi' and mstatus = 1 group by nomor) p2
                            on
                                p1.id = p2.id
                    ) supl
                    on
                        supl.nomor = od.supplier 
                right join
                    (
                        select b1.* from barang b1
                        right join
                            (select max(id) as id, kode from barang b group by kode) b2
                            on
                                b1.id = b2.id
                    ) brg
                    on
                        brg.kode = od.item
                right join
                    (
                        select p.id, p.kode, p.perusahaan as nama from perusahaan p
                        right join
                            (select max(id) as id, kode from perusahaan group by kode) _p
                            on
                                _p.id = p.id
                    ) prs
                    on
                        prs.kode = od.perusahaan 
            ) data
            where
                data.datang between '".$start_date."' and '".$end_date."' and
                data.kode_perusahaan = '".$perusahaan."'
                ".$sql_unit."
            group by
                data.noreg,
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.kode_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            order by
                data.datang asc
        ";
        $d_beli = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_beli->count() ) {
            $data = $d_beli->toArray();
        }

        return $data;
    }

    public function getDataPakan( $start_date, $end_date, $unit, $perusahaan )
    {
        $sql_unit = "and data.unit in ('".implode("', '", $unit)."')";
        if ( in_array('all', $unit) ) {
            $sql_unit = null;
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            from
            (
                select
                    op.no_order,
                    tp.tgl_terima as datang,
                    null as nama,
                    null as kandang,
                    SUBSTRING(op.no_order, 5, 3)  as unit,
                    supl.nama as supplier,
                    brg.nama as barang,
                    prs.nama as nama_perusahaan,
                    prs.kode as kode_perusahaan,
                    dtp.jumlah,
                    op.harga,
                    dtp.jumlah * op.harga as total
                from det_terima_pakan dtp 
                right join
                    terima_pakan tp 
                    on
                        dtp.id_header = tp.id
                right join
                    kirim_pakan kp 
                    on
                        tp.id_kirim_pakan = kp.id
                right join
                    (
                        select opd.*, _op.no_order, _op.tgl_trans, _op.rcn_kirim, _op.supplier from order_pakan_detail opd 
                        right join
                            order_pakan _op
                            on
                                opd.id_header = _op.id
                    ) op 
                    on
                        op.no_order = kp.no_order and
                        op.barang = dtp.item 
                right join
                    (
                        select p1.* from pelanggan p1
                        right join
                            (select max(id) as id, nomor from pelanggan where tipe = 'supplier' and jenis <> 'ekspedisi' and mstatus = 1 group by nomor) p2
                            on
                                p1.id = p2.id
                    ) supl
                    on
                        supl.nomor = op.supplier 
                right join
                    (
                        select b1.* from barang b1
                        right join
                            (select max(id) as id, kode from barang b group by kode) b2
                            on
                                b1.id = b2.id
                    ) brg
                    on
                        brg.kode = dtp.item 
                right join
                    (
                        select p.id, p.kode, p.perusahaan as nama from perusahaan p
                        right join
                            (select max(id) as id, kode from perusahaan group by kode) _p
                            on
                                _p.id = p.id
                    ) prs
                    on
                        prs.kode = op.perusahaan
            ) data
            where
                data.datang is not null and
                data.datang between '".$start_date."' and '".$end_date."' and
                data.kode_perusahaan = '".$perusahaan."'
                ".$sql_unit."
            group by
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.kode_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            order by
                data.datang asc
        ";
        $d_beli = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_beli->count() ) {
            $data = $d_beli->toArray();
        }

        return $data;
    }

    public function getDataVoadip( $start_date, $end_date, $unit, $perusahaan )
    {
        $sql_unit = "and data.unit in ('".implode("', '", $unit)."')";
        if ( in_array('all', $unit) ) {
            $sql_unit = null;
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            from
            (
                select
                    ov.no_order,
                    tv.tgl_terima as datang,
                    null as nama,
                    null as kandang,
                    SUBSTRING(ov.no_order, 5, 3)  as unit,
                    supl.nama as supplier,
                    brg.nama as barang,
                    prs.nama as nama_perusahaan,
                    prs.kode as kode_perusahaan,
                    dtv.jumlah,
                    ov.harga,
                    dtv.jumlah * ov.harga as total
                from det_terima_voadip dtv 
                right join
                    terima_voadip tv 
                    on
                        dtv.id_header = tv.id
                right join
                    kirim_voadip kp 
                    on
                        tv.id_kirim_voadip = kp.id
                right join
                    (
                        select ovd.*, _ov.no_order, _ov.supplier from order_voadip_detail ovd 
                        right join
                            order_voadip _ov
                            on
                                ovd.id_order = _ov.id
                    ) ov 
                    on
                        ov.no_order = kp.no_order and
                        ov.kode_barang = dtv.item 
                right join
                    (
                        select p1.* from pelanggan p1
                        right join
                            (select max(id) as id, nomor from pelanggan where tipe = 'supplier' and jenis <> 'ekspedisi' and mstatus = 1 group by nomor) p2
                            on
                                p1.id = p2.id
                    ) supl
                    on
                        supl.nomor = ov.supplier 
                right join
                    (
                        select b1.* from barang b1
                        right join
                            (select max(id) as id, kode from barang b group by kode) b2
                            on
                                b1.id = b2.id
                    ) brg
                    on
                        brg.kode = dtv.item 
                right join
                    (
                        select p.id, p.kode, p.perusahaan as nama from perusahaan p
                        right join
                            (select max(id) as id, kode from perusahaan group by kode) _p
                            on
                                _p.id = p.id
                    ) prs
                    on
                        prs.kode = ov.perusahaan
            ) data
            where
                data.datang is not null and
                data.datang between '".$start_date."' and '".$end_date."' and
                data.kode_perusahaan = '".$perusahaan."'
                ".$sql_unit."
            group by
                data.no_order,
                data.datang,
                data.nama,
                data.kandang,
                data.unit,
                data.supplier,
                data.barang,
                data.nama_perusahaan,
                data.kode_perusahaan,
                data.jumlah,
                data.harga,
                data.total
            order by
                data.datang asc
        ";
        $d_beli = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_beli->count() ) {
            $data = $d_beli->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $jenis = $params['jenis'];
            $start_date = $params['start_date'].' 00:00:00.000';
            $end_date = $params['end_date'].' 23:59:59.999';
            $unit = $params['unit'];
            $perusahaan = $params['perusahaan'];

            $data = null;
            if ( stristr($jenis, 'doc') !== FALSE ) {
                $data = $this->getDataDoc( $start_date, $end_date, $unit, $perusahaan );
            } else if ( stristr($jenis, 'pakan') !== FALSE ) {
                $data = $this->getDataPakan( $start_date, $end_date, $unit, $perusahaan );
            } else if ( stristr($jenis, 'voadip') !== FALSE ) {
                $data = $this->getDataVoadip( $start_date, $end_date, $unit, $perusahaan );
            }

            $content['data'] = $data;

            $html = $this->load->view($this->pathView.'list', $content, TRUE);

            $this->result['status'] = 1;
            $this->result['html'] = $html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function excryptParams()
    {
        $params = $this->input->post('params');

        try {
            $params_encrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = $params_encrypt;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportExcel($params_encrypt)
    {
        $params = json_decode( exDecrypt($params_encrypt), true );

        $jenis = $params['jenis'];
        $start_date = $params['start_date'].' 00:00:00.000';
        $end_date = $params['end_date'].' 23:59:59.999';
        $unit = $params['unit'];
        $perusahaan = $params['perusahaan'];

        $data = null;
        if ( stristr($jenis, 'doc') !== FALSE ) {
            $data = $this->getDataDoc( $start_date, $end_date, $unit, $perusahaan );
        } else if ( stristr($jenis, 'pakan') !== FALSE ) {
            $data = $this->getDataPakan( $start_date, $end_date, $unit, $perusahaan );
        } else if ( stristr($jenis, 'voadip') !== FALSE ) {
            $data = $this->getDataVoadip( $start_date, $end_date, $unit, $perusahaan );
        }

        $content['data'] = $data;

        $res_view_html = $this->load->view($this->pathView.'export_excel', $content, true);

        // header("Content-type: application/xls");
        // header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        // header("Content-type:   application/x-msexcel; charset=utf-8");
        header("Content-type:   application/ms-excel; charset=utf-8");
        $filename = 'PEMBELIAN_'.strtoupper($jenis).'_'.str_replace('-', '', $params['start_date']).'_'.str_replace('-', '', $params['end_date']).'.xls';
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}