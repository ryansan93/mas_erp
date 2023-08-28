<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends Public_Controller {

	private $pathView = 'parameter/supplier/';
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
				'assets/parameter/supplier/js/supplier.js'
			));

			$this->add_external_css(array(
				'assets/jquery/easy-autocomplete/easy-autocomplete.min.css',
				'assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css',
				'assets/parameter/supplier/css/supplier.css',
			));

			$data = $this->includes;

			$content['title_panel'] = 'Master Data Supplier';
			$content['akses'] = $akses;
			$content['list_provinsi'] = $this->getLokasi('PV');
			$content['list_lampiran_supplier'] = $this->getNamaLampiran("SUPPLIER")->first();
			$content['list_lampiran_usaha_supplier'] = $this->getNamaLampiran("USAHA_SUPPLIER")->first();
			$content['list_lampiran_rekening_supplier'] = $this->getNamaLampiran("REKENING_SUPPLIER")->first();
			$content['list_lampiran_dds_supplier'] = $this->getNamaLampiran("LAMPIRAN_SUPPLIER")->first();

			// load list pelanggan
			// $detail_content['pelanggans'] = $this->getListSupplier();
			$data['title_menu'] = 'Master Supplier';
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

    public function list_supl()
    {
        $akses = hakAkses($this->url);

        $data = $this->getListSupplier();

        $content['akses'] = $akses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'list', $content);
        
        echo $html;
    }

    public function add_form()
    {
        $akses = hakAkses($this->url);

        $content['akses'] = $akses;
        $content['list_provinsi'] = $this->getLokasi('PV');
		$content['list_lampiran_supplier'] = $this->getNamaLampiran("SUPPLIER", "KTP Supplier")->first();
		$content['list_lampiran_usaha_supplier'] = $this->getNamaLampiran("SUPPLIER", "NPWP Supplier")->first();
		$content['list_lampiran_rekening_supplier'] = $this->getNamaLampiran("BANK_SUPPLIER", "Rekening Supplier")->first();
		$content['list_lampiran_dds_supplier'] = $this->getNamaLampiran("SUPPLIER", "DDS Supplier")->first();
        $content['data'] = null;
        $html = $this->load->view($this->pathView . 'add_form', $content);
        
        return $html;
    }

    public function edit_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        // mengambil data supplier
		$m_supplier = new \Model\Storage\Supplier_model();
		$d_supplier = $m_supplier->where('tipe', 'supplier')->where('id', $id)->with('telepons')->with('banks')->with('logs')->first();
		
		// mengambil lokasi
		$lokasi = new \Model\Storage\Lokasi_model();
		$kec = $lokasi->where('id', $d_supplier['alamat_kecamatan'])->first();
		$kota = $lokasi->where('id', $kec['induk'])->first();
		$prov = $lokasi->where('id', $kota['induk'])->first();
		$kec_usaha = $lokasi->where('id', $d_supplier['usaha_kecamatan'])->first();
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

		// mengambil lampiran supplier
		$m_nama_lampiran = new \Model\Storage\NamaLampiran_model;
		$d_nama_lampiran = $m_nama_lampiran->where('jenis', 'BANK_SUPPLIER')->first();

		$m_lampiran = new \Model\Storage\Lampiran_model;
		$d_lampiran = $m_lampiran->where('tabel', 'bank_supplier')->where('nama_lampiran', $d_nama_lampiran['id'])->get()->toArray();

		// cetak_r($d_pelanggan->toArray(), 1);

		$lampiran_ktp = $this->getLampiranSupplier($d_supplier['id'], 'KTP Supplier');
		$lampiran_npwp = $this->getLampiranSupplier($d_supplier['id'], 'NPWP Supplier');
		$lampiran_dds = $this->getLampiranSupplier($d_supplier['id'], 'DDS Supplier');

		$content['data'] = $d_supplier;
		$content['lokasi'] = $detail_lokasi;
		$content['l_ktp'] = $lampiran_ktp;
		$content['l_npwp'] = $lampiran_npwp;
		$content['l_dds'] = $lampiran_dds;
        $content['akses'] = $akses;

        $content['list_provinsi'] = $this->getLokasi('PV');
		$content['list_lampiran_supplier'] = $this->getNamaLampiran("SUPPLIER", "KTP Supplier")->first();
		$content['list_lampiran_usaha_supplier'] = $this->getNamaLampiran("SUPPLIER", "NPWP Supplier")->first();
		$content['list_lampiran_rekening_supplier'] = $this->getNamaLampiran("BANK_SUPPLIER", "Rekening Supplier")->first();
		$content['list_lampiran_dds_supplier'] = $this->getNamaLampiran("SUPPLIER", "DDS Supplier")->first();

        $html = $this->load->view($this->pathView . 'edit_form', $content);
        
        return $html;
    }

    public function view_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        // mengambil data supplier
		$m_supplier = new \Model\Storage\Supplier_model();
		$d_supplier = $m_supplier->where('tipe', 'supplier')->where('id', $id)->with('telepons')->with('banks')->with('logs')->first();
		
		// mengambil lokasi
		$lokasi = new \Model\Storage\Lokasi_model();
		$kec = $lokasi->where('id', $d_supplier['alamat_kecamatan'])->first();
		$kota = $lokasi->where('id', $kec['induk'])->first();
		$prov = $lokasi->where('id', $kota['induk'])->first();
		$kec_usaha = $lokasi->where('id', $d_supplier['usaha_kecamatan'])->first();
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
		$d_nama_lampiran = $m_nama_lampiran->where('jenis', 'BANK_SUPPLIER')->first();

		$m_lampiran = new \Model\Storage\Lampiran_model;
		$d_lampiran = $m_lampiran->where('tabel', 'bank_supplier')->where('nama_lampiran', $d_nama_lampiran['id'])->get();

		$lampiran_ktp = $this->getLampiranSupplier($d_supplier['id'], 'KTP Supplier');
		$lampiran_npwp = $this->getLampiranSupplier($d_supplier['id'], 'NPWP Supplier');
		$lampiran_dds = $this->getLampiranSupplier($d_supplier['id'], 'DDS Supplier');

		$content['data'] = $d_supplier;
		$content['lokasi'] = $detail_lokasi;
		$content['l_ktp'] = $lampiran_ktp;
		$content['l_npwp'] = $lampiran_npwp;
		$content['l_dds'] = $lampiran_dds;
		$content['tbl_logs'] = $this->getLogs($d_supplier->nomor);
        $content['akses'] = $akses;

        $html = $this->load->view($this->pathView . 'view_form', $content);
        
        return $html;
    }

    public function getLogs($nomor = null) {
	    $m_supl = new \Model\Storage\Supplier_model;
    	$d_supl = $m_supl->where('nomor', $nomor)->where('tipe', 'supplier')->orderBy('version', 'asc')->get()->toArray();

    	$logs = array();
    	foreach ($d_supl as $key => $v_supl) {
	    	$m_log = new \Model\Storage\LogTables_model;
	    	$d_log = $m_log->where('tbl_name', 'pelanggan')->where('tbl_id', $v_supl['id'])->get()->toArray();

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

    public function getLampiranSupplier($id, $nama) {
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

		$result = $this->getLokasi($jenis, $induk);
		$this->result['content'] = $result;
		$this->result['status'] = 1;
		display_json($this->result);
	}

	public function save() {
		$params = json_decode($this->input->post('data_supplier'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		$mappingFiles = mappingFiles($files);

		$status = "submit";

		// supplier
		$m_supplier = new \Model\Storage\Supplier_model();
		$supplier_id = $m_supplier->getNextIdentity();

		$m_supplier->id = $supplier_id;		
		$m_supplier->jenis = $params['jenis_supplier'];
		$kode_jenis = ($params['jenis_supplier'] == "internal") ? "A" : "B";
		$m_supplier->nomor = $m_supplier->getNextNomor($kode_jenis);
		$m_supplier->nama = $params['nama'];
		$m_supplier->nik = $params['ktp'];
		$m_supplier->cp = $params['cp'];
		$m_supplier->npwp = $params['npwp'];
		$m_supplier->alamat_kecamatan = $params['alamat_supplier']['kecamatan'];
		$m_supplier->alamat_kelurahan = $params['alamat_supplier']['kelurahan'];
		$m_supplier->alamat_rt = $params['alamat_supplier']['rt'] ?: null;
		$m_supplier->alamat_rw = $params['alamat_supplier']['rw'] ?: null;
		$m_supplier->alamat_jalan = $params['alamat_supplier']['alamat'] ?: null;
		$m_supplier->usaha_kecamatan = $params['alamat_usaha']['kecamatan'];
		$m_supplier->usaha_kelurahan = $params['alamat_usaha']['kelurahan'];
		$m_supplier->usaha_rt = $params['alamat_usaha']['rt'] ?: null;
		$m_supplier->usaha_rw = $params['alamat_usaha']['rw'] ?: null;
		$m_supplier->usaha_jalan = $params['alamat_usaha']['alamat'] ?: null;
		$m_supplier->status = $status;
		$m_supplier->mstatus = 1;
		$m_supplier->tipe = 'supplier';
		$m_supplier->platform = $params['platform'];
		$m_supplier->version = 1;
		$m_supplier->save();

		$deskripsi_log_supplier = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/save', $m_supplier, $deskripsi_log_supplier );

		// telepon supplier
		$telepons = $params['telepons'];
		foreach ($telepons as $k => $telepon) {
			$m_telp = new \Model\Storage\TelpPelanggan_model();
			$m_telp->id = $m_telp->getNextIdentity();
			$m_telp->pelanggan = $supplier_id;
			$m_telp->nomor = $telepon;
			$m_telp->save();
			Modules::run( 'base/event/save', $m_telp, $deskripsi_log_supplier );
    	}

    	// rekening dan bank supplier
    	$banks = $params['banks'];
    	foreach ($banks as $k => $bank) {
    		$m_bank = new \Model\Storage\BankPelanggan_model();
    		$bank_plg_id = $m_bank->getNextIdentity();

    		$m_bank->id = $bank_plg_id;
    		$m_bank->pelanggan = $supplier_id;
    		$m_bank->bank = $bank['nama_bank'];
    		$m_bank->rekening_nomor = $bank['nomer_rekening'];
    		$m_bank->rekening_pemilik = $bank['nama_pemilik'];
    		$m_bank->rekening_cabang_bank = $bank['cabang_bank'];
    		$m_bank->save();
    		Modules::run( 'base/event/save', $m_telp, $deskripsi_log_supplier );

    		$lampiran = $bank['lampiran'];
    		$file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
    		$file_name = $path_name = null;
    		$isMoved = 0;
    		if (!empty($file)) {
    			$moved = uploadFile($file);
    			$isMoved = $moved['status'];
    			
	    		if ($isMoved) {
	    			$file_name = $moved['name'];
	    			$path_name = $moved['path'];

	    			$m_lampiran = new \Model\Storage\Lampiran_model();
	    			$m_lampiran->tabel = 'bank_pelanggan';
	    			$m_lampiran->tabel_id = $bank_plg_id;
	    			$m_lampiran->nama_lampiran = $lampiran['id'];
	    			$m_lampiran->filename = $file_name ;
	    			$m_lampiran->path = $path_name;
	    			$m_lampiran->status = 1;
	    			$m_lampiran->save();
	    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_supplier );

	    		}else {
	    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
	    		}
    		}
    	}

    	$lampirans = $params['lampirans'];
    	foreach ($lampirans as $lampiran) {
    		$file = $mappingFiles[ $lampiran['sha1'] . '_' . $lampiran['name'] ] ?: '';
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
	    			$m_lampiran->tabel_id = $supplier_id;
	    			$m_lampiran->nama_lampiran = $lampiran['id'];
	    			$m_lampiran->filename = $file_name ;
	    			$m_lampiran->path = $path_name;
	    			$m_lampiran->status = 1;
	    			$m_lampiran->save();
	    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_supplier );

	    		}else {
	    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
	    		}
    		}
    	}

    	$lampiran_dds = $params['lampiran_dds'];
		$file = $mappingFiles[ $lampiran_dds['sha1'] . '_' . $lampiran_dds['name'] ] ?: '';
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
    			$m_lampiran->tabel_id = $supplier_id;
    			$m_lampiran->nama_lampiran = $lampiran_dds['id'];
    			$m_lampiran->filename = $file_name ;
    			$m_lampiran->path = $path_name;
    			$m_lampiran->status = 1;
    			$m_lampiran->save();
    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_supplier );
    		}else {
    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
    		}
		}

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data supplier sukses disimpan';
      	$this->result['content'] = array('id'=>$supplier_id);

    	display_json($this->result);
	}

	public function edit() {
		$params = json_decode($this->input->post('data_supplier'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		if (!empty($files)) {
            $mappingFiles = mappingFiles($files);
        }

		$supplier_id_old = $params['id'];
		$status = $params['status'];
		$mstatus = $params['mstatus'];
		$version = $params['version'] + 1;

		// supplier
		$m_supplier = new \Model\Storage\Supplier_model();
		$supplier_id = $m_supplier->getNextIdentity();

		$m_supplier->id = $supplier_id;
		$m_supplier->jenis = $params['jenis_supplier'];
		$m_supplier->nomor = $params['nomor'];
		$m_supplier->nama = $params['nama'];
		$m_supplier->nik = $params['ktp'];
		$m_supplier->cp = $params['cp'];
		$m_supplier->npwp = $params['npwp'];
		$m_supplier->alamat_kecamatan = $params['alamat_supplier']['kecamatan'];
		$m_supplier->alamat_kelurahan = $params['alamat_supplier']['kelurahan'];
		$m_supplier->alamat_rt = $params['alamat_supplier']['rt'] ?: null;
		$m_supplier->alamat_rw = $params['alamat_supplier']['rw'] ?: null;
		$m_supplier->alamat_jalan = $params['alamat_supplier']['alamat'] ?: null;
		$m_supplier->usaha_kecamatan = $params['alamat_usaha']['kecamatan'];
		$m_supplier->usaha_kelurahan = $params['alamat_usaha']['kelurahan'];
		$m_supplier->usaha_rt = $params['alamat_usaha']['rt'] ?: null;
		$m_supplier->usaha_rw = $params['alamat_usaha']['rw'] ?: null;
		$m_supplier->usaha_jalan = $params['alamat_usaha']['alamat'] ?: null;
		$m_supplier->status = $status;
		$m_supplier->mstatus = $mstatus;
		$m_supplier->tipe = 'supplier';
		$m_supplier->platform = $params['platform'];
		$m_supplier->version = $version;
		$m_supplier->save();

		$deskripsi_log_supplier = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/update', $m_supplier, $deskripsi_log_supplier );

		// telepon supplier
		$telepons = $params['telepons'];
		foreach ($telepons as $k => $telepon) {
			$m_telp = new \Model\Storage\TelpPelanggan_model();
			$m_telp->id = $m_telp->getNextIdentity();

			$m_telp->pelanggan = $supplier_id;
			$m_telp->nomor = $telepon;
			$m_telp->save();
			Modules::run( 'base/event/update', $m_telp, $deskripsi_log_supplier );
    	}

    	// rekening dan bank supplier
    	$banks = $params['banks'];
    	foreach ($banks as $k => $bank) {
    		$m_bank = new \Model\Storage\BankPelanggan_model();
    		$bank_plg_id = $m_bank->getNextIdentity();

    		$m_bank->id = $bank_plg_id;
    		$m_bank->pelanggan = $supplier_id;
    		$m_bank->bank = $bank['nama_bank'];
    		$m_bank->rekening_nomor = $bank['nomer_rekening'];
    		$m_bank->rekening_pemilik = $bank['nama_pemilik'];
    		$m_bank->rekening_cabang_bank = $bank['cabang_bank'];
    		$m_bank->save();
    		Modules::run( 'base/event/update', $m_telp, $deskripsi_log_supplier );

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
	    			Modules::run( 'base/event/update', $m_lampiran, $deskripsi_log_supplier );

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
                	$d_lampiran = $m_lampiran->where('tabel_id', $supplier_id_old)
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
                    $m_lampiran->tabel_id = $supplier_id;
                    $m_lampiran->nama_lampiran = $lampiran['id'];
                    $m_lampiran->filename = $file_name ;
                    $m_lampiran->path = $path_name;
                    $m_lampiran->status = 1;
                    $m_lampiran->save();
                    Modules::run( 'base/event/update', $m_lampiran, $deskripsi_log_supplier );
                }
            }
        }

        $lampiran_dds = $params['lampiran_dds'];
        if ( !empty($lampiran_dds) ) {
			$file_name = $path_name = null;
			$isMoved = 0;
	        if ( !empty($lampiran_dds['sha1']) ) {
				$file = $mappingFiles[ $lampiran_dds['sha1'] . '_' . $lampiran_dds['name'] ] ?: '';
				if (!empty($file)) {
					$moved = uploadFile($file);
					$file_name = $moved['name'];
					$path_name = $moved['path'];
					$isMoved = $moved['status'];
				}
	        } elseif ( empty($lampiran_dds['sha1']) && !empty($lampiran_dds['old']) ) {
	        	$m_lampiran = new \Model\Storage\Lampiran_model();
	        	$d_lampiran = $m_lampiran->where('tabel_id', $supplier_id_old)
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
				$m_lampiran->tabel_id = $supplier_id;
				$m_lampiran->nama_lampiran = $lampiran_dds['id'];
				$m_lampiran->filename = $file_name ;
				$m_lampiran->path = $path_name;
				$m_lampiran->status = 1;
				$m_lampiran->save();
				Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_supplier );
			} else {
				display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
			}
        }

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data supplier sukses di edit';
      	$this->result['content'] = array('id'=>$supplier_id);

    	display_json($this->result);
	}

	public function ack() {
		$id = $this->input->post('params');

		$status = getStatus(2);

		$m_supplier = new \Model\Storage\Supplier_model();
		$m_supplier->where('id', $id)->update(
			array(
				'status' => $status
			)
		);

		$d_supplier = $m_supplier->where('id', $id)->first();

		$deskripsi_log_supplier = 'di-' . $status . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/save', $d_supplier, $deskripsi_log_supplier );

    	$this->result['status'] = 1;
      	$this->result['message'] = 'Data supplier sukses di ACK';
      	$this->result['content'] = array('id' => $id);

    	display_json($this->result);
	}

	public function approve() {

	}

	public function reject() {

	}

	public function nonAktif() {
		$params = json_decode($this->input->post('data'),TRUE);
		$files = isset($_FILES['files']) ? $_FILES['files'] : [];

		if ( !empty($files) ) {
			$mappingFiles = mappingFiles($files);
		}

		$m_supplier = new \Model\Storage\Supplier_model();
		if ( $params['tipe'] == 'aktif' ) {
			$m_supplier->where('nomor', trim( $params['nomor'] ) )->where('tipe', 'supplier')
									   ->update(
									   		array(
									   			'mstatus' => 1
									   		)
									   	);
		} else {
			$m_supplier->where('nomor', trim( $params['nomor'] ) )->where('tipe', 'supplier')
									   ->update(
									   		array(
									   			'mstatus' => 0
									   		)
									   	);
		}

		$d_supplier = $m_supplier->where('nomor', trim( $params['nomor'] ) )->first();

		$deskripsi_log_supplier = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		Modules::run( 'base/event/update', $d_supplier, $deskripsi_log_supplier );

		$lampirans = $params['lampiran'];
		if ( !empty($lampirans) ) {
			foreach ($lampirans as $l) {
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
	    			$m_lampiran->tabel_id = $d_supplier['nomor'];
	    			$m_lampiran->nama_lampiran = $l['id'];
	    			$m_lampiran->filename = $file_name ;
	    			$m_lampiran->path = $path_name;
	    			$m_lampiran->save();
	    			Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log_supplier );

	    		}else {
	    			display_json(['status'=>0, 'message'=>'error, segera hubungi tim IT']);
	    		}
			}
		}

		$this->result['status'] = 1;
      	$this->result['message'] = 'Data supplier sukses diperbaharui';
      	display_json($this->result);
	}

	// public function loadFormInput($tipe = null) {
	// 	// loading list yang diperlukan
	// 	$content['akses'] = $this->getAkses();
	// 	$content['list_provinsi'] = $this->getLokasi('PV');
	// 	$content['list_lampiran_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "KTP Pelanggan")->first();
	// 	$content['list_lampiran_usaha_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "NPWP Pelanggan")->first();
	// 	$content['list_lampiran_rekening_pelanggan'] = $this->getNamaLampiran("BANK_PELANGGAN", "Rekening Pelanggan")->first();
	// 	$content['list_lampiran_dds_pelanggan'] = $this->getNamaLampiran("PELANGGAN", "DDP Pelanggan")->first();

	// 	if ($tipe == null) {
	// 		echo $this->load->view($this->pathView . 'input_pelanggan', $content, true);
	// 	} else {
	// 		return $this->load->view($this->pathView . 'input_pelanggan', $content, true);
	// 	}
	// }

	public function loadFormStatus() {
		$nomor = $this->input->get('params');

		$akses = hakAkses($this->url);

		$content['akses'] = $akses;
		$content['data_detail'] = $this->getDataForStatus($nomor);
		$html = $this->load->view($this->pathView . 'form_status_supplier', $content, true);

		echo $html;
	}

	public function getDataForStatus($nomor) {
		$m_supplier = new \Model\Storage\Supplier_model();
		$d_supplier = $m_supplier->where('nomor', $nomor)->where('tipe', 'supplier')->first();

		return $d_supplier;
	}

	public function loadFormSldAwal() {
		$nomor = $this->input->get('params');

		$akses = hakAkses($this->url);

		$content['akses'] = $akses;
		$content['data_detail'] = null;
		$html = $this->load->view($this->pathView . 'form_saldo_awal', $content, true);

		echo $html;
	}

	public function getListSupplier() {
		$m_supplier = new \Model\Storage\Supplier_model();
		$d_nomor = $m_supplier->select('nomor')->distinct('nomor')->where('tipe', 'supplier')->where('jenis', '<>', 'ekspedisi')->get()->toArray();

		$datas = array();
		if ( !empty($d_nomor) ) {
			foreach ($d_nomor as $nomor) {
				$supplier = $m_supplier->where('tipe', 'supplier')
										  ->where('nomor', $nomor['nomor'])
										  ->where('jenis', '<>', 'ekspedisi')
										  ->orderBy('version', 'desc')
										  ->orderBy('id', 'desc')
										  ->with(['logs'])
										  ->first()->toArray();

				$ket = [];
				$keterangan = '';
		        foreach ($supplier['logs'] as $log){
					$keterangan = $log['deskripsi'] . ' pada ' . dateTimeFormat($log['waktu']);
					array_push($ket, $keterangan);
				}

				$m_lokasi = new \Model\Storage\Lokasi_model();
				$detail_lokasi = $m_lokasi->where('id', '=', $supplier['alamat_kecamatan'])->first();

				$detail_kota = $m_lokasi->where('id', '=', $detail_lokasi['induk'])->first();

				$data_array = array(
					'id' => $supplier['id'],
					'nip' => $supplier['nomor'],
					'nama' => $supplier['nama'],
					'nik' => $supplier['nik'],
					'alamat' => $detail_kota['nama'],
					'mstatus' => $supplier['mstatus'],
					'status' => $supplier['status'],
					'saldo_awal' => 0,
					'keterangan' => $keterangan,
					'jenis' => ($supplier['jenis'] != 'ekspedisi') ? 'supplier' : 'ekspedisi'
				);

				array_push($datas, $data_array);
			}
		}

		return $datas;
	}

	// public function refreshListPelanggan() {
	// 	$content['akses'] = $this->getAkses();
	// 	$content['pelanggans'] = $this->getListSupplier();
	// 	$html = $this->load->view($this->pathView . 'list_pelanggan_baru', $content, true);

	// 	echo $html;
	// }

	// public function viewDataPelanggan() {
	// 	$id = $this->input->get('params');

	// 	// mengambil data pelanggan
	// 	$m_pelanggan = new \Model\Storage\Pelanggan();
	// 	$d_pelanggan = $m_pelanggan->where('nomor', $id)->with('telepons')->with('banks')->with('logs')->first();
		
	// 	// mengambil lokasi
	// 	$lokasi = new \Model\Storage\Lokasi();
	// 	$kec = $lokasi->where('id', $d_pelanggan['alamat_kecamatan'])->first();
	// 	$kota = $lokasi->where('id', $kec['induk'])->first();
	// 	$prov = $lokasi->where('id', $kota['induk'])->first();
	// 	$kec_usaha = $lokasi->where('id', $d_pelanggan['usaha_kecamatan'])->first();
	// 	$kota_usaha = $lokasi->where('id', $kec_usaha['induk'])->first();
	// 	$prov_usaha = $lokasi->where('id', $kota_usaha['induk'])->first();

	// 	// simpan data lokasi
	// 	$detail_lokasi = array(
	// 		'prov_id' => $prov['id'],
	// 		'prov' => $prov['nama'],
	// 		'kota_id' => $kota['id'],
	// 		'kota' => $kota['nama'],
	// 		'kec_id' => $kec['id'],
	// 		'kec' => $kec['nama'],
	// 		'prov_usaha_id' => $prov_usaha['id'],
	// 		'prov_usaha' => $prov_usaha['nama'],
	// 		'kota_usaha_id' => $kota_usaha['id'],
	// 		'kota_usaha' => $kota_usaha['nama'],
	// 		'kec_usaha_id' => $kec_usaha['id'],
	// 		'kec_usaha' => $kec_usaha['nama']
	// 	);

	// 	// mengambil lampiran pelanggan
	// 	$lampiran_ktp = $this->getLampiranPelanggan($d_pelanggan['id'], 'pelanggan')->first();
	// 	$lampiran_npwp = $this->getLampiranPelanggan($d_pelanggan['id'], 'usaha_pelanggan')->first();
	// 	$lampiran_rekening = $this->getLampiranPelanggan($d_pelanggan['id'], 'rekening_pelanggan');
	// 	$lampiran_dds = $this->getLampiranPelanggan($d_pelanggan['id'], 'lampiran_pelanggan')->first();

	// 	$content['akses'] = $this->getAkses();
	// 	$content['data'] = $d_pelanggan;
	// 	$content['lokasi'] = $detail_lokasi;
	// 	$content['l_ktp'] = $lampiran_ktp;
	// 	$content['l_npwp'] = $lampiran_npwp;
	// 	$content['l_rekening'] = $lampiran_rekening;
	// 	$content['l_dds'] = $lampiran_dds;

	// 	if ($content['akses']['submit'] || ($content['akses']['approve'] && $content['akses']['reject'])) {
	// 		echo $this->load->view($this->pathView . 'view_data_pelanggan', $content, true);
	// 	} else if ($content['akses']['ack']) {
	// 		echo $this->load->view($this->pathView . 'ack_pelanggan', $content, true);
	// 	}
	// }

	public function model($status)
    {
        if ( is_numeric($status) ) {
            $status = getStatus($status);
        }

        $m_supplier = new \Model\Storage\Supplier_model();
        $dashboard = $m_supplier->getDashboard($status);

        return $dashboard;
    }
}