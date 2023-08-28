<?php defined('BASEPATH') or exit('No direct script access allowed');

class Rdim extends Public_Controller
{
    private $pathView = 'transaksi/rdim/';
    private $status_rdimsubmit = [
        2 => 'Dibatalkan',
        1 => 'Aktif',
        0 => 'Tidak Aktif'
    ];
    private $url;

    public function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
    }

    public function index()
    {
        $akses = hakAkses($this->url);
        if ( $akses['a_view'] == 1 ) {
            $this->add_external_js(array(
                'assets/jquery/list.min.js',
                'assets/toastr/js/toastr.js',
                'assets/jquery/tupage-table/jquery.tupage.table.js',
                'assets/transaksi/rdim/js/rdim.js'

            ));

            $this->add_external_css(array(
                'assets/toastr/css/toastr.css',
                'assets/jquery/tupage-table/jquery.tupage.table.css',
                'assets/transaksi/rdim/css/rdim.css'
            ));

            $data = $this->includes;

            $content['title_panel'] = 'Rencana DOC in Mingguan';
            $content['current_uri'] = $this->current_uri;
            $content['akses'] = $akses;
            // cetak_r($this->getDataPerwakilan());
            $content['rdim_data_perwakilan_mitra'] = $this->getDataPerwakilan();
            $content['tim_panens'] = $this->getDataTimpanen();
            $content['vaksin'] = $this->getVaksin();

            // cetak_r($this->getDataPerwakilan());

            $status = getStatus('approve');
            $content['periodes'] = $this->getPeriodeRdim($status);
            
            $html = $this->add_form();
            $content['add_form'] = $html;

            // cetak_r( $this->add_form() );

            $data['title_menu'] = 'Rencana Chick In Mingguan';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, true);
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

    public function add_form()
    {
        $akses = hakAkses($this->url);

        $content['rdim_data_perwakilan_mitra'] = $this->getDataPerwakilan();

        $status = getStatus('approve');
        $content['periodes'] = $this->getPeriodeRdim($status);
        $content['tim_panens'] = $this->getDataTimpanen();
        $content['vaksin'] = $this->getVaksin();

        $content['akses'] = $akses;
        $content['data'] = null;
        $html = $this->load->view('transaksi/rdim/add_form', $content, true);
        
        return $html;
    }

    public function view_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->with(['logs','dRdimSubmit'])->find($id);
        $dataMapping = $this->rdimMapping($d_rdim);
        $content['data'] = $dataMapping;

        $content['akses'] = $akses;
        $html = $this->load->view('transaksi/rdim/view_form', $content, true);
        
        return $html;
    }

    public function edit_form($id, $resubmit)
    {
        $akses = hakAkses($this->url);

        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->with(['logs','dRdimSubmit'])->find($id);
        $dataMapping = $this->rdimMapping($d_rdim);

        $content['data'] = $dataMapping;
        $content['rdim_data_perwakilan_mitra'] = $this->getDataPerwakilan();
        $content['tim_panens'] = $this->getDataTimpanen();
        $content['vaksin'] = $this->getVaksin();
        $content['akses'] = $akses;

        $html = $this->load->view('transaksi/rdim/edit_form', $content, true);
        
        return $html;
    }

    public function getTabContents($value='')
    {
        $tab_contents = array(
            'riwayat_tab_content',
            'rencana_doc_in_mingguan',
            'pembatalan_doc_in',
        );

        $content['akses'] = hakAkses($this->url);
        foreach ($tab_contents as $tab_content) {

            if ($tab_content == 'rencana_doc_in_mingguan') {
                $content['rdim_data_perwakilan_mitra'] = $this->getDataPerwakilan();
                $content['tim_panens'] = $this->getDataTimpanen();
            }else if ($tab_content == 'pembatalan_doc_in') {
                $status = getStatus('approve');
                $content['periodes'] = $this->getPeriodeRdim($status);
            }
            $view_contents[ $tab_content ] = $this->load->view($this->pathView . $tab_content, $content, true);
        }

        return $view_contents;
    }

    public function getPeriodeRdim($status)
    {
        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->where('g_status', $status)->orderBy('id','DESC')->take(2)->get();
        return $d_rdim;
    } // end - getPeriodeRdim

    public function getDataTimpanen()
    {
        $m_timpanen = new \Model\Storage\MsTimPanen_model();
        $d_timpanen = $m_timpanen->active()->orderBy('nama_timpanen', 'ASC')->get();
        return $d_timpanen;
    } // end - getDataTimpanen

    public function getDataPerwakilan()
    {

        $m_perwakilan = new \Model\Storage\Wilayah_model();
        $d_perwakilan = $m_perwakilan->perwakilan()->with(['mitra_mapping', 'dPerwakilanMapping','unit'])->orderBy('nama', 'ASC')->get();

        $data = array();
        foreach ($d_perwakilan as $perwakilan) {
            if ( !empty($perwakilan->mitra_mapping) ) {
                $formatPb = array();
                foreach ($perwakilan->dPerwakilanMapping as $pm) {
                    if ( !empty($pm->dHitungBudidayaItem) ) {
                        if ($pm->dHitungBudidayaItem->dSapronakKesepakatan) {
                            $perusahaan = json_decode(json_encode($pm->dHitungBudidayaItem->dSapronakKesepakatan->data_perusahaan), True);

                            $format = $pm->dHitungBudidayaItem->dSapronakKesepakatan->pola_kerjasama->item_code . ' (' .  trim($pm->dHitungBudidayaItem->dSapronakKesepakatan->item_pola) . ') ' . $perusahaan['perusahaan'];
                            $pola = $pm->dHitungBudidayaItem->dSapronakKesepakatan->pola_kerjasama->item_code . ' (' .  trim($pm->dHitungBudidayaItem->dSapronakKesepakatan->item_pola) . ')';

                            $formatPb[$pm->dHitungBudidayaItem->dSapronakKesepakatan->nomor] = array(
                                'id' => $pm->id,
                                'format' => $format,
                                'pola' => $pola,
                                'perusahaan' => $perusahaan['kode']
                            );
                        }
                    }
                }

                $units = array_map(function($item){
                    return $item['nama'];
                }, $perwakilan->unit->toArray());

                $id_unit = array_map(function($item){
                    return $item['id'];
                }, $perwakilan->unit->toArray());

                $ppl = array();
                if ( !empty($id_unit) ) {
                    $m_uk = new \Model\Storage\UnitKaryawan_model();
                    $d_uk = $m_uk->select('id_karyawan')->distinct('id_karyawan')->whereIn('unit', $id_unit)->get()->toArray();

                    if ( !empty($d_uk) ) {
                        foreach ($d_uk as $k_uk => $v_uk) {
                            $m_k = new \Model\Storage\Karyawan_model();
                            $d_k = $m_k->where('id', $v_uk['id_karyawan'])->where('jabatan', 'ppl')->first();

                            if ( !empty($d_k) ) {
                                array_push($ppl, $d_k->toArray());
                            }
                        }
                    }
                }

                $kanit = array();
                if ( !empty($id_unit) ) {
                    $m_uk = new \Model\Storage\UnitKaryawan_model();
                    $d_uk = $m_uk->select('id_karyawan')->distinct('id_karyawan')->whereIn('unit', $id_unit)->get()->toArray();

                    if ( !empty($d_uk) ) {
                        foreach ($d_uk as $k_uk => $v_uk) {
                            $m_k = new \Model\Storage\Karyawan_model();
                            $d_k = $m_k->where('id', $v_uk['id_karyawan'])->where('jabatan', 'kepala unit')->first();

                            if ( !empty($d_k) ) {
                                array_push($kanit, $d_k->toArray());
                            }
                        }
                    }
                }

                $marketing = array();
                if ( !empty($id_unit) ) {
                    $m_uk = new \Model\Storage\UnitKaryawan_model();
                    $d_uk = $m_uk->select('id_karyawan')->distinct('id_karyawan')->whereIn('unit', $id_unit)->get()->toArray();

                    if ( !empty($d_uk) ) {
                        foreach ($d_uk as $k_uk => $v_uk) {
                            $m_k = new \Model\Storage\Karyawan_model();
                            $d_k = $m_k->where('id', $v_uk['id_karyawan'])->where('jabatan', 'marketing')->first();

                            if ( !empty($d_k) ) {
                                array_push($marketing, $d_k->toArray());
                            }
                        }
                    }
                }

                $koordinator = array();
                if ( !empty($id_unit) ) {
                    $m_uk = new \Model\Storage\UnitKaryawan_model();
                    $d_uk = $m_uk->select('id_karyawan')->distinct('id_karyawan')->whereIn('unit', $id_unit)->get()->toArray();

                    if ( !empty($d_uk) ) {
                        foreach ($d_uk as $k_uk => $v_uk) {
                            $m_k = new \Model\Storage\Karyawan_model();
                            $d_k = $m_k->where('id', $v_uk['id_karyawan'])->where('jabatan', 'koordinator')->first();

                            if ( !empty($d_k) ) {
                                array_push($koordinator, $d_k->toArray());
                            }
                        }
                    }
                }

                $data_perwakilan = array(
                    'id' => $perwakilan->id,
                    'nama' => $perwakilan->nama,
                    'formatPb' => $formatPb,
                    'kanit' => $kanit,
                    'ppl' => $ppl,
                    'marketing' => $marketing,
                    'koordinator' => $koordinator,
                    'units' => $units
                );

                $data_mitra = array();
                foreach ($perwakilan->mitra_mapping as $mapping) {
                    if ( !empty($mapping) ) {
                        $mitra = $mapping->dMitra;

                        $kandangs = array();
                        foreach ($mapping->kandangs as $kandang) {

                            $total_luas = 0;
                            foreach ($kandang->bangunans as $bangunan) {
                                $panjang = $bangunan->meter_panjang ?: 0;
                                $lebar = $bangunan->meter_lebar ?: 0;
                                $jml = $bangunan->jumlah_unit ?: 0;
                                $luas = ( $panjang * $lebar ) * $jml;

                                $total_luas += $luas;
                            }

                            $kapasitas = $kandang->ekor_kapasitas ?: 0;
                            $kandangs[] = array(
                                'id' => $kandang->id,
                                'nim' => $mapping->nim,
                                'group' => 'G-' . $kandang->grup,
                                'nomor' => strlen($kandang->kandang) < 2 ? '0' . $kandang->kandang : $kandang->kandang,
                                'tipe' => $kandang->tipe,
                                'kapasitas' => $kapasitas,
                                'luas' => $total_luas,
                                'densitas' => ($kapasitas / $total_luas ),
                                'alamat'=> $kandang->alamat_jalan,
                                'kecamatan' => $kandang->dKecamatan->nama,
                                'kabupaten' => $kandang->dKecamatan->dKota->nama,
                            );
                        }

                        if ( !empty($mapping->dMitra) ) {
                            // $mitra_key = $mitra->nama . $mapping->id;
                            $mitra_key = $mitra->nama . $mapping->nomor;
                            $data_mitra[$mitra_key] = array(
                                'mapping_id' => $mapping->id,
                                'mitra_id' => $mitra->id,
                                'nama' => $mitra->nama,
                                'nim' => $mapping->nim,
                                'alamat' => $mitra->alamat_jalan,
                                'kecamatan' => $mitra->dKecamatan->nama,
                                'kabupaten' => $mitra->dKecamatan->dKota->nama,
                                'jenis' => getJenisMitra($mitra->jenis),
                                'kandangs' => $kandangs,
                            );
                        }
                    }
                }

                ksort($data_mitra);

                $data[ $perwakilan->id ]['parent'] = $data_perwakilan;
                $data[ $perwakilan->id ]['child'] = $data_mitra;
            }
        }

        // cetak_r($data, 1);

        return $data;
    } // end - getDataPerwakilan

    public function getDataKandangMitraRDIM()
    {
        $nim = $this->input->get('nim');
        $kandang = $this->input->get('kandang');

        $noreg = $this->generateNoreg($nim, $kandang);

        $m = new \Model\Storage\Mitra_model();

        $d = array(
            'ip1' => 0,
            'ip2' => 0,
            'ip3' => 0,
            'next_noreg' => $this->generateNoreg($nim, $kandang),
        );

        if ($d) {
            $this->result['status'] = 1;
            $this->result['message'] = 'sukses';
            $this->result['content'] = $d;
        }else{
            $this->result['message'] = 'Data mitra tidak ditemukan, silakan konfirmasi IT!';
        }

        display_json( $this->result );
    } // end - getDataKandangMitraRDIM

    public function generateNoreg($nim = null, $kandang = null)
    {
        $noreg = null;

        $m_mmp = new \Model\Storage\MitraMapping_model();
        $d_mmp = $m_mmp->where('nim', trim($nim))->first();

        $m_kandang = new \Model\Storage\Kandang_model();
        $d_kandang = $m_kandang->where('mitra_mapping', trim($d_mmp['id']))
                               ->where('kandang', number_format($kandang))
                               ->first();

        // $_nim = substr($nim, 0, 2) . substr($nim, 4);
        $_nim = trim($nim);

        $m_rdims = new \Model\Storage\RdimSubmit_model();
        $d_rdims = $m_rdims->where('nim', trim($nim))
                           ->where('kandang', $d_kandang['id'])
                           ->orderBy('tgl_docin', 'DESC')
                           ->first();

        if ( empty($d_rdims) ) {
            $noreg = trim($_nim) . '01' . trim($d_kandang['kandang']);
        } else {
            $_noreg = $d_rdims['noreg'];
            $jml_nim = strlen(trim($nim));

            $_siklus = trim(substr($_noreg, $jml_nim, 2));
            $siklus = $_siklus + 1;

            $str_siklus = null;
            if ( strlen($siklus) == 1) {
                $str_siklus = '0'.$siklus;
            } else {
                $str_siklus = $siklus;
            }

            $str_kandang = null;
            if ( strlen(number_format($d_kandang['kandang'])) == 1) {
                $str_kandang = '0'.number_format($d_kandang['kandang']);
            } else {
                $str_kandang = $d_kandang['kandang'];
            }

            $noreg = $_nim . $str_siklus . $str_kandang;
        }

        return $noreg;
    } // end - generateNoreg

    public function getVaksin()
    {
        $m_vaksin = new \Model\Storage\Vaksin_model();
        $d_vaksin = $m_vaksin->where('status', getStatus('submit'))->get();

        return $d_vaksin;
    }

    public function addNewRdim()
    {
        $content['rdim_data_perwakilan_mitra'] = $this->getDataPerwakilan();
        $content['tim_panens'] = $this->getDataTimpanen();
        $content['addNewRdim'] = true;
        $content['akses'] = hakAkses($this->url);
        $html = $this->load->view($this->pathView . 'rencana_doc_in_mingguan', $content, true);
        echo $html;
    } // end - addNewRdim

    public function saveRdim()
    {
        $params = $this->input->post('params');

        // NOTE: preparing data
        $periode = $params['periode'];
        $details = $params['details'];

        // NOTE: 1. save header -> rdim
        $m_rdim = new \Model\Storage\Rdim_model();
        $next_doc_number = $m_rdim->getNextDocNum('ADM/RDIM');

        $m_rdim->nomor = $next_doc_number;
        $m_rdim->mulai = $periode['start'];
        $m_rdim->selesai = $periode['end'];
        $m_rdim->g_status = getStatus('submit');
        $m_rdim->save();

        $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
        Modules::run( 'base/event/save', $m_rdim, $deskripsi_log);
        $id_rdim = $m_rdim->id;

        // NOTE: 2. save detail -> rdim_submit
        foreach ($details as $item) {
            $m_rs = new \Model\Storage\RdimSubmit_model();
            $m_rs->id_rdim = $id_rdim;
            $m_rs->tgl_docin = $item['tanggal'] ;
            $m_rs->nim = $item['nim'] ;
            $m_rs->kandang = $item['kandang'] ;
            $m_rs->populasi = $item['populasi'] ;
            $m_rs->noreg = $item['noreg'] ;
            // $m_rs->prokes = $item['program_kesehatan'] ;
            $m_rs->pengawas = $item['pengawas'] ;
            $m_rs->sampling = $item['tim_sampling'] ;
            $m_rs->tim_panen = $item['tim_panen'] ;
            $m_rs->koar = $item['koordinator_area'] ;
            $m_rs->format_pb = $item['formatPb'] ;
            $m_rs->pola_mitra = $item['pola'] ;
            $m_rs->grup = $item['group'];
            $m_rs->status = 1;
            // $m_rs->ip1 = $item['ip_terakhir_1'];
            // $m_rs->ip2 = $item['ip_terakhir_2'];
            // $m_rs->ip3 = $item['ip_terakhir_3'];
            $m_rs->tipe_densitas = $item['tipe_densitas'];
            $m_rs->perusahaan = $item['perusahaan'];
            $m_rs->vaksin = $item['vaksin'];
            $m_rs->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_rs, $deskripsi_log);
        }

        $this->result['status'] = 1;
        $this->result['message'] = 'Data berhasil disimpan';
        $this->result['content'] = array('id' => $id_rdim);
        display_json($this->result);
    } // end - saveRdim

    public function editRdim()
    {
        $params = $this->input->post('params');

        // NOTE: preparing data
        $periode = $params['periode'];
        $details = $params['details'];

        // NOTE: 1. update header -> rdim
        $id_rdim = $params['id'];
        $m_rdim = new \Model\Storage\Rdim_model();

        $m_rdim->where('id', $id_rdim)
               ->update(
                    array(
                        'mulai' => $periode['start'],
                        'selesai' => $periode['end'],
                        'g_status' => getStatus('submit')
                    )
                );

        $d_rdim = $m_rdim->where('id', $id_rdim)->first();

        $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
        Modules::run( 'base/event/update', $d_rdim, $deskripsi_log);

        $m_rs = new \Model\Storage\RdimSubmit_model();
        $m_rs->where('id_rdim', $id_rdim)->delete();

        // NOTE: 2. save detail -> rdim_submit
        foreach ($details as $item) {
            $m_rs_new = new \Model\Storage\RdimSubmit_model();
            $m_rs_new->id_rdim = $id_rdim;
            $m_rs_new->tgl_docin = $item['tanggal'] ;
            $m_rs_new->nim = $item['nim'] ;
            $m_rs_new->kandang = $item['kandang'] ;
            $m_rs_new->populasi = $item['populasi'] ;
            $m_rs_new->noreg = $item['noreg'] ;
            // $m_rs_new->prokes = $item['program_kesehatan'] ;
            $m_rs_new->pengawas = $item['pengawas'] ;
            $m_rs_new->sampling = $item['tim_sampling'] ;
            $m_rs_new->tim_panen = $item['tim_panen'] ;
            $m_rs_new->koar = $item['koordinator_area'] ;
            $m_rs_new->format_pb = $item['formatPb'] ;
            $m_rs_new->pola_mitra = $item['pola'] ;
            $m_rs_new->grup = $item['group'];
            $m_rs_new->status = 1;
            // $m_rs_new->ip1 = $item['ip_terakhir_1'];
            // $m_rs_new->ip2 = $item['ip_terakhir_2'];
            // $m_rs_new->ip3 = $item['ip_terakhir_3'];
            $m_rs_new->tipe_densitas = $item['tipe_densitas'];
            $m_rs_new->perusahaan = $item['perusahaan'];
            $m_rs_new->vaksin = $item['vaksin'];
            $m_rs_new->save();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_rs_new, $deskripsi_log);
        }

        $this->result['status'] = 1;
        $this->result['message'] = 'Data berhasil diubah';
        $this->result['content'] = array('id' => $id_rdim);
        display_json($this->result);
    } // end - editRdim

    public function list_rdim()
    {
        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->with(['logs'])->where('g_status', '<>', getStatus('delete') )->orderBy('id', 'DESC')->take(50)->get();

        $content['datas'] = $d_rdim;
        $content['akses'] = hakAkses($this->url);
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    } // end - list_rdim

    public function viewRdim($id)
    {
        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->with(['logs','dRdimSubmit'])->find($id);
        $dataMapping = $this->rdimMapping($d_rdim);
        $content['data'] = $dataMapping;
        $content['akses'] = hakAkses($this->url);
        $html = $this->load->view($this->pathView . 'view_form', $content, true);
        return $html;
    } // end - viewRdim

    private function rdimMapping($d_rdim)
    {
        // NOTE: header -> rdim
        $rdim = array(
            'id' => $d_rdim->id,
            'nomor' => $d_rdim->nomor,
            'mulai' => $d_rdim->mulai,
            'selesai' => $d_rdim->selesai,
            'status'=> $d_rdim->g_status,
            'alasan_tolak' => $d_rdim->alasan_tolak,
            'logs' =>$d_rdim->logs
        );

        // NOTE: details -> rdim_submit
        $rdim_submit = array();
        foreach ($d_rdim->dRdimSubmit as $rs) {
            // $rs -> rsim_submit
            $lampirans = array();
            foreach ($rs->lampirans as $vLamp) {
                if ($vLamp->d_nama_lampiran->nama == 'Pembatalan Rencana DOC in Mingguan') {
                    $lampirans['batal'] = array(
                        'path' => 'uploads/' . $vLamp->path,
                        'filename' => $vLamp->filename,
                    );
                }
            }

            $m_karyawan = new \Model\Storage\Karyawan_model();
            $pengawas = $m_karyawan->select('id', 'nama')->where('id', $rs->pengawas)->first();
            $sampling = $m_karyawan->select('id', 'nama')->where('id', $rs->sampling)->first();
            $tim_panen = $m_karyawan->select('id', 'nama')->where('id', $rs->tim_panen)->first();
            $koar = $m_karyawan->select('id', 'nama')->where('id', $rs->koar)->first();

            $item = array(
                'id' => $rs->id,
                'tanggal' => $rs->tgl_docin,
                'id_mitra' => $rs->dMitraMapping->dMitra->id,
                'mitra' => $rs->dMitraMapping->dMitra->nama,
                'kandang' => $rs->dKandang->kandang,
                'populasi' => $rs->populasi,
                // 'ip1' => $rs->ip1,
                // 'ip2' => $rs->ip2,
                // 'ip3' => $rs->ip3,
                'kapasitas' => $rs->dKandang->ekor_kapasitas,
                'istirahat' => '-',
                'hutang' => '-',
                'jut' => '-',
                'kecamatan' => $rs->dKandang->dKecamatan->nama,
                'kabupaten' => $rs->dKandang->dKecamatan->dKota->nama,
                'noreg' => $rs->noreg,
                // 'prokes' => $rs->prokes,
                // 'pengawas' => $rs->pengawas,
                // 'sampling' => $rs->sampling,
                // 'id_tim_panen' => $rs->dTimpanen->nik_timpanen,
                // 'tim_panen' => $rs->dTimpanen->nama_timpanen,
                // 'koar' => $rs->koar,
                'id_pengawas' => $pengawas['id'],
                'pengawas' => $pengawas['nama'],
                'id_sampling' => $sampling['id'],
                'sampling' => $sampling['nama'],
                'id_tim_panen' => $tim_panen['id'],
                'tim_panen' => $tim_panen['nama'],
                'id_koar' => $koar['id'],
                'koar' => $koar['nama'],
                'densitas' => $rs->tipe_densitas,
                'format_pb' => $rs->pola_mitra . ' ' . $rs['data_perusahaan']['perusahaan'],
                'pola' => getJenisMitra( $rs->dMitraMapping->dMitra->jenis ),
                'status' => $rs->status,
                'ket_alasan' => $rs->ket_alasan,
                'group' => $rs->grup,
                'lampirans' => $lampirans,
                'perusahaan' => $rs->perusahaan,
                'vaksin' => $rs['data_vaksin']['nama_vaksin'],
                'id_vaksin' => $rs['data_vaksin']['id']
            );

            // NOTE: perwakilan -> header row
            $status_rs = 'status-' . $item['status'];
            $perwakilan_id = $rs->dMitraMapping->perwakilan;

            if ( ! isset($rdim_submit[ $status_rs ][$perwakilan_id])) {
                $units = array();
                if ( !empty($rs->dMitraMapping->dPerwakilan->unit) ) {
                    $units = array_map(function($unit){
                        return $unit['nama'];
                    }, $rs->dMitraMapping->dPerwakilan->unit->toArray());
                }

                $header = array(
                    'id' => $perwakilan_id,
                    'perwakilan' => !empty($rs->dMitraMapping->dPerwakilan->nama) ? $rs->dMitraMapping->dPerwakilan->nama : null,
                    'units' => $units
                );

                $rdim_submit[ $status_rs ][$perwakilan_id]['header'] = $header;
            }

            $rdim_submit[ $status_rs ][$perwakilan_id]['details'][] = $item;
        }

        $rdimMapping = array(
            'rdim' => $rdim,
            'rdim_submit' => $rdim_submit
        );
        return $rdimMapping;
    } // end - rdimMapping

    public function loadContentRdim()
    {
        $id = $this->input->get('id');
        $action = $this->input->get('action');
        $content = array();
        $html = "url not found";
        if ( !empty($id) && is_numeric($id) ) {
            // NOTE: view/edit data StandarBudidaya (ajax)
            $html = (strtolower($action) == 'edit') ? $this->editRdim($id) : $this->viewRdim($id);
        }else{
            $content['akses'] = hakAkses($this->url);
        }
        echo $html;
    } // end - loadContentRdim

    public function ack()
    {
        $params = $this->input->post('params');
        $id = $params['id'];
        $action = $params['action'];
        $event = null;

        $m_rdim = new \Model\Storage\Rdim_model();
        $update = $m_rdim->find($id);
        $g_status = '';
        if (!empty($update)) {
                $g_status = getStatus('ack');
                $update->g_status = $g_status;

            if (!empty($update)) {
                $update->save();
                $event = Modules::run( 'base/event/update', $update, 'di-'.$action.' oleh ' . $this->userdata['detail_user']['nama_detuser'] );
            }

            if ($event) {
                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di ack';
            }else{
                $this->result['message'] = 'Data gagal di ack';
            }

        }else{
            $this->result['message'] = 'Data not found';
        }

        $this->result['content'] = array('id' => $id);
        display_json($this->result);
    } // end - ack

    public function approveReject()
    {
        $params = $this->input->post('params');
        $id = $params['id'];
        $action = $params['action'];
        $event = null;

        $m_rdim = new \Model\Storage\Rdim_model();
        $update = $m_rdim->find($id);
        $g_status = '';
        if (!empty($update)) {

            if ($action == 'approve') {
                $g_status = getStatus('approve');
                $update->g_status = $g_status;
            } elseif ($action == 'reject') {
                $update->alasan_tolak = $params['alasan_tolak'];
                $g_status = getStatus('reject');
                $update->g_status = $g_status;
            }

            if (!empty($update)) {
                $update->save();
                $event = Modules::run( 'base/event/update', $update, 'di-'.$action.' oleh ' . $this->userdata['detail_user']['nama_detuser'] );
            }

            if ($event) {
                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di approve';
            }else{
                $this->result['message'] = 'Data gagal di approve';
            }

        }else{
            $this->result['message'] = 'Data not found';
        }

        $this->result['content'] = array('id' => $id);
        display_json($this->result);
    } // end - approveReject

    public function getDataPembatalanRdim()
    {
        $id_rdim = $this->input->get('id_rdim');

        $m_rs = new \Model\Storage\RdimSubmit_model();
        $d_rs = $m_rs->where('id_rdim', $id_rdim)->where('status', 1)->with(['dMitraMapping'])->get();
        $datas = array();
        foreach ($d_rs as $rs) {
            $data = array(
                'id' => $rs->id,
                'mitra' => $rs->dMitraMapping->dMitra->nama,
                'noreg' => $rs->noreg
            );
            $datas[ $data['id'] ] = $data;
        }
        ksort($datas);
        $content['lists'] = $datas;
        $html = $this->load->view($this->pathView . 'list_item_pembatalan_rdim', $content, true);
        echo $html;
    } // end - getDataPembatalanRdim

    public function savePembatalanRdim()
    {

        $data_rs = json_decode($this->input->post('data_rs'),TRUE);
        $files = isset($_FILES['files']) ? $_FILES['files'] : [];
        $mappingFiles = mappingFiles($files);

        // NOTE: cari id nama lampiran
        $m_lampiran = new \Model\Storage\NamaLampiran_model();
        $d_lampiran = $m_lampiran->where('nama', 'Pembatalan Rencana DOC in Mingguan')->first();
        $lampiranId = $d_lampiran->id;

        $deskripsi_log = 'pembatalan di-submit oleh ' . $this->userdata['Nama_User'];
        $idRdims = array();
        foreach ($data_rs as $rs) {
            $m_rs = new \Model\Storage\RdimSubmit_model();
            $update = $m_rs->find($rs['id_rs']);
            $update->status = 2;
            $update->ket_alasan = $rs['ket_alasan'];
            $update->save();
            Modules::run( 'base/event/save', $update, $deskripsi_log );
            $idRdims[$update->id_rdim] = $update->id_rdim;

            $file = $mappingFiles[ $rs['sha1'] ] ?: '';
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
                $m_lampiran->tabel = 'rdim_submit';
                $m_lampiran->tabel_id = $rs['id_rs'];
                $m_lampiran->nama_lampiran = $lampiranId;
                $m_lampiran->filename = $file_name ;
                $m_lampiran->path = $path_name;
                $m_lampiran->save();
                Modules::run( 'base/event/save', $m_lampiran, $deskripsi_log );
            }
        }

        foreach ($idRdims as $idRdim) {
            $m_rdim = new \Model\Storage\Rdim_model();
            $update = $m_rdim->find($idRdim);
            $update->g_status = getStatus('submit');
            $update->save();
            Modules::run( 'base/event/save', $update, $deskripsi_log );
        }

        $this->result['status'] = 1;
        $this->result['message'] = 'Data berhasil disimpan';
        display_json($this->result);
    } // end - savePembatalanRdim

    public function cetak_kontrak($id)
    {
        $this->load->library('PDFGenerator');

        $m_rs = new \Model\Storage\RdimSubmit_model();
        $d_rs = $m_rs->where('id', $id)->where('status', 1)->with(['dMitraMapping'])->first()->toArray();

        $m_rdim = new \Model\Storage\Rdim_model();
        $d_rdim = $m_rdim->with(['logs'])->find($d_rs['id_rdim'])->toArray();

        $periode = tglIndonesia($d_rdim['mulai'], '-', ' ', true) . ' - ' . tglIndonesia($d_rdim['selesai'], '-', ' ', true);
        $dataMapping = $this->rdimMappingCetak($d_rs, $periode);

        $pola = $d_rs['pola_mitra'];

        $coba = explode(' ', $pola);

        $pola_budidaya = ($coba[0] == 'TR') ? 'tanggung rugi' : 'tidak tanggung rugi';
        $pola = str_replace(')', '', $coba[1]);
        $item_pola = str_replace('(', '', $pola);

        $m_pk = new \Model\Storage\PolaKerjasama_model();
        $d_pk = $m_pk->select('id')->where('item', trim($pola_budidaya))->first()->toArray();

        $m_sk = new \Model\Storage\SapronakKesepakatan_model();
        $d_sk = $m_sk->where('pola', trim($d_pk['id']))
                     ->where('item_pola', trim($item_pola))
                     ->where('mulai', '>=', $d_rdim['mulai'])
                     ->whereNull('berakhir')
                     ->orWhere('berakhir', '<=', $d_rdim['selesai'])
                     ->with(['harga_sapronak', 'harga_performa', 'harga_sepakat', 'hitung_budidaya', 'selisih_pakan', 'lampiran', 'logs', 'pola_kerjasama', 'data_perusahaan'])
                     ->orderBy('id', 'DESC')->first();

        // cetak_r($d_sk);

        $content['data'] = $dataMapping;
        $content['data_kontrak'] = $d_sk;
        $res_view_html = $this->load->view('transaksi/rdim/cetak_kontrak', $content, true);

        $this->pdfgenerator->generate($res_view_html, "KONTRAK (".$d_sk['nomor'].")", 'a4', 'portrait');
    }

    public function rdimMappingCetak($data = null, $periode = null)
    {
        $v_data = $data;
        $_data = array();
        if ( !empty($v_data) ) {
            $provinsi = !empty($v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['d_kota']['d_provinsi']['nama']) ? ', '.$v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['d_kota']['d_provinsi']['nama'] : '';
            $kab_kota = !empty($v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['d_kota']['nama']) ? ', '.$v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['d_kota']['nama'] : '';
            $kecamatan = !empty($v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['nama']) ? ', Kec.'.$v_data['d_mitra_mapping']['d_mitra']['d_kecamatan']['nama'] : '';
            $kelurahan = !empty($v_data['d_mitra_mapping']['d_mitra']['alamat_kelurahan']) ? ', Kel.'.$v_data['d_mitra_mapping']['d_mitra']['alamat_kelurahan'] : '';
            $rt = !empty($v_data['d_mitra_mapping']['d_mitra']['alamat_rt']) ? ', RT.'.$v_data['d_mitra_mapping']['d_mitra']['alamat_rt'] : '';
            $rw = !empty($v_data['d_mitra_mapping']['d_mitra']['alamat_rw']) ? '/RW.'.$v_data['d_mitra_mapping']['d_mitra']['alamat_rw'] : '';
            $jalan = !empty($v_data['d_mitra_mapping']['d_mitra']['alamat_jalan']) ? $v_data['d_mitra_mapping']['d_mitra']['alamat_jalan'] : '';

            $alamat = $jalan.$rt.$rw.$kelurahan.$kecamatan.$kab_kota.$provinsi;

            $_data['periode'] = $periode;
            $_data['nama'] = $v_data['d_mitra_mapping']['d_mitra']['nama'];
            $_data['ktp'] = $v_data['d_mitra_mapping']['d_mitra']['ktp'];
            $_data['alamat'] = $alamat;
            $_data['alamat_kdg'] = $alamat;
            $_data['populasi'] = $v_data['populasi'];
        }

        return $_data;
    }

    public function model($status)
    {
        $m_rdim = new \Model\Storage\Rdim_model();
        $dashboard = $m_rdim->getDashboard($status);

        return $dashboard;
    }
}
