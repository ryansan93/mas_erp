<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KasKecil extends Public_Controller {

    private $pathView = 'report/kas_kecil/';
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
                "assets/report/kas_kecil/js/kas-kecil.js",
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                "assets/report/kas_kecil/css/kas-kecil.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;
            $content['unit'] = $this->getUnit();
            $content['title_menu'] = 'Laporan Kas Kecil';

            // Load Indexx
            $data['view'] = $this->load->view($this->pathView.'index', $content, TRUE);
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

    public function getCoa($unit)
    {
        $_unit = null;
        if ( stristr($unit, 'all') === false ) {
            $_unit = $unit;
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select coa from coa where nama_coa like '%kas kecil ".$_unit."%'
        ";
        $d_coa = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_coa->count() > 0 ) {
            $data = $d_coa->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $unit = $params['unit'];
            $periode = $params['periode'];

            $startDate = substr($periode, 0, 7).'-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $status_btn_tutup_bulan = 1;
            $sql_saldo_unit = "and sk.unit = '".$unit."'";
            $sql_group_by_saldo_unit = ", sk.unit";
            $sql_unit = "and j.unit = '".$unit."'";

            $_unit = null;
            if ( stristr($unit, 'all') !== false ) {
                $_unit = 'all';
                $sql_saldo_unit = null;
                $sql_group_by_saldo_unit = null;
                $sql_unit = null;
            } else {
                if ( stristr($unit, 'pusat') !== false ) {
                    $_unit = 'pusat';

                    $sql_unit = "and dj.unit = '".$unit."'";
                }

                if ( stristr($unit, 'pusat') === false ) {
                    $_unit = 'unit';
                }
            }

            $_no_coa = $this->getCoa( $_unit );
            $no_coa = null;
            foreach ($_no_coa as $key => $value) {
                $no_coa[] = $value['coa'];
            }

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    sk.periode as tanggal,
                    '' as no_akun_transaksi,
                    'SALDO AWAL' as nama_akun_transaksi,
                    '' as pic,
                    'SALDO AWAL '+CONVERT(varchar(10), sk.periode, 103) as keterangan,
                    sum(sk.saldo_awal) as debit,
                    0 as kredit,
                    min(sk.saldo_akhir) as saldo_akhir
                from saldo_kas sk
                where
                    sk.periode between '".$startDate."' and '".$endDate."'
                    ".$sql_saldo_unit."
                group by
                    sk.periode
                    ".$sql_group_by_saldo_unit."
            ";
            $d_sk = $m_conf->hydrateRaw( $sql );

            $data_saldo = null;
            if ( $d_sk->count() > 0 ) {
                $data_saldo = $d_sk->toArray()[0];

                if ( $data_saldo['saldo_akhir'] > 0 ) {
                    $status_btn_tutup_bulan = 0;
                }
            } else {
                $data_saldo = array(
                    'tanggal' => $startDate,
                    'no_akun_transaksi' => '',
                    'nama_akun_transaksi' => 'SALDO AWAL',
                    'pic' => '',
                    'keterangan' => 'SALDO AWAL '.date('d/m/Y', strtotime($startDate)),
                    'debit' => 0,
                    'kredit' => 0,
                    'status' => 0
                );
            }

            $data = null;

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select * from
                (
                    select 
                        djt.id,
                        dj.tanggal,
                        djt.sumber_coa as no_akun_transaksi,
                        djt.nama as nama_akun_transaksi,
                        dj.pic,
                        dj.keterangan,
                        dj.nominal as debit,
                        0 as kredit
                    from det_jurnal dj
                    right join
                        det_jurnal_trans djt
                        on
                            dj.det_jurnal_trans_id = djt.id
                    right join
                        jurnal j
                        on
                            dj.id_header = j.id
                    where
                        djt.tujuan_coa in ('".implode("', '", $no_coa)."') and
                        dj.tanggal between '".$startDate."' and '".$endDate."'
                        ".$sql_unit."

                    union all

                    select 
                        djt.id,
                        dj.tanggal,
                        djt.tujuan_coa as no_akun_transaksi,
                        djt.nama as nama_akun_transaksi,
                        dj.pic,
                        dj.keterangan,
                        0 as debit,
                        dj.nominal as kredit
                    from det_jurnal dj
                    right join
                        det_jurnal_trans djt
                        on
                            dj.det_jurnal_trans_id = djt.id
                    right join
                        jurnal j
                        on
                            dj.id_header = j.id
                    where
                        djt.sumber_coa in ('".implode("', '", $no_coa)."') and
                        dj.tanggal between '".$startDate."' and '".$endDate."'
                        ".$sql_unit."
                ) _data
                order by
                    _data.tanggal asc,
                    _data.id asc
            ";
            $d_debit = $m_conf->hydrateRaw( $sql );

            if ( $d_debit->count() > 0 ) {
                $data = $d_debit->toArray();
            }

            $content['data_saldo'] = $data_saldo;
            $content['data'] = $data;
            $html = $this->load->view($this->pathView.'list', $content, TRUE);

            $this->result['status'] = 1;
            $this->result['status_btn_tutup_bulan'] = $status_btn_tutup_bulan;
            $this->result['html'] = $html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $unit = $params['unit'];
            $periode = $params['periode'];

            $startDate = substr($periode, 0, 7).'-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select top 1
                    sk.id
                from saldo_kas sk
                where
                    sk.periode between '".$startDate."' and '".$endDate."' and
                    sk.unit = '".$unit."'
            ";
            $d_sk = $m_conf->hydrateRaw( $sql );

            if ( $d_sk->count() > 0 ) {
                $id = $d_sk->toArray()[0]['id'];

                $m_sk1 = new \Model\Storage\SaldoKas_model();
                $now = $m_sk1->getDate();

                $waktu = $now['waktu'];

                $m_sk1->where('id', $id)->update(
                    array(
                        'saldo_akhir' => $params['saldo_akhir']
                    )
                );

                $d_sk1 = $m_sk1->where('id', $id)->first();

                $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/update', $d_sk1, $deskripsi_log );

                $m_sk2 = new \Model\Storage\SaldoKas_model();
                $m_sk2->tgl_trans = $waktu;
                $m_sk2->unit = $unit;
                $m_sk2->periode = date('Y-m-d', strtotime($startDate. ' + 1 months'));
                $m_sk2->saldo_awal = $params['saldo_akhir'];
                $m_sk2->saldo_akhir = 0;
                $m_sk2->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_sk2, $deskripsi_log );
            } else {
                $m_sk1 = new \Model\Storage\SaldoKas_model();
                $now = $m_sk1->getDate();

                $waktu = $now['waktu'];

                $m_sk1->tgl_trans = $waktu;
                $m_sk1->unit = $unit;
                $m_sk1->periode = $startDate;
                $m_sk1->saldo_awal = 0;
                $m_sk1->saldo_akhir = $params['saldo_akhir'];
                $m_sk1->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_sk1, $deskripsi_log );

                $m_sk2 = new \Model\Storage\SaldoKas_model();
                $m_sk2->tgl_trans = $waktu;
                $m_sk2->unit = $unit;
                $m_sk2->periode = date('Y-m-d', strtotime($startDate. ' + 1 months'));
                $m_sk2->saldo_awal = $params['saldo_akhir'];
                $m_sk2->saldo_akhir = 0;
                $m_sk2->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_sk2, $deskripsi_log );
            }

            $m_sk = new \Model\Storage\SewaKantor_model();
            $d_sk = $m_sk->where('mulai', '<=', $startDate)->where('akhir', '>=', $startDate)->where('unit', $unit)->first();
            if ( $d_sk ) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "exec insert_jurnal 'OPERASIONAL UNIT', NULL, NULL, 0, 'sewa_kantor', ".$d_sk->id.", NULL, 1, 1, '".$startDate."'";
                $m_conf->hydrateRaw( $sql );
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di tutup.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}