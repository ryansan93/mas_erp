<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Peternak extends Public_Controller {

    private $url;
    private $status_kandang = [
        1 => 'Aktif',
        0 => 'Tidak Aktif'
    ];
    private $isMobile = false;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;

        $this->load->library('Mobile_Detect');
        $detect = new Mobile_Detect();

        if ( $detect->isMobile() ) {
            $this->isMobile = true;
        }
    }

    public function index()
    {
        $akses = hakAkses($this->url);
        if ( $akses['a_view'] == 1 ) {
            $this->add_external_js(
                array(
                    'assets/jquery/maskedinput/jquery.maskedinput.min.js',
                    'assets/pagination-ry/pagination.js',
                    'assets/parameter/peternak/js/peternak.js'
                )
            );
            $this->add_external_css(
                array(
                    'assets/pagination-ry/pagination.css',
                    'assets/parameter/peternak/css/peternak.css'
                )
            );
            $data = $this->includes;

            $content['title_panel'] = 'Pengajuan Data Mitra';
            // $content['tipe_lokasi'] = $this->getTipeLokasi();
            // $content['tipe_kandang'] = $this->getTipeKandang();
            // $content['status_kandang'] = $this->status_kandang;
            // $content['jenis_mitra'] = $this->getJenisMitra();
            // $content['list_provinsi'] = $this->getLokasi('PV');
            // $content['list_perwakilan'] = $this->getListPerwakilan();
            // $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
            // $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
            // $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');

            // $content['list_mitra'] = $this->getListIndexMitra();
			$content['list_mitra'] = null;

            $content['akses'] = $akses;
            $content['resubmit'] = null;
            $content['add_form'] = $this->add_form();

            $data['title_menu'] = 'Master Peternak';
            $data['view'] = $this->load->view('parameter/peternak/index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        } 
    }

    public function load_form()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');
        $html = '';

        if ( !empty($id) ) {
            if ( !empty($resubmit) ) {
                /* NOTE : untuk edit */
                $html = $this->edit_form($id, $resubmit);
            } else {
                /* NOTE : untuk view */
                $html = $this->view_form($id, $resubmit);
            }
        } else {
            /* NOTE : untuk add */
            $html = $this->add_form();
        }

        echo $html;
    }

    public function amount_of_data()
    {
        $search_by = $this->input->post('search_by');
        $search_val = $this->input->post('search_val');

        $m_mitra = new \Model\Storage\Mitra_model();
        $d_nomor = null;
        if ( !empty($search_by) && !empty($search_val) ) {
            if ( stristr($search_by, 'nama') !== FALSE ) {
                $d_nomor = $m_mitra->select('nomor', 'nama')->distinct('nomor')->where('nama', 'like', '%'.$search_val.'%')->where('mstatus', 1)->orderBy('nama', 'asc')->get()->toArray();
            } else if ( stristr($search_by, 'unit') !== FALSE ) {
                $m_wilayah = new \Model\Storage\Wilayah_model();
                $d_wilayah = $m_wilayah->select('id')->where('kode', 'like', '%'.$search_val.'%')->get();

                if ( $d_wilayah->count() > 0 ) {
                    $d_wilayah = $d_wilayah->toArray();
                    $m_kdg = new \Model\Storage\Kandang_model();
                    $d_kdg = $m_kdg->select('mitra_mapping')->whereIn('unit', $d_wilayah)->get();
                    if ( $d_kdg->count() > 0 ) {
                        $d_kdg = $d_kdg->toArray();
                        $m_mm = new \Model\Storage\MitraMapping_model();
                        $d_mm = $m_mm->select('mitra')->whereIn('id', $d_kdg)->get()->toArray();

                        $d_nomor = $m_mitra->select('nomor', 'nama')->distinct('nomor')->whereIn('id', $d_mm)->orderBy('nama', 'asc')->get()->toArray();
                    }
                }
            }
        } else {
            $d_nomor = $m_mitra->select('nomor', 'nama')->distinct('nomor')->where('mstatus', 1)->orderBy('nama', 'asc')->get()->toArray();
        }

        $list_nomor = array();
        $jml_row = 25;
        $jml_page = 0;
        $idx_row = 0;
        if ( !empty($d_nomor) ) {
            foreach ($d_nomor as $k_nomor => $v_nomor) {
                if ( $idx_row == $jml_row ) {
                    $idx_row = 0;
                    $jml_page++;
                }

                $list_nomor[$jml_page][$idx_row] = $v_nomor['nomor'];

                $idx_row++;
            }
        }

        $this->result['content'] = array(
            'jml_row' => $jml_row,
            'jml_page' => count($list_nomor),
            'list' => $list_nomor
        );                     

        display_json( $this->result );
    }

    public function list_sk()
    {   
        $list_nomor = $this->input->get('params');

        $akses = hakAkses($this->url);

        $content['list_mitra'] = $this->getListIndexMitra($list_nomor);
        $content['akses'] = $akses;
        $content['resubmit'] = null;
        $html = $this->load->view('parameter/peternak/list', $content);
        
        echo $html;
    }

    public function add_form()
    {
        $akses = hakAkses($this->url);

        $content['title_panel'] = 'Pengajuan Data Mitra';
        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['status_kandang'] = $this->status_kandang;
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');
        $content['perusahaan'] = $this->getPerusahaan();

        $content['akses'] = $akses;
        $content['resubmit'] = null;
        $content['data'] = null;
        $html = $this->load->view('parameter/peternak/add_form', $content, true);
        
        return $html;
    }

    public function view_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['status_kandang'] = $this->status_kandang;
        $content['mitra'] = $this->getDataMitra($id);
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');
        $content['perusahaan'] = $this->getPerusahaan();

        $content['akses'] = $akses;
        $content['data'] = 'VIEW FORM';
        $content['resubmit'] = $resubmit;
        $html = $this->load->view('parameter/peternak/view_form', $content);
        
        return $html;
    }

    public function edit_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        // $content['tipe_lokasi'] = $this->getTipeLokasi();
        // $content['tipe_kandang'] = $this->getTipeKandang();
        // $content['jenis_mitra'] = $this->getJenisMitra();
        // $content['status_kandang'] = $this->status_kandang;
        $content['mitra'] = $this->getDataMitra($id);
        // $content['list_provinsi'] = $this->getLokasi('PV');
        // $content['list_perwakilan'] = $this->getListPerwakilan();
        // $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        // $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        // $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');

        $content['title_panel'] = 'Pengajuan Data Mitra';
        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['status_kandang'] = $this->status_kandang;
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');
        $content['perusahaan'] = $this->getPerusahaan();

        $content['akses'] = $akses;
        $content['data'] = 'EDIT FORM';
        $content['resubmit'] = $resubmit;
        $html = $this->load->view('parameter/peternak/edit_form', $content);
        
        return $html;
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
                    'kode' => $d_perusahaan->kode,
                    'jenis_mitra' => $d_perusahaan->jenis_mitra
                );
            }

            ksort($data);
        }

        return $data;
    }

    public function getListIndexMitra($list_nomor)
    {
        $m_mitra = new \Model\Storage\Mitra_model();

        $data = array();
        if ( !empty($list_nomor) && $list_nomor != 'undefined' ) {
            foreach ($list_nomor as $nomor) {
                $mitra = $m_mitra->where('nomor', $nomor)
                                 ->orderBy('version', 'desc')
                                 ->orderBy('id', 'desc')
                                 ->first()->toArray();

                if ( $mitra['mstatus'] == 1 ) {
                    $ket = [];
                    $keterangan = '';

                    $m_lt = new \Model\Storage\LogTables_model();
                    $d_lt = $m_lt->select('deskripsi', 'waktu')->where('tbl_name', 'mitra')->where('tbl_id', $mitra['id'])->get()->toArray();

                    foreach ($d_lt as $log){
                        $keterangan = $log['deskripsi'] . ' pada ' . dateTimeFormat($log['waktu']);
                        array_push($ket, $keterangan);
                    }
        			
        			$_unit = null;
                    $m_mm = new \Model\Storage\MitraMapping_model();
                    $d_mm = $m_mm->where('mitra', $mitra['id'])->get()->toArray();
        			foreach ($d_mm as $v_mm) {
                        $m_kdg = new \Model\Storage\Kandang_model();
                        $d_kdg = $m_kdg->where('mitra_mapping', $v_mm['id'])->with(['d_unit'])->get()->toArray();
        				foreach ( $d_kdg as $v_kdg ) {
        					$_unit[ $v_kdg['d_unit']['kode'] ] = $v_kdg['d_unit']['kode'];
        				}
        			}
        			$unit = !empty($_unit) ? implode(', ', $_unit) : '-';

                    $key = $mitra['nama'].'|'.$mitra['nomor'];
                    $data[ $key ] = array(
                        'id' => $mitra['id'],
                        'nomor' => $mitra['nomor'],
                        'ktp' => $mitra['ktp'],
                        'nama' => $mitra['nama'],
                        'alamat' => $mitra['alamat_kelurahan'] . ', ' . $mitra['alamat_jalan'] . ', RT/RW : '. $mitra['alamat_rt'] . '/' . $mitra['alamat_rw'],
                        'status' => $mitra['status'],
                        'keterangan' => $keterangan,
        				'unit' => $unit
                    );

                    // ksort($data);
                }
            }
        }

        return $data;
    }

    public function add()
    {
        $this->set_title( 'Master Mitra' );
        $this->add_external_js(array(
            'assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js',
            'assets/jquery/maskedinput/jquery.maskedinput.min.js',
            'assets/master/mitra.js',
        ));
        $this->add_external_css(array(
            'assets/jquery/easy-autocomplete/easy-autocomplete.min.css',
            'assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css',
            'assets/master/css/mitra.css',
        ));
        $data = $this->includes;

        $order = $this->input->get('ord');
        $content['title_panel'] = 'Pengajuan Data Mitra';
        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['status_kandang'] = $this->status_kandang;
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');
        $content['current_uri'] = $this->current_uri;
        // load views
        $data['content'] = $this->load->view($this->pathView . 'form_pengajuan_data_mitra', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function view_mitra($mitra_id)
    {
        $this->set_title( 'Master Mitra' );
        $this->add_external_js(array('assets/master/mitra.js'));
        $this->add_external_css(array('assets/master/css/mitra.css'));
        $data = $this->includes;

        $order = $this->input->get('ord');
        $content['title_panel'] = 'Master Mitra';
        $content['current_uri'] = $this->current_uri;
        $content['list_status'] = array('all', 'submit', 'ack','approve');
        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['status_kandang'] = $this->status_kandang;
        $content['mitra'] = $this->getDataMitra($mitra_id);
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');

        $data['content'] = $this->load->view($this->pathView . 'view_data_mitra', $content, TRUE);
        $this->load->view($this->template, $data);

    }

    public function update_mitra($mitra_id)
    {
        $this->add_external_js(array(
            'assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js',
            'assets/jquery/maskedinput/jquery.maskedinput.min.js',
            'assets/master/mitra.js',
        ));
        $this->add_external_css(array(
            'assets/jquery/easy-autocomplete/easy-autocomplete.min.css',
            'assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css',
            'assets/master/css/mitra.css',
        ));
        $data = $this->includes;

        $order = $this->input->get('ord');
        $content['title_panel'] = 'Master Mitra';
        $content['current_uri'] = $this->current_uri;
        $content['list_status'] = array('all', 'submit', 'ack','approve');
        $content['tipe_lokasi'] = $this->getTipeLokasi();
        $content['tipe_kandang'] = $this->getTipeKandang();
        $content['jenis_mitra'] = $this->getJenisMitra();
        $content['status_kandang'] = $this->status_kandang;
        $content['mitra'] = $this->getDataMitra($mitra_id);
        $content['list_provinsi'] = $this->getLokasi('PV');
        $content['list_perwakilan'] = $this->getListPerwakilan();
        $content['list_lampiran_mitra'] = $this->getListLampiran('MITRA');
        $content['list_lampiran_kandang'] = $this->getListLampiran('KANDANG');
        $content['list_lampiran_jaminan'] = $this->getListLampiran('MITRA_JAMINAN');

        $data['content'] = $this->load->view($this->pathView . 'update_data_mitra', $content, TRUE);
        $this->load->view($this->template, $data);

    }

    public function getDataMitra($id)
    {
        $m_mitra = new \Model\Storage\Mitra_model();
        $d_mitra = $m_mitra->with(['telepons', 'lampirans', 'lampirans_jaminan', 'dKecamatan', 'perwakilans', 'logs', 'posisi'])->find($id);

        return $d_mitra;
    }

    public function getTipeLokasi()
    {
        $tipe_lokasi = $this->config->item('tipe_lokasi');
        $data = array();
        foreach ($tipe_lokasi as $key => $val) {
            if ( in_array($key, ['KB', 'KT']) ) {
                $data[$key] = $val;
            }
        }
        return $data;
    }

    public function getTipeKandang()
    {
        $tipe_kandang = $this->config->item('tipe_kandang');
        return $tipe_kandang;
    }

    public function getJenisMitra()
    {
        $jenis_mitra = $this->config->item('jenis_mitra');
        return $jenis_mitra;
    }

    public function getListLampiran($jenis)
    {
        $m_lampiran = new \Model\Storage\NamaLampiran_model();
        $d_lampiran = $m_lampiran->where('jenis', $jenis)->get();
        return $d_lampiran;
    }

    public function getListPerwakilan()
    {
        $m_wilayah = new \Model\Storage\Wilayah_model();
        $d_wilayah = $m_wilayah->where('jenis', 'PW')->orderBy('nama', 'ASC')->get();
        return $d_wilayah;
    }

    public function getListUnit()
    {
        $induk = $this->input->get('induk');
        $m_wilayah = new \Model\Storage\Wilayah_model();
        $d_wilayah = $m_wilayah ->where('jenis', 'UN')
                                ->where('induk', $induk)
                                ->orderBy('nama', 'ASC')->get();
        $data = ($d_wilayah) ? $d_wilayah->toArray() : [];
        display_json( $data );
    }

    public function getLokasi($jenis, $induk = null)
    {
        $m_lokasi = new \Model\Storage\Lokasi_model();
        if ($induk == null) {
            $d_lokasi = $m_lokasi ->where('jenis', $jenis)->orderBy('nama', 'ASC')->get();
        }else{
            $d_lokasi = $m_lokasi ->where('jenis', $jenis)->where('induk', $induk)->orderBy('nama', 'ASC')->get();
        }
        return $d_lokasi;
    }

    public function getLokasiJson()
    {
        $jenis = $this->input->get('jenis');
        $induk = $this->input->get('induk');

        $result = $this->getLokasi($jenis, $induk);
        $this->result['content'] = $result;
        $this->result['status'] = 1;
        display_json($this->result);
    }

    public function autocomplete_lokasi()
    {
        $term = $this->input->get('term');
        $jenis = $this->input->get('tipe_lokasi');
        $induk = $this->input->get('induk');
        $data = array();
        $m_wilayah = new \Model\Storage\Lokasi_model();
        if (empty($induk)) {
            $d_wilayah = $m_wilayah ->where('jenis', $jenis)
                                    ->where('nama', 'LIKE', "%{$term}%")
                                    ->orderBy('nama', 'ASC')->get();
        }else{
            $d_wilayah = $m_wilayah ->where('jenis', $jenis)
                                    ->where('nama', 'LIKE', "%{$term}%")
                                    ->where('induk', $induk)
                                    ->orderBy('nama', 'ASC')->get();
        }

        foreach ($d_wilayah as $key => $val) {
            $data[] = array(
                'label'=>$val['nama'],
                'value'=>$val['nama'],
                'id' => $val['id']
            );
        }

        if (empty($data)) {
            $data = array(
                'label'=>"not found",
                'value'=>"",
                'id' => ""
            );
        }

        display_json($data);
    }

    public function save()
    {
        $params = json_decode($this->input->post('data_mitra'),TRUE);
        $files = isset($_FILES['files']) ? $_FILES['files'] : [];
        $mappingFiles = mappingFiles($files);

        // NOTE: 0. prepare
        $model = new \Model\Storage\Conf();
        $now = $model->getDate();

        try {
            $status = 'submit';
            // NOTE: 1. simpan mitra
            $m_mitra = new \Model\Storage\Mitra_model();
            $id_mitra = $m_mitra->getNextIdentity();
            $nomor_mitra = $m_mitra->getNextNomor();

            $m_mitra->id = $id_mitra;
            $m_mitra->nomor = $nomor_mitra;
            $m_mitra->nama = $params['nama'];
            $m_mitra->ktp = $params['ktp'];
            $m_mitra->npwp = $params['npwp'];
            $m_mitra->alamat_kecamatan = $params['alamat']['kecamatan'];
            $m_mitra->alamat_kelurahan = $params['alamat']['kelurahan'];
            $m_mitra->alamat_rt = $params['alamat']['rt'] ?: null;
            $m_mitra->alamat_rw = $params['alamat']['rw'] ?: null;
            $m_mitra->alamat_jalan = $params['alamat']['alamat'] ?: null;
            $m_mitra->bank = $params['d_bank']['bank'] ?: null;
            $m_mitra->rekening_cabang_bank = $params['d_bank']['cabang'] ?: null;
            $m_mitra->rekening_nomor = $params['d_bank']['rekening'] ?: null;
            $m_mitra->rekening_pemilik = $params['d_bank']['pemilik'] ?: null;
            $m_mitra->status = $status;
            $m_mitra->keterangan_jaminan = $params['keterangan_jaminan'];
            $m_mitra->jenis = $params['jenis_mitra'];
            $m_mitra->mstatus = 1;
            $m_mitra->version = 1;
            $m_mitra->perusahaan = $params['perusahaan'];
            $m_mitra->save();

            $telepons = $params['telepons'];
            foreach ($telepons as $k => $telepon) {
                $m_telp = new \Model\Storage\TeleponMitra_model();
                $m_telp->id = $m_telp->getNextIdentity();
                $m_telp->mitra = $id_mitra;
                $m_telp->nomor = $telepon;
                $m_telp->save();
            }

            // NOTE: simpan lampiran mitra
            $lampirans = $params['lampirans'];
            if ( !empty($lampirans) ) {
                foreach ($lampirans as $lampiran) {
                    $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
                    // cetak_r($file);
                    $file_name = $path_name = null;
                    $isMoved = 0;
                    if (!empty($file)) {
                        $moved = uploadFile($file);
                        $isMoved = $moved['status'];
                    }
                    if ($isMoved) {
                        $file_name = $moved['name'];
                        $path_name = $moved['path'];

                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $m_lampiran->tabel = 'mitra';
                        $m_lampiran->tabel_id = $id_mitra;
                        $m_lampiran->nama_lampiran = $lampiran['id'];
                        $m_lampiran->filename = $file_name ;
                        $m_lampiran->path = $path_name;
                        $m_lampiran->status = 1;
                        $m_lampiran->save();
                    }else {
                        display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
                    }
                }
            }

            // NOTE: simpan lampiran jaminan
            $lampirans_jaminan = $params['lampirans_jaminan'];
            if ( !empty($lampirans_jaminan) ) {
                foreach ($lampirans_jaminan as $lJaminan) {
                    $file = $mappingFiles[ $lJaminan['sha1'] . '_' . $lJaminan['name'] ] ?: '';
                    $file_name = $path_name = null;
                    $isMoved = 0;
                    if (!empty($file)) {
                        $moved = uploadFile($file);
                        $file_name = $moved['name'];
                        $path_name = $moved['path'];
                        $isMoved = $moved['status'];
                    }
                    if ($isMoved) {
                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $m_lampiran->tabel = 'mitra_jaminan';
                        $m_lampiran->tabel_id = $id_mitra;
                        $m_lampiran->nama_lampiran = $lJaminan['id'];
                        $m_lampiran->filename = $file_name ;
                        $m_lampiran->path = $path_name;
                        $m_lampiran->status = 1;
                        $m_lampiran->save();
                    }
                }
            }

            // NOTE: 2. simpan perwakilan + kandang
            $perwakilans = $params['d_perwakilans'];
            foreach ($perwakilans as $perwakilan) {
                $m_mitra_mapping = new \Model\Storage\MitraMapping_model();
                $nim = $m_mitra_mapping->getNextNim();
                $mitra_mapping_id = $m_mitra_mapping->getNextIdentity();
                $m_mitra_mapping->id = $mitra_mapping_id;
                $m_mitra_mapping->mitra = $id_mitra;
                $m_mitra_mapping->perwakilan = $perwakilan['perwakilan_id'];
                // $m_mitra_mapping->nim = $perwakilan['nim'];
                $m_mitra_mapping->nim = $nim;
                $m_mitra_mapping->nomor = $nomor_mitra;
                $m_mitra_mapping->save();

                // NOTE: 2.1 simpan kandang
                $kandangs = $perwakilan['d_kandangs'];
                foreach ($kandangs as $kandang) {
                    $m_kandang = new \Model\Storage\Kandang_model();
                    $kandang_id = $m_kandang->getNextIdentity();
                    $m_kandang->id = $kandang_id;
                    $m_kandang->mitra_mapping = $mitra_mapping_id;
                    $m_kandang->kandang = $kandang['no'];
                    $m_kandang->unit = $kandang['unit'];
                    $m_kandang->tipe = $kandang['tipe'];
                    $m_kandang->ekor_kapasitas = $kandang['kapasitas'];
                    $m_kandang->alamat_kecamatan = $kandang['alamat']['kecamatan'];
                    $m_kandang->alamat_kelurahan = $kandang['alamat']['kelurahan'];
                    $m_kandang->alamat_rt = $kandang['alamat']['rt'];
                    $m_kandang->alamat_rw = $kandang['alamat']['rw'];
                    $m_kandang->alamat_jalan = $kandang['alamat']['alamat'];
                    $m_kandang->ongkos_angkut = $kandang['ongkos_angkut'];
                    $m_kandang->grup = $kandang['grup'];
                    $m_kandang->status = $kandang['status'];
                    $m_kandang->save();

                    $bangunans = $kandang['bangunans'];
                    foreach ($bangunans as $bangunan) {
                        $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
                        $m_bangunan_kandang->id = $m_bangunan_kandang->getNextIdentity();
                        $m_bangunan_kandang->kandang = $kandang_id;
                        $m_bangunan_kandang->bangunan = $bangunan['no'];
                        $m_bangunan_kandang->meter_panjang = $bangunan['panjang'];
                        $m_bangunan_kandang->meter_lebar = $bangunan['lebar'];
                        $m_bangunan_kandang->jumlah_unit = $bangunan['jml_unit'];
                        $m_bangunan_kandang->save();
                    }

                    // NOTE: simpan lampiran kandang
                    $lampirans = $kandang['lampirans'];
                    if ( !empty($lampirans) ) {
                        foreach ($lampirans as $lampiran) {
                            $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
                            $file_name = $path_name = null;
                            $isMoved = 0;
                            if (!empty($file)) {
                                $moved = uploadFile($file);
                                $isMoved = $moved['status'];

                                $file_name = isset($moved['name']) ?: "";
                                $path_name = isset($moved['path']) ?: "";
                            }
                            if ($isMoved) {
                                $m_lampiran = new \Model\Storage\Lampiran_model();
                                $m_lampiran->tabel = 'kandang';
                                $m_lampiran->tabel_id = $kandang_id;
                                $m_lampiran->nama_lampiran = $lampiran['id'];
                                $m_lampiran->filename = $file_name ;
                                $m_lampiran->path = $path_name;
                                $m_lampiran->status = 1;
                                $m_lampiran->save();
                            }
                        }
                    }
                }
            }

            $d_mitra = $m_mitra->where('id', $id_mitra)->with(['telepons', 'perwakilans'])->first();

            $deskripsi_log_mitra = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_mitra, $deskripsi_log_mitra );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data mitra sukses disimpan';
            $this->result['content'] = array('id'=>$id_mitra);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    public function save_after_approve()
    {
        $params = json_decode($this->input->post('data_mitra'),TRUE);
        $files = isset($_FILES['files']) ? $_FILES['files'] : [];

        if (!empty($files)) {
            $mappingFiles = mappingFiles($files);
        }

        try{
            // NOTE: 0. prepare
            $model = new \Model\Storage\Conf();
            $now = $model->getDate();
            
            // NOTE: 1. update mitra
            $m_mitra = new \Model\Storage\Mitra_model();
            $id_mitra_old = $params['id_mitra'];
            $d_mitra = $m_mitra->where('id', $id_mitra_old)->first();

            // NOTE: 1. simpan mitra
            $m_mitra = new \Model\Storage\Mitra_model();
            $id_mitra = $m_mitra->getNextIdentity();
            $nomor_mitra = $d_mitra->nomor;

            $m_mitra->id = $id_mitra;
            $m_mitra->nomor = $nomor_mitra;
            $m_mitra->nama = $params['nama'];
            $m_mitra->ktp = $params['ktp'];
            $m_mitra->npwp = $params['npwp'];
            $m_mitra->alamat_kecamatan = $params['alamat']['kecamatan'];
            $m_mitra->alamat_kelurahan = $params['alamat']['kelurahan'];
            $m_mitra->alamat_rt = $params['alamat']['rt'] ?: null;
            $m_mitra->alamat_rw = $params['alamat']['rw'] ?: null;
            $m_mitra->alamat_jalan = $params['alamat']['alamat'] ?: null;
            $m_mitra->bank = $params['d_bank']['bank'] ?: null;
            $m_mitra->rekening_cabang_bank = $params['d_bank']['cabang'] ?: null;
            $m_mitra->rekening_nomor = $params['d_bank']['rekening'] ?: null;
            $m_mitra->rekening_pemilik = $params['d_bank']['pemilik'] ?: null;
            $m_mitra->status = $d_mitra->status;
            $m_mitra->keterangan_jaminan = $params['keterangan_jaminan'];
            $m_mitra->jenis = $params['jenis_mitra'];
            $m_mitra->mstatus = $d_mitra->mstatus;
            $m_mitra->version = ($d_mitra->version) + 1;
            $m_mitra->perusahaan = $params['perusahaan'];
            $m_mitra->save();

            // NOTE : update telepon mitra
            $telepons = $params['telepons'];
            foreach ($telepons as $k => $telepon) {
                $m_telp = new \Model\Storage\TeleponMitra_model();
                $m_telp->id = $m_telp->getNextIdentity();
                $m_telp->mitra = $id_mitra;
                $m_telp->nomor = $telepon;
                $m_telp->save();
            }

            // NOTE: update lampiran mitra
            $lampirans = $params['lampirans'];
            if ( !empty($lampirans) ) {
                foreach ($lampirans as $lampiran) {
                    if ( !empty($lampiran['sha1']) ) {
                        $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
                    }

                    $file_name = $path_name = null;
                    $isMoved = 0;
                    if ( !empty($lampiran['sha1']) ) {
                        $moved = uploadFile($file);
                        $file_name = $moved['name'];
                        $path_name = $moved['path'];
                        $isMoved = $moved['status'];
                    } elseif ( empty($lampiran['sha1']) && !empty($lampiran['old']) ) {
                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $d_lampiran = $m_lampiran->where('tabel_id', $id_mitra_old)
                                                 ->where('tabel', 'mitra')
                                                 ->where('nama_lampiran', $lampiran['id'])
                                                 ->orderBy('id', 'desc')
                                                 ->first();

                        $file_name = $d_lampiran['filename'];
                        $path_name = $d_lampiran['path'];
                        $isMoved = 1;
                    }

                    if ($isMoved) {
                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $m_lampiran->tabel = 'mitra';
                        $m_lampiran->tabel_id = $id_mitra;
                        $m_lampiran->nama_lampiran = $lampiran['id'];
                        $m_lampiran->filename = $file_name ;
                        $m_lampiran->path = $path_name;
                        $m_lampiran->status = 1;
                        $m_lampiran->save();

                    }else {
                        display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
                    }
                }
            }

            // NOTE: update lampiran jaminan
            $lampirans_jaminan = $params['lampirans_jaminan'];
            if ( !empty($lampirans_jaminan) ) {
                foreach ($lampirans_jaminan as $lampiran) {
                    if ( !empty($lampiran['sha1']) ) {
                        $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
                    }

                    $file_name = $path_name = null;
                    $isMoved = 0;
                    if ( !empty($lampiran['sha1']) ) {
                        $moved = uploadFile($file);
                        $file_name = $moved['name'];
                        $path_name = $moved['path'];
                        $isMoved = $moved['status'];
                    } elseif ( empty($lampiran['sha1']) && !empty($lampiran['old']) ) {
                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $d_lampiran = $m_lampiran->where('tabel_id', $id_mitra_old)
                                                 ->where('tabel', 'mitra_jaminan')
                                                 ->where('nama_lampiran', $lampiran['id'])
                                                 ->orderBy('id', 'desc')
                                                 ->first();

                        $file_name = $d_lampiran['filename'];
                        $path_name = $d_lampiran['path'];
                        $isMoved = 1;
                    }

                    if ($isMoved) {
                        $m_lampiran = new \Model\Storage\Lampiran_model();
                        $m_lampiran->tabel = 'mitra_jaminan';
                        $m_lampiran->tabel_id = $id_mitra;
                        $m_lampiran->nama_lampiran = $lampiran['id'];
                        $m_lampiran->filename = $file_name ;
                        $m_lampiran->path = $path_name;
                        $m_lampiran->status = 1;
                        $m_lampiran->save();

                    }else {
                        display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
                    }
                }
            }

            // NOTE: save perwakilan + kandang
            $perwakilans = $params['d_perwakilans'];
            foreach ($perwakilans as $perwakilan) {

                // NOTE: save mitra_mapping
                $m_mitra_mapping = new \Model\Storage\MitraMapping_model();
                $mitra_mapping_id = $m_mitra_mapping->getNextIdentity();
                $m_mitra_mapping->id = $mitra_mapping_id;
                $m_mitra_mapping->mitra = $id_mitra;
                $m_mitra_mapping->perwakilan = $perwakilan['perwakilan_id'];
                $m_mitra_mapping->nim = $perwakilan['nim'];
                $m_mitra_mapping->nomor = $nomor_mitra;
                $m_mitra_mapping->save();

                $kandangs = $perwakilan['d_kandangs'];
                foreach ($kandangs as $kandang) {

                    $kandang_id_old = null;
                    if ( !empty($kandang['id_kdg']) ) {
                        $kandang_id_old = $kandang['id_kdg'];
                    }

                    // NOTE: simpan kandang
                    $m_kandang = new \Model\Storage\Kandang_model();
                    $kandang_id = $m_kandang->getNextIdentity();
                    $m_kandang->id = $kandang_id;
                    $m_kandang->mitra_mapping = $mitra_mapping_id;
                    $m_kandang->kandang = $kandang['no'];
                    $m_kandang->unit = $kandang['unit'];
                    $m_kandang->tipe = $kandang['tipe'];
                    $m_kandang->ekor_kapasitas = $kandang['kapasitas'];
                    $m_kandang->alamat_kecamatan = $kandang['alamat']['kecamatan'];
                    $m_kandang->alamat_kelurahan = $kandang['alamat']['kelurahan'];
                    $m_kandang->alamat_rt = $kandang['alamat']['rt'];
                    $m_kandang->alamat_rw = $kandang['alamat']['rw'];
                    $m_kandang->alamat_jalan = $kandang['alamat']['alamat'];
                    $m_kandang->ongkos_angkut = $kandang['ongkos_angkut'];
                    $m_kandang->grup = $kandang['grup'];
                    $m_kandang->status = $kandang['status'];
                    $m_kandang->save();

                    $bangunans = $kandang['bangunans'];
                    foreach ($bangunans as $bangunan) {
                        $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
                        $m_bangunan_kandang->id = $m_bangunan_kandang->getNextIdentity();
                        $m_bangunan_kandang->kandang = $kandang_id;
                        $m_bangunan_kandang->bangunan = $bangunan['no'];
                        $m_bangunan_kandang->meter_panjang = $bangunan['panjang'];
                        $m_bangunan_kandang->meter_lebar = $bangunan['lebar'];
                        $m_bangunan_kandang->jumlah_unit = $bangunan['jml_unit'];
                        $m_bangunan_kandang->save();
                    }

                    // NOTE: simpan lampiran kandang
                    $lampirans = $kandang['lampirans'];
                    if ( !empty($lampirans) ) {
                        foreach ($lampirans as $lampiran) {
                            $file = null;
                            if ( !empty($lampiran['sha1']) ) {
                                $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
                            }

                            $file_name = $path_name = null;
                            $isMoved = 0;
                            if ( !empty($file) ) {
                                $moved = uploadFile($file);
                                $file_name = $moved['name'];
                                $path_name = $moved['path'];
                                $isMoved = $moved['status'];
                            } elseif ( empty($lampiran['sha1']) && !empty($lampiran['old']) ) {
                                $m_lampiran = new \Model\Storage\Lampiran_model();
                                $d_lampiran = $m_lampiran->where('tabel_id', $kandang_id_old)
                                                         ->where('tabel', 'kandang')
                                                         ->where('nama_lampiran', $lampiran['id'])
                                                         ->orderBy('id', 'desc')
                                                         ->first();

                                $file_name = $d_lampiran['filename'];
                                $path_name = $d_lampiran['path'];
                                $isMoved = 1;
                            }

                            if ($isMoved) {
                                $m_lampiran = new \Model\Storage\Lampiran_model();
                                $m_lampiran->tabel = 'kandang';
                                $m_lampiran->tabel_id = $kandang_id;
                                $m_lampiran->nama_lampiran = $lampiran['id'];
                                $m_lampiran->filename = $file_name ;
                                $m_lampiran->path = $path_name;
                                $m_lampiran->status = 1;
                                $m_lampiran->save();

                            }else {
                                display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
                            }
                        }
                    }
                }
            }

            $d_mitra = $m_mitra->where('id', $id_mitra)->with(['telepons', 'perwakilans'])->first();

            $deskripsi_log_mitra = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_mitra, $deskripsi_log_mitra );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data mitra sukses diupdate';
            $this->result['content'] = array('id'=>$id_mitra);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    // NOTE: save after approve old
    // public function save_after_approve()
    // {
    //     $params = json_decode($this->input->post('data_mitra'),TRUE);
    //     $files = isset($_FILES['files']) ? $_FILES['files'] : [];

    //     if (!empty($files)) {
    //         $mappingFiles = mappingFiles($files);
    //     }

    //     try{
    //         // NOTE: 0. prepare
    //         $model = new \Model\Storage\Conf();
    //         $now = $model->getDate();

    //         $status = 'submit';
    //         // NOTE: 1. update mitra
    //         $m_mitra = new \Model\Storage\Mitra_model();
    //         $id_mitra = $params['id_mitra'];

    //         $m_mitra->where('id', $id_mitra)->update(
    //             array(
    //                 'nama' => $params['nama'],
    //                 'ktp' => $params['ktp'],
    //                 'npwp' => $params['npwp'],
    //                 'alamat_kecamatan' => $params['alamat']['kecamatan'],
    //                 'alamat_kelurahan' => $params['alamat']['kelurahan'],
    //                 'alamat_rt' => $params['alamat']['rt'] ?: null,
    //                 'alamat_rw' => $params['alamat']['rw'] ?: null,
    //                 'alamat_jalan' => $params['alamat']['alamat'] ?: null,
    //                 'bank' => $params['d_bank']['bank'] ?: null,
    //                 'rekening_cabang_bank' => $params['d_bank']['cabang'] ?: null,
    //                 'rekening_nomor' => $params['d_bank']['rekening'] ?: null,
    //                 'rekening_pemilik' => $params['d_bank']['pemilik'] ?: null,
    //                 'keterangan_jaminan' => $params['keterangan_jaminan'],
    //                 'status' => $status,
    //                 'jenis' => $params['jenis_mitra']
    //             )
    //         );

    //         $d_mitra = $m_mitra->where('id', $id_mitra)->first();

    //         $deskripsi_log_mitra = 'di-'. $status .' oleh ' . $this->userdata['detail_user']['nama_detuser'];
    //         Modules::run( 'base/event/update', $d_mitra, $deskripsi_log_mitra );

    //         // NOTE : update telepon mitra
    //         $m_telp = new \Model\Storage\TeleponMitra_model();
    //         $m_telp->where('mitra', $id_mitra)->delete();

    //         $telepons = $params['telepons'];
    //         foreach ($telepons as $k => $telepon) {
    //             $m_telp = new \Model\Storage\TeleponMitra_model();
    //             $m_telp->id = $m_telp->getNextIdentity();
    //             $m_telp->mitra = $id_mitra;
    //             $m_telp->nomor = $telepon;
    //             $m_telp->save();
    //             Modules::run( 'base/event/save', $m_telp, $deskripsi_log_mitra );
    //         }

    //         // NOTE: update lampiran mitra
    //         $lampirans = $params['lampirans'];
    //         if ( !empty($lampirans) ) {
    //             foreach ($lampirans as $lampiran) {
    //                 $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
    //                 $file_name = $path_name = null;
    //                 $isMoved = 0;
    //                 if (!empty($file)) {
    //                     $moved = uploadFile($file);
    //                     $file_name = $moved['name'];
    //                     $path_name = $moved['path'];
    //                     $isMoved = $moved['status'];
    //                 }
    //                 if ($isMoved) {
    //                     $m_lampiran = new \Model\Storage\Lampiran_model();
    //                 if ( !empty($lampiran['old']) ) {
    //                     $m_lampiran->where('tabel_id', $id_mitra)
    //                        ->where('tabel', 'mitra')
    //                        ->where('path', $lampiran['old'])
    //                        ->update( array('status' => 0) );
    //                     }

    //                     $m_lampiran->tabel = 'mitra';
    //                     $m_lampiran->tabel_id = $id_mitra;
    //                     $m_lampiran->nama_lampiran = $lampiran['id'];
    //                     $m_lampiran->filename = $file_name ;
    //                     $m_lampiran->path = $path_name;
    //                     $m_lampiran->status = 1;
    //                     $m_lampiran->save();

    //                     Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_mitra );
    //                 }else {
    //                     display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
    //                 }
    //             }
    //         }

    //         // NOTE: update lampiran jaminan
    //         $lampirans_jaminan = $params['lampirans_jaminan'];
    //         if ( !empty($lampirans_jaminan) ) {
    //             foreach ($lampirans_jaminan as $lJaminan) {
    //                 $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
    //                 $file_name = $path_name = null;
    //                 $isMoved = 0;
    //                 if (!empty($file)) {
    //                     $moved = uploadFile($file);
    //                     $file_name = $moved['name'];
    //                     $path_name = $moved['path'];
    //                     $isMoved = $moved['status'];
    //                 }
    //                 if ($isMoved) {
    //                     $m_lampiran = new \Model\Storage\Lampiran_model();
    //                 if ( !empty($lJaminan['old']) ) {
    //                     $m_lampiran->where('tabel_id', $id_mitra)
    //                                ->where('tabel', 'mitra_jaminan')
    //                                ->where('path', $lJaminan['old'])
    //                                ->update( array('status' => 0) );
    //                     }

    //                     $m_lampiran->tabel = 'mitra_jaminan';
    //                     $m_lampiran->tabel_id = $id_mitra;
    //                     $m_lampiran->nama_lampiran = $lJaminan['id'];
    //                     $m_lampiran->filename = $file_name ;
    //                     $m_lampiran->path = $path_name;
    //                     $m_lampiran->status = 1;
    //                     $m_lampiran->save();

    //                     Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_mitra );
    //                 }
    //             }
    //         }

    //         // NOTE: update perwakilan + kandang
    //         $perwakilans = $params['d_perwakilans'];
    //         foreach ($perwakilans as $perwakilan) {
    //             // NOTE : cek data perakilan sudah ada atau belum
    //             $mitra_mapping_id = $perwakilan['mitra_mapping'];
    //             if ( empty($perwakilan['mitra_mapping']) ) {
    //                 // mitra_mapping = perwakilan
    //                 $m_mitra_mapping = new \Model\Storage\MitraMapping_model();
    //                 $mitra_mapping_id = $m_mitra_mapping->getNextIdentity();
    //                 $m_mitra_mapping->id = $mitra_mapping_id;
    //                 $m_mitra_mapping->mitra = $id_mitra;
    //                 $m_mitra_mapping->perwakilan = $perwakilan['perwakilan_id'];
    //                 $m_mitra_mapping->nim = $perwakilan['nim'];
    //                 $m_mitra_mapping->save();
    //                 Modules::run( 'base/event/save', $m_mitra_mapping, $deskripsi_log_mitra );
    //             }

    //             $kandangs = $perwakilan['d_kandangs'];
    //             foreach ($kandangs as $kandang) {
    //                 if ( empty($kandang['id_kdg']) ) {
    //                     // NOTE: simpan kandang baru
    //                     $m_kandang = new \Model\Storage\Kandang_model();
    //                     $kandang_id = $m_kandang->getNextIdentity();
    //                     $m_kandang->id = $kandang_id;
    //                     $m_kandang->mitra_mapping = $mitra_mapping_id;
    //                     $m_kandang->kandang = $kandang['no'];
    //                     $m_kandang->unit = $kandang['unit'];
    //                     $m_kandang->tipe = $kandang['tipe'];
    //                     $m_kandang->ekor_kapasitas = $kandang['kapasitas'];
    //                     $m_kandang->alamat_kecamatan = $kandang['alamat']['kecamatan'];
    //                     $m_kandang->alamat_kelurahan = $kandang['alamat']['kelurahan'];
    //                     $m_kandang->alamat_rt = $kandang['alamat']['rt'];
    //                     $m_kandang->alamat_rw = $kandang['alamat']['rw'];
    //                     $m_kandang->alamat_jalan = $kandang['alamat']['alamat'];
    //                     $m_kandang->ongkos_angkut = $kandang['ongkos_angkut'];
    //                     $m_kandang->grup = $kandang['grup'];
    //                     $m_kandang->status = $kandang['status'];
    //                     $m_kandang->save();
    //                     Modules::run( 'base/event/save', $m_kandang, $deskripsi_log_mitra );

    //                     $bangunans = $kandang['bangunans'];
    //                     foreach ($bangunans as $bangunan) {
    //                         $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
    //                         $m_bangunan_kandang->id = $m_bangunan_kandang->getNextIdentity();
    //                         $m_bangunan_kandang->kandang = $kandang_id;
    //                         $m_bangunan_kandang->bangunan = $bangunan['no'];
    //                         $m_bangunan_kandang->meter_panjang = $bangunan['panjang'];
    //                         $m_bangunan_kandang->meter_lebar = $bangunan['lebar'];
    //                         $m_bangunan_kandang->jumlah_unit = $bangunan['jml_unit'];
    //                         $m_bangunan_kandang->save();
    //                         Modules::run( 'base/event/save', $m_bangunan_kandang, $deskripsi_log_mitra );
    //                     }

    //                     // NOTE: simpan lampiran kandang
    //                     $lampirans = $kandang['lampirans'];
    //                     foreach ($lampirans as $lampiran) {
    //                         $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
    //                         $file_name = $path_name = null;
    //                         $isMoved = 0;
    //                         if (!empty($file)) {
    //                             $moved = uploadFile($file);
    //                             $isMoved = $moved['status'];

    //                             $file_name = $moved['name'] ?: "";
    //                             $path_name = $moved['path'] ?: "";
    //                         }
    //                         if ($isMoved) {
    //                             $m_lampiran = new \Model\Storage\Lampiran_model();
    //                             $m_lampiran->tabel = 'kandang';
    //                             $m_lampiran->tabel_id = $kandang_id;
    //                             $m_lampiran->nama_lampiran = $lampiran['id'];
    //                             $m_lampiran->filename = $file_name ;
    //                             $m_lampiran->path = $path_name;
    //                             $m_lampiran->status = 1;
    //                             $m_lampiran->save();
    //                             Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_mitra );
    //                         }
    //                     }
    //                 } else {
    //                     // NOTE : ubah kandang lama
    //                     $m_kandang = new \Model\Storage\Kandang_model();
    //                     $kandang_id = $kandang['id_kdg'];

    //                     $stts = ($kandang['status'] == 1) ? 1 : 0;

    //                     $m_kandang->where('id', $kandang_id)->update(
    //                         array(
    //                             'kandang' => $kandang['no'],
    //                             'unit' => $kandang['unit'],
    //                             'tipe' => $kandang['tipe'],
    //                             'ekor_kapasitas' => $kandang['kapasitas'],
    //                             'alamat_kecamatan' => $kandang['alamat']['kecamatan'],
    //                             'alamat_kelurahan' => $kandang['alamat']['kelurahan'],
    //                             'alamat_rt' => $kandang['alamat']['rt'],
    //                             'alamat_rw' => $kandang['alamat']['rw'],
    //                             'alamat_jalan' => $kandang['alamat']['alamat'],
    //                             'ongkos_angkut' => $kandang['ongkos_angkut'],
    //                             'grup' => $kandang['grup'],
    //                             'status' => $stts
    //                         )
    //                     );

    //                     $d_kandang = $m_kandang->where('id', $kandang_id)->first();

    //                     Modules::run( 'base/event/update', $d_kandang, $deskripsi_log_mitra );

    //                     $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
    //                     $m_bangunan_kandang->where('kandang', $kandang['id_kdg'])->delete();

    //                     $bangunans = $kandang['bangunans'];
    //                     foreach ($bangunans as $bangunan) {
    //                         $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
    //                         $m_bangunan_kandang->id = $m_bangunan_kandang->getNextIdentity();
    //                         $m_bangunan_kandang->kandang = $kandang_id;
    //                         $m_bangunan_kandang->bangunan = $bangunan['no'];
    //                         $m_bangunan_kandang->meter_panjang = $bangunan['panjang'];
    //                         $m_bangunan_kandang->meter_lebar = $bangunan['lebar'];
    //                         $m_bangunan_kandang->jumlah_unit = $bangunan['jml_unit'];
    //                         $m_bangunan_kandang->save();
    //                         Modules::run( 'base/event/save', $m_bangunan_kandang, $deskripsi_log_mitra );
    //                     }

    //                     // NOTE: update lampiran kandang
    //                     $lampirans = $kandang['lampirans'];
    //                     if ( !empty($lampirans) ) {
    //                         foreach ($lampirans as $lampiran) {
    //                             $file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
    //                             $file_name = $path_name = null;
    //                             $isMoved = 0;
    //                             if (!empty($file)) {
    //                                 $moved = uploadFile($file);
    //                                 $file_name = $moved['name'];
    //                                 $path_name = $moved['path'];
    //                                 $isMoved = $moved['status'];
    //                             }
    //                             if ($isMoved) {
    //                                 $m_lampiran = new \Model\Storage\Lampiran_model();
    //                                 if ( !empty($lampiran['old']) ) {
    //                                     $m_lampiran->where('tabel_id', $kandang_id)
    //                                                ->where('tabel', 'kandang')
    //                                                ->where('path', $lampiran['old'])
    //                                                ->update( array('status' => 0) );
    //                                 }

    //                                 $m_lampiran->tabel = 'kandang';
    //                                 $m_lampiran->tabel_id = $kandang_id;
    //                                 $m_lampiran->nama_lampiran = $lampiran['id'];
    //                                 $m_lampiran->filename = $file_name ;
    //                                 $m_lampiran->path = $path_name;
    //                                 $m_lampiran->status = 1;
    //                                 $m_lampiran->save();
    //                                 Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_mitra );
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         $this->result['status'] = 1;
    //         $this->result['message'] = 'Data mitra sukses diupdate';
    //         $this->result['content'] = array('id'=>$id_mitra);
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         $this->result['message'] = "Gagal : " . $e->getMessage();
    //     }

    //     display_json($this->result);
    // }

    public function delete()
    {
        $id_mitra = $this->input->post('params');

        try {
            $status = 'delete';

            $m_mitra = new \Model\Storage\Mitra_model();
            $d_mitra_by_id = $m_mitra->where('id', $id_mitra)->first();
            
            $m_mitra->where('nomor', $d_mitra_by_id->nomor)->update(
                array(
                    'status' => $status,
                    'mstatus' => 0
                )
            );

            $d_mitra = $m_mitra->where('id', $id_mitra)->with(['telepons', 'perwakilans'])->first();

            $deskripsi_log_mitra = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_mitra, $deskripsi_log_mitra );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data mitra sukses di-hapus';
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    public function ackReject()
    {
        $action = $this->input->post('action'); // sebagai status

        try {
            $mitra_ids = $this->input->post('ids');

            $deskripsi_log = 'di-' . $action . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
            foreach ($mitra_ids as $id_mitra) {
                $m_mitra = new \Model\Storage\Mitra_model();
                $now = $m_mitra->getDate();

                $d_mitra = $m_mitra->find($id_mitra);
                $d_mitra->status = $action;
                $d_mitra->save();
                Modules::run( 'base/event/update', $d_mitra, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['action'] = $action;
                $this->result['message'] = 'Data mitra sukses di-' . $action;
                $this->result['content'] = array('id'=>$id_mitra);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $this->result['message'] = "Gagal : " . $e->getMessage();
        }

        display_json($this->result);
    }

    public function logLampiran($lampiran_id, $action = 'ditambahkan')
    {
        $m_loglampiran = new \Model\Storage\LogLampiran_model();
        $m_loglampiran->lampiran_id = $lampiran_id;
        $m_loglampiran->user_id = $this->userid;
        $m_loglampiran->deskripsi = $action . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
        $m_loglampiran->save();
    }


    public function rekening_koran($mitra_id)
    {
        $this->set_title( 'Rekening Koran - Mitra' );
        $this->add_external_js(array(
            'assets/master/mitra.js',
        ));
        $this->add_external_css(array(
            'assets/bootstrap-3.3.5/css/awesome-bootstrap-checkbox.css',
            'assets/master/css/mitra.css',
        ));
        $data = $this->includes;

        $order = $this->input->get('ord');
        $content['title_panel'] = 'Rekening Koran';
        $content['current_uri'] = $this->current_uri;
        $content['mitra'] = $this->getDataMitra($mitra_id);
        $content['akuns'] = $this->getAkunRK();
        $content['filters'] = $this->config->item('jenis_trx_rekening_koran');
        // load views
        $data['content'] = $this->load->view($this->pathView . 'rekening_koran', $content, TRUE);
        $this->load->view($this->template, $data);
    }

    public function getAkunRK()
    {
        $m_akun  = new \Model\Storage\AkunRK_model();
        return $m_akun->get();
    }

    public function getDataRK()
    {
        $mitra_id = $this->input->get('mitra_id');
        $nim_id = $this->input->get('nim_id');
        $request_row = $this->input->get('row');
        $filter_trx = $this->input->get('kode');
        $m_mitra_mapping = new \Model\Storage\MitraMapping_model();
        $datas = array();
        $d_kandang = array();

        /* note : jut masuk dalam hitungan transaksi rekening_koran
        if ($nim_id == 'ALL') {
            $datas = $m_mitra_mapping->where('mitra',$mitra_id)->with(['kandangs','juts'])->get();
        }else{
            $datas = $m_mitra_mapping->where('id', $nim_id)->with(['kandangs','juts'])->get();
        }

        $populasi = $jut = 0;
        $id_nims = array();
        foreach ($datas as $data) {
            $kandangs = $data->kandangs;
            $juts = $data->juts;
            $populasi += $kandangs->sum('ekor_kapasitas');
            $jut += $juts->sum('debet');
            $id_nims[] = $data->id;
        }
        */

        if ($nim_id == 'ALL') {
            $datas = $m_mitra_mapping->where('mitra',$mitra_id)->with(['kandangs'])->get();
        }else{
            $datas = $m_mitra_mapping->where('id', $nim_id)->with(['kandangs'])->get();

            // NOTE: get data kandang
            $m_kandang = new \Model\Storage\Kandang_model();
            $d_kandang = $m_kandang->select(['id', 'kandang'])->where('mitra_mapping', $nim_id)->get()->toArray();
        }

        $jut = $datas->sum('jut');
        $populasi = 0;
        $id_nims = array();
        foreach ($datas as $data) {
            $kandangs = $data->kandangs;
            $populasi += $kandangs->sum('ekor_kapasitas');
            $id_nims[] = $data->id;
        }

        $m_rk = new \Model\Storage\MitraRekeningKoran_model();
        if ($filter_trx == 'ALL') {
            $d_rk = $m_rk->whereIn('mitra_mapping', $id_nims)->where('status', '!=', 'delete');
        }else{
            $d_rk = $m_rk->whereIn('mitra_mapping', $id_nims)->where('status', '!=', 'delete')->where('kode_akun', 'LIKE', $filter_trx . '%');
        }

        $content['total'] = array(
            'count_trx' => $d_rk->count(),
            'jml_kredit' => $d_rk->sum('kredit'),
            'jml_debet' => $d_rk->sum('debet'),
        );
        $content['datas'] = $d_rk->with(['lampiran','kandang'])->orderBy('id', 'ASC')->take($request_row)->get();
        $histories = $this->load->view($this->pathView . 'list_rekening_koran', $content, TRUE);

        $this->result['status'] = 1;
        $this->result['message'] = 'success';
        $this->result['content'] = array(
            'populasi' => $populasi,
            'jut' => abs($jut),
            'kandangs' => $d_kandang,
            'histories' => $histories
        );

        display_json($this->result);
    }

    public function saveRK()
    {
        $params = json_decode($this->input->post('params'),TRUE);
        $attach = isset($_FILES['attach']) ? $_FILES['attach'] : null;

        $nim_id = $params['nim_id'];
        $trx = strtoupper( $params['jenis_trx'] );

        if ($nim_id != 'ALL') {
            $kredit = $params['kredit'];
            $debet = $params['debet'];

            $m_rk = new \Model\Storage\MitraRekeningKoran_model();

            // NOTE: update status reject menjadi delete sebelum dilakukan penyimpanan RK baru supaya saldo acuan bukan bersumber dari data yang ditolak
            $m_rk->where('mitra_mapping', $nim_id)->whereStatus('reject')->update(['status'=>'delete']);

            // NOTE: ambil saldo terakhir dari nim yang bersangkutan
            $d_rk = $m_rk->where('mitra_mapping', $nim_id)->whereNotIn('status', ['delete', 'reject'])->orderBy('id', 'DESC')->first();
            $saldo = empty($d_rk) ? 0 : $d_rk->saldo;
            if ( $trx == 'DEBET' ) {
                $saldo += $debet;
            }else{
                $saldo -= $kredit;
            }

            $status = 'submit';
            $m_rk = new \Model\Storage\MitraRekeningKoran_model();
            $m_rk->mitra_mapping = $nim_id;
            $m_rk->tgl_buku = $params['tgl_buku'];
            $m_rk->kode_akun = $params['kode_akun'];
            $m_rk->bukti = $params['bukti'];
            $m_rk->phb = $params['phb'];
            $m_rk->nkk = $params['nkk'];
            $m_rk->siklus = $params['siklus'] ?: null;
            $m_rk->kandang_id = $params['kandang_id'] ?: null;
            $m_rk->keterangan = $params['keterangan'];
            $m_rk->debet = $debet;
            $m_rk->kredit = $kredit;
            $m_rk->saldo = $saldo;
            $m_rk->status = $status;
            $m_rk->save();

            $deskripsi_log = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_rk, $deskripsi_log);
            if (!empty($attach)) {
                Modules::run( 'base/lampiran/save', $m_rk, $attach);
            }

            $html = '<tr class="data">
                        <td class="tanggal">'.tglIndonesia($params['tgl_buku']).'</td>
                        <td class="akun_rk">'.$params['kode_akun'].'</td>
                        <td class="no-bukti">'.$params['bukti'].'</td>
                        <td class="no-phb">'.$params['phb'].'</td>
                        <td class="no-nkk">'.$params['nkk'].'</td>
                        <td class="keterangan">'.$params['keterangan'].'</td>
                        <td class="debet">'.angkaDecimal($debet).'</td>
                        <td class="kredit">'.angkaDecimal($kredit).'</td>
                        <td class="saldo">'.angkaDecimal($saldo).'</td>
                    </tr>';

            $this->result['status'] = 1;
            $this->result['message'] = 'success';
            $this->result['content'] = array(
                'html' => $html
            );

            display_json($this->result);
        }
    }

    public function rekening_koran_list_ack($_title = 'ACK')
    {
        if (hasAkses('master/mitra/rekening_koran')) {

        $this->set_title( 'Rekening Koran - Mitra' );
        $this->add_external_js(array(
            'assets/master/mitra.js',
        ));
        $this->add_external_css(array(
            'assets/bootstrap-3.3.5/css/awesome-bootstrap-checkbox.css',
            'assets/master/css/mitra.css',
        ));
        $data = $this->includes;

        $order = $this->input->get('ord');
        $content['title_panel'] = 'List Rekening Koran - ' . $_title;
        $content['current_uri'] = $this->current_uri;
        $datas = array();

        $pathView = '';
        if (hasAkses('master/mitra/rekening_koran/ack')){
            $datas = $this->getListRekeningKoranForAck();
            $pathView = $this->pathView . 'list_rekening_koran_for_ack';
        }
        else	// NOTE: tampilkan RK yang di reject
        if (hasAkses('master/mitra/rekening_koran/submit')){
            $datas = $this->getListRekeningKoranForAck('reject');
            $pathView = $this->pathView . 'list_rekening_koran_for_reject';
        }

        $content['datas'] = $datas;
        $data['content'] = $this->load->view($pathView, $content, TRUE);
        $this->load->view($this->template, $data);

        }else{
            showErrorAkses();
        }
    }

    public function getListRekeningKoranForAck( $status = 'submit')
    {
        $m_rk = new \Model\Storage\MitraRekeningKoran_model();
        $d_rk = $m_rk->where('status', $status)->orderBy('id', 'ASC')->with(['perwakilan','lampiran', 'kandang'])->get();
        // $d_rk = $m_rk->with(['perwakilan','lampiran'])->orderBy('id', 'ASC')->get();

        $datas = array();
        foreach ($d_rk as $rk) {
            $trx = array(
                'id' => $rk->id,
                'tanggal' => $rk->tgl_buku,
                'akun' => $rk->kode_akun,
                'bukti' => $rk->bukti,
                'phb' => $rk->phb,
                'nkk' => $rk->nkk,
                'siklus' => $rk->siklus,
                'kandang' => isset($rk->kandang) ? $rk->kandang->kandang : "-",
                'keterangan' => $rk->keterangan,
                'lampiran' => empty( $rk->lampiran) ? [] :  $rk->lampiran->toArray(),
                'debet' => $rk->debet,
                'kredit' => $rk->kredit,
                'saldo' => $rk->saldo,
            );

            $idx = $rk->perwakilan->dMitra->nama . '_|_' . $rk->mitra_mapping;
            if (! isset($datas[ $idx ])) {
                $datas[ $idx ] = array(
                    'mitra_id' => $rk->perwakilan->dMitra->id,
                    'nomor' => $rk->perwakilan->dMitra->nomor,
                    'mitra' => $rk->perwakilan->dMitra->nama,
                    'ktp' => $rk->perwakilan->dMitra->ktp,
                    'nim' => $rk->perwakilan->nim,
                    'trxs' => array()
                );
            }

            $datas[ $idx ]['trxs'][] = $trx;
        }

        ksort($datas);

        return $datas;
    }

    public function updateAckRK()
    {
        $params = $this->input->post('params');
        $nim_id = $params['nim_id'];
        $ack_ids = isset($params['ack_ids']) ? $params['ack_ids']: [];
        $reject_ids = isset($params['reject_ids']) ? $params['reject_ids']: [];
        $action = $params['action'];

        $m_rk = new \Model\Storage\MitraRekeningKoran_model();
        // NOTE: ACK
        foreach ($ack_ids as $ack_id) {
            $ack_rk = $m_rk->find($ack_id);
            $ack_rk->status = 'ack';
            $ack_rk->save();
            $event = Modules::run( 'base/event/update', $ack_rk, 'di-ack oleh ' . $this->userdata['Nama_User'] );
        }

        // NOTE: REJECT
        foreach ($reject_ids as $reject_id) {
            $reject_rk = $m_rk->find($reject_id);
            $reject_rk->status = 'reject';
            $reject_rk->save();
            $event = Modules::run( 'base/event/update', $reject_rk, 'di-reject oleh ' . $this->userdata['Nama_User'] );
        }

        if ($event) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di-' . $action;
        }else{
            $this->result['message'] = 'Data gagal di-ack';
        }

        display_json($this->result);
    }

    public function saveJut()
    {
        $params = $this->input->post('params');
        $nim_id = $params['nim_id'];
        $jut = $params['jut'];

        $model = new \Model\Storage\MitraMapping_model();
        $data = $model->find($nim_id);
        $data->jut = $jut;
        $data->save();

        $event = Modules::run( 'base/event/update', $data, 'di-submit oleh ' . $this->userdata['Nama_User'] );
        if ($event) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Data JUT berhasil di-submit';
        }else{
            $this->result['message'] = 'Data JUT gagal di-submit';
        }

        display_json($this->result);
    }

    public function rekening_koran_list_reject()
    {
        $this->rekening_koran_list_ack('Reject');
    }

    public function form_export_excel()
    {
        $html = $this->load->view('parameter/peternak/form_export_excel', null); 
        
        echo $html;
    }

    public function verifikasi_export_excel()
    {
        $params = $this->input->post('params');

        $username = $params['username'];
        $password = $params['password'];

        $admins = $this->config->item('auth_export_excel')['auth_peternak'];

        if ( stristr($username, $admins[0]['user']) !== FALSE && $password == $admins[0]['pin'] ) {
            $this->result['status'] = 1;
        } else {
            $this->result['message'] = 'Username dan Password yang anda masukkan tidak cocok.';
        }

        display_json($this->result);
    }

    public function export_excel()
    {
        $m_mitra = new \Model\Storage\Mitra_model();
        $list_nomor = $m_mitra->select('nomor')->distinct('nomor')->get()->toArray();

        $data = array();
        foreach ($list_nomor as $k_nomor => $nomor) {
            $mitra = $m_mitra->where('nomor', $nomor)
                             ->with(['dKecamatan'])
                             ->orderBy('version', 'desc')
                             ->orderBy('id', 'desc')
                             ->first()->toArray();

            $kdg = '';
            $unit = '';

            $m_mm = new \Model\Storage\MitraMapping_model();
            $d_mm = $m_mm->select('id')->where('mitra', $mitra['id'])->get();
            if ( $d_mm->count() > 0 ) {
                $d_mm = $d_mm->toArray();

                $m_kdg = new \Model\Storage\Kandang_model();
                $d_kdg = $m_kdg->whereIn('mitra_mapping', $d_mm)->with(['d_unit'])->get();
                if ( $d_kdg->count() > 0 ) {
                    $d_kdg = $d_kdg->toArray();

                    foreach ($d_kdg as $k_kdg => $v_kdg) {
                        $kandang = $v_kdg['kandang'];
                        if ( $kdg != '' ) {
                            $kdg .= ', '.$kandang;
                        } else {
                            $kdg .= $kandang;
                        }

                        $_unit = $v_kdg['d_unit']['kode'];
                        if ( $unit != '' ) {
                            $unit .= ', '.$_unit;
                        } else {
                            $unit .= $_unit;
                        }
                    }
                }
            }

            $jalan = empty($mitra['alamat_jalan']) ? '' : strtoupper('DSN.'.trim(str_replace('DSN.', '', $mitra['alamat_jalan'])));
            $rt = empty($mitra['alamat_rt']) ? '' : strtoupper(' RT.'.$mitra['alamat_rt']);
            $rw = empty($mitra['alamat_rw']) ? '' : strtoupper('/RW.'.$mitra['alamat_rw']);
            $kelurahan = empty($mitra['alamat_kelurahan']) ? '' : strtoupper(' ,'.$mitra['alamat_kelurahan']);
            $kecamatan = empty($mitra['alamat_kecamatan']) ? '' : strtoupper(' ,'.$mitra['d_kecamatan']['nama']);
            $kabupaten = empty($mitra['d_kecamatan']['d_kota']) ? '' : strtoupper(' ,'.$mitra['d_kecamatan']['d_kota']['nama']);
            $provinsi = empty($mitra['d_kecamatan']['d_kota']['d_provinsi']) ? '' : strtoupper(' ,'.$mitra['d_kecamatan']['d_kota']['d_provinsi']['nama']);

            $alamat = $jalan.$rt.$rw.$kelurahan.$kecamatan.$kabupaten.$provinsi;

            $key = $mitra['nama'].'|'.$mitra['nomor'];
            $data[ $key ] = array(
                'id' => $mitra['id'],
                'nomor' => $mitra['nomor'],
                'ktp' => $mitra['ktp'],
                'npwp' => $mitra['npwp'],
                'nama' => $mitra['nama'],
                'alamat' => $alamat,
                'kdg' => $kdg,
                'unit' => $unit,
                'status' => $mitra['status']
            );

            ksort($data);
        }

        $content['data'] = $data;
        $res_view_html = $this->load->view('parameter/peternak/export_excel', $content, true);

        $filename = 'export-peternak-'.str_replace('-', '', date('Y-m-d')).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }

    public function model($status)
    {
        if ( is_numeric($status) ) {
            $status = getStatus($status);
        }

        $m_mitra = new \Model\Storage\Mitra_model();
        $dashboard = $m_mitra->getDashboard($status);

        return $dashboard;
    }

    public function tes()
    {
        $m_mitra = new \Model\Storage\Mitra_model();
        $nomor_mitra = $m_mitra->getNextNomor();

        cetak_r( $nomor_mitra );
    }
}
