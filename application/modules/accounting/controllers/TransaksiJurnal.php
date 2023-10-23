<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TransaksiJurnal extends Public_Controller
{
    private $pathView = 'accounting/transaksi_jurnal/';
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
				'assets/accounting/transaksi_jurnal/js/transaksi-jurnal.js'
			));
			$this->add_external_css(array(
				'assets/select2/css/select2.min.css',
				'assets/accounting/transaksi_jurnal/css/transaksi-jurnal.css'
			));

			$data = $this->includes;

			$data['title_menu'] = 'Transaksi Jurnal';

			$content['add_form'] = $this->add_form();
            $content['riwayat'] = $this->riwayat();

			$content['akses'] = $this->hakAkses;
			$data['view'] = $this->load->view($this->pathView . 'index', $content, true);

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
            $html = $this->detail_form($id);
        } else if ( !empty($id) && $edit == 'edit' ) {
            // NOTE: edit data BASTTB (ajax)
            $html = $this->edit_form($id);
        }else{
            $html = $this->add_form();
        }

        echo $html;
    }

    public function getDataCoa()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select * from coa c
            order by
                c.coa asc,
                c.nama_coa asc
        ";
        $d_coa = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_coa->count() > 0 ) {
            $data = $d_coa->toArray();
        }

        return $data;
    }

    public function riwayat()
    {
        $data = null;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select max(id) as id, kode from jurnal_trans group by kode
        ";
        $d_jt_id = $m_conf->hydrateRaw( $sql );

        if ( $d_jt_id->count() > 0 ) {
            $d_jt_id = $d_jt_id->toArray();

            $id = null;
            foreach ($d_jt_id as $key => $value) {
                $id[] = $value['id'];
            }

            $m_jt = new \Model\Storage\JurnalTrans_model();
            $d_jt = $m_jt->whereIn('id', $id)->orderBy('nama', 'asc')->with(['detail', 'sumber_tujuan'])->get();

            if ( $d_jt->count() > 0 ) {
                $data = $d_jt->toArray();
            }
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'riwayat', $content, true);

        return $html;
    }

	public function add_form()
	{
        $content['coa'] = $this->getDataCoa();
		$html = $this->load->view($this->pathView . 'add_form', $content, true);

		return $html;
	}

    public function detail_form($id)
    {
        $m_jt = new \Model\Storage\JurnalTrans_model();
        $d_jt = $m_jt->where('id', $id)->orderBy('nama', 'asc')->with(['detail', 'sumber_tujuan'])->first();

        $data = null;
        if ( $d_jt ) {
            $d_jt = $d_jt->toArray();

            $data = $d_jt;
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'detail_form', $content, true);

        return $html;
    }

    public function edit_form($id)
    {
        $m_jt = new \Model\Storage\JurnalTrans_model();
        $d_jt = $m_jt->where('id', $id)->orderBy('nama', 'asc')->with(['detail', 'sumber_tujuan'])->first();

        $data = null;
        if ( $d_jt ) {
            $d_jt = $d_jt->toArray();

            $data = $d_jt;
        }

        $content['coa'] = $this->getDataCoa();
        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'edit_form', $content, true);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_jt = new \Model\Storage\JurnalTrans_model();
            $kode = $m_jt->getNextId();

            $m_jt->kode = $kode;
            $m_jt->nama = $params['nama'];
            $m_jt->unit = $params['peruntukan'];
            $m_jt->mstatus = 1;
            $m_jt->save();

            $id = $m_jt->id;
            foreach ($params['detail'] as $k_det => $v_det) {
                $m_djt = new \Model\Storage\DetJurnalTrans_model();
                $m_djt->id_header = $id;
                $m_djt->nama = $v_det['nama'];
                $m_djt->sumber = $v_det['sumber'];
                $m_djt->sumber_coa = $v_det['sumber_coa'];
                $m_djt->tujuan = $v_det['tujuan'];
                $m_djt->tujuan_coa = $v_det['tujuan_coa'];
                $m_djt->submit_periode = $v_det['submit_periode'];
                $m_djt->save();
            }

            // if ( isset($params['sumber_tujuan']) && !empty($params['sumber_tujuan']) ) {
            //     foreach ($params['sumber_tujuan'] as $k_det => $v_det) {
            //         $m_jtst = new \Model\Storage\JurnalTransSumberTujuan_model();
            //         $m_jtst->id_header = $id;
            //         $m_jtst->nama = $v_det['nama'];
            //         $m_jtst->save();
            //     }
            // }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jt, $deskripsi_log);

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

            $m_jt = new \Model\Storage\JurnalTrans_model();
            $d_jt = $m_jt->where('id', $id)->first();

            $m_jt->where('id', $id)->update(
                array(
                    'mstatus' => 0
                )
            );

            $m_jt = new \Model\Storage\JurnalTrans_model();
            $m_jt->kode = $d_jt->kode;
            $m_jt->nama = $params['nama'];
            $m_jt->unit = $params['peruntukan'];
            $m_jt->mstatus = 1;
            $m_jt->save();

            $id = $m_jt->id;
            foreach ($params['detail'] as $k_det => $v_det) {
                $m_djt = new \Model\Storage\DetJurnalTrans_model();
                $m_djt->id_header = $id;
                $m_djt->nama = $v_det['nama'];
                $m_djt->sumber = $v_det['sumber'];
                $m_djt->sumber_coa = $v_det['sumber_coa'];
                $m_djt->tujuan = $v_det['tujuan'];
                $m_djt->tujuan_coa = $v_det['tujuan_coa'];
                $m_djt->submit_periode = $v_det['submit_periode'];
                $m_djt->save();
            }

            // $m_jtst = new \Model\Storage\JurnalTransSumberTujuan_model();
            // $m_jtst->where('id_header', $id)->delete();

            // if ( isset($params['sumber_tujuan']) && !empty($params['sumber_tujuan']) ) {
            //     foreach ($params['sumber_tujuan'] as $k_det => $v_det) {
            //         $m_jtst = new \Model\Storage\JurnalTransSumberTujuan_model();
            //         $m_jtst->id_header = $id;
            //         $m_jtst->nama = $v_det['nama'];
            //         $m_jtst->save();
            //     }
            // }

            $d_jt = $m_jt->where('id', $id)->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jt, $deskripsi_log);

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

            $m_jt = new \Model\Storage\JurnalTrans_model();
            $m_jt->where('id', $id)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_jt = $m_jt->where('id', $id)->first();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_jt, $deskripsi_log);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $m_jt = new \Model\Storage\JurnalTrans_model();
        $kode = $m_jt->getNextId();

        cetak_r( $kode );
    }
}