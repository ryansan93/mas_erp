<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RealisasiPembayaran extends Public_Controller
{
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
                'assets/pembayaran/realisasi_pembayaran/js/realisasi-pembayaran.js'
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                'assets/pembayaran/realisasi_pembayaran/css/realisasi-pembayaran.css'
            ));

            $data = $this->includes;

            $data['title_menu'] = 'Realisasi Pembayaran';

            $mitra = null;
            $perusahaan = $this->get_perusahaan();

            $content['add_form'] = $this->add_form($mitra, $perusahaan);
            $content['riwayat'] = $this->riwayat($mitra, $perusahaan);

            $content['akses'] = $this->hakAkses;
            $data['view'] = $this->load->view('pembayaran/realisasi_pembayaran/index', $content, true);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function load_form()
    {
        $params = $this->input->get('params');
        $edit = $this->input->get('edit');

        $id = $params['id'];

        $content = array();
        $html = "url not found";
        
        if ( !empty($id) && $edit != 'edit' ) {
            // NOTE: view data BASTTB (ajax)
            $html = $this->detail_form( $id );
        } else if ( !empty($id) && $edit == 'edit' ) {
            // NOTE: edit data BASTTB (ajax)
            $perusahaan = $this->get_perusahaan();
            $html = $this->edit_form($id, $perusahaan);
        }else{
            $perusahaan = $this->get_perusahaan();
            $html = $this->add_form(null, $perusahaan);
        }

        echo $html;
    }

    public function get_lists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];

        $kode_perusahaan = null;

        $m_perusahaan = new \Model\Storage\Perusahaan_model();
        foreach ($params['perusahaan'] as $k => $val) {
            $d_perusahaan = null;
            if ( $val != 'all' ) {
                $d_perusahaan = $m_perusahaan->select('kode')->where('kode', $val)->groupBy('kode')->get();
            } else {
                $d_perusahaan = $m_perusahaan->select('kode')->groupBy('kode')->get();
            }

            if ( !empty($d_perusahaan) ) {
                $d_perusahaan = $d_perusahaan->toArray();

                foreach ($d_perusahaan as $k_perusahaan => $v_perusahaan) {
                    $kode_perusahaan[] = $v_perusahaan['kode'];
                }
            }
        }

        $m_rp = new \Model\Storage\RealisasiPembayaran_model();
        $d_rp = $m_rp->whereBetween('tgl_bayar', [$start_date, $end_date])
                     ->whereIn('perusahaan', $kode_perusahaan)->orderBy('tgl_bayar', 'desc')->with(['d_perusahaan', 'd_supplier', 'd_ekspedisi', 'd_mitra', 'detail'])->get();

        $data = null;
        if ( $d_rp->count() > 0 ) {
            $d_rp = $d_rp->toArray();

            foreach ($d_rp as $k_rp => $v_rp) {
                $jumlah = 0;
                foreach ($v_rp['detail'] as $k_det => $v_det) {
                    $jumlah += $v_det['bayar'];
                }

                $data[ $k_rp ] = array(
                    'id' => $v_rp['id'],
                    'tgl_bayar' => $v_rp['tgl_bayar'],
                    'nomor' => $v_rp['nomor'],
                    'd_perusahaan' => $v_rp['d_perusahaan'],
                    'supplier' => $v_rp['supplier'],
                    'd_supplier' => $v_rp['d_supplier'],
                    'ekspedisi' => $v_rp['ekspedisi'],
                    'd_ekspedisi' => $v_rp['d_ekspedisi'],
                    'peternak' => $v_rp['peternak'],
                    'd_peternak' => $v_rp['d_mitra'],
                    'lampiran' => $v_rp['lampiran'],
                    'no_bukti' => $v_rp['no_bukti'],
                    'jumlah' => $jumlah,
                    'jml_transfer' => $v_rp['jml_transfer'],
                    'cn' => $v_rp['cn']
                );
            }
        }

        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/list_riwayat', $content, true);

        echo $html;
    }

    public function get_unit()
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

    public function get_perusahaan()
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

    public function get_supplier()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                plg1.* 
            from pelanggan plg1
            right join
                (select max(id) as id, nomor from pelanggan where tipe = 'supplier' and jenis <> 'ekspedisi' group by nomor) plg2
                on
                    plg1.id = plg2.id
            where
                plg1.mstatus = 1
        ";
        $d_supl = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_supl->count() > 0 ) {
            $d_supl = $d_supl->toArray();

            foreach ($d_supl as $key => $value) {
                $key = strtoupper($value['nama']).' - '.$value['nomor'];
                $data[ $key ] = array(
                    'nama' => strtoupper($value['nama']),
                    'nomor' => $value['nomor']
                );
            }

            ksort($data);
        }
        
        // $m_supplier = new \Model\Storage\Supplier_model();
        // $nomor_supplier = $m_supplier->select('nomor')->distinct('nomor')->where('tipe', 'supplier')->where('jenis', '<>', 'ekspedisi')->get();

        // $data = null;
        // if ( $nomor_supplier->count() > 0 ) {
        //     $nomor_supplier = $nomor_supplier->toArray();

        //     foreach ($nomor_supplier as $k => $val) {
        //         $m_supplier = new \Model\Storage\Supplier_model();
        //         $d_supplier = $m_supplier->where('nomor', $val['nomor'])->where('tipe', 'supplier')->where('jenis', '<>', 'ekspedisi')->orderBy('version', 'desc')->first();

        //         $key = strtoupper($d_supplier->nama).' - '.$d_supplier['nomor'];
        //         $data[ $key ] = array(
        //             'nama' => strtoupper($d_supplier->nama),
        //             'nomor' => $d_supplier->nomor
        //         );
        //     }

        //     ksort($data);
        // }

        return $data;
    }

    public function get_ekspedisi()
    {
        $data = null;

        $m_ekspedisi = new \Model\Storage\Ekspedisi_model();
        $sql = "
            select 
                eks.id,
                eks.nomor,
                eks.nama
            from ekspedisi eks 
            right join 
                (select max(id) as id, nomor from ekspedisi group by nomor) as e 
                on
                    eks.id = e.id
            where
                eks.mstatus = 1 
            group by
                eks.id,
                eks.nomor,
                eks.nama
            order by eks.nama asc
        ";
        $d_ekspedisi = $m_ekspedisi->hydrateRaw( $sql );
        if ( $d_ekspedisi->count() > 0 ) {
            $data = $d_ekspedisi->toArray();
        }

        return $data;
    }

    public function get_mitra()
    {
        $params = $this->input->post('params');

        $m_duser = new \Model\Storage\DetUser_model();
        $d_duser = $m_duser->where('id_user', $this->userid)->first();

        $m_karyawan = new \Model\Storage\Karyawan_model();
        $d_karyawan = $m_karyawan->where('nama', 'like', strtolower(trim($d_duser->nama_detuser)).'%')->first();

        $sql_kode_unit = null;
        if ( !in_array('all', $params['kode_unit']) ) {
            $sql_kode_unit = "and w.kode in ('".implode("', '", $params['kode_unit'])."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                m.nomor,
                m.nama,
                w.kode as unit
            from kandang k
            right join
                wilayah w
                on
                    k.unit = w.id
            right join
                mitra_mapping mm
                on
                    k.mitra_mapping = mm.id
            right join
                mitra m
                on
                    m.id = mm.mitra
            where
                w.kode is not null and
                m.mstatus = 1
                ".$sql_kode_unit."
            group by
                m.nomor,
                m.nama,
                w.kode
            order by
                m.nama asc
        ";
        $d_mitra = $m_conf->hydrateRaw( $sql );

        // cetak_r( $sql, 1 );

        $data = null;
        if ( $d_mitra->count() > 0 ) {
            $data = $d_mitra->toArray();
        }

        // $kode_unit = array();
        // $kode_unit_all = null;
        // if ( !empty( $params['kode_unit'] ) ) {
        //     foreach ($params['kode_unit'] as $k_ku => $v_ku) {
        //         if ( stristr($v_ku, 'all') !== FALSE ) {
        //             $kode_unit_all = 'all';

        //             break;
        //         } else {
        //             array_push($kode_unit, $v_ku);
        //         }
        //     }
        // }

        // $m_mitra = new \Model\Storage\Mitra_model();
        // $_d_mitra = $m_mitra->select('nomor')->distinct('nomor')->get();

        // $_data = array();
        // if ( $_d_mitra->count() > 0 ) {
        //     $_d_mitra = $_d_mitra->toArray();
        //     foreach ($_d_mitra as $k_mitra => $v_mitra) {
        //         $d_mitra = $m_mitra->select('nama', 'nomor')->where('nomor', $v_mitra['nomor'])->orderBy('id', 'desc')->first();

        //         $m_mm = new \Model\Storage\MitraMapping_model();
        //         $d_mm = $m_mm->where('nomor', $d_mitra->nomor)->orderBy('id', 'desc')->first();

        //         if ( $d_mm ) {
        //             $m_kdg = new \Model\Storage\Kandang_model();
        //             $d_kdg = $m_kdg->where('mitra_mapping', $d_mm->id)->with(['d_unit'])->first();

        //             $key = $d_mitra->nama.' | '.$d_mitra->nomor;
        //             if ( empty($kode_unit_all) ) {
        //                 foreach ($kode_unit as $k_ku => $v_ku) {
        //                     if ( $v_ku == $d_kdg->d_unit->kode ) {
        //                         $_data[ $key ] = array(
        //                             'nomor' => $d_mitra->nomor,
        //                             'nama' => $d_mitra->nama,
        //                             'unit' => $d_kdg->d_unit->kode
        //                         );
        //                     }
        //                 }
        //             } else {
        //                 $_data[ $key ] = array(
        //                     'nomor' => $d_mitra->nomor,
        //                     'nama' => $d_mitra->nama,
        //                     'unit' => $d_kdg->d_unit->kode
        //                 );
        //             }
        //         }
        //     }

        //     ksort($_data);
        // }

        // $data = array();
        // if ( count( $_data ) ) {
        //     foreach ($_data as $k_data => $v_data) {
        //         $data[] = $v_data;
        //     }
        // }

        $this->result['content'] = $data;

        display_json( $this->result );
    }

    public function get_data_rencana_bayar()
    {
        $params = $this->input->post('params');

        $data = array();
        if ( $params['jenis_pembayaran'] == 'plasma' ) {
            // PETERNAK
            $m_kpp = new \Model\Storage\KonfirmasiPembayaranPeternak_model();
            $d_kpp = $m_kpp->whereBetween('tgl_bayar', [$params['start_date'], $params['end_date']])
                           ->whereIn('mitra', $params['mitra'])
                           ->where('perusahaan', $params['perusahaan'])->get();

            if ( $d_kpp->count() > 0 ) {
                $d_kpp = $d_kpp->toArray();

                foreach ($d_kpp as $k_kpp => $v_kpp) {
                    $m_mitra = new \Model\Storage\Mitra_model();
                    $d_mitra = $m_mitra->where('nomor', $v_kpp['mitra'])->orderBy('version', 'desc')->first();

                    $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                    $bayar = $m_rpd->where('no_bayar', $v_kpp['nomor'])->sum('bayar');

                    $data[] = array(
                        'tgl_bayar' => $v_kpp['tgl_bayar'],
                        'transaksi' => 'PLASMA',
                        'no_bayar' => $v_kpp['nomor'],
                        'periode' => $v_kpp['periode'],
                        'nama_penerima' => $d_mitra->nama,
                        'tagihan' => $v_kpp['total'],
                        'bayar' => $bayar,
                        'jumlah' => ($v_kpp['total'] > $bayar) ? $v_kpp['total'] - $bayar : 0
                    );
                }
            }
        } else if ( $params['jenis_pembayaran'] == 'supplier' ) {
            if ( $params['jenis_transaksi'][0] == 'all' ) {
                $doc = $this->get_rencana_pembayaran_doc( $params );
                if ( count($doc) > 0 ) {
                    foreach ($doc as $k => $v) {
                        $data[] = $v;
                    }
                }   
                $pakan = $this->get_rencana_pembayaran_pakan( $params );
                if ( count($pakan) > 0 ) {
                    foreach ($pakan as $k => $v) {
                        $data[] = $v;
                    }
                }
                $voadip = $this->get_rencana_pembayaran_voadip( $params );
                if ( count($voadip) > 0 ) {
                    foreach ($voadip as $k => $v) {
                        $data[] = $v;
                    }
                }
            } else {
                foreach ($params['jenis_transaksi'] as $k_jt => $v_jt) {
                    if ( $v_jt == 'doc' ) {
                        // DOC
                        $doc = $this->get_rencana_pembayaran_doc( $params );
                        if ( count($doc) > 0 ) {
                            foreach ($doc as $k => $v) {
                                $data[] = $v;
                            }
                        }
                    }
                    if ( $v_jt == 'pakan' ) {
                        // PAKAN
                        $pakan = $this->get_rencana_pembayaran_pakan( $params );
                        if ( count($pakan) > 0 ) {
                            foreach ($pakan as $k => $v) {
                                $data[] = $v;
                            }
                        }
                    }
                    if ( $v_jt == 'voadip' ) {
                        // VOADIP
                        $voadip = $this->get_rencana_pembayaran_voadip( $params );
                        if ( count($voadip) > 0 ) {
                            foreach ($voadip as $k => $v) {
                                $data[] = $v;
                            }
                        }
                    }
                }
            }
        } else if ( $params['jenis_pembayaran'] == 'ekspedisi' ) {
            $data = $this->get_rencana_pembayaran_ekspedisi( $params );
        }
        
        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/list_rencana_pembayaran', $content, true);

        $this->result['html'] = $html;

        display_json( $this->result );
    }

    public function get_rencana_pembayaran_doc($params)
    {
        $data = array();

        $m_kpd = new \Model\Storage\KonfirmasiPembayaranDoc_model();
        $d_kpd = $m_kpd->whereBetween('tgl_bayar', [$params['start_date'], $params['end_date']])
                       ->where('supplier', $params['supplier'])
                       ->where('perusahaan', $params['perusahaan'])->get();

        if ( $d_kpd->count() > 0 ) {
            $d_kpd = $d_kpd->toArray();

            foreach ($d_kpd as $k_kpd => $v_kpd) {
                $m_supplier = new \Model\Storage\Supplier_model();
                $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $params['supplier'])->orderBy('version', 'desc')->first();

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $bayar = $m_rpd->where('no_bayar', $v_kpd['nomor'])->sum('bayar');

                $data[] = array(
                    'tgl_bayar' => $v_kpd['tgl_bayar'],
                    'transaksi' => 'DOC',
                    'no_bayar' => $v_kpd['nomor'],
                    'periode' => $v_kpd['periode'],
                    'nama_penerima' => $d_supplier->nama,
                    'tagihan' => $v_kpd['total'],
                    'bayar' => $bayar,
                    'jumlah' => ($v_kpd['total'] > $bayar) ? $v_kpd['total'] - $bayar : 0
                );
            }
        }

        return $data;
    }

    public function get_rencana_pembayaran_pakan($params)
    {
        $data = array();

        $m_kpp = new \Model\Storage\KonfirmasiPembayaranPakan_model();
        $d_kpp = $m_kpp->whereBetween('tgl_bayar', [$params['start_date'], $params['end_date']])
                       ->where('supplier', $params['supplier'])
                       ->where('perusahaan', $params['perusahaan'])->get();

        if ( $d_kpp->count() > 0 ) {
            $d_kpp = $d_kpp->toArray();

            foreach ($d_kpp as $k_kpp => $v_kpp) {
                $m_supplier = new \Model\Storage\Supplier_model();
                $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $params['supplier'])->orderBy('version', 'desc')->first();

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $bayar = $m_rpd->where('no_bayar', $v_kpp['nomor'])->sum('bayar');

                $data[] = array(
                    'tgl_bayar' => $v_kpp['tgl_bayar'],
                    'transaksi' => 'PAKAN',
                    'no_bayar' => $v_kpp['nomor'],
                    'periode' => $v_kpp['periode'],
                    'nama_penerima' => $d_supplier->nama,
                    'tagihan' => $v_kpp['total'],
                    'bayar' => $bayar,
                    'jumlah' => ($v_kpp['total'] > $bayar) ? $v_kpp['total'] - $bayar : 0
                );
            }
        }

        return $data;
    }

    public function get_rencana_pembayaran_voadip($params)
    {
        $data = array();

        $m_kpv = new \Model\Storage\KonfirmasiPembayaranVoadip_model();
        $d_kpv = $m_kpv->whereBetween('tgl_bayar', [$params['start_date'], $params['end_date']])
                       ->where('supplier', $params['supplier'])
                       ->where('perusahaan', $params['perusahaan'])->get();

        if ( $d_kpv->count() > 0 ) {
            $d_kpv = $d_kpv->toArray();

            foreach ($d_kpv as $k_kpv => $v_kpv) {
                $m_supplier = new \Model\Storage\Supplier_model();
                $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $params['supplier'])->orderBy('version', 'desc')->first();

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $bayar = $m_rpd->where('no_bayar', $v_kpv['nomor'])->sum('bayar');

                $data[] = array(
                    'tgl_bayar' => $v_kpv['tgl_bayar'],
                    'transaksi' => 'VOADIP',
                    'no_bayar' => $v_kpv['nomor'],
                    'periode' => $v_kpv['periode'],
                    'nama_penerima' => $d_supplier->nama,
                    'tagihan' => $v_kpv['total'],
                    'bayar' => $bayar,
                    'jumlah' => ($v_kpv['total'] > $bayar) ? $v_kpv['total'] - $bayar : 0
                );
            }
        }

        return $data;
    }

    public function get_rencana_pembayaran_ekspedisi($params)
    {
        $data = array();

        $m_kpoap = new \Model\Storage\KonfirmasiPembayaranOaPakan_model();
        $d_kpoap = $m_kpoap->whereBetween('tgl_bayar', [$params['start_date'], $params['end_date']])
                       ->where('ekspedisi_id', $params['ekspedisi'])
                       ->where('perusahaan', $params['perusahaan'])->get();

        if ( $d_kpoap->count() > 0 ) {
            $d_kpoap = $d_kpoap->toArray();

            foreach ($d_kpoap as $k_kpoap => $v_kpoap) {
                $m_ekspedisi = new \Model\Storage\Ekspedisi_model();
                $sql = "
                    select 
                        eks.id,
                        eks.nomor,
                        eks.nama
                    from ekspedisi eks 
                    right join 
                        (select max(id) as id, nomor from ekspedisi group by nomor) as e 
                        on
                            eks.id = e.id
                    where
                        eks.mstatus = 1 and
                        eks.nomor = '".$params['ekspedisi']."' 
                    group by
                        eks.id,
                        eks.nomor,
                        eks.nama
                    order by eks.nama asc
                ";
                $d_ekspedisi = $m_ekspedisi->hydrateRaw( $sql );
                if ( $d_ekspedisi->count() > 0 ) {
                    $d_ekspedisi = $d_ekspedisi->toArray();
                }

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $bayar = $m_rpd->where('no_bayar', $v_kpoap['nomor'])->sum('bayar');

                $data[] = array(
                    'tgl_bayar' => $v_kpoap['tgl_bayar'],
                    'transaksi' => 'OA PAKAN',
                    'no_bayar' => $v_kpoap['nomor'],
                    'periode' => $v_kpoap['periode'],
                    'nama_penerima' => $d_ekspedisi[0]['nama'],
                    'tagihan' => $v_kpoap['total'],
                    'bayar' => $bayar,
                    'jumlah' => ($v_kpoap['total'] > $bayar) ? $v_kpoap['total'] - $bayar : 0
                );
            }
        }

        return $data;
    }

    public function riwayat($mitra, $perusahaan)
    {
        $content['unit'] = $this->get_unit();
        $content['mitra'] = $mitra;
        $content['perusahaan'] = $perusahaan;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/riwayat', $content, true);

        return $html;
    }

    public function add_form($mitra, $perusahaan)
    {
        $content['unit'] = $this->get_unit();
        $content['supplier'] = $this->get_supplier();
        $content['ekspedisi'] = $this->get_ekspedisi();
        $content['perusahaan'] = $perusahaan;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/add_form', $content, true);

        return $html;
    }

    public function detail_form($id)
    {
        $m_rp = new \Model\Storage\RealisasiPembayaran_model();
        $d_rp = $m_rp->where('id', $id)->with(['d_perusahaan', 'd_supplier', 'd_mitra', 'd_ekspedisi', 'detail', 'cn_realisasi_pembayaran', 'd_potongan', 'logs'])->first();

        $data = null;
        if ( $d_rp ) {
            $d_rp = $d_rp->toArray();

            $jumlah = 0;
            $jenis_transaksi = null;
            foreach ($d_rp['detail'] as $k_det => $v_det) {
                $jumlah += $v_det['bayar'];
                $jenis_transaksi[] = $v_det['transaksi'];
            }

            $log = !empty($d_rp['logs']) ? $d_rp['logs'][ count($d_rp['logs'])-1 ] : null;
            $start_date = prev_date(date('Y-m-d')).' 00:00:00';
            $end_date = date('Y-m-d').' 23:59:59';

            $delete = 0;
            if ( empty($log) or (!empty($log) && $log['waktu'] >= $start_date && $log['waktu'] <= $end_date) ) {
                $delete = 1;
            }

            $jenis_pembayaran = null;
            if ( !empty($d_rp['supplier']) ) {
                $jenis_pembayaran = 'SUPPLIER';
            } else if ( !empty($d_rp['peternak']) ) {
                $jenis_pembayaran = 'PLASMA';
            } else if ( !empty($d_rp['ekspedisi']) ) {
                $jenis_pembayaran = 'EKSPEDISI';
            }

            $data = array(
                'id' => $d_rp['id'],
                'tgl_bayar' => $d_rp['tgl_bayar'],
                'no_bayar' => $d_rp['nomor'],
                'jml_transfer' => $d_rp['jml_transfer'],
                'total_potongan' => $d_rp['potongan'],
                'cn' => $d_rp['cn'],
                'jumlah_bayar' => $jumlah,
                'jenis_pembayaran' => $jenis_pembayaran,
                'jenis_transaksi' => implode(', ', $jenis_transaksi),
                'supplier' => $d_rp['d_supplier']['nama'],
                'peternak' => $d_rp['d_mitra']['nama'],
                'ekspedisi' => $d_rp['d_ekspedisi']['nama'],
                'perusahaan' => $d_rp['d_perusahaan']['perusahaan'],
                'detail' => $d_rp['detail'],
                'cn_realisasi_pembayaran' => $d_rp['cn_realisasi_pembayaran'],
                'potongan' => $d_rp['d_potongan'],
                'delete' => $delete
            );
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/detail_form', $content, true);

        return $html;
    }

    public function edit_form($id, $perusahaan)
    {
        $m_rp = new \Model\Storage\RealisasiPembayaran_model();
        $d_rp = $m_rp->where('id', $id)->with(['d_perusahaan', 'd_supplier', 'detail'])->first();

        $data = null;
        if ( $d_rp ) {
            $d_rp = $d_rp->toArray();

            $jumlah = 0;
            $jenis_pembayaran = null;
            $jenis_transaksi = null;
            $start_date = null;
            $end_date = null;
            $nama_penerima = null;
            $detail = null;
            foreach ($d_rp['detail'] as $k_det => $v_det) {
                $d_konfirmasi = null;
                $nama_penerima = null;
                if ( stristr($v_det['transaksi'], 'doc') !== false ) {
                    $m_kpd = new \Model\Storage\KonfirmasiPembayaranDoc_model();
                    $d_konfirmasi = $m_kpd->where('nomor', $v_det['no_bayar'])->first();

                    $m_supplier = new \Model\Storage\Supplier_model();
                    $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $d_konfirmasi->supplier)->orderBy('version', 'desc')->first();

                    $nama_penerima = $d_supplier->nama;
                }
                if ( stristr($v_det['transaksi'], 'pakan') !== false ) {
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPakan_model();
                    $d_konfirmasi = $m_kpp->where('nomor', $v_det['no_bayar'])->first();

                    $m_supplier = new \Model\Storage\Supplier_model();
                    $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $d_konfirmasi->supplier)->orderBy('version', 'desc')->first();

                    $nama_penerima = $d_supplier->nama;
                }
                if ( stristr($v_det['transaksi'], 'voadip') !== false ) {
                    $m_kpv = new \Model\Storage\KonfirmasiPembayaranVoadip_model();
                    $d_konfirmasi = $m_kpv->where('nomor', $v_det['no_bayar'])->first();

                    $m_supplier = new \Model\Storage\Supplier_model();
                    $d_supplier = $m_supplier->where('tipe', 'supplier')->where('nomor', $d_konfirmasi->supplier)->orderBy('version', 'desc')->first();

                    $nama_penerima = $d_supplier->nama;
                }
                if ( stristr($v_det['transaksi'], 'peternak') !== false ) {
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPeternak_model();
                    $d_konfirmasi = $m_kpp->where('nomor', $v_det['no_bayar'])->first();

                    $m_mitra = new \Model\Storage\Mitra_model();
                    $d_mitra = $m_mitra->where('nomor', $d_konfirmasi->mitra)->orderBy('version', 'desc')->first();

                    $nama_penerima = $d_mitra->nama;
                }

                if ( !empty($d_konfirmasi) ) {
                    if ( empty($start_date) ) {
                        $start_date = $d_konfirmasi->tgl_bayar;
                    } else {
                        if ( $start_date > $d_konfirmasi->tgl_bayar ) {
                            $start_date = $d_konfirmasi->tgl_bayar;
                        }
                    }
                    if ( empty($end_date) ) {
                        $end_date = $d_konfirmasi->tgl_bayar;
                    } else {
                        if ( $end_date < $d_konfirmasi->tgl_bayar ) {
                            $end_date = $d_konfirmasi->tgl_bayar;
                        }
                    }
                }

                $jumlah += $v_det['bayar'];
                $jenis_transaksi[] = $v_det['transaksi'];

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $bayar = $m_rpd->where('no_bayar', $v_det['no_bayar'])->where('id_header', '<>', $id)->sum('bayar');

                $detail[] = array(
                    'tgl_rcn_bayar' => $d_konfirmasi->tgl_bayar,
                    'transaksi' => (stristr($v_det['transaksi'], 'peternak') !== false) ? 'PLASMA' : $v_det['transaksi'],
                    'no_bayar' => $v_det['no_bayar'],
                    'periode' => $d_konfirmasi->periode,
                    'nama_penerima' => $nama_penerima,
                    'tagihan' => $v_det['tagihan'],
                    'bayar' => 0,
                    'jumlah' => $v_det['tagihan'],
                );
            }

            $data = array(
                'id' => $d_rp['id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'tgl_bayar' => $d_rp['tgl_bayar'],
                'no_bayar' => $d_rp['nomor'],
                'jumlah_bayar' => $jumlah,
                'jenis_pembayaran' => !empty($d_rp['supplier']) ? 'SUPPLIER' : 'PLASMA',
                'jenis_transaksi' => $jenis_transaksi,
                'supplier' => $d_rp['supplier'],
                'unit' => null,
                'peternak' => null,
                'perusahaan' => $d_rp['perusahaan'],
                'detail' => $detail
            );
        }

        $content['unit'] = $this->get_unit();
        $content['supplier'] = $this->get_supplier();
        $content['ekspedisi'] = $this->get_ekspedisi();
        $content['perusahaan'] = $perusahaan;
        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/edit_form', $content, true);

        return $html;
    }

    public function realisasi_pembayaran()
    {
        $params = $this->input->get('params');

        $data = null;

        $total_cn = 0;
        $total_potongan = 0;
        $total = 0;
        $total_bayar = 0;
        $detail = null;
        foreach ($params['detail'] as $k_det => $v_det) {
            $total += $v_det['tagihan'];

            $bayar = 0;

            if ( isset($params['id']) ) {
                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $d_rpd = $m_rpd->where('no_bayar', $v_det['no_bayar'])->where('id_header', $params['id'])->first();

                if ( !empty($d_rpd) ) {
                    $bayar = $d_rpd->bayar;
                    $total_bayar += $d_rpd->bayar;
                }
            }

            $detail[] = array(
                'transaksi' => $v_det['transaksi'],
                'no_bayar' => $v_det['no_bayar'],
                'tagihan' => $v_det['tagihan'],
                'bayar' => $bayar
            );
        }

        $m_perusahaan = new \Model\Storage\Perusahaan_model();
        $d_perusahaan = $m_perusahaan->where('kode', $params['perusahaan'])->orderBy('version', 'desc')->first();

        $nomor = null;
        $tgl_bayar = null;
        $rekening = null;
        $no_bukti = null;
        $lampiran = null;
        $jml_transfer = 0;
        if ( isset($params['id']) ) {
            $m_rp = new \Model\Storage\RealisasiPembayaran_model();
            $d_rp = $m_rp->where('id', $params['id'])->first();

            $nomor = $d_rp->nomor;
            $tgl_bayar = $d_rp->tgl_bayar;
            $rekening = $d_rp->no_rek;
            $no_bukti = $d_rp->no_bukti;
            $lampiran = $d_rp->lampiran;
        }

        $d_supplier = null;
        $d_mitra = null;
        $ekspedisi = null;
        $bank_ekspedisi = null;
        if ( stristr($params['jenis_pembayaran'], 'supplier') !== false ) {
            $m_supplier = new \Model\Storage\Supplier_model();
            $d_supplier = $m_supplier->where('nomor', $params['supplier'])->where('tipe', 'supplier')->where('jenis', '<>', 'ekspedisi')->orderBy('version', 'desc')->with(['banks'])->first();
        } else if ( stristr($params['jenis_pembayaran'], 'plasma') !== false ) {
            $m_mitra = new \Model\Storage\Mitra_model();
            $d_mitra = $m_mitra->where('nomor', $params['peternak'])->orderBy('version', 'desc')->first();

            $rekening = $d_mitra->rekening_nomor.' - '.$d_mitra->bank;
        } else if ( stristr($params['jenis_pembayaran'], 'ekspedisi') !== false ) {
            $m_ekspedisi = new \Model\Storage\Ekspedisi_model();
            $sql = "
                select 
                    eks.id,
                    eks.nomor,
                    eks.nama
                from ekspedisi eks 
                right join 
                    (select max(id) as id, nomor from ekspedisi group by nomor) as e 
                    on
                        eks.id = e.id
                where
                    eks.mstatus = 1 and
                    eks.nomor = '".$params['ekspedisi']."' 
                group by
                    eks.id,
                    eks.nomor,
                    eks.nama
                order by eks.nama asc
            ";
            $d_ekspedisi = $m_ekspedisi->hydrateRaw( $sql );
            if ( $d_ekspedisi->count() > 0 ) {
                $ekspedisi = $d_ekspedisi->toArray();
            }

            $m_bank_ekspedisi = new \Model\Storage\BankEkspedisi_model();
            $sql = "
                select be.* from bank_ekspedisi be
                right join
                    (
                        select e1.* from ekspedisi e1
                        right join
                            (select max(id) as id, nomor from ekspedisi group by nomor) e2
                            on
                                e1.id = e2.id

                    ) eks
                    on
                        be.ekspedisi_id = eks.id
                where
                    eks.nomor = '".$params['ekspedisi']."'
            ";
            $d_bank_ekspedisi = $m_bank_ekspedisi->hydrateRaw( $sql );
            if ( $d_bank_ekspedisi->count() > 0 ) {
                $bank_ekspedisi = $d_bank_ekspedisi->toArray();
            }
        }

        $data = array(
            'id' => isset($params['id']) ? $params['id'] : null,
            'jenis_pembayaran' => $params['jenis_pembayaran'],
            'jml_transfer' => $jml_transfer,
            'total_cn' => $total_cn,
            'total_potongan' => $total_potongan,
            'total' => $total,
            'total_bayar' => $total_bayar,
            'nomor' => $nomor,
            'tgl_bayar' => $tgl_bayar,
            'rekening' => $rekening,
            'no_bukti' => $no_bukti,
            'lampiran' => $lampiran,
            'no_perusahaan' => $d_perusahaan->kode,
            'perusahaan' => $d_perusahaan->perusahaan,
            'no_supplier' => !empty($d_supplier) ? $d_supplier->nomor : null,
            'supplier' => !empty($d_supplier) ? $d_supplier->nama : null,
            'bank_supplier' => !empty($d_supplier) ? $d_supplier->banks : null,
            'no_peternak' => !empty($d_mitra) ? $d_mitra->nomor : null,
            'peternak' => !empty($d_mitra) ? $d_mitra->nama : null,
            'no_ekspedisi' => !empty($ekspedisi) ? $ekspedisi[0]['nomor'] : null,
            'ekspedisi' => !empty($ekspedisi) ? $ekspedisi[0]['nama'] : null,
            'bank_ekspedisi' => $bank_ekspedisi,
            'detail' => $detail
        );

        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/realisasi_pembayaran', $content, true);

        echo $html;
    }

    public function save()
    {
        $data = json_decode($this->input->post('data'),TRUE);
        $files = isset($_FILES['files']) ? $_FILES['files'] : [];

        try {
            // cetak_r( $data, 1 );

            $jenis_transaksi = null;

            $file_name = $path_name = null;
            $isMoved = 0;
            if (!empty($files)) {
                $moved = uploadFile($files);
                $isMoved = $moved['status'];
            }
            if ($isMoved) {
                $file_name = $moved['name'];
                $path_name = $moved['path'];

                $m_rp = new \Model\Storage\RealisasiPembayaran_model();
                $nomor = $m_rp->getNextNomor();

                $m_rp->nomor = $nomor;
                $m_rp->tgl_bayar = $data['tgl_bayar'];
                $m_rp->perusahaan = $data['perusahaan'];
                $m_rp->supplier = isset($data['supplier']) ? $data['supplier'] : null;
                $m_rp->peternak = isset($data['peternak']) ? $data['peternak'] : null;
                $m_rp->no_rek = $data['no_rek'];
                $m_rp->no_bukti = $data['no_bukti'];
                $m_rp->lampiran = $path_name;
                $m_rp->cn = isset($data['total_cn']) ? $data['total_cn'] : 0;
                $m_rp->jml_transfer = $data['jml_transfer'];
                $m_rp->jml_bayar = $data['bayar'];
                $m_rp->keterangan = isset($data['keterangan']) ? $data['keterangan'] : null;
                $m_rp->ekspedisi = isset($data['ekspedisi']) ? $data['ekspedisi'] : null;
                $m_rp->potongan = $data['total_potongan'];
                $m_rp->save();

                $id = $m_rp->id;
                foreach ($data['detail'] as $k_det => $v_det) {
                    $jenis_transaksi = $v_det['transaksi'];

                    $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                    $m_rpd->id_header = $id;
                    $m_rpd->transaksi = $v_det['transaksi'];
                    $m_rpd->no_bayar = $v_det['no_bayar'];
                    $m_rpd->tagihan = $v_det['tagihan'];
                    $m_rpd->bayar = $v_det['bayar'];
                    $m_rpd->save();

                    if ( stristr($v_det['transaksi'], 'doc') !== false ) {
                        if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                            $m_kpd = new \Model\Storage\KonfirmasiPembayaranDoc_model();
                            $m_kpd->where('nomor', $v_det['no_bayar'])->update(
                                array( 'lunas' => 1 )
                            );
                        }
                    }
                    if ( stristr($v_det['transaksi'], 'pakan') !== false ) {
                        if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                            $m_kpp = new \Model\Storage\KonfirmasiPembayaranPakan_model();
                            $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                                array( 'lunas' => 1 )
                            );
                        }
                    }
                    if ( stristr($v_det['transaksi'], 'voadip') !== false ) {
                        if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                            $m_kpv = new \Model\Storage\KonfirmasiPembayaranVoadip_model();
                            $m_kpv->where('nomor', $v_det['no_bayar'])->update(
                                array( 'lunas' => 1 )
                            );
                        }
                    }
                    if ( stristr($v_det['transaksi'], 'peternak') !== false ) {
                        if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                            $m_kpp = new \Model\Storage\KonfirmasiPembayaranPeternak_model();
                            $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                                array( 'lunas' => 1 )
                            );
                        }
                    }
                }

                if ( isset($data['cn']) && !empty($data['cn']) ) {
                    foreach ($data['cn'] as $k_cn => $v_cn) {
                        $m_rpc = new \Model\Storage\RealisasiPembayaranCn_model();
                        $m_rpc->id_header = $id;
                        $m_rpc->det_jurnal_id = $v_cn['id'];
                        $m_rpc->saldo = $v_cn['saldo'];
                        // $m_rpc->pakai = $v_cn['pakai'];
                        $m_rpc->sisa_saldo = $v_cn['sisa_saldo'];
                        $m_rpc->save();

                        $m_djurnal = new \Model\Storage\DetJurnal_model();
                        $m_djurnal->where('id', $v_cn['id'])->update(
                            array(
                                'saldo' => $v_cn['sisa_saldo']
                            )
                        );
                    }
                }

                if ( isset($data['potongan']) && !empty($data['potongan']) ) {
                    foreach ($data['potongan'] as $k_potongan => $v_potongan) {
                        $m_rpp = new \Model\Storage\RealisasiPembayaranPotongan_model();
                        $m_rpp->id_header = $id;
                        $m_rpp->det_jurnal_trans_id = $v_potongan['id'];
                        $m_rpp->nominal = $v_potongan['nominal'];
                        $m_rpp->save();
                    }
                }

                $jenis_transaksi = ($jenis_transaksi == 'PLASMA') ? 'RHPP' : $jenis_transaksi;

                $m_conf = new \Model\Storage\Conf();
                $sql = "exec insert_jurnal '".$jenis_transaksi."', '".$nomor."', NULL, NULL, 'realisasi_pembayaran', ".$id.", NULL, 1";
                $d_conf = $m_conf->hydrateRaw( $sql );

                $d_rp = $m_rp->where('id', $id)->first();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $d_rp, $deskripsi_log);

                $this->result['status'] = 1;
                $this->result['content'] = array('id' => $id);
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                $this->result['message'] = 'Error, segera hubungi tim IT.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $data = json_decode($this->input->post('data'),TRUE);
        $files = isset($_FILES['files']) ? $_FILES['files'] : [];

        try {
            $jenis_transaksi = null;

            $file_name = $path_name = null;
            $isMoved = 0;
            if (!empty($files)) {
                $moved = uploadFile($files);
                $isMoved = $moved['status'];
            }

            $m_rp = new \Model\Storage\RealisasiPembayaran_model();
            if ($isMoved) {
                $file_name = $moved['name'];
                $path_name = $moved['path'];
            } else {
                $d_rp = $m_rp->where('id', $data['id'])->first();
                $path_name = $d_rp->lampiran;
            }

            $m_rp->where('id', $data['id'])->update(
                array(
                    'tgl_bayar' => $data['tgl_bayar'],
                    'perusahaan' => $data['perusahaan'],
                    'supplier' => isset($data['supplier']) ? $data['supplier'] : null,
                    'peternak' => isset($data['peternak']) ? $data['peternak'] : null,
                    'no_rek' => $data['no_rek'],
                    'no_bukti' => $data['no_bukti'],
                    'lampiran' => $path_name,
                    'keterangan' => isset($data['keterangan']) ? $data['keterangan'] : null
                )
            );

            $id = $data['id'];

            $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
            $d_rpd = $m_rpd->where('id_header', $id)->delete();

            foreach ($data['detail'] as $k_det => $v_det) {
                $jenis_transaksi = $v_det['transaksi'];

                $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
                $m_rpd->id_header = $id;
                $m_rpd->transaksi = $v_det['transaksi'];
                $m_rpd->no_bayar = $v_det['no_bayar'];
                $m_rpd->tagihan = $v_det['tagihan'];
                $m_rpd->bayar = $v_det['bayar'];
                $m_rpd->save();

                if ( stristr($v_det['transaksi'], 'doc') !== false ) {
                    $lunas = 0;
                    if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                        $lunas = 1;
                    }
                    $m_kpd = new \Model\Storage\KonfirmasiPembayaranDoc_model();
                    $m_kpd->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'pakan') !== false ) {
                    $lunas = 0;
                    if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                        $lunas = 1;
                    }
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPakan_model();
                    $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'voadip') !== false ) {
                    $lunas = 0;
                    if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                        $lunas = 1;
                    }
                    $m_kpv = new \Model\Storage\KonfirmasiPembayaranVoadip_model();
                    $m_kpv->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'peternak') !== false ) {
                    $lunas = 0;
                    if ( $v_det['tagihan'] == $v_det['bayar'] ) {
                        $lunas = 1;
                    }
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPeternak_model();
                    $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
            }

            $d_rp = $m_rp->where('id', $id)->first();

            $jenis_transaksi = ($jenis_transaksi == 'PLASMA') ? 'RHPP' : $jenis_transaksi;

            $m_conf = new \Model\Storage\Conf();
            $sql = "exec insert_jurnal '".$jenis_transaksi."', '".$d_rp->nomor."', NULL, NULL, 'realisasi_pembayaran', ".$id.", ".$id.", 2";
            $d_conf = $m_conf->hydrateRaw( $sql );

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_rp, $deskripsi_log);

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

            $m_rp = new \Model\Storage\RealisasiPembayaran_model();
            $d_rp = $m_rp->where('id', $id)->first();

            $m_rpd = new \Model\Storage\RealisasiPembayaranDet_model();
            $d_rpd = $m_rpd->where('id_header', $id)->get()->toArray();

            $m_rpcn = new \Model\Storage\RealisasiPembayaranCn_model();
            $d_rpcn = $m_rpcn->where('id_header', $id)->get();

            foreach ($d_rpd as $k_det => $v_det) {
                if ( stristr($v_det['transaksi'], 'doc') !== false ) {
                    $lunas = 0;
                    $m_kpd = new \Model\Storage\KonfirmasiPembayaranDoc_model();
                    $m_kpd->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'pakan') !== false ) {
                    $lunas = 0;
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPakan_model();
                    $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'voadip') !== false ) {
                    $lunas = 0;
                    $m_kpv = new \Model\Storage\KonfirmasiPembayaranVoadip_model();
                    $m_kpv->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
                if ( stristr($v_det['transaksi'], 'peternak') !== false ) {
                    $lunas = 0;
                    $m_kpp = new \Model\Storage\KonfirmasiPembayaranPeternak_model();
                    $m_kpp->where('nomor', $v_det['no_bayar'])->update(
                        array( 'lunas' => $lunas )
                    );
                }
            }

            if ( $d_rpcn->count() > 0 ) {
                $d_rpcn = $d_rpcn->toArray();

                foreach ($d_rpcn as $k_rpcn => $v_rpcn) {
                    $m_dj = new \Model\Storage\DetJurnal_model();
                    $d_dj = $m_dj->where('id', $v_rpcn['det_jurnal_id'])->first();

                    $saldo = (float) $d_dj->saldo;
                    $sisa_saldo = (float) $d_dj->sisa_saldo;
                    $saldo_kembali = $saldo - $sisa_saldo;

                    $m_dj->where('id', $v_rpcn['det_jurnal_id'])->update(
                        array(
                            'saldo' => ($saldo+$saldo_kembali)
                        )
                    );
                }
            }

            $m_conf = new \Model\Storage\Conf();
            $sql = "exec insert_jurnal NULL, NULL, NULL, NULL, 'realisasi_pembayaran', ".$id.", ".$id.", 3";
            $d_conf = $m_conf->hydrateRaw( $sql );

            $m_rp->where('id', $id)->delete();
            $m_rpd->where('id_header', $id)->delete();
            $m_rpcn = new \Model\Storage\RealisasiPembayaranCn_model();
            $m_rpcn->where('id_header', $id)->delete();
            $m_rpp = new \Model\Storage\RealisasiPembayaranPotongan_model();
            $m_rpp->where('id_header', $id)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_rp, $deskripsi_log);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalPilihCN()
    {
        $params = $this->input->get('params');

        $m_jurnalt = new \Model\Storage\JurnalTrans_model();
        $d_jurnalt = $m_jurnalt->where('nama', 'like', '%CREDIT NOTE%')->where('mstatus', 1)->first();

        $det_jurnal_trans_id = null;
        if ( $d_jurnalt ) {
            $m_djurnalt = new \Model\Storage\DetJurnalTrans_model();
            if ( $params['jenis_transaksi'][0] == 'all' ) {
                $d_djurnalt = $m_djurnalt->where('id_header', $d_jurnalt->id)->get();
                if ( $d_djurnalt->count() > 0 ) {
                    $d_djurnalt = $d_djurnalt->toArray();

                    foreach ($d_djurnalt as $k_djurnalt => $v_djurnalt) {
                        $det_jurnal_trans_id[] = $v_djurnalt['id'];
                    }
                }
            } else {
                foreach ($params['jenis_transaksi'] as $k_jt => $v_jt) {
                    $d_djurnalt = $m_djurnalt->where('id_header', $d_jurnalt->id)->where('nama', 'like', '%'.$v_jt.'%')->get();

                    if ( $d_djurnalt->count() > 0 ) {
                        $d_djurnalt = $d_djurnalt->toArray();

                        foreach ($d_djurnalt as $k_djurnalt => $v_djurnalt) {
                            $det_jurnal_trans_id[] = $v_djurnalt['id'];
                        }
                    }
                }
            }
        }

        $m_djurnal = new \Model\Storage\DetJurnal_model();
        $d_djurnal = $m_djurnal->where('supplier', $params['supplier'])->where('perusahaan', $params['perusahaan'])->where('saldo', '>', 0)->whereIn('det_jurnal_trans_id', $det_jurnal_trans_id)->with(['jurnal_trans_detail', 'd_supplier', 'd_perusahaan'])->orderBy('tanggal', 'asc')->get();

        $data = null;
        if ( $d_djurnal->count() > 0 ) {
            $data = $d_djurnal->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/modal_pilih_cn', $content, true);

        echo $html;
    }

    public function modalPotongan()
    {
        $params = $this->input->get('params');

        $list_coa_potongan = array('130611', '130513', '130621', '130523', '130612', '130622', '950100');

        $data = null;
        foreach ($params['jenis_transaksi'] as $k_jt => $v_jt) {
            $nama_transaksi = $v_jt;

            if ( stristr( $nama_transaksi, 'peternak' ) !== false ) {
                $nama_transaksi = 'rhpp';
            }

            $m_jurnalt = new \Model\Storage\JurnalTrans_model();
            $d_jurnalt = $m_jurnalt->where('nama', 'like', '%'.$nama_transaksi.'%')->where('mstatus', 1)->first();            

            $det_jurnal_trans_id = null;
            if ( $d_jurnalt ) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select 
                        djt.id,
                        djt.id_header,
                        djt.sumber_coa as no_coa,
                        djt.nama,
                        djt.sumber,
                        djt.sumber_coa,
                        djt.tujuan,
                        djt.tujuan_coa
                    from det_jurnal_trans djt
                    right join
                        coa c
                        on
                            djt.sumber_coa = c.coa
                    where
                        djt.id_header = ".$d_jurnalt->id." and
                        djt.sumber_coa in ('".implode("', '", $list_coa_potongan)."') and
                        c.id_perusahaan = '".$params['perusahaan']."'
                    group by
                        djt.id,
                        djt.id_header,
                        djt.nama,
                        djt.sumber,
                        djt.sumber_coa,
                        djt.tujuan,
                        djt.tujuan_coa
                    order by
                        djt.nama asc
                ";
                $d_djt = $m_conf->hydrateRaw( $sql );

                if ( $d_djt->count() > 0 ) {
                    $d_djt = $d_djt->toArray();

                    foreach ($d_djt as $key => $value) {
                        $data[] = $value;
                    }
                }
            }
        }

        $content['data'] = $data;
        $html = $this->load->view('pembayaran/realisasi_pembayaran/modal_potongan', $content, true);

        echo $html;
    }

    public function tes()
    {
        $selisih_umur = abs(selisihTanggal('2022-01-12', '2021-12-11'));

        cetak_r( $selisih_umur );
    }
}