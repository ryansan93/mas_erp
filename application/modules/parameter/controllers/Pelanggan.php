<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan extends Public_Controller {

	private $pathView = 'parameter/pelanggan/';
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
	public function index() {
		$akses = hakAkses($this->url);
        if ( $akses['a_view'] == 1 ) {
			$this->add_external_js(array(
				'assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js',
				'assets/jquery/maskedinput/jquery.maskedinput.min.js',
				'assets/pagination-ry/pagination.js',
				'assets/parameter/pelanggan/js/pelanggan.js'
			));

			$this->add_external_css(array(
				'assets/jquery/easy-autocomplete/easy-autocomplete.min.css',
				'assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css',
				'assets/pagination-ry/pagination.css',
				'assets/parameter/pelanggan/css/pelanggan.css',
			));

			$data = $this->includes;

			$content['title_panel'] = 'Master Data Pelanggan';
			$content['akses'] = $akses;
			// $content['list_provinsi'] = $this->getLokasi('PV');
			// $content['list_lampiran_pelanggan'] = $this->getNamaLampiran("PELANGGAN")->first();
			// $content['list_lampiran_usaha_pelanggan'] = $this->getNamaLampiran("USAHA_PELANGGAN")->first();
			// $content['list_lampiran_rekening_pelanggan'] = $this->getNamaLampiran("REKENING_PELANGGAN")->first();
			// $content['list_lampiran_ddp_pelanggan'] = $this->getNamaLampiran("LAMPIRAN_PELANGGAN")->first();

			$content['add_form'] = $this->add_form();

			// load list pelanggan
			// $detail_content['pelanggans'] = $this->getListPelanggan();

			$data['title_menu'] = 'Master Pelanggan';
			$data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

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

        $m_plg = new \Model\Storage\Pelanggan_model();
        $d_nomor = null;

        $sql = "
    		select max(plg.id)
    		from
    			pelanggan plg
    		group by
    			plg.nomor
    	";

        $d_id = $m_plg->hydrateRaw($sql);
        if ( $d_id->count() > 0 ) {
        	$d_id = $d_id->toArray();

	        if ( !empty($search_by) && !empty($search_val) ) {
	            if ( stristr($search_by, 'nama') !== FALSE ) {
	            	$d_nomor = $m_plg->select('nomor', 'nama')->distinct('nomor')->where($search_by, 'like', '%'.$search_val.'%')->whereIn('id', $d_id)->where('tipe', 'pelanggan')->where('mstatus', 1)->orderBy('nama', 'asc')->get()->toArray();
	            }
	        } else {
	        	$d_nomor = $m_plg->select('nomor', 'nama')->distinct('nomor')->whereIn('id', $d_id)->where('tipe', 'pelanggan')->where('mstatus', 1)->orderBy('nama', 'asc')->get()->toArray();
	        }
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

	            $list_nomor[$jml_page][$idx_row] = trim($v_nomor['nomor']);

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

    public function list_plg()
    {
    	$list_nomor = $this->input->get('params');

        $akses = hakAkses($this->url);

        $data = $this->getListPelanggan($list_nomor);

        $content['akses'] = $akses;
        $content['data'] = $data;

        $html = $this->load->view('parameter/pelanggan/list', $content);
        
        echo $html;
    }

    public function add_form()
    {
        $akses = hakAkses($this->url);

        $content['akses'] = $akses;
        $content['list_provinsi'] = $this->getLokasi('PV');
		$content['list_lampiran_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "KTP Pelanggan")->first();
		$content['list_lampiran_usaha_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "NPWP Pelanggan")->first();
		$content['list_lampiran_rekening_pelanggan'] = $this->getNamaLampiran("BANK_PELANGGAN", "Rekening Pelanggan")->first();
		$content['list_lampiran_ddp_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "DDP Pelanggan")->first();
        $content['data'] = null;
        $html = $this->load->view('parameter/pelanggan/add_form', $content, true);
        
        return $html;
    }

    public function edit_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        // mengambil data pelanggan
		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$d_pelanggan = $m_pelanggan->where('tipe', 'pelanggan')->where('id', $id)->with('telepons')->with('banks')->with('logs')->first();
		
		// mengambil lokasi
		$lokasi = new \Model\Storage\Lokasi_model();
		$kec = $lokasi->where('id', $d_pelanggan['alamat_kecamatan'])->first();
		$kota = $lokasi->where('id', $kec['induk'])->first();
		$prov = $lokasi->where('id', $kota['induk'])->first();
		$kec_usaha = $lokasi->where('id', $d_pelanggan['usaha_kecamatan'])->first();
		$kota_usaha = $lokasi->where('id', $kec_usaha['induk'])->first();
		$prov_usaha = $lokasi->where('id', $kota_usaha['induk'])->first();

		// simpan data lokasi
		$detail_lokasi = array(
			'prov_id' => $prov['id'],
			'prov' => $prov['nama'],
			'kota_id' => $kota['id'],
			'kota_jenis' => $kota['jenis'],
			'kota' => $kota['nama'],
			'kec_id' => $kec['id'],
			'kec' => $kec['nama'],
			'prov_usaha_id' => $prov_usaha['id'],
			'prov_usaha' => $prov_usaha['nama'],
			'kota_usaha_id' => $kota_usaha['id'],
			'kota_usaha' => $kota_usaha['nama'],
			'kota_usaha_jenis' => $kota_usaha['jenis'],
			'kec_usaha_id' => $kec_usaha['id'],
			'kec_usaha' => $kec_usaha['nama']
		);

		// mengambil lampiran pelanggan
		$m_nama_lampiran = new \Model\Storage\NamaLampiran_model;
		$d_nama_lampiran = $m_nama_lampiran->where('jenis', 'BANK_PELANGGAN')->first();

		$m_lampiran = new \Model\Storage\Lampiran_model;
		$d_lampiran = $m_lampiran->where('tabel', 'bank_pelanggan')->where('nama_lampiran', $d_nama_lampiran['id'])->get()->toArray();

		// cetak_r($d_pelanggan->toArray(), 1);

		$lampiran_ktp = $this->getLampiranPelanggan($d_pelanggan['id'], 'KTP Pelanggan');
		$lampiran_npwp = $this->getLampiranPelanggan($d_pelanggan['id'], 'NPWP Pelanggan');
		$lampiran_ddp = $this->getLampiranPelanggan($d_pelanggan['id'], 'DDP Pelanggan');

		$content['data'] = $d_pelanggan;
		$content['lokasi'] = $detail_lokasi;
		$content['l_ktp'] = $lampiran_ktp;
		$content['l_npwp'] = $lampiran_npwp;
		$content['l_ddp'] = $lampiran_ddp;
        $content['akses'] = $akses;

        $content['list_provinsi'] = $this->getLokasi('PV');
		$content['list_lampiran_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "KTP Pelanggan")->first();
		$content['list_lampiran_usaha_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "NPWP Pelanggan")->first();
		$content['list_lampiran_rekening_pelanggan'] = $this->getNamaLampiran("BANK_PELANGGAN", "Rekening Pelanggan")->first();
		$content['list_lampiran_ddp_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "DDP Pelanggan")->first();

        $html = $this->load->view('parameter/pelanggan/edit_form', $content, true);
        
        return $html;
    }

    public function view_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        // mengambil data pelanggan
		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$d_pelanggan = $m_pelanggan->where('tipe', 'pelanggan')->where('id', $id)->with(['telepons', 'banks', 'posisi', 'logs'])->first();
		
		// mengambil lokasi
		$lokasi = new \Model\Storage\Lokasi_model();
		$kec = $lokasi->where('id', $d_pelanggan['alamat_kecamatan'])->first();
		$kota = $lokasi->where('id', $kec['induk'])->first();
		$prov = $lokasi->where('id', $kota['induk'])->first();
		$kec_usaha = $lokasi->where('id', $d_pelanggan['usaha_kecamatan'])->first();
		$kota_usaha = $lokasi->where('id', $kec_usaha['induk'])->first();
		$prov_usaha = $lokasi->where('id', $kota_usaha['induk'])->first();

		// simpan data lokasi
		$detail_lokasi = array(
			'prov_id' => $prov['id'],
			'prov' => $prov['nama'],
			'kota_id' => $kota['id'],
			'kota' => $kota['nama'],
			'kec_id' => $kec['id'],
			'kec' => $kec['nama'],
			'prov_usaha_id' => $prov_usaha['id'],
			'prov_usaha' => $prov_usaha['nama'],
			'kota_usaha_id' => $kota_usaha['id'],
			'kota_usaha' => $kota_usaha['nama'],
			'kec_usaha_id' => $kec_usaha['id'],
			'kec_usaha' => $kec_usaha['nama']
		);

		// mengambil lampiran pelanggan
		$m_nama_lampiran = new \Model\Storage\NamaLampiran_model;
		$d_nama_lampiran = $m_nama_lampiran->where('jenis', 'BANK_PELANGGAN')->first();

		$m_lampiran = new \Model\Storage\Lampiran_model;
		$d_lampiran = $m_lampiran->where('tabel', 'bank_pelanggan')->where('nama_lampiran', $d_nama_lampiran['id'])->get();

		$lampiran_ktp = $this->getLampiranPelanggan($d_pelanggan['id'], 'KTP Pelanggan');
		$lampiran_npwp = $this->getLampiranPelanggan($d_pelanggan['id'], 'NPWP Pelanggan');
		$lampiran_ddp = $this->getLampiranPelanggan($d_pelanggan['id'], 'DDP Pelanggan');

		$content['data'] = !empty($d_pelanggan) ? $d_pelanggan->toArray() : null;
		$content['lokasi'] = $detail_lokasi;
		$content['l_ktp'] = $lampiran_ktp;
		$content['l_npwp'] = $lampiran_npwp;
		$content['l_ddp'] = $lampiran_ddp;
		$content['tbl_logs'] = $this->getLogs($d_pelanggan->nomor);
        $content['akses'] = $akses;

        $html = $this->load->view('parameter/pelanggan/view_form', $content, true);
        
        return $html;
    }

    public function getLogs($nomor = null) {
	    $m_plg = new \Model\Storage\Pelanggan_model;
    	$d_plg = $m_plg->where('nomor', $nomor)->where('tipe', 'pelanggan')->orderBy('version', 'asc')->get()->toArray();

    	$logs = array();
    	foreach ($d_plg as $key => $v_plg) {
	    	$m_log = new \Model\Storage\LogTables_model;
	    	$d_log = $m_log->select('deskripsi', 'waktu')->where('tbl_name', 'pelanggan')->where('tbl_id', $v_plg['id'])->get()->toArray();

	    	if ( !empty($d_log) ) {
	    		foreach ($d_log as $key => $v_log) {
    				$logs[] = $v_log;
	    		}
	    	}
    	}

    	return $logs;
    }

	public function getNamaLampiran($jenis = null, $nama = null) {
		$m_lampiran = new \Model\Storage\NamaLampiran_model();
		$d_lampiran = $m_lampiran->where('jenis', $jenis)->where('nama', $nama)->get();
		return $d_lampiran;
    }

    public function getLampiranPelanggan($id, $nama) {
    	$m_lampiran = new \Model\Storage\Lampiran_model();
    	$d_lampiran = $m_lampiran->where('tabel_id', $id)->with(['d_nama_lampiran'])->get()->toArray();

    	$data = null;
    	foreach ($d_lampiran as $key => $v_lampiran) {
    		$nama_lampiran = $v_lampiran['d_nama_lampiran']['nama'];
    		if ( $nama_lampiran == $nama ) {
    			$data = $v_lampiran;
    		}
    	}

    	return $data;
    }

	public function getLokasi($jenis, $induk = null) {
		$m_lokasi = new \Model\Storage\Lokasi_model();
		if ($induk == null) {
			$d_lokasi = $m_lokasi ->where('jenis', $jenis)->orderBy('nama', 'ASC')->get();
		}else{
			$d_lokasi = $m_lokasi ->where('jenis', $jenis)->where('induk', $induk)->orderBy('nama', 'ASC')->get();
		}
		return $d_lokasi;
    }

	public function getLokasiJson() {
		$jenis = $this->input->get('jenis');
		$induk = $this->input->get('induk');

		$data = $this->getLokasi($jenis, $induk);

		$this->result['content'] = $data;
		$this->result['status'] = 1;

		display_json($this->result);
	}

	public function save() {
		$params = json_decode($this->input->post('data_pelanggan'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		$mappingFiles = mappingFiles($files);

		$status = "submit";

		// pelanggan
		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$pelanggan_id = $m_pelanggan->getNextIdentity();

		$kode_jenis = ($params['jenis_pelanggan'] == "internal") ? "A" : "B";

		$no_pelanggan = $m_pelanggan->getNextNomor($kode_jenis);

		$m_pelanggan->id = $pelanggan_id;		
		$m_pelanggan->jenis = $params['jenis_pelanggan'];
		$m_pelanggan->nomor = $no_pelanggan;
		$m_pelanggan->nama = $params['nama'];
		$m_pelanggan->nik = $params['ktp'];
		$m_pelanggan->cp = $params['cp'];
		$m_pelanggan->npwp = $params['npwp'];
		$m_pelanggan->alamat_kecamatan = $params['alamat_pelanggan']['kecamatan'];
		$m_pelanggan->alamat_kelurahan = $params['alamat_pelanggan']['kelurahan'];
		$m_pelanggan->alamat_rt = $params['alamat_pelanggan']['rt'] ?: null;
		$m_pelanggan->alamat_rw = $params['alamat_pelanggan']['rw'] ?: null;
		$m_pelanggan->alamat_jalan = $params['alamat_pelanggan']['alamat'] ?: null;
		$m_pelanggan->usaha_kecamatan = $params['alamat_usaha']['kecamatan'];
		$m_pelanggan->usaha_kelurahan = $params['alamat_usaha']['kelurahan'];
		$m_pelanggan->usaha_rt = $params['alamat_usaha']['rt'] ?: null;
		$m_pelanggan->usaha_rw = $params['alamat_usaha']['rw'] ?: null;
		$m_pelanggan->usaha_jalan = $params['alamat_usaha']['alamat'] ?: null;
		$m_pelanggan->status = $status;
		$m_pelanggan->mstatus = 1;
		$m_pelanggan->tipe = 'pelanggan';
		$m_pelanggan->platform = $params['platform'];
		$m_pelanggan->version = 1;
		$m_pelanggan->save();

		$deskripsi_log_pelanggan = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/save', $m_pelanggan, $deskripsi_log_pelanggan );

		// telepon pelanggan
		$telepons = $params['telepons'];
		foreach ($telepons as $k => $telepon) {
			$m_telp = new \Model\Storage\TelpPelanggan_model();
			$m_telp->id = $m_telp->getNextIdentity();
			$m_telp->pelanggan = $pelanggan_id;
			$m_telp->nomor = $telepon;
			$m_telp->save();
			Modules::run( 'base/event/save', $m_telp, $deskripsi_log_pelanggan );
    	}

    	// rekening dan bank pelanggan
    	$banks = $params['banks'];
    	foreach ($banks as $k => $bank) {
    		$m_bank = new \Model\Storage\BankPelanggan_model();
    		$bank_plg_id = $m_bank->getNextIdentity();

    		$m_bank->id = $bank_plg_id;
    		$m_bank->pelanggan = $pelanggan_id;
    		$m_bank->bank = $bank['nama_bank'];
    		$m_bank->rekening_nomor = $bank['nomer_rekening'];
    		$m_bank->rekening_pemilik = $bank['nama_pemilik'];
    		$m_bank->rekening_cabang_bank = $bank['cabang_bank'];
    		$m_bank->save();
    		Modules::run( 'base/event/save', $m_telp, $deskripsi_log_pelanggan );

    		$lampiran = $bank['lampiran'];
    		$file_name = $path_name = null;
    		if ( !empty($lampiran) ) {
	    		$file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
	    		$isMoved = 0;
	    		if (!empty($file)) {
	    			$moved = uploadFile($file);
	    			$isMoved = $moved['status'];
	    		}
	    		if ($isMoved) {
	    			$file_name = $moved['name'];
					$path_name = $moved['path'];
	    		}
	    		
				$m_lampiran = new \Model\Storage\Lampiran_model();
				$m_lampiran->tabel = 'bank_pelanggan';
				$m_lampiran->tabel_id = $bank_plg_id;
				$m_lampiran->nama_lampiran = $lampiran['id'];
				$m_lampiran->filename = $file_name ;
				$m_lampiran->path = $path_name;
				$m_lampiran->status = 1;
				$m_lampiran->save();
				Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_pelanggan );
    		}
    		// if ($isMoved) {

    		// } else {
    		// 	display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
    		// }
    	}

    	$lampirans = $params['lampirans'];
    	foreach ($lampirans as $lampiran) {
    		$file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
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
    			$m_lampiran->tabel = 'pelanggan';
    			$m_lampiran->tabel_id = $pelanggan_id;
    			$m_lampiran->nama_lampiran = $lampiran['id'];
    			$m_lampiran->filename = $file_name ;
    			$m_lampiran->path = $path_name;
    			$m_lampiran->status = 1;
    			$m_lampiran->save();
    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_pelanggan );

    		}else {
    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
    		}
    	}

    	$lampiran_ddp = $params['lampiran_ddp'];
		$file = $mappingFiles[ $lampiran_ddp['sha1'] . '_' . $lampiran_ddp['name'] ] ?: '';
		$file_name = $path_name = null;
		$isMoved = 0;
		if (!empty($file)) {
			$moved = uploadFile($file);
			$isMoved = $moved['status'];
			
    		if ($isMoved) {
    			$file_name = $moved['name'];
    			$path_name = $moved['path'];

    			$m_lampiran = new \Model\Storage\Lampiran_model();
    			$m_lampiran->tabel = 'pelanggan';
    			$m_lampiran->tabel_id = $pelanggan_id;
    			$m_lampiran->nama_lampiran = $lampiran_ddp['id'];
    			$m_lampiran->filename = $file_name ;
    			$m_lampiran->path = $path_name;
    			$m_lampiran->status = 1;
    			$m_lampiran->save();
    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_pelanggan );
    		}else {
    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
    		}
		}

    	$m_sp = new \Model\Storage\SaldoPelanggan_model();
		$m_sp->jenis_saldo = 'D';
		$m_sp->no_pelanggan = $no_pelanggan;
		$m_sp->id_trans = NULL;
		$m_sp->tgl_trans = date('Y-m-d');
		$m_sp->jenis_trans = 'pembayaran_pelanggan';
		$m_sp->nominal = 0;
		$m_sp->saldo = 0;
		$m_sp->tgl_mulai_bayar = prev_date(date('Y-m-d'), 7);
		$m_sp->save();

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data pelanggan sukses disimpan';
      	$this->result['content'] = array('id'=>$pelanggan_id);

    	display_json($this->result);
	}

	public function edit() {
		$params = json_decode($this->input->post('data_pelanggan'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		if (!empty($files)) {
            $mappingFiles = mappingFiles($files);
        }

		$pelanggan_id_old = $params['id'];
		$status = $params['status'];
		$mstatus = $params['mstatus'];
		$version = $params['version'] + 1;

		// pelanggan
		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$pelanggan_id = $m_pelanggan->getNextIdentity();

		$m_pelanggan->id = $pelanggan_id;
		$m_pelanggan->jenis = $params['jenis_pelanggan'];
		$m_pelanggan->nomor = $params['nomor'];
		$m_pelanggan->nama = $params['nama'];
		$m_pelanggan->nik = $params['ktp'];
		$m_pelanggan->cp = $params['cp'];
		$m_pelanggan->npwp = $params['npwp'];
		$m_pelanggan->alamat_kecamatan = $params['alamat_pelanggan']['kecamatan'];
		$m_pelanggan->alamat_kelurahan = $params['alamat_pelanggan']['kelurahan'];
		$m_pelanggan->alamat_rt = $params['alamat_pelanggan']['rt'] ?: null;
		$m_pelanggan->alamat_rw = $params['alamat_pelanggan']['rw'] ?: null;
		$m_pelanggan->alamat_jalan = $params['alamat_pelanggan']['alamat'] ?: null;
		$m_pelanggan->usaha_kecamatan = $params['alamat_usaha']['kecamatan'];
		$m_pelanggan->usaha_kelurahan = $params['alamat_usaha']['kelurahan'];
		$m_pelanggan->usaha_rt = $params['alamat_usaha']['rt'] ?: null;
		$m_pelanggan->usaha_rw = $params['alamat_usaha']['rw'] ?: null;
		$m_pelanggan->usaha_jalan = $params['alamat_usaha']['alamat'] ?: null;
		$m_pelanggan->status = $status;
		$m_pelanggan->mstatus = $mstatus;
		$m_pelanggan->tipe = 'pelanggan';
		$m_pelanggan->platform = $params['platform'];
		$m_pelanggan->version = $version;
		$m_pelanggan->save();

		$deskripsi_log_pelanggan = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/update', $m_pelanggan, $deskripsi_log_pelanggan );

		// telepon pelanggan
		$telepons = $params['telepons'];
		foreach ($telepons as $k => $telepon) {
			$m_telp = new \Model\Storage\TelpPelanggan_model();
			$m_telp->id = $m_telp->getNextIdentity();

			$m_telp->pelanggan = $pelanggan_id;
			$m_telp->nomor = $telepon;
			$m_telp->save();
			Modules::run( 'base/event/update', $m_telp, $deskripsi_log_pelanggan );
    	}

    	// rekening dan bank pelanggan
    	$banks = $params['banks'];
    	foreach ($banks as $k => $bank) {
    		$m_bank = new \Model\Storage\BankPelanggan_model();
    		$bank_plg_id = $m_bank->getNextIdentity();

    		$m_bank->id = $bank_plg_id;
    		$m_bank->pelanggan = $pelanggan_id;
    		$m_bank->bank = $bank['nama_bank'];
    		$m_bank->rekening_nomor = $bank['nomer_rekening'];
    		$m_bank->rekening_pemilik = $bank['nama_pemilik'];
    		$m_bank->rekening_cabang_bank = $bank['cabang_bank'];
    		$m_bank->save();
    		Modules::run( 'base/event/update', $m_telp, $deskripsi_log_pelanggan );

    		$lampiran = $bank['lampiran'];
    		if ( !empty($lampiran) ) {
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
                	$d_lampiran = $m_lampiran->where('tabel_id', $bank['id_old'])
                							 ->where('tabel', 'bank_pelanggan')
                							 ->where('nama_lampiran', $lampiran['id'])
                							 ->orderBy('id', 'desc')
                							 ->first();

                	$file_name = $d_lampiran['filename'];
                    $path_name = $d_lampiran['path'];
                    $isMoved = 1;
                }

	    		if ($isMoved) {
	    			$m_lampiran = new \Model\Storage\Lampiran_model();
	    			$m_lampiran->tabel = 'bank_pelanggan';
	    			$m_lampiran->tabel_id = $bank_plg_id;
	    			$m_lampiran->nama_lampiran = $lampiran['id'];
	    			$m_lampiran->filename = $file_name ;
	    			$m_lampiran->path = $path_name;
	    			$m_lampiran->status = 1;
	    			$m_lampiran->save();
	    			Modules::run( 'base/event/update', $m_lampiran, $deskripsi_log_pelanggan );

	    		}else {
	    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
	    		}
	    	}
    	}

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
                	$d_lampiran = $m_lampiran->where('tabel_id', $pelanggan_id_old)
                							 ->where('tabel', 'pelanggan')
                							 ->where('nama_lampiran', $lampiran['id'])
                							 ->orderBy('id', 'desc')
                							 ->first();

                	$file_name = $d_lampiran['filename'];
                    $path_name = $d_lampiran['path'];
                    $isMoved = 1;
                }

                if ($isMoved) {
                    $m_lampiran = new \Model\Storage\Lampiran_model();
                    $m_lampiran->tabel = 'pelanggan';
                    $m_lampiran->tabel_id = $pelanggan_id;
                    $m_lampiran->nama_lampiran = $lampiran['id'];
                    $m_lampiran->filename = $file_name ;
                    $m_lampiran->path = $path_name;
                    $m_lampiran->status = 1;
                    $m_lampiran->save();
                    Modules::run( 'base/event/update', $m_lampiran, $deskripsi_log_pelanggan );
                }
            }
        }

        $lampiran_ddp = $params['lampiran_ddp'];
        if ( !empty($lampiran_ddp) ) {
			$file_name = $path_name = null;
			$isMoved = 0;
	        if ( !empty($lampiran_ddp['sha1']) ) {
				$file = $mappingFiles[ $lampiran_ddp['sha1'] . '_' . $lampiran_ddp['name'] ] ?: '';
				if (!empty($file)) {
					$moved = uploadFile($file);
					$file_name = $moved['name'];
					$path_name = $moved['path'];
					$isMoved = $moved['status'];
				}
	        } elseif ( empty($lampiran_ddp['sha1']) && !empty($lampiran_ddp['old']) ) {
	        	$m_lampiran = new \Model\Storage\Lampiran_model();
	        	$d_lampiran = $m_lampiran->where('tabel_id', $pelanggan_id_old)
	        							 ->where('tabel', 'pelanggan')
	        							 ->where('nama_lampiran', $lampiran['id'])
	        							 ->orderBy('id', 'desc')
	        							 ->first();

	        	$file_name = $d_lampiran['filename'];
	            $path_name = $d_lampiran['path'];
	            $isMoved = 1;
	        }

			if ($isMoved) {
				$m_lampiran = new \Model\Storage\Lampiran_model();
				$m_lampiran->tabel = 'pelanggan';
				$m_lampiran->tabel_id = $pelanggan_id;
				$m_lampiran->nama_lampiran = $lampiran_ddp['id'];
				$m_lampiran->filename = $file_name ;
				$m_lampiran->path = $path_name;
				$m_lampiran->status = 1;
				$m_lampiran->save();
				Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_pelanggan );
			} else {
				display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
			}
        }

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data pelanggan sukses di edit';
      	$this->result['content'] = array('id'=>$pelanggan_id);

    	display_json($this->result);
	}

	public function ack() {
		$id = $this->input->post('params');

		$status = getStatus(2);

		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$m_pelanggan->where('id', $id)->update(
			array(
				'status' => $status
			)
		);

		$d_pelanggan = $m_pelanggan->where('id', $id)->first();

		$deskripsi_log_pelanggan = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/save', $d_pelanggan, $deskripsi_log_pelanggan );

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data pelanggan sukses di ACK';
      	$this->result['content'] = array('id' => $id);

    	display_json($this->result);
	}

	public function nonAktif() {
		$params = json_decode($this->input->post('data'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		if ( !empty($files) ) {
			$mappingFiles = mappingFiles($files);
		}

		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		if ( $params['tipe'] == 'aktif' ) {
			$m_pelanggan->where('nomor', trim( $params['nomor'] ) )->where('tipe', 'pelanggan')
									   ->update(
									   		array(
									   			'mstatus' => 1
									   		)
									   	);
		} else {
			$m_pelanggan->where('nomor', trim( $params['nomor'] ) )->where('tipe', 'pelanggan')
									   ->update(
									   		array(
									   			'mstatus' => 0
									   		)
									   	);
		}

		$d_pelanggan = $m_pelanggan->where('nomor', trim( $params['nomor'] ) )->first();

		$deskripsi_log_pelanggan = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/update', $d_pelanggan, $deskripsi_log_pelanggan );

		$lampirans = $params['lampiran'];
		if ( !empty($lampirans) ) {
			foreach ($lampirans as $l) {
				$file = $mappingFiles[ $l['sha1'] . '_' . $l['name'] ] ?: '';
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
	    			$m_lampiran->tabel = 'pelanggan';
	    			$m_lampiran->tabel_id = $d_pelanggan['id'];
	    			$m_lampiran->nama_lampiran = $l['id'];
	    			$m_lampiran->filename = $file_name ;
	    			$m_lampiran->path = $path_name;
	    			$m_lampiran->save();
	    			// Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_pelanggan );

	    		}else {
	    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
	    		}
			}
		}

		$this->result['status'] = 1;
      	$this->result['message'] = 'Data pelanggan sukses diperbaharui';
      	display_json($this->result);
	}

	public function loadFormStatus() {
		$nomor = $this->input->get('params');

		$akses = hakAkses($this->url);

		$content['akses'] = $akses;
		$content['data_detail'] = $this->getDataForStatus($nomor);
		$html = $this->load->view($this->pathView . 'form_status_pelanggan', $content, true);

		echo $html;
	}

	public function getDataForStatus($nomor) {
		$m_pelanggan = new \Model\Storage\Pelanggan_model();
		$d_pelanggan = $m_pelanggan->where('nomor', $nomor)->where('tipe', 'pelanggan')->first();

		return $d_pelanggan;
	}

	public function loadFormSldAwal() {
		$nomor = $this->input->get('params');

		$akses = hakAkses($this->url);

		$content['akses'] = $akses;
		$content['data_detail'] = null;
		$html = $this->load->view($this->pathView . 'form_saldo_awal', $content, true);

		echo $html;
	}

	public function getListPelanggan($list_nomor) {
		$datas = array();

		if ( !empty($list_nomor) && $list_nomor != 'undifined' ) {
			foreach ($list_nomor as $nomor) {
				$m_pelanggan = new \Model\Storage\Pelanggan_model();
				$pelanggan = $m_pelanggan->where('tipe', 'pelanggan')
										  ->where('nomor', $nomor)
										  ->orderBy('id', 'desc')
										  ->first();

				if ( $pelanggan ) {
					$pelanggan = $pelanggan->toArray();

					$ket = [];
					$keterangan = '';

					$m_lt = new \Model\Storage\LogTables_model();
                    $d_lt = $m_lt->select('deskripsi', 'waktu')->where('tbl_name', 'pelanggan')->where('tbl_id', $pelanggan['id'])->get()->toArray();

			        foreach ($d_lt as $log){
						$keterangan = $log['deskripsi'] . ' pada ' . dateTimeFormat($log['waktu']);
						array_push($ket, $keterangan);
					}

					$m_lokasi = new \Model\Storage\Lokasi_model();
					$detail_lokasi = $m_lokasi->where('id', '=', $pelanggan['alamat_kecamatan'])->first();

					$detail_kota = $m_lokasi->where('id', '=', $detail_lokasi['induk'])->first();

					$key = $pelanggan['nama'].'|'.$pelanggan['nomor'];
					$datas[$key] = array(
						'id' => $pelanggan['id'],
						'nip' => $pelanggan['nomor'],
						'nama' => $pelanggan['nama'],
						'nik' => $pelanggan['nik'],
						'alamat' => $detail_kota['nama'],
						'mstatus' => $pelanggan['mstatus'],
						'status' => $pelanggan['status'],
						'saldo_awal' => 0,
						'keterangan' => $keterangan
					);

					ksort($datas);
				}
			}
		}

		return $datas;
	}

	public function form_export_excel()
    {
        $html = $this->load->view('parameter/pelanggan/form_export_excel', null); 
        
        echo $html;
    }

    public function verifikasi_export_excel()
    {
        $params = $this->input->post('params');

        $username = $params['username'];
        $password = $params['password'];

        $admins = $this->config->item('auth_export_excel')['auth_pelanggan'];

        if ( stristr($username, $admins[0]['user']) !== FALSE && $password == $admins[0]['pin'] ) {
            $this->result['status'] = 1;
        } else {
            $this->result['message'] = 'Username dan Password yang anda masukkan tidak cocok.';
        }

        display_json($this->result);
    }

	public function export_excel()
    {
        $m_pelanggan = new \Model\Storage\Pelanggan_model();
        $list_nomor = $m_pelanggan->select('nomor')->distinct('nomor')->where('tipe', 'pelanggan')->get()->toArray();

        $data = array();
        foreach ($list_nomor as $k_nomor => $nomor) {
            $pelanggan = $m_pelanggan->where('nomor', $nomor)
            				 ->where('tipe', 'pelanggan')
                             ->with(['kecamatan'])
                             ->orderBy('version', 'desc')
                             ->orderBy('id', 'desc')
                             ->first()->toArray();

            $jalan = empty($pelanggan['alamat_jalan']) ? '' : strtoupper('DSN.'.trim(str_replace('DSN.', '', $pelanggan['alamat_jalan'])));
            $rt = empty($pelanggan['alamat_rt']) ? '' : strtoupper(' RT.'.$pelanggan['alamat_rt']);
            $rw = empty($pelanggan['alamat_rw']) ? '' : strtoupper('/RW.'.$pelanggan['alamat_rw']);
            $kelurahan = empty($pelanggan['alamat_kelurahan']) ? '' : strtoupper(' ,'.$pelanggan['alamat_kelurahan']);
            $kecamatan = empty($pelanggan['alamat_kecamatan']) ? '' : strtoupper(' ,'.$pelanggan['kecamatan']['nama']);
            $kabupaten = empty($pelanggan['kecamatan']['d_kota']) ? '' : strtoupper(' ,'.$pelanggan['kecamatan']['d_kota']['nama']);
            $provinsi = empty($pelanggan['kecamatan']['d_kota']['d_provinsi']) ? '' : strtoupper(' ,'.$pelanggan['kecamatan']['d_kota']['d_provinsi']['nama']);

            $alamat = $jalan.$rt.$rw.$kelurahan.$kecamatan.$kabupaten.$provinsi;

            $key = $pelanggan['nama'].'|'.$pelanggan['nomor'];
            $data[ $key ] = array(
                'id' => $pelanggan['id'],
                'nomor' => $pelanggan['nomor'],
                'ktp' => $pelanggan['nik'],
                'npwp' => $pelanggan['npwp'],
                'nama' => $pelanggan['nama'],
                'alamat' => $alamat,
                'unit' => str_replace(' ,', '', $kabupaten),
                'status' => $pelanggan['mstatus']
            );

            ksort($data);
        }

        $content['data'] = $data;
        $res_view_html = $this->load->view('parameter/pelanggan/export_excel', $content, true);

        $filename = 'export-pelanggan-'.str_replace('-', '', date('Y-m-d')).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;

        // cetak_r($data);
    }

	public function model($status)
    {
        if ( is_numeric($status) ) {
            $status = getStatus($status);
        }

        $m_pelanggan = new \Model\Storage\Pelanggan_model();
        $dashboard = $m_pelanggan->getDashboard($status);

        return $dashboard;
    }

    public function tes()
    {
    	$m_pelanggan = new \Model\Storage\Pelanggan_model();
    	$nomor = $m_pelanggan->getNextNomor('A');

    	cetak_r( $nomor );
    }
}