<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assets extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        $this->accountType = $this->session->userdata('accountType');
         if($access == ''){
            redirect("Login");
        }  
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
    }


    public function index()  {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Assets Entry";
        $data['assets'] = $this->Other_model->get_all_asset_info();
        $data['content'] = $this->load->view('Administrator/assets/assets_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function insert_Assets()  {
        $data = array(
            "as_date"       =>date('Y-m-d'),
            "as_name"       =>$this->input->post('assetsname'),
            "as_qty"        =>$this->input->post('qty'),
            "as_rate"       =>$this->input->post('rate'),
            "as_amount"     =>$this->input->post('amount'),
            "status"        =>'a',
            "AddBy"         =>$this->session->userdata("FullName"),
            "AddTime"       =>date("Y-m-d H:i:s"),
            "branchid"      =>$this->session->userdata('BRANCHid'),
        );
        $this->mt->save_data('tbl_assets',$data);
        echo json_encode(TRUE); 
    }


    public function Assets_edit($id=null)
    {
        $data['edit'] = $this->db->where('as_id',$id)->get('tbl_assets')->row();
        $this->load->view('Administrator/assets/edit_assets', $data);
    }


    public function Update_Assets($id=null)
    {
        $data = array(
            "as_name"       =>$this->input->post('assetsname'),
            "as_qty"        =>$this->input->post('qty'),
            "as_rate"       =>$this->input->post('rate'),
            "as_amount"     =>$this->input->post('amount')
        );
        $up = $this->db->where('as_id',$id)->update('tbl_assets',$data );
        if($up):
            echo json_encode(TRUE); 
        else: return false; endif;
    }


    public function Assets_delete($id=null)
    {
        $data = array( 'status' => 'd' );
        $up = $this->db->where('as_id',$id)->update('tbl_assets',$data );
        if($up):
            echo json_encode(TRUE); 
        else: return false; endif;
    }





}
