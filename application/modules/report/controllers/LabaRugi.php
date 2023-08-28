<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LabaRugi extends Public_Controller {

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
                "assets/report/laba_rugi/js/laba-rugi.js",
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                "assets/report/laba_rugi/css/laba-rugi.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;
            $content['unit'] = $this->getUnit();
            $content['title_menu'] = 'Laba Rugi';

            // Load Indexx
            $data['view'] = $this->load->view('report/laba_rugi/index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
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

    public function getData()
    {
        $params = $this->input->get('params');

        $unit = $params['unit'];
        $bulan = $params['bulan'];
        $tahun = substr($params['tahun'], 0, 4);

        $sql_unit = "rdim_submit.kode_unit = '".$unit."' and";
        if ( $unit == 'all' ) {
            $sql_unit = null;
        }

        $i = 0;
        $_bulan = 12;
        if ( $bulan != 'all' ) {
            $i = $bulan-1;
            $_bulan = $bulan;
        }

        $data = null;
        for (; $i < $_bulan; $i++) { 
            $angka_bulan = (strlen($i+1) == 1) ? '0'.$i+1 : $i+1;

            $date = $tahun.'-'.$angka_bulan.'-01';
            $start_date = date("Y-m-d", strtotime($date)).' 00:00:00';
            $end_date = date("Y-m-t", strtotime($date)).' 23:59:59';

            $sql = "
                select
                    count(*) as jumlah_rhpp,
                    sum(ekor_panen) as ekor_panen,
                    sum(total_pakai_pakan) as total_pakai_pakan,
                    sum(lama_panen) / count(*) as lama_panen,
                    sum(ekor_panen) as ekor_panen,
                    sum(kg_panen) as kg_panen,
                    sum(total) as total,
                    sum(rata_harga_panen) / count(*) as rata_harga_panen,
                    sum(umur) / count(*) as umur,
                    ABS(((sum(populasi_panen) - sum(ekor_panen)) / sum(populasi_panen)) * 100) as deplesi,
                    sum(fcr) / count(*) as fcr,
                    sum(bb) / count(*) as bb,
                    sum(ip) / count(*) as ip,
                    sum(rhpp_ke_pusat) / count(*) as rata_rhpp_ke_pusat,
                    sum(transfer) / count(*) as rata_transfer,
                    sum(lr_inti) as lr_inti,
                    ABS(sum(lr_inti)) - sum(biaya_operasional) as lr_inti_tanpa_ops_300,
                    sum(bonus_pasar) as bonus_pasar,
                    sum(tot_pembelian_sapronak) as total_sapronak,
                    sum(pdpt_peternak_belum_pajak) as total_pendapatan_peternak,
                    (sum(pdpt_peternak_belum_pajak) / sum(populasi_panen)) as rata_total_pendapatan_peternak,
                    sum(biaya_materai) as total_biaya_materai,
                    sum(biaya_operasional) as total_biaya_ops_300,
                    sum(modal_inti) / count(*) as modal_inti,
                    sum(modal_inti_sebenarnya) / count(*) as modal_inti_sebenarnya
                from (
                    select 
                        ts.noreg,
                        ts.tgl_tutup,
                        CAST(drs.ekor_panen AS FLOAT) as ekor_panen,
                        sum(drs.kg_panen) as kg_panen,
                        drs.total as total,
                        drs.rata_harga_panen as rata_harga_panen,
                        kp_tujuan.total_pakan as total_pakan_terima,
                        CASE
                            WHEN kp_asal.total_pakan IS NOT NULL THEN kp_asal.total_pakan
                            ELSE 0
                        END AS total_pakan_pindah,
                        CASE
                            WHEN rp.total_retur IS NOT NULL THEN rp.total_retur
                            ELSE 0
                        END AS total_retur,
                        CASE
                            WHEN kp_asal.total_pakan IS NOT NULL AND rp.total_retur IS NOT NULL THEN (kp_tujuan.total_pakan - kp_asal.total_pakan - rp.total_retur)
                            WHEN kp_asal.total_pakan IS NOT NULL AND rp.total_retur IS NULL THEN (kp_tujuan.total_pakan - kp_asal.total_pakan)
                            WHEN kp_asal.total_pakan IS NULL AND rp.total_retur IS NOT NULL THEN (kp_tujuan.total_pakan - rp.total_retur)
                            ELSE kp_tujuan.total_pakan
                        END AS total_pakai_pakan,
                        drs.tgl_panen_awal,
                        drs.tgl_panen_akhir,
                        drs.lama_panen,
                        (DateDiff (Day,rhpp.tgl_docin,max(drs.tgl_panen_akhir)) + 1) as umur,
                        CAST(rhpp.populasi AS FLOAT) as populasi_panen,
                        CAST(rhpp.fcr AS FLOAT) as fcr,
                        CAST(rhpp.bb AS FLOAT) as bb,
                        CAST(rhpp.ip AS FLOAT) as ip,
                        (sum((DateDiff (Day,drs.tgl_panen_akhir,ts.tgl_tutup) + 1)) / count(*)) as rhpp_ke_pusat,
                        (sum((DateDiff (Day,drs.tgl_panen_akhir,kp.tgl_bayar) + 1)) / count(*)) as transfer,
                        rhpp.lr_inti,
                        rhpp.bonus_pasar,
                        rhpp.tot_pembelian_sapronak,
                        rhpp.pdpt_peternak_belum_pajak,
                        rhpp.biaya_materai,
                        rhpp.biaya_operasional,
                        (rhpp.tot_pembelian_sapronak+rhpp.pdpt_peternak_belum_pajak+rhpp.biaya_materai+rhpp.biaya_operasional) / sum(drs.kg_panen) as modal_inti,
                        ((rhpp.tot_pembelian_sapronak+rhpp.pdpt_peternak_belum_pajak+rhpp.biaya_materai+rhpp.biaya_operasional)-rhpp.bonus_pasar) / sum(drs.kg_panen) as modal_inti_sebenarnya
                    from
                        tutup_siklus ts 
                    right join
                        (
                            select max(id) as id, noreg from tutup_siklus group by noreg
                        ) _ts
                        on
                            ts.id = _ts.id
                    right join
                        (
                            select r.noreg, r.populasi, r.tgl_docin, r.jml_panen_ekor, r.jml_panen_kg, r.bb, r.fcr, r.deplesi, r.rata_umur, r.ip, rhpp_inti.lr_inti, rhpp_plasma.bonus_pasar, rhpp_inti.tot_pembelian_sapronak, rhpp_plasma.pdpt_peternak_belum_pajak, rhpp_inti.biaya_materai, rhpp_inti.biaya_operasional
                            from rhpp r
                            right join
                                (
                                    select noreg, bonus_pasar, pdpt_peternak_belum_pajak from rhpp r where jenis = 'rhpp_plasma'
                                ) rhpp_plasma
                                on
                                    rhpp_plasma.noreg = r.noreg 
                            right join
                                (
                                    select noreg, lr_inti, biaya_materai, biaya_operasional, tot_pembelian_sapronak from rhpp r where jenis = 'rhpp_inti'
                                ) rhpp_inti
                                on
                                    rhpp_inti.noreg = r.noreg
                            right join
                                tutup_siklus ts 
                                on
                                    r.id_ts = ts.id
                            right join
                                (
                                    select rs.noreg, k.id as id_kandang, k.kandang as no_kandang, w.kode as kode_unit from rdim_submit rs
                                    right join
                                        kandang k 
                                        on
                                            rs.kandang = k.id
                                    right join
                                        wilayah w 
                                        on
                                            k.unit = w.id
                                )  rdim_submit
                                on
                                    rdim_submit.noreg = r.noreg 
                            where
                                r.lr_inti is not null and
                                ".$sql_unit."
                                ts.tgl_tutup between '".$start_date."' and '".$end_date."'
                            group by r.noreg, r.populasi, r.tgl_docin, r.jml_panen_ekor, r.jml_panen_kg, r.bb, r.fcr, r.deplesi, r.rata_umur, r.ip, rhpp_inti.lr_inti, rhpp_plasma.bonus_pasar, rhpp_inti.tot_pembelian_sapronak, rhpp_plasma.pdpt_peternak_belum_pajak, rhpp_inti.biaya_materai, rhpp_inti.biaya_operasional
                        ) rhpp
                        on
                            rhpp.noreg = ts.noreg
                    right join
                        (
                            select rs.noreg, k.id as id_kandang, k.kandang as no_kandang, w.kode as kode_unit from rdim_submit rs
                            right join
                                kandang k 
                                on
                                    rs.kandang = k.id
                            right join
                                wilayah w 
                                on
                                    k.unit = w.id
                        )  rdim_submit
                        on
                            rdim_submit.noreg = rhpp.noreg 
                    left join
                        (
                            select 
                                rs.noreg as noreg, 
                                min(rs.tgl_panen) as tgl_panen_awal,
                                max(rs.tgl_panen) as tgl_panen_akhir,
                                (DateDiff (Day,min(rs.tgl_panen),max(rs.tgl_panen)) + 1) as lama_panen,
                                (DateDiff (Day,min(rs.tgl_panen),max(rs.tgl_panen)) + 1) as umur_panen,
                                sum(drs.ekor) as ekor_panen,
                                sum(drs.tonase) as kg_panen,
                                sum(drs.tonase * drs.harga) as total, 
                                (sum(drs.tonase * drs.harga) / sum(drs.tonase)) as rata_harga_panen
                            from det_real_sj drs 
                            right join
                                (select max(id) as id, tgl_panen, noreg from real_sj group by tgl_panen, noreg) rs 
                                on
                                    drs.id_header = rs.id
                            group by 
                                rs.noreg  
                        ) drs 
                        on
                            drs.noreg = rhpp.noreg
                    left join
                        (
                            select kp.tujuan as tujuan, sum(dtp.jumlah) as total_pakan from kirim_pakan kp 
                            left join
                                terima_pakan tp 
                                on
                                    kp.id = tp.id_kirim_pakan 
                            left join
                                det_terima_pakan dtp  
                                on
                                    tp.id = dtp.id_header 
                            group by
                                kp.tujuan
                        ) kp_tujuan
                        on
                            drs.noreg = kp_tujuan.tujuan 
                    left join
                        (
                            select 
                                kp.asal as asal, 
                                sum(dtp.jumlah) AS total_pakan 
                            from kirim_pakan kp 
                            left join
                                terima_pakan tp 
                                on
                                    kp.id = tp.id_kirim_pakan 
                            left join
                                det_terima_pakan dtp  
                                on
                                    tp.id = dtp.id_header 
                            group by
                                kp.asal
                        ) kp_asal
                        on
                            drs.noreg = kp_asal.asal 
                    left join
                        (
                            select rp.id_asal, sum(drp.jumlah) as total_retur from retur_pakan rp 
                            left join
                                det_retur_pakan drp 
                                on
                                    rp.id = drp.id_header 
                            group by
                                rp.id_asal
                        ) as rp
                        on
                            drs.noreg = rp.id_asal
                    left join
                        (
                            select
                                kppd2.noreg,
                                kpp.tgl_bayar
                            from 
                                konfirmasi_pembayaran_peternak_det2 kppd2
                            right join
                                konfirmasi_pembayaran_peternak_det kppd
                                on
                                    kppd2.id_header = kppd.id 
                            right join
                                konfirmasi_pembayaran_peternak kpp 
                                on
                                    kpp.id = kppd.id_header 
                            group by
                                kppd2.noreg,
                                kpp.tgl_bayar
                        ) kp
                        on
                            kp.noreg = ts.noreg 
                    where
                        ".$sql_unit."
                        ts.tgl_tutup between '".$start_date."' and '".$end_date."'
                    group by
                        ts.noreg,
                        ts.tgl_tutup,
                        kp_tujuan.total_pakan,
                        kp_asal.total_pakan,
                        rp.total_retur,
                        drs.ekor_panen,
                        drs.total,
                        drs.rata_harga_panen,
                        drs.tgl_panen_awal,
                        drs.tgl_panen_akhir,
                        drs.lama_panen,
                        drs.umur_panen,
                        rhpp.tgl_docin,
                        rhpp.populasi,
                        rhpp.fcr,
                        rhpp.bb,
                        rhpp.ip,
                        rhpp.lr_inti,
                        rhpp.bonus_pasar,
                        rhpp.tot_pembelian_sapronak,
                        rhpp.pdpt_peternak_belum_pajak,
                        rhpp.biaya_materai,
                        rhpp.biaya_operasional
                ) as data
            ";

            $m_conf = new \Model\Storage\Conf();
            $d_conf = $m_conf->hydrateRaw($sql);

            // cetak_r( $sql, 1 );

            $_data = null;
            if ( $d_conf->count() > 0 ) {
                $_data = $d_conf->toArray();
            }

            $nama_bulan = explode(' ', tglIndonesia($date, '-', ' ', true))[1];

            $kolom_performa = array('ekor_panen', 'total_pakai_pakan', 'lama_panen', 'umur', 'deplesi', 'fcr', 'bb', 'ip');
            $kolom_panen_dan_rhpp_plasma = array('ekor_panen', 'kg_panen', 'total', 'rata_harga_panen', 'total_pendapatan_peternak', 'rata_total_pendapatan_peternak', 'rata_rhpp_ke_pusat', 'rata_transfer');
            $kolom_laporan_inti = array('modal_inti', 'modal_inti_sebenarnya', 'total_biaya_ops_300', 'lr_inti_tanpa_ops_300', 'lr_inti', 'total_biaya_ops_300');
            $performa = null;
            $panen_dan_rhpp_plasma = null;
            $laporan_inti = null;
            if ( !empty($_data) ) {
                if ( !empty($kolom_performa) ) {
                    foreach ($kolom_performa as $key => $value) {
                        if ( !empty($_data[0][ $value ]) ) {
                            $performa[ $value ] = $_data[0][ $value ];
                        }
                    }
                }

                if ( !empty($kolom_panen_dan_rhpp_plasma) ) {
                    foreach ($kolom_panen_dan_rhpp_plasma as $key => $value) {
                        if ( !empty($_data[0][ $value ]) ) {
                            $panen_dan_rhpp_plasma[ $value ] = $_data[0][ $value ];
                        }
                    }
                }

                if ( !empty($kolom_laporan_inti) ) {
                    foreach ($kolom_laporan_inti as $key => $value) {
                        if ( !empty($_data[0][ $value ]) ) {
                            $laporan_inti[ $value ] = $_data[0][ $value ];
                        }
                    }
                }
            }

            $data[ $i ]['bulan'] = $nama_bulan;
            $data[ $i ]['jumlah_rhpp'] = $_data[0]['jumlah_rhpp'];
            $data[ $i ]['data'] = array(
                'performa' => $performa, 
                'panen_dan_rhpp_plasma' => $panen_dan_rhpp_plasma, 
                'laporan_inti' => $laporan_inti
            );
        }

        $content['data'] = $data;
        $content['tahun'] = $tahun;
        $content['unit'] = $unit;

        $html = $this->load->view('report/laba_rugi/list', $content, TRUE);

        echo $html;
    }

    public function getDataViewForm($unit, $bulan, $tahun)
    {
        $angka_bulan = (strlen($bulan) == 1) ? '0'.$bulan : $bulan;

        $sql_unit = null;
        if ( $unit != 'all' ) {
            $sql_unit = "rdim_submit.kode_unit = '".$unit."' and";
        }

        $date = $tahun.'-'.$angka_bulan.'-01';
        $start_date = date("Y-m-d", strtotime($date)).' 00:00:00';
        $end_date = date("Y-m-t", strtotime($date)).' 23:59:59';

        $sql = "
            select 
                rdim_submit.nama_mitra,
                rdim_submit.no_kandang,
                CAST(SUBSTRING(ts.noreg, 8, 2) as int) as no_siklus,
                ts.noreg,
                ts.tgl_docin,
                CAST(rhpp.populasi AS FLOAT) as populasi_panen,
                rhpp.nama_doc,
                ts.tgl_tutup,
                CAST(drs.ekor_panen AS FLOAT) as ekor_panen,
                drs.kg_panen as kg_panen,
                drs.total as total,
                drs.rata_harga_panen as rata_harga_panen,
                kp_tujuan.kode_barang as kode_barang,
                kp_tujuan.nama_barang as nama_barang,
                sum(kp_tujuan.total_pakan) as total_pakan,
                drs.tgl_panen_awal,
                drs.tgl_panen_akhir,
                drs.lama_panen,
                (DateDiff (Day,rhpp.tgl_docin,max(drs.tgl_panen_akhir)) + 1) as umur,
                CAST(rhpp.fcr AS FLOAT) as fcr,
                CAST(rhpp.bb AS FLOAT) as bb,
                CAST(rhpp.ip AS FLOAT) as ip,
                CAST(rhpp.deplesi AS FLOAT) as deplesi,
                kp.tgl_bayar,
                (DateDiff (Day,drs.tgl_panen_akhir,ts.tgl_tutup) + 1) as rhpp_ke_pusat,
                (DateDiff (Day,ts.tgl_tutup,kp.tgl_bayar)) as transfer,
                rhpp.lr_inti,
                rhpp.bonus_pasar,
                rhpp.tot_pembelian_sapronak,
                rhpp.pdpt_peternak_belum_pajak,
                rhpp.biaya_materai,
                rhpp.biaya_operasional,
                (rhpp.tot_pembelian_sapronak+rhpp.pdpt_peternak_belum_pajak+rhpp.biaya_materai+rhpp.biaya_operasional) / drs.kg_panen as modal_inti,
                ((rhpp.tot_pembelian_sapronak+rhpp.pdpt_peternak_belum_pajak+rhpp.biaya_materai+rhpp.biaya_operasional)-rhpp.bonus_pasar) / drs.kg_panen as modal_inti_sebenarnya
            from
                tutup_siklus ts 
            right join
                (
                    select r.noreg, r.populasi, r.tgl_docin, r.jml_panen_ekor, r.jml_panen_kg, r.bb, r.fcr, r.deplesi, r.rata_umur, r.ip, rhpp_inti.lr_inti, rhpp_plasma.bonus_pasar, rhpp_inti.tot_pembelian_sapronak, rhpp_plasma.pdpt_peternak_belum_pajak, rhpp_inti.biaya_materai, rhpp_inti.biaya_operasional, rhpp_inti.barang as nama_doc
                    from rhpp r
                    right join
                        (
                            select noreg, bonus_pasar, pdpt_peternak_belum_pajak from rhpp r where jenis = 'rhpp_plasma'
                        ) rhpp_plasma
                        on
                            rhpp_plasma.noreg = r.noreg 
                    right join
                        (
                            select 
                                r.noreg, 
                                r.lr_inti, 
                                r.biaya_materai, 
                                r.biaya_operasional, 
                                r.tot_pembelian_sapronak,
                                rd.barang
                            from rhpp r 
                            right join
                                rhpp_doc rd
                                on
                                    r.id = rd.id_header
                            where 
                                r.jenis = 'rhpp_inti'
                        ) rhpp_inti
                        on
                            rhpp_inti.noreg = r.noreg
                    right join
                        tutup_siklus ts 
                        on
                            r.id_ts = ts.id
                    right join
                        (
                            select 
                                rs.noreg, 
                                k.id as id_kandang, 
                                k.kandang as no_kandang, 
                                w.kode as kode_unit 
                            from 
                                rdim_submit rs
                            right join
                                kandang k 
                                on
                                    rs.kandang = k.id
                            right join
                                wilayah w 
                                on
                                    k.unit = w.id
                        )  rdim_submit
                        on
                            rdim_submit.noreg = r.noreg 
                    where
                        r.lr_inti is not null and
                        ".$sql_unit."
                        ts.tgl_tutup between '".$start_date."' and '".$end_date."'
                    group by r.noreg, r.populasi, r.tgl_docin, r.jml_panen_ekor, r.jml_panen_kg, r.bb, r.fcr, r.deplesi, r.rata_umur, r.ip, rhpp_inti.lr_inti, rhpp_plasma.bonus_pasar, rhpp_inti.tot_pembelian_sapronak, rhpp_plasma.pdpt_peternak_belum_pajak, rhpp_inti.biaya_materai, rhpp_inti.biaya_operasional, rhpp_inti.barang
                ) rhpp
                on
                    rhpp.noreg = ts.noreg
            right join
                (
                    select 
                        rs.noreg, 
                        m.nama as nama_mitra,
                        k.id as id_kandang, 
                        k.kandang as no_kandang, 
                        w.kode as kode_unit 
                    from rdim_submit rs
                    right join
                        kandang k 
                        on
                            rs.kandang = k.id
                    right join
                        wilayah w 
                        on
                            k.unit = w.id
                    right join
                        (
                            select mm1.* from mitra_mapping mm1
                            right join
                                (select max(id) as id, nim from mitra_mapping group by nim ) mm2
                                on
                                    mm1.id = mm2.id
                        ) mm
                        on
                            rs.nim = mm.nim
                    right join
                        mitra m
                        on
                            mm.mitra = m.id
                )  rdim_submit
                on
                    rdim_submit.noreg = rhpp.noreg 
            left join
                (
                    select 
                        rs.noreg as noreg, 
                        min(rs.tgl_panen) as tgl_panen_awal,
                        max(rs.tgl_panen) as tgl_panen_akhir,
                        (DateDiff (Day,min(rs.tgl_panen),max(rs.tgl_panen)) + 1) as lama_panen,
                        (DateDiff (Day,min(rs.tgl_panen),max(rs.tgl_panen)) + 1) as umur_panen,
                        sum(drs.ekor) as ekor_panen,
                        sum(drs.tonase) as kg_panen,
                        sum(drs.tonase * drs.harga) as total, 
                        (sum(drs.tonase * drs.harga) / sum(drs.tonase)) as rata_harga_panen
                    from 
                        det_real_sj drs 
                    right join
                        (select max(id) as id, tgl_panen, noreg from real_sj group by tgl_panen, noreg) rs 
                        on
                            drs.id_header = rs.id
                    group by 
                        rs.noreg  
                ) drs 
                on
                    drs.noreg = rhpp.noreg
            right join
                (
                    select 
                        kp.tujuan,
                        kp.kode_barang,
                        brg.nama as nama_barang,
                        kp.total_pakan_terima,
                        pindah_pakan.total_pindah_pakan,
                        retur_pakan.total_retur_pakan,
                        kp.total_pakan_terima - (ISNULL(pindah_pakan.total_pindah_pakan, 0) + ISNULL(retur_pakan.total_retur_pakan, 0)) as total_pakan
                    from 
                        (
                            select 
                                kp.tujuan as tujuan, 
                                dtp.item as kode_barang, 
                                sum(dtp.jumlah) as total_pakan_terima 
                            from kirim_pakan kp 
                            right join
                                terima_pakan tp 
                                on
                                    kp.id = tp.id_kirim_pakan 
                            right join
                                det_terima_pakan dtp  
                                on
                                    tp.id = dtp.id_header 
                            group by
                                kp.tujuan,
                                dtp.item
                        ) kp
                    left join
                        (
                            select 
                                kp.asal as asal, 
                                dtp.item as kode_barang, 
                                sum(dtp.jumlah) AS total_pindah_pakan 
                            from kirim_pakan kp 
                            left join
                                terima_pakan tp 
                                on
                                    kp.id = tp.id_kirim_pakan 
                            left join
                                det_terima_pakan dtp  
                                on
                                    tp.id = dtp.id_header 
                            group by
                                kp.asal,
                                dtp.item
                        ) pindah_pakan
                        on
                            pindah_pakan.asal = kp.tujuan and
                            pindah_pakan.kode_barang = kp.kode_barang
                    left join
                        (
                            select 
                                rp.id_asal, 
                                drp.item as kode_barang,
                                sum(drp.jumlah) as total_retur_pakan 
                            from retur_pakan rp 
                            left join
                                det_retur_pakan drp 
                                on
                                    rp.id = drp.id_header 
                            group by
                                rp.id_asal,
                                drp.item
                        ) retur_pakan
                        on
                            retur_pakan.id_asal = kp.tujuan and
                            retur_pakan.kode_barang = kp.kode_barang
                    right join
                        (
                            select brg1.* from barang brg1
                            right join
                                (select max(id) as id, kode from barang group by kode) brg2
                                on
                                    brg1.id = brg2.id
                        ) brg
                        on
                            brg.kode = kp.kode_barang
                ) kp_tujuan
                on
                    drs.noreg = kp_tujuan.tujuan 
            left join
                (
                    select
                        kppd2.noreg,
                        kpp.tgl_bayar
                    from 
                        konfirmasi_pembayaran_peternak_det2 kppd2
                    right join
                        konfirmasi_pembayaran_peternak_det kppd
                        on
                            kppd2.id_header = kppd.id 
                    right join
                        konfirmasi_pembayaran_peternak kpp 
                        on
                            kpp.id = kppd.id_header 
                    group by
                        kppd2.noreg,
                        kpp.tgl_bayar
                ) kp
                on
                    kp.noreg = ts.noreg 
            where
                ".$sql_unit."
                ts.tgl_tutup between '".$start_date."' and '".$end_date."'
            group by
                rdim_submit.nama_mitra,
                rdim_submit.no_kandang,
                ts.noreg,
                ts.tgl_docin,
                rhpp.populasi,
                rhpp.nama_doc,
                ts.tgl_tutup,
                kp_tujuan.kode_barang,
                kp_tujuan.nama_barang,
                kp_tujuan.total_pakan,
                drs.ekor_panen,
                drs.kg_panen,
                drs.total,
                drs.rata_harga_panen,
                drs.tgl_panen_awal,
                drs.tgl_panen_akhir,
                drs.lama_panen,
                drs.umur_panen,
                kp.tgl_bayar,
                rhpp.tgl_docin,
                rhpp.fcr,
                rhpp.bb,
                rhpp.ip,
                rhpp.deplesi,
                rhpp.lr_inti,
                rhpp.bonus_pasar,
                rhpp.tot_pembelian_sapronak,
                rhpp.pdpt_peternak_belum_pajak,
                rhpp.biaya_materai,
                rhpp.biaya_operasional
        ";

        $m_conf = new \Model\Storage\Conf();
        $d_rhpp = $m_conf->hydrateRaw($sql);

        $data = null;
        if ( $d_rhpp->count() > 0 ) {
            $d_rhpp = $d_rhpp->toArray();

            foreach ($d_rhpp as $key => $value) {
                if ( !isset($data[ $value['noreg'] ]) ) {
                    $data[ $value['noreg'] ] = array(
                        'nama_mitra' => $value['nama_mitra'],
                        'no_kandang' => $value['no_kandang'],
                        'no_siklus' => $value['no_siklus'],
                        'noreg' => $value['noreg'],
                        'tgl_docin' => $value['tgl_docin'],
                        'nama_doc' => $value['nama_doc'],
                        'tgl_tutup' => $value['tgl_tutup'],
                        'ekor_panen' => $value['ekor_panen'],
                        'kg_panen' => $value['kg_panen'],
                        'total' => $value['total'],
                        'rata_harga_panen' => $value['rata_harga_panen'],
                        'detail_pakan' => null,
                        'tgl_panen_awal' => $value['tgl_panen_awal'],
                        'tgl_panen_akhir' => $value['tgl_panen_akhir'],
                        'lama_panen' => $value['lama_panen'],
                        'umur' => $value['umur'],
                        'populasi_panen' => $value['populasi_panen'],
                        'fcr' => $value['fcr'],
                        'bb' => $value['bb'],
                        'ip' => $value['ip'],
                        'deplesi' => $value['deplesi'],
                        'tgl_bayar' => $value['tgl_bayar'],
                        'rhpp_ke_pusat' => $value['rhpp_ke_pusat'],
                        'transfer' => $value['transfer'],
                        'lr_inti' => $value['lr_inti'],
                        'bonus_pasar' => $value['bonus_pasar'],
                        'tot_pembelian_sapronak' => $value['tot_pembelian_sapronak'],
                        'pdpt_peternak_belum_pajak' => $value['pdpt_peternak_belum_pajak'],
                        'biaya_materai' => $value['biaya_materai'],
                        'biaya_operasional' => $value['biaya_operasional'],
                        'modal_inti' => $value['modal_inti'],
                        'modal_inti_sebenarnya' => $value['modal_inti_sebenarnya']
                    );

                    $key = $value['nama_barang'].' | '.$value['kode_barang'];

                    if ( !isset($data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]) ) {
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['kode_barang'] = isset($value['kode_barang']) ? $value['kode_barang'] : null;
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['nama_barang'] = isset($value['nama_barang']) ? $value['nama_barang'] : null;
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['total_pakan'] = isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                        $data[ $value['noreg'] ]['detail_pakan']['total'] = isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                    } else {
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['total_pakan'] += isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                        $data[ $value['noreg'] ]['detail_pakan']['total'] += isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                    }
                } else {
                    $key = $value['nama_barang'].' | '.$value['kode_barang'];

                    if ( !isset($data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]) ) {
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['kode_barang'] = isset($value['kode_barang']) ? $value['kode_barang'] : null;
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['nama_barang'] = isset($value['nama_barang']) ? $value['nama_barang'] : null;
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['total_pakan'] = isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                        $data[ $value['noreg'] ]['detail_pakan']['total'] = isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                    } else {
                        $data[ $value['noreg'] ]['detail_pakan']['detail'][ $key ]['total_pakan'] += isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                        $data[ $value['noreg'] ]['detail_pakan']['total'] += isset($value['total_pakan']) ? $value['total_pakan'] : 0;
                    }
                }

                ksort($data[ $value['noreg'] ]['detail_pakan']['detail']);
                ksort($data);
            }
        }

        return $data;
    }

    public function viewForm()
    {
        $params = $this->input->get('params');

        $unit = $params['unit'];
        $bulan = $params['bulan'];
        $tahun = $params['tahun'];

        $data = $this->getDataViewForm($unit, $bulan, $tahun);

        $angka_bulan = (strlen($bulan) == 1) ? '0'.$bulan : $bulan;
        $date = $tahun.'-'.$angka_bulan.'-01';
        $nama_bulan = explode(' ', tglIndonesia($date, '-', ' ', true))[1];

        $content['nama_bulan'] = $nama_bulan;
        $content['tahun'] = $tahun;
        $content['data'] = $data;
        $html = $this->load->view('report/laba_rugi/viewForm', $content, TRUE);

        echo $html;
    }
}