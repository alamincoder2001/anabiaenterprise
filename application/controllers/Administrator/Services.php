<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Services extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->sbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
         if($access == '' ){
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
        $this->load->model('SMS_model', 'sms', true);
    }
    
    public function index($id=null)  {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $this->cart->destroy();
        $this->session->unset_userdata('cheque');
        $data['title'] = "Product Services";
        $invoice = $this->mt->generateServicesInvoice();
        $data['salesId'] = $id;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/services/add_services', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function s_record(){
         $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Services Invoice"; 
        $data['content'] = $this->load->view('Administrator/services/s_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function service_record(){
         $data = json_decode($this->input->raw_input_stream);
         $datas['searchtype'] = $data->searchtype;
         $datas['service_startdate'] = $data->service_startdate;
         $datas['service_enddate'] = $data->service_enddate;
         $datas['emp'] = $data->emp;
         $this->load->view('Administrator/services/services_record', $data);
    }

    function service_record_delete(){
        $data = json_decode($this->input->raw_input_stream);
        $id   = $data->id;
        $this->db->query("DELETE FROM tbl_services_master WHERE sv_m_id=?",$data->id);
        $this->db->query("DELETE FROM tbl_services_details WHERE sv_m_id=?",$data->id);
        echo 'deleted';
    }

    public function services_invoice()  {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Services Invoice"; 
        $data['content'] = $this->load->view('Administrator/services/services_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

     public function serviceInvoicePrint($saleId)  {
        $data['title'] = "Service Invoice";
        $data['salesId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/services/services_invoice_print', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    // public function services_invoices(){
    //     $invoices = $this->db->query("SELECT sv_invoice FROM tbl_services_master ORDER BY sv_m_id DESC")->result();
    //     echo json_encode($invoices);
    // }
     
     public function services_invoices(){


        $data = json_decode($this->input->raw_input_stream);
        // print_r($data->salesId);
        // exit;
        $branchId = $this->session->userdata("BRANCHid");

        $clauses = "";
       

        if(isset($data->salesId) && $data->salesId != 0 && $data->salesId != ''){
            $clauses .= " and sv_m_id = '$data->salesId'";
            $saleDetails = $this->db->query("
                select *,p.Product_Name
                from tbl_services_details as svd 
                INNER JOIN tbl_product p 
                ON svd.sv_d_product_id = p.Product_SlNo
                where svd.sv_m_id = ?
            ", $data->salesId)->result();

    
            $res['saleDetails'] = $saleDetails;
        }
        $sales = $this->db->query("
            select 
            svm.*,
            e.*,
            br.Brunch_name
            from tbl_services_master svm
            left join tbl_employee e on e.Employee_SlNo = svm.sv_emp_id
            left join tbl_brunch br on br.brunch_id = svm.sv_branch
            where svm.sv_branch = '$branchId'
            and svm.sv_status = 'yes'
            $clauses
            order by svm.sv_m_id desc
        ")->result();
        
        $res['sales'] = $sales;

        echo json_encode($res);
    }
    

    public function IMEINumberExistCheck(){
        $data = json_decode($this->input->raw_input_stream);
        $count = $this->db->query("SELECT s_d_id FROM tbl_services_details WHERE s_d_imei=?",$data->IMEI)->num_rows();
        echo json_encode($count);
    }
    public function addServices(){
        $res = ['success'=>false, 'message'=>''];
        try{
            $data = json_decode($this->input->raw_input_stream);
            $invoice = $data->sales->invoiceNo;

            $sales = array(
                'sv_invoice' => $invoice,
                'sv_emp_id' => $data->sales->employeeId,
                'sv_date' => $data->sales->salesDate,
                'sv_total_sale_amount' => $data->sales->total,
                'sv_sale_total_discount' => $data->sales->discount,
                'sv_vat_amount' => $data->sales->vat,
                'sv_transport_cost' => $data->sales->transportCost,
                'sv_sale_subtotal' => $data->sales->subTotal,
                'sv_paid_amount' => $data->sales->paid,
                'sv_sale_due' => $data->sales->due,
                'sv_desc' => $data->sales->note,
                'sv_status' => 'yes',
                'sv_branch' => $this->session->userdata("BRANCHid"),
                "service_by" => $this->session->userdata("FullName")
            );
            
            $this->db->insert('tbl_services_master', $sales);
            
            $serviseId = $this->db->insert_id();
            
            foreach($data->cart as $cartProduct){


                $saleDetails = array(
                    'sv_m_id' => $serviseId,
                    's_d_imei' => $cartProduct->IMEI,
                    'sv_d_product_id' => $cartProduct->Product_SlNo,
                    'sv_d_total' => $cartProduct->total,
                    'sv_d_desc'=>$cartProduct->desc,
                    'sv_d_customer_name'=>$cartProduct->c_name,
                    'sv_d_customer_address'=>$cartProduct->c_address
                );
                $this->db->insert('tbl_services_details', $saleDetails);
                $salesDetailsId = $this->db->insert_id();
            }
            $res = ['success'=>true, 'message'=>'Service Add Success', 'serviseId'=>$serviseId];

        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }


    public function update_services(){
         $res = ['success'=>false, 'message'=>''];
        try{
            $data = json_decode($this->input->raw_input_stream);
            $invoice = $data->sales->invoiceNo;

            $sales = array(
                // 'sv_invoice' => $invoice,
                'sv_emp_id' => $data->sales->employeeId,
                // 'sv_date' => $data->sales->salesDate,
                'sv_total_sale_amount' => $data->sales->total,
                'sv_sale_total_discount' => $data->sales->discount,
                'sv_vat_amount' => $data->sales->vat,
                'sv_transport_cost' => $data->sales->transportCost,
                'sv_sale_subtotal' => $data->sales->subTotal,
                'sv_paid_amount' => $data->sales->paid,
                'sv_sale_due' => $data->sales->due,
                'sv_desc' => $data->sales->note,
                'sv_branch' => $this->session->userdata("BRANCHid")
            );
            
            $this->db->where('sv_m_id', $data->sales->salesId);
            $this->db->update('tbl_services_master', $sales);
            
            $this->db->query("DELETE FROM tbl_services_details WHERE sv_m_id=?",$data->sales->salesId);
            foreach($data->cart as $cartProduct){


                $saleDetails = array(
                    'sv_m_id'=>$data->sales->salesId,
                    's_d_imei' => $cartProduct->IMEI,
                    'sv_d_product_id' => $cartProduct->Product_SlNo,
                    'sv_d_total' => $cartProduct->total,
                    'sv_d_desc'=>$cartProduct->desc,
                    'sv_d_customer_name'=>$cartProduct->c_name,
                    'sv_d_customer_address'=>$cartProduct->c_address
                );
                $this->db->insert('tbl_services_details', $saleDetails);
                $salesDetailsId = $this->db->insert_id();
            }
            $res = ['success'=>true, 'message'=>'Service update Success', 'serviseId'=>$data->sales->salesId];

        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }

    
    
}