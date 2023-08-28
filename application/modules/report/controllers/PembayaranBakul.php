<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PembayaranBakul extends Public_Controller {

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
                "assets/jquery/list.min.js",
                'assets/jquery/tupage-table/jquery.tupage.table.js',
                "assets/report/pembayaran_bakul/js/pembayaran-bakul.js",
            ));
            $this->add_external_css(array(
                'assets/jquery/tupage-table/jquery.tupage.table.css',
                "assets/report/pembayaran_bakul/css/pembayaran-bakul.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;
            $content['data'] = null;
            $content['pelanggan'] = $this->get_pelanggan();
            $content['title_menu'] = 'Laporan Pembayaran Bakul';

            // Load Indexx
            $data['view'] = $this->load->view('report/pembayaran_bakul/index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function get_pelanggan()
    {
        $m_plg = new \Model\Storage\Pelanggan_model();
        $d_plg = $m_plg->select('nomor')->get();

        $data = null;
        if ( $d_plg->count() > 0 ) {
            $d_plg = $d_plg->toArray();
            foreach ($d_plg as $k_plg => $v_plg) {
                $_d_plg = $m_plg->where('nomor', $v_plg['nomor'])->where('tipe', 'pelanggan')->where('mstatus', 1)->with(['kecamatan'])->orderBy('version', 'desc')->first();

                if ( $_d_plg ) {
                    $_d_plg = $_d_plg->toArray();
                    $kota_kab = str_replace('Kota ', '', str_replace('Kab ', '', $_d_plg['kecamatan']['d_kota']['nama']));
                    $key = $kota_kab.'|'.$_d_plg['nama'].'|'.$_d_plg['nomor'];
                    $data[$key] = $_d_plg;

                    ksort($data);
                }
            }
        }

        return $data;
    }
}