<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
    }

    public function index()
    {

        redirect("Administrator/Purchase/order");
    }

    public function getPurchases()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata('BRANCHid');

        $dateClause = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $dateClause = " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $purchaseIdClause = "";
        if (isset($data->purchaseId) && $data->purchaseId != null) {
            $purchaseIdClause = " and pm.PurchaseMaster_SlNo = '$data->purchaseId'";

            $res['purchaseDetails']   = $this->db->query("
                select
                    pd.*,
                    p.Product_Name,
                    p.Product_Code,
                    p.ProductCategory_ID,
                    p.Product_SellingPrice,
                    p.is_imei,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_purchasedetails pd 
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where pd.PurchaseMaster_IDNo = '$data->purchaseId'
            ")->result();
        }
        $purchases = $this->db->query("
            select
            pm.*,
            s.Supplier_Name,
            s.Supplier_Mobile,
            s.Supplier_Email,
            s.Supplier_Code,
            s.Supplier_Address,
            s.Supplier_Type
            from tbl_purchasemaster pm
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pm.PurchaseMaster_BranchID = '$branchId' 
            and pm.status = 'a'
            $purchaseIdClause $dateClause
            order by pm.PurchaseMaster_SlNo desc
        ")->result();



        if (isset($data->purchaseId) && $data->purchaseId != null) {
            $res['purchaseDetails'] = array_map(function ($purchase) {
                $purchase->imei = $this->db->query("SELECT * FROM tbl_product_serial_numbers WHERE purchase_details_id =?  ", $purchase->PurchaseDetails_SlNo)->result();
                return $purchase;
            },  $res['purchaseDetails']);
        }

        $res['purchases'] = $purchases;
        echo json_encode($res);
    }

    public function getPurchaseDetailsForReturn()
    {
        $data = json_decode($this->input->raw_input_stream);
        $purchaseDetails = $this->db->query("
            select
            pd.*,
            pd.PurchaseDetails_Rate as return_rate,
            p.Product_Name,
            pc.ProductCategory_Name,
            ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0.00) as returned_quantity,
            ifnull(sum(prd.PurchaseReturnDetails_ReturnAmount), 0.00) as returned_amount
            from tbl_purchasedetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            left join tbl_purchasereturn pr on pr.PurchaseMaster_InvoiceNo = pm.PurchaseMaster_InvoiceNo
            left join tbl_purchasereturndetails prd on prd.PurchaseReturn_SlNo = pr.PurchaseReturn_SlNo
            where pm.PurchaseMaster_SlNo = ?
            group by pd.Product_IDNo
        ", $data->purchaseId)->result();

        echo json_encode($purchaseDetails);
    }

    public function addPurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchaseReturn = array(
                'PurchaseMaster_InvoiceNo' => $data->invoice->PurchaseMaster_InvoiceNo,
                'Supplier_IDdNo' => $data->invoice->Supplier_SlNo,
                'PurchaseReturn_ReturnDate' => $data->purchaseReturn->returnDate,
                'PurchaseReturn_ReturnAmount' => $data->purchaseReturn->total,
                'PurchaseReturn_Description' => $data->purchaseReturn->note,
                'Status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'PurchaseReturn_brunchID' => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_purchasereturn', $purchaseReturn);
            $purchaseReturnId = $this->db->insert_id();

            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'PurchaseReturn_SlNo' => $purchaseReturnId,
                    'PurchaseReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'PurchaseReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'PurchaseReturnDetails_ReturnAmount' => $product->return_amount,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'PurchaseReturnDetails_brachid' => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasereturndetails', $returnDetails);

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }
            $res = ['success' => true, 'message' => 'Purchase return success'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getPurchaseReturnDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pr.PurchaseReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pr.Supplier_IDdNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and prd.PurchaseReturnDetailsProduct_SlNo = '$data->productId'";
        }

        $returnDetails = $this->db->query("
            select 
                prd.*,
                p.Product_Code,
                p.Product_Name,
                pr.PurchaseMaster_InvoiceNo,
                pr.PurchaseReturn_ReturnDate,
                pr.Supplier_IDdNo,
                pr.PurchaseReturn_Description,
                s.Supplier_Code,
                s.Supplier_Name
            from tbl_purchasereturndetails prd
            join tbl_product p on p.Product_SlNo = prd.PurchaseReturnDetailsProduct_SlNo
            join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
            left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where pr.PurchaseReturn_brunchID = ?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($returnDetails);
    }

    public function order()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Order";

        $invoice = $this->mt->generatePurchaseInvoice();

        $data['purchaseId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseEdit($purchaseId)
    {
        $data['title'] = "Purchase Order";
        $data['purchaseId'] = $purchaseId;
        $data['invoice'] = $this->db->query("select PurchaseMaster_InvoiceNo from tbl_purchasemaster where PurchaseMaster_SlNo = ?", $purchaseId)->row()->PurchaseMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseExcel()
    {
        $this->cart->destroy();
        $data['title'] = "Purchase Order";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order_excel', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function createProductSheet()
    {
        $this->cart->destroy();
        $data['title'] = "Create Product Sheet";
        $data['content'] = $this->load->view('Administrator/purchase/product_sheet', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function excelFileFormate()
    {
        $data['title'] = "Purchase Order";
        $data['content'] = $this->load->view('Administrator/purchase/excel_file_foramate', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function returns()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function returnsIemi()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_imei_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchase_imei_return()
    {
        $data = json_decode($this->input->raw_input_stream);

        $datas = array(
            'ps_p_r_status' => 'yes',
            'ps_p_r_amount' => $data->return_amout
        );
        $this->db->where('ps_imei_number', $data->imei_number);
        $this->db->update('tbl_product_serial_numbers', $datas);
        $result = $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [1, $data->prod_id, $this->session->userdata('BRANCHid')]);

        if ($result) {
            echo 'return';
        }
    }



    public function damage_entry()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Damage Entry";
        $data['damageCode'] = $this->mt->generateDamageCode();
        $data['content'] = $this->load->view('Administrator/purchase/damage_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function stock()
    {
        $data['title'] = "Purchase Stock List";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_stock_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function Selectsuplier()
    {


        $sid = $this->input->post('sid');
        $query = $this->db->query("SELECT * FROM tbl_supplier where Supplier_SlNo = '$sid'");
        $data['Supplier'] = $query->row();
        $this->load->view('Administrator/purchase/ajax_suplier', $data);
    }

    function SelectPruduct()
    {
        $ProID = $this->input->post('ProID');
        $querys = $this->db->query("
            SELECT 
            tbl_product.*,
            tbl_unit.*, 
            tbl_brand.*  
            FROM tbl_product
            left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID
            left join tbl_brand on tbl_brand.brand_SiNo=tbl_product.brand
            where tbl_product.Product_SlNo = '$ProID'
        ");

        $data['Product'] = $querys->row();
        $this->load->view('Administrator/purchase/ajax_product', $data);
    }

    function get_return_imei_number()
    {
        $imei_number = $this->input->post('imei_number');
        $get_ps_detail = $this->db->query("SELECT * FROM tbl_product_serial_numbers WHERE ps_imei_number='$imei_number'")->result_array();
        $ps_prod_id = $get_ps_detail[0]['ps_prod_id'];
        $ps_purchase_inv = $get_ps_detail[0]['ps_purchase_inv'];
        $get_m_detail =  $this->db->query("SELECT * FROM tbl_purchasemaster WHERE  PurchaseMaster_InvoiceNo='$ps_purchase_inv'")->result_array();

        $PurchaseMaster_IDNo = $get_m_detail[0]['PurchaseMaster_SlNo'];
        $imei_detail = $this->db->query("select ps.*,pd.PurchaseDetails_Rate,p.Product_Name,p.Product_SlNo
        FROM tbl_product_serial_numbers as ps 
        INNER JOIN tbl_purchasedetails as pd
        ON pd.Product_IDNo='$ps_prod_id' AND pd.PurchaseMaster_IDNo ='$PurchaseMaster_IDNo'
        INNER JOIN tbl_product as p 
        ON p.Product_SlNo ='$ps_prod_id'
        WHERE  ps.ps_imei_number ='$imei_number'")->result();

        $html = '<table class="table table-bordered">
                 <tr height="30">
                <th>IMEI Number</th>
                <th>Product Name</th>
                 <th>Purchase Rate</th>
                 <th>Return Amount</th>
                 <th>Action</th>
                 ';
        foreach ($imei_detail as $key => $value) {

            $html .= '</tr>
                     <tr>
                     <td style="text-align: center;">' . $value->ps_imei_number . '</td>
                     <td style="text-align: center;">' . $value->Product_Name . '</td>
                     <td style="text-align: center;">' . $value->PurchaseDetails_Rate . '</td>
                     <td style="text-align: center;">' . $value->PurchaseDetails_Rate . '</td>
                     <td style="display: flex;align-content: center;justify-content: center;"><button style="width: 118px;padding: 7px;margin: 2px;border: navajowhite;" class="btn btn-success btn-sm" id="parchase_return_btn" data-prod_id="' . $value->Product_SlNo . '" data-id="' . $value->ps_imei_number . '" data-amount="' . $value->PurchaseDetails_Rate . '">Purchase Return</button></td>
                 </tr>';
        }
        $html .= '</table>';
        echo $html;
    }

    function SelectCat()
    {
        $data['ProCat'] = $this->input->post('ProCat');
        $this->load->view('Administrator/purchase/ajax_CatWiseProduct', $data);
    }


    function delete_imeis()
    {
        $data = json_decode($this->input->raw_input_stream);
        foreach ($data->product as $key => $value) {

            $this->db->query("delete from tbl_product_serial_numbers where ps_imei_number = ?", $value->imeiNumber);
        }
    }
    public function addPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->purchase->invoice;
            $invoiceCount = $this->db->query("select * from tbl_purchasemaster where PurchaseMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generatePurchaseInvoice();
            }

            $supplierId = $data->purchase->supplierId;
            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);
                $supplier['Supplier_Code'] = $this->mt->generateSupplierCode();
                $supplier['Status'] = 'a';
                $supplier['AddBy'] = $this->session->userdata("FullName");
                $supplier['AddTime'] = date('Y-m-d H:i:s');
                $supplier['Supplier_brinchid'] = $this->session->userdata('BRANCHid');

                $this->db->insert('tbl_supplier', $supplier);
                $supplierId = $this->db->insert_id();
            }

            $purchase = array(
                'Supplier_SlNo' => $supplierId,
                'PurchaseMaster_InvoiceNo' => $invoice,
                'PurchaseMaster_OrderDate' => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor' => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_PaidAmount' => $data->purchase->paid,
                'PurchaseMaster_DueAmount' => $data->purchase->due,
                'previous_due' => $data->purchase->previousDue,
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'PurchaseMaster_BranchID' => $this->session->userdata('BRANCHid'),
                'reference' => $data->purchase->reference
            );

            $this->db->insert('tbl_purchasemaster', $purchase);
            $purchaseId = $this->db->insert_id();
            $i = 0;
            foreach ($data->cartProducts as $product) {
                $i++;
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo' => $purchaseId,
                    'Product_IDNo' => $product->productId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate' => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount' => $product->total,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID' => $this->session->userdata('BRANCHid'),
                    'PurchaseDetails_Discount' => $product->discount

                );

                $this->db->insert('tbl_purchasedetails', $purchaseDetails);
                $purchase_d_id = $this->db->insert_id();

                foreach ($product->IMEICartStore as $imeikey => $imeivalue) {

                    $rate = $imeivalue->purchaseRate;
                    $dis_per = $imeivalue->discount / 100;
                    $total_dis = $rate - ($rate * $dis_per);

                    $data = array(
                        'ps_prod_id' => $imeivalue->productId,
                        'ps_imei_number' => $imeivalue->imeiNumber,
                        'ps_purchase_inv' => $invoice,
                        'ps_purchase_supp_id' => $supplierId,
                        'ps_p_status' => 'yes',
                        'ps_brunch_id' => $this->session->userdata('BRANCHid'),
                        'ps_status' => 'a',
                        'purchase_details_id' => $purchase_d_id,
                        'purchase_rate' => $imeivalue->purchaseRate,
                        'purchase_discount' => $imeivalue->discount,
                        'purchase_total' => $total_dis,
                        'purchase_date' => date('Y-m-d')
                    );
                    $this->db->insert("tbl_product_serial_numbers", $data);
                }



                $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                if ($inventoryCount == 0) {
                    $inventory = array(
                        'product_id' => $product->productId,
                        'purchase_quantity' => $product->quantity,
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity + ? 
                        where product_id = ? 
                        and branch_id = ?
                    ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                }

                $this->db->query("update tbl_product set Product_Purchase_Rate = ?, Product_SellingPrice = ? where Product_SlNo = ?", [$product->purchaseRate, $product->salesRate, $product->productId]);

                /// 


            }


            $res = ['success' => true, 'message' => 'Purchase Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updatePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchaseId = $data->purchase->purchaseId;

            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);
                $supplier['UpdateBy'] = $this->session->userdata("FullName");
                $supplier['UpdateTime'] = date('Y-m-d H:i:s');

                $this->db->where('Supplier_SlNo', $data->purchase->supplierId)->update('tbl_supplier', $supplier);
            }

            $purchase = array(
                'Supplier_SlNo' => $data->purchase->supplierId,
                'PurchaseMaster_InvoiceNo' => $data->purchase->invoice,
                'PurchaseMaster_OrderDate' => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor' => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_PaidAmount' => $data->purchase->paid,
                'PurchaseMaster_DueAmount' => $data->purchase->due,
                'previous_due' => $data->purchase->previousDue,
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'a',
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'PurchaseMaster_BranchID' => $this->session->userdata('BRANCHid'),
                'reference' => $data->purchase->reference
            );
            $this->db->where('PurchaseMaster_SlNo', $purchaseId);
            $this->db->update('tbl_purchasemaster', $purchase);

            $oldPurchaseDetails = $this->db->query("select * from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId)->result();
            $this->db->query("delete from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId);

            foreach ($oldPurchaseDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_quantity = purchase_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }
            $getInvo =  $this->db->query("SELECT PurchaseMaster_InvoiceNo FROM tbl_purchasemaster WHERE PurchaseMaster_SlNo='$purchaseId'")->result_array();
            $purchase_inv = $getInvo[0]['PurchaseMaster_InvoiceNo'];
            //  $this->db->query("delete from tbl_product_serial_numbers where ps_purchase_inv = ?", $purchase_inv);


            foreach ($data->cartProducts as $product) {

                $purchaseDetails = array(
                    'PurchaseMaster_IDNo' => $purchaseId,
                    'Product_IDNo' => $product->productId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate' => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount' => $product->total,
                    'Status' => 'a',
                    'UpdateBy' => $this->session->userdata("FullName"),
                    'UpdateTime' => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID' => $this->session->userdata('BRANCHid'),
                    'PurchaseDetails_Discount' => $product->discount
                );
                $this->db->insert('tbl_purchasedetails', $purchaseDetails);
                $purchase_d_id = $this->db->insert_id();

                foreach ($product->IMEICartStore as $imeikey => $imeivalue) {
                    $checkAvaImei = $this->db->query('select * from tbl_product_serial_numbers where 
            ps_purchase_inv=? and ps_imei_number=? and ps_prod_id=?', [$purchase_inv, $imeivalue->imeiNumber, $imeivalue->productId])->num_rows();
                    if ($checkAvaImei < 1) {
                        $rate = $imeivalue->purchaseRate;
                        $dis_per = $imeivalue->discount / 100;
                        $total_dis = $rate - ($rate * $dis_per);
                        $data = array(
                            'ps_prod_id' => $imeivalue->productId,
                            'ps_imei_number' => $imeivalue->imeiNumber,
                            'ps_purchase_inv' => $purchase_inv,
                            'ps_p_status' => 'yes',
                            'ps_brunch_id' => $this->session->userdata('BRANCHid'),
                            'ps_status' => 'a',
                            'purchase_details_id' => $purchase_d_id,
                            'purchase_rate' => $imeivalue->purchaseRate,
                            'purchase_discount' => $imeivalue->discount,
                            'purchase_total' => $total_dis,
                            'purchase_date' => date('Y-m-d')
                        );
                        $this->db->insert("tbl_product_serial_numbers", $data);
                    } else {
                        $this->db->query('update tbl_product_serial_numbers SET  purchase_details_id=? WHERE ps_purchase_inv=? and ps_imei_number=? and ps_prod_id=? ', [$purchase_d_id, $purchase_inv, $imeivalue->imeiNumber, $imeivalue->productId]);
                    }
                }


                $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                if ($inventoryCount == 0) {
                    $inventory = array(
                        'product_id' => $product->productId,
                        'purchase_quantity' => $product->quantity,
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                }
            }






            $res = ['success' => true, 'message' => 'Purchase Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function check_soldIMEI()
    {
        $data = json_decode($this->input->raw_input_stream);
        $ava = $this->db->query("SELECT * FROM tbl_product_serial_numbers WHERE ps_purchase_inv=? and ps_prod_id=? and ps_s_status='yes' AND  ps_s_r_status='no' AND ps_p_r_status='no' ", [$data->invoice, $data->prodId])->num_rows();
        if ($ava > 0) {
            echo json_encode('yes');
        } else {
            echo json_encode('no');
        }
    }

    function PurchasereturnSearch()
    {
        $invoice = $this->input->post('invoiceno');
        $sql = $this->db->query("SELECT * FROM tbl_purchasemaster WHERE PurchaseMaster_SlNo = '$invoice'");
        $row = $sql->row();
        $data['proID'] = $row->PurchaseMaster_SlNo;
        $data['proinv'] = $row->PurchaseMaster_InvoiceNo;
        $da['store'] = $row->PurchaseMaster_PurchaseFor;
        $this->session->set_userdata($da);
        $this->load->view('Administrator/purchase/purchase_return_list', $data);
    }

    function PurchaseSotckChack()
    {
        $productsID = $this->input->post('productsID');
        $PurchaseFrom = $this->input->post('PurchaseFrom');
        $totalarray = sizeof($productsID);
        for ($j = 0; $j < $totalarray; $j++) {
            $pid = $productsID[$j];
            $PurchaseFrom = $PurchaseFrom[$j];
            $branchID = $this->session->userdata("BRANCHid");

            $sql = mysql_query("SELECT * FROM tbl_purchaseinventory WHERE PurchaseInventory_Store='$PurchaseFrom' AND purchProduct_IDNo = '$pid'  AND PurchaseInventory_brunchid = '$branchID'");
            $stock = "";
            while ($orw = mysql_fetch_array($sql)) {
                $stock = $orw['PurchaseInventory_TotalQuantity'];
            }
            $sqll = mysql_query("SELECT * FROM tbl_saleinventory WHERE SaleInventory_Store='$PurchaseFrom' AND sellProduct_IdNo = '$pid' AND SaleInventory_brunchid = '$branchID'");
            $rox = mysql_fetch_array($sqll);
            $curentstock = $stock - $rox['SaleInventory_TotalQuantity'];
            $curentstock += $rox['SaleInventory_ReturnQuantity'];
            $sqlss = mysql_query("SELECT * FROM tbl_purchaseinventory WHERE PurchaseInventory_Store = '$PurchaseFrom' AND purchProduct_IDNo = '$pid' AND PurchaseInventory_brunchid = '$branchID'");
            $roxx = mysql_fetch_array($sqlss);
            $returnQty = $roxx['PurchaseInventory_ReturnQuantity'];
            $damageQty = $roxx['PurchaseInventory_DamageQuantity'];
            $curentstock = $curentstock - $returnQty;
            echo $curentstock = $curentstock - $damageQty;
        }
    }

    function PurchaseReturnInsert()
    {
        $returnqty = $this->input->post('returnqty');
        $returnamount = $this->input->post('returnamount');
        $return_date = $this->input->post('return_date');
        $productID = $this->input->post('productID');
        $invoices = $this->input->post('invoice');
        $supplier_id = $this->input->post('Supplier_No');
        $totalQty = "";
        $RAmount = "";
        $totalarray = sizeof($returnqty);
        for ($j = 0; $j < $totalarray; $j++) {
            $rqtys = $this->input->post('returnqty');
            $totalQty = $rqtys[$j] + $totalQty;
            $ramounts = $this->input->post('returnamount');
            $RAmount = $ramounts[$j] + $RAmount;
        }

        $sqlll = $this->db->query("SELECT * FROM tbl_purchasereturn where PurchaseMaster_InvoiceNo = '$invoices'");
        $ros = $sqlll->row();
        $iid = $ros->PurchaseReturn_SlNo;
        $ivo = $ros->PurchaseMaster_InvoiceNo;

        $totalqt = $ros->PurchaseReturn_ReturnQuantity;
        $totalamou = $ros->PurchaseReturn_ReturnAmount;
        $fld = 'PurchaseReturn_SlNo';

        if ($ivo > 0) {
            $return = array(
                "PurchaseMaster_InvoiceNo" => $this->input->post('invoice'),
                "PurchaseReturn_ReturnDate" => $this->input->post('return_date'),
                "PurchaseReturn_ReturnQuantity" => $totalQty + $totalqt,
                "PurchaseReturn_ReturnAmount" => $RAmount + $totalamou,
                "PurchaseReturn_Description" => $this->input->post('Notes'),
                "Supplier_IDdNo" => $this->input->post('Supplier_No'),

                "AddBy" => $this->session->userdata("FullName"),
                "PurchaseReturn_brunchID" => $this->session->userdata("BRANCHid"),
                "AddTime" => date("Y-m-d H:i:s")
            );
            $return_id = $this->mt->update_data('tbl_purchasereturn', $return, $iid, $fld);
            for ($i = 0; $i < $totalarray; $i++) {
                $returnqtyss = $this->input->post('returnqty');
                $returnamounts = $this->input->post('returnamount');
                $productIDs = $this->input->post('productID');

                $productsCodes = $this->input->post('productsCodes');
                $productsCodes = $productsCodes[$i];
                $packnames = $this->input->post('packname');
                $packnames = $packnames[$i];
                $productsName = $this->input->post('productsName');
                $productsName = $productsName[$i];
                if ($packnames == $productsName) {
                    $sqj = $this->db->query("SELECT * FROM tbl_package_create WHERE create_pacageID ='" . $productsCodes . "'");
                    $romio = $sqj->result();
                    foreach ($romio as $romio) {

                        $sqn = $this->db->query("SELECT * FROM tbl_product WHERE Product_Code = '" . $romio->create_proCode . "'");
                        $ron = $sqn->row();
                        //cteate_qty
                        $returns = array(
                            "PurchaseReturn_SlNo" => $iid,
                            "PurchaseReturnDetails_ReturnDate" => $this->input->post('return_date'),
                            "PurchaseReturnDetailsProduct_SlNo" => $ron->Product_SlNo, //$productIDs[$i]
                            "PurchaseReturnDetails_ReturnQuantity" => $returnqtyss[$i] * $romio->cteate_qty,
                            "PurchaseReturnDetails_pacQty" => $returnqtyss[$i],
                            "PurchaseReturnDetails_ReturnAmount" => $returnamounts[$i],

                            "AddBy" => $this->session->userdata("FullName"),
                            "PurchaseReturnDetails_brachid" => $this->session->userdata("BRANCHid"),
                            "AddTime" => date("Y-m-d H:i:s")
                        );
                        $this->Billing_model->SalesReturn('tbl_purchasereturndetails', $returns);
                    }
                } else {
                    if ($returnqtyss[$i] != 0 and $returnamounts[$i] != 0) {
                        $returns = array(
                            "PurchaseReturn_SlNo" => $iid,
                            "PurchaseReturnDetails_ReturnDate" => $this->input->post('return_date'),
                            "PurchaseReturnDetailsProduct_SlNo" => $productIDs[$i],
                            "PurchaseReturnDetails_ReturnQuantity" => $returnqtyss[$i],
                            "PurchaseReturnDetails_ReturnAmount" => $returnamounts[$i],

                            "AddBy" => $this->session->userdata("FullName"),
                            "PurchaseReturnDetails_brachid" => $this->session->userdata("BRANCHid"),
                            "AddTime" => date("Y-m-d H:i:s")
                        );
                        $this->Billing_model->SalesReturn('tbl_purchasereturndetails', $returns);
                    }
                }
            }
        } else {
            $return = array(
                "PurchaseMaster_InvoiceNo" => $this->input->post('invoice'),
                "PurchaseReturn_ReturnDate" => $this->input->post('return_date'),
                "PurchaseReturn_ReturnQuantity" => $totalQty,
                "PurchaseReturn_ReturnAmount" => $RAmount,
                "PurchaseReturn_Description" => $this->input->post('Notes'),
                "Supplier_IDdNo" => $this->input->post('Supplier_No'),

                "AddBy" => $this->session->userdata("FullName"),
                "PurchaseReturn_brunchID" => $this->session->userdata("BRANCHid"),
                "AddTime" => date("Y-m-d H:i:s")
            );
            $return_id = $this->Billing_model->SalesReturn('tbl_purchasereturn', $return);

            for ($i = 0; $i < $totalarray; $i++) {
                $returnqtyss = $this->input->post('returnqty');
                $returnamounts = $this->input->post('returnamount');
                $productIDs = $this->input->post('productID');

                $productsCodes = $this->input->post('productsCodes');
                $packnames = $this->input->post('packname');
                $packnames = $packnames[$i];
                $productsName = $this->input->post('productsName');
                $productsName = $productsName[$i];
                if ($packnames == $productsName) {
                    $sqj = $this->db->query("SELECT * FROM tbl_package_create WHERE create_pacageID ='" . $productsCodes[$i] . "'");
                    $romio = $sqj->result();
                    foreach ($romio as $romio) {

                        $sqn = $this->db->query("SELECT * FROM tbl_product WHERE Product_Code = '" . $romio->create_proCode . "'");
                        $ron = $sqn->row();
                        //cteate_qty
                        if ($returnqtyss[$i] != 0 and $returnamounts[$i] != 0) {
                            $returns = array(
                                "PurchaseReturn_SlNo" => $return_id,
                                "PurchaseReturnDetails_ReturnDate" => $this->input->post('return_date'),
                                "PurchaseReturnDetailsProduct_SlNo" => $ron->Product_SlNo, //$productIDs[$i]
                                "PurchaseReturnDetails_ReturnQuantity" => $returnqtyss[$i] * $romio->cteate_qty,
                                "PurchaseReturnDetails_pacQty" => $returnqtyss[$i],
                                "PurchaseReturnDetails_ReturnAmount" => $returnamounts[$i],

                                "AddBy" => $this->session->userdata("FullName"),
                                "PurchaseReturnDetails_brachid" => $this->session->userdata("BRANCHid"),
                                "AddTime" => date("Y-m-d H:i:s")
                            );
                            $this->Billing_model->SalesReturn('tbl_purchasereturndetails', $returns);
                        }
                    }
                } else {
                    if ($returnqtyss[$i] != 0 and $returnamounts[$i] != 0) {
                        $returns = array(
                            "PurchaseReturn_SlNo" => $return_id,
                            "PurchaseReturnDetails_ReturnDate" => $this->input->post('return_date'),
                            "PurchaseReturnDetailsProduct_SlNo" => $productIDs[$i],
                            "PurchaseReturnDetails_ReturnQuantity" => $returnqtyss[$i],
                            "PurchaseReturnDetails_ReturnAmount" => $returnamounts[$i],

                            "AddBy" => $this->session->userdata("FullName"),
                            "PurchaseReturnDetails_brachid" => $this->session->userdata("BRANCHid"),
                            "AddTime" => date("Y-m-d H:i:s")
                        );
                        $this->Billing_model->SalesReturn('tbl_purchasereturndetails', $returns);
                        $purchase_return_payment = array(
                            "SPayment_date" => date('Y-m-d'),
                            "SPayment_invoice" => $invoices,
                            "SPayment_customerID" => $supplier_id,
                            "SPayment_TransactionType" => 'RP',
                            "SPayment_notes" => 'Purchase Returns',
                            "SPayment_amount" => $returnamounts[$i],
                            "SPayment_Addby" => $this->session->userdata("FullName"),
                            "SPayment_brunchid" => $this->session->userdata('BRANCHid')
                        );
                        $this->mt->save_data("tbl_supplier_payment", $purchase_return_payment);
                    }
                }
            }
        }
        for ($f = 0; $f < $totalarray; $f++) {
            $productIDs = $this->input->post('productID');
            $rqtyss = $this->input->post('returnqty');
            //------------------------------------------
            $productsCodes = $this->input->post('productsCodes');
            $productsCodes = $productsCodes[$f];
            $packnames = $this->input->post('packname');
            $packnames = $packnames[$f];
            $productsName = $this->input->post('productsName');
            $productsName = $productsName[$f];
            if ($packnames == $productsName) {
                $sqj = $this->db->query("SELECT * FROM tbl_package_create WHERE create_pacageID ='" . $productsCodes . "'");
                $romio = $sqj->result();
                foreach ($romio as $romio) {
                    $store = $this->session->userdata('store');
                    $sqn = $this->db->query("SELECT * FROM tbl_product WHERE Product_Code = '" . $romio->create_proCode . "'");
                    $ron = $sqn->row();
                    //cteate_qty 
                    $sqls = $this->db->query("SELECT * FROM tbl_purchaseinventory WHERE PurchaseInventory_Store='$store' AND  purchProduct_IDNo ='" . $ron->Product_SlNo . "'");
                    $ROW = $sqls->row();
                    $qTys = $romio->cteate_qty * $rqtyss[$f];
                    $id = $ROW->PurchaseInventory_SlNo;
                    $qt = $ROW->PurchaseInventory_ReturnQuantity;
                    $pacKqty = $ROW->PurchaseInventory_returnqty;
                    $fld = "PurchaseInventory_SlNo";
                    $returns = array(
                        "PurchaseInventory_ReturnQuantity" => $qTys + $qt,
                        "PurchaseInventory_returnqty" => $rqtyss[$f] + $pacKqty
                    );
                    $this->mt->update_data('tbl_purchaseinventory', $returns, $id, $fld);
                }
            } else {
                $store = $this->session->userdata('store');
                $sqls = $this->db->query("SELECT * FROM tbl_purchaseinventory WHERE PurchaseInventory_Store='$store' AND purchProduct_IDNo ='" . $productIDs[$f] . "'");
                $ROW = $sqls->row();
                $id = $ROW->PurchaseInventory_SlNo;
                $qt = $ROW->PurchaseInventory_ReturnQuantity;
                $pacKqty = $ROW->PurchaseInventory_returnqty;
                $fld = "PurchaseInventory_SlNo";
                $returns = array(
                    "PurchaseInventory_ReturnQuantity" => $rqtyss[$f] + $qt,
                    "PurchaseInventory_returnqty" => $rqtyss[$f] + $pacKqty
                );
                $this->mt->update_data('tbl_purchaseinventory', $returns, $id, $fld);
            }
        }
        $this->load->view('Administrator/sales/blankpage');
    }

    public function purchase_bill()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Invoice";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_bill', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchase_invoice_search()
    {
        $id = $this->input->post('purchasemsid');
        $data['PurchID'] = $id;
        $this->session->set_userdata('PurchID', $id);
        $data['purchase'] = $this->Purchase_model->single_purchase_master_info($id);
        $data['products'] = $this->Purchase_model->invoice_wise_purchase_products($id);
        $this->load->view('Administrator/purchase/purchase_invoice_search', $data);
    }

    public function purchase_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Record";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getPurchaseRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }
        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and pm.AddBy = '$data->userFullName'";
        }

        $purchases = $this->db->query("
            select 
                pm.*,
                s.Supplier_Code,
                s.Supplier_Name,
                s.Supplier_Mobile,
                s.Supplier_Address,
                br.Brunch_name
            from tbl_purchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_brunch br on br.brunch_id = pm.PurchaseMaster_BranchID
            where pm.PurchaseMaster_BranchID = '$branchId'
            and pm.status = 'a'
            $clauses
        ")->result();

        foreach ($purchases as $purchase) {
            $purchase->purchaseDetails = $this->db->query("
                select 
                    pd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_purchasedetails pd
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where pd.PurchaseMaster_IDNo = ?
                and pd.Status != 'd'
            ", $purchase->PurchaseMaster_SlNo)->result();
        }

        echo json_encode($purchases);
    }

    public function getPurchaseDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and s.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
            select 
                pd.*,
                p.Product_Name,
                pc.ProductCategory_Name,
                pm.PurchaseMaster_InvoiceNo,
                pm.PurchaseMaster_OrderDate,
                pm.reference,
                s.Supplier_Code,
                s.Supplier_Name
            from tbl_purchasedetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.Status != 'd' and pm.PurchaseMaster_BranchID=?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($saleDetails);
    }

    /*Delete Purchase Record*/
    public function  deletePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchase = $this->db->select('*')->where('PurchaseMaster_SlNo', $data->purchaseId)->get('tbl_purchasemaster')->row();
            if ($purchase->status != 'a') {
                $res = ['success' => false, 'message' => 'Purchase not found'];
                echo json_encode($res);
                exit;
            }

            /*Get Purchase Details Data*/
            $purchaseDetails = $this->db->select('Product_IDNo,PurchaseDetails_TotalQuantity')->where('PurchaseMaster_IDNo', $data->purchaseId)->get('tbl_purchasedetails')->result();

            foreach ($purchaseDetails as $detail) {
                /*Get Product Current Quantity*/
                $totalQty = $this->db->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $purchase->PurchaseMaster_BranchID])->get('tbl_currentinventory')->row()->purchase_quantity;

                /* Subtract Product Quantity form  Current Quantity  */
                $newQty = $totalQty - $detail->PurchaseDetails_TotalQuantity;

                /*Update Purchase Inventory*/
                $this->db->set('purchase_quantity', $newQty)->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $purchase->PurchaseMaster_BranchID])->update('tbl_currentinventory');
            }

            /*Delete Purchase Details*/
            $this->db->set('Status', 'd')->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_purchasedetails');

            /*Delete Purchase Master Data*/
            $this->db->set('status', 'd')->where('PurchaseMaster_SlNo', $data->purchaseId)->update('tbl_purchasemaster');

            $res = ['success' => true, 'message' => 'Successfully deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }


    public function purchase_supplierName()
    {
        $id = $this->input->post('Supplierid');
        $sql = mysql_query("SELECT * FROM tbl_supplier WHERE Supplier_SlNo = '$id'");
        $row = mysql_fetch_array($sql);
        $datas['SupplierName'] = $row['Supplier_Name'];
        $this->load->view('Administrator/purchase/purchase_supplier_name', $datas);
    }

    function search_purchase_record()
    {
        $datas['title'] = 'Product';
        $dAta['searchtype'] = $searchtype = $this->input->post('searchtype');
        $dAta['productsearchtype'] = $productsearchtype = $this->input->post('productsearchtype');
        $dAta['Purchase_startdate'] = $Purchase_startdate = $this->input->post('Purchase_startdate');
        $dAta['Purchase_enddate'] = $Purchase_enddate = $this->input->post('Purchase_enddate');
        $dAta['Supplierid'] = $Supplierid = $this->input->post('Supplierid');
        $dAta['Productid'] = $Productid = $this->input->post('Productid');
        $this->session->set_userdata($dAta);

        $BranchID = $this->session->userdata('BRANCHid');

        if ($searchtype == "All") {
            $sql = "SELECT tbl_purchasemaster.*, tbl_supplier.* FROM tbl_purchasemaster left join tbl_supplier on tbl_supplier.Supplier_SlNo = tbl_purchasemaster.Supplier_SlNo WHERE tbl_purchasemaster.PurchaseMaster_BranchID='$BranchID' and tbl_purchasemaster.status = 'a' AND tbl_purchasemaster.PurchaseMaster_OrderDate between '$Purchase_startdate' AND '$Purchase_enddate'";
        } elseif ($searchtype == "Supplier") {
            $sql = "SELECT tbl_purchasemaster.*, tbl_supplier.* FROM tbl_purchasemaster left join tbl_supplier on tbl_supplier.Supplier_SlNo = tbl_purchasemaster.Supplier_SlNo WHERE tbl_purchasemaster.Supplier_SlNo = '$Supplierid' and tbl_purchasemaster.status = 'a' and  tbl_purchasemaster.PurchaseMaster_OrderDate between  '$Purchase_startdate' and '$Purchase_enddate'";
        }
        /* else if($searchtype == "Product"){
            $sql = "SELECT tbl_purchasemaster.*,tbl_purchasedetails.*, tbl_supplier.* FROM tbl_purchasemaster left join tbl_purchasedetails on tbl_purchasedetails.PurchaseMaster_IDNo = tbl_purchasemaster.PurchaseMaster_SlNo left join tbl_supplier on tbl_supplier.Supplier_SlNo = tbl_purchasemaster.Supplier_SlNo WHERE tbl_purchasedetails.Product_IDNo = '$Productid' and  tbl_purchasemaster.PurchaseMaster_OrderDate between '$Purchase_startdate' and '$Purchase_enddate'";
        } */
        $result = $this->db->query($sql);
        $datas["record"] = $result->result();
        $this->load->view('Administrator/purchase/purchase_record_list', $datas);
        //$this->load->view('Administrator/index',$datas);
    }

    function purchase_stock()
    {
        $data['title'] = "Purchase Stock";
        $data['content'] = $this->load->view('Administrator/stock/purchase_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function addDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $damage = array(
                'Damage_InvoiceNo' => $data->Damage_InvoiceNo,
                'Damage_Date' => $data->Damage_Date,
                'Damage_Description' => $data->Damage_Description,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'Damage_brunchid' => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_damage', $damage);
            $damageId = $this->db->insert_id();

            $damageDetails = array(
                'Damage_SlNo' => $damageId,
                'Product_SlNo' => $data->Product_SlNo,
                'DamageDetails_DamageQuantity' => $data->DamageDetails_DamageQuantity,
                'damage_amount' => $data->damage_amount,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s')
            );

            $this->db->insert('tbl_damagedetails', $damageDetails);

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity + ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$data->DamageDetails_DamageQuantity, $data->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $res = ['success' => true, 'message' => 'Damage entry success', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->Damage_SlNo;

            $damage = array(
                'Damage_InvoiceNo' => $data->Damage_InvoiceNo,
                'Damage_Date' => $data->Damage_Date,
                'Damage_Description' => $data->Damage_Description,
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            );

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', $damage);

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->row();

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity - ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$oldProduct->DamageDetails_DamageQuantity, $oldProduct->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $damageDetails = array(
                'Product_SlNo' => $data->Product_SlNo,
                'DamageDetails_DamageQuantity' => $data->DamageDetails_DamageQuantity,
                'damage_amount' => $data->damage_amount,
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            );

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damagedetails', $damageDetails);

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity + ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$data->DamageDetails_DamageQuantity, $data->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $res = ['success' => true, 'message' => 'Damage updated successfully', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getDamages()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->damageId) && $data->damageId != '') {
            $clauses .= " and d.Product_SlNo = '$data->damageId'";
        }
        $damages = $this->db->query("
            select
                dd.Product_SlNo,
                dd.DamageDetails_DamageQuantity,
                dd.damage_amount,
                d.Damage_SlNo,
                d.Damage_InvoiceNo,
                d.Damage_Date,
                d.Damage_Description,
                p.Product_Code,
                p.Product_Name
            from tbl_damagedetails dd
            join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
            join tbl_product p on p.Product_SlNo = dd.Product_SlNo
            where d.status = 'a' and dd.status = 'a'
            $clauses
        ")->result();

        echo json_encode($damages);
    }

    public function deleteDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->damageId;

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->row();
            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity - ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$oldProduct->DamageDetails_DamageQuantity, $oldProduct->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', ['status' => 'd']);
            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damagedetails', ['status' => 'd']);

            $res = ['success' => true, 'message' => 'Damage deleted successfully', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function damage_product_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product damage list";
        $data['products'] = $this->db->query("select * from tbl_product p where p.status = 'a' and p.is_service = 'false'")->result();
        $data['content'] = $this->load->view('Administrator/purchase/damage_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function damage_select_product()
    {
        $prod_id = $this->input->post('prod_id');
        if ($prod_id == 'All') {
            $data['records'] = $this->Product_model->all_damage_product_list();
        } else {
            $data['records'] = $this->Product_model->demage_poduct_list_by_product_id($prod_id);
        }
        $this->load->view('Administrator/purchase/damage_list_search', $data);
    }

    public function purchaseInvoicePrint($purchaseId)
    {
        $data['title'] = "Purchase Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_to_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getPurchasesImei()
    {
        $data = json_decode($this->input->raw_input_stream);
        $get_p_inv = $this->db->query("SELECT PurchaseMaster_InvoiceNo FROM tbl_purchasemaster WHERE PurchaseMaster_SlNo=?", $data->purchaseId)->result_array();
        $purchaseInvoice = $get_p_inv[0]['PurchaseMaster_InvoiceNo'];
        $imeis = $this->db->query("SELECT ps_imei_number,ps_prod_id FROM tbl_product_serial_numbers WHERE ps_purchase_inv=?", $purchaseInvoice)->result();
        echo json_encode($imeis);
    }

    public function returns_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function purchase_return_record()
    {
        $datas['searchtype'] = $searchtype = $this->input->post('searchtype');
        $datas['productID'] = $productID = $this->input->post('productID');
        $datas['startdate'] = $startdate = $this->input->post('startdate');
        $datas['enddate'] = $enddate = $this->input->post('enddate');
        $this->session->set_userdata($datas);
        //echo "<pre>";print_r($datas);exit;
        $this->load->view('Administrator/purchase/return_list', $datas);
    }

    public function purchase_update_form($PurchaseMaster_SlNo)
    {
        $this->cart->destroy();
        $data['title'] = "Product Purchase Update";
        $data['pm_sup'] = $pm_sup = $this->Billing_model->select_supplier_purhase_master($PurchaseMaster_SlNo);
        $data['product_purchase_det'] = $cartData =  $this->Billing_model->select_product_parchase_details($PurchaseMaster_SlNo);
        $this->_purchase_update_add_cart($cartData, $pm_sup);
        $data['products'] = $this->Product_model->products_by_brunch();

        $data['content'] = $this->load->view('Administrator/purchase/purchase_order_update', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    /*Used to add product details to cart for update product purchase.... called in purchase_update_form()*/
    private function _purchase_update_add_cart($cartData, $pm_sup)
    {

        foreach ($cartData as $data) :
            $insert_data = array(
                'id' => $data->Product_SlNo,
                'ProCat' => $data->ProductCategory_ID,
                'name' => $data->Product_Name,
                'category' => $data->ProductCategory_Name,
                'proCode' => $data->Product_Code,
                'price' => $data->Product_Purchase_Rate,
                'cost' => $data->purchase_cost,
                'qty' => $data->PurchaseDetails_TotalQuantity,
                'PurchaseMaster_SlNo' => $pm_sup->PurchaseMaster_SlNo,
                'PurchaseDetails_SlNo' => $data->PurchaseDetails_SlNo,
                'PurchaseMaster_InvoiceNo' => $pm_sup->PurchaseMaster_InvoiceNo,
                'PurchaseMaster_PaidAmount' => $pm_sup->PurchaseMaster_PaidAmount,
            );
            $this->cart->insert($insert_data);
        endforeach;
    }

    public function product_delete()
    {
        // $id = $this->input->post('deleted');
        $PurchaseMaster_SlNo = $this->input->post('PurchaseMaster_SlNo');
        $PurchaseMaster_InvoiceNo = $this->input->post('PurchaseMaster_InvoiceNo');
        $PurchaseMaster_TotalAmount = $this->input->post('PurchaseMaster_TotalAmount');
        $PurchaseMaster_DiscountAmount = $this->input->post('PurchaseMaster_DiscountAmount');
        $PurchaseMaster_Tax = $this->input->post('PurchaseMaster_Tax');
        $PurchaseMaster_Freight = $this->input->post('PurchaseMaster_Freight');
        $PurchaseMaster_SubTotalAmount = $this->input->post('PurchaseMaster_SubTotalAmount');
        $PurchaseMaster_PaidAmount = $this->input->post('PurchaseMaster_PaidAmount');
        $PurchaseMaster_DueAmount = $this->input->post('PurchaseMaster_DueAmount');

        $id = $this->input->post('PurchaseDetails_SlNo');
        $Product_IDNo = $this->input->post('Product_IDNo');
        $PurchaseDetails_TotalQuantity = $this->input->post('PurchaseDetails_TotalQuantity');
        $PurchaseDetails_TotalAmount = $this->input->post('PurchaseDetails_TotalAmount');
        //exit;
        $fld = 'PurchaseDetails_SlNo';
        $delete = $this->mt->delete_data("tbl_purchasedetails", $id, $fld);
        if (isset($delete)) {
            $SSI = mysql_query("SELECT * FROM tbl_purchaseinventory WHERE purchProduct_IDNo='$Product_IDNo'");
            $sirow = mysql_fetch_array($SSI);
            $data1['PurchaseInventory_TotalQuantity'] = $sirow['PurchaseInventory_TotalQuantity'] - $PurchaseDetails_TotalQuantity;
            $this->Billing_model->update_purchaseinventory("tbl_purchaseinventory", $data1, $Product_IDNo);

            $count = $this->db->from("tbl_purchasedetails")->where('PurchaseMaster_IDNo', $PurchaseMaster_SlNo)->count_all_results();
            if ($count == 0) {
                $data2['PurchaseMaster_TotalAmount'] = 0;
                $data2['PurchaseMaster_DiscountAmount'] = 0;
                $data2['PurchaseMaster_Tax'] = 0;
                $data2['PurchaseMaster_Freight'] = 0;
                $data2['PurchaseMaster_DueAmount'] = 0;
                $data2['PurchaseMaster_SubTotalAmount'] = 0;
                $this->Billing_model->update_purchasemaster("tbl_purchasemaster", $data2, $PurchaseMaster_SlNo);
            } else {
                $totalAmount = $PurchaseMaster_TotalAmount - $PurchaseDetails_TotalAmount;
                $data2['PurchaseMaster_TotalAmount'] = $totalAmount;
                $data2['PurchaseMaster_SubTotalAmount'] = $PurchaseMaster_SubTotalAmount - ($PurchaseDetails_TotalAmount / 100 * $PurchaseMaster_Tax + $PurchaseDetails_TotalAmount);
                $this->Billing_model->update_purchasemaster("tbl_purchasemaster", $data2, $PurchaseMaster_SlNo);
            }
            /* $SP = mysql_query("SELECT * FROM tbl_supplier_payment WHERE `SPayment_invoice`='$PurchaseMaster_InvoiceNo'");
            $cprow = mysql_fetch_array($SP);		
            $data['SPayment_amount']= $total;
            $this->Billing_model->update_supplier_payment("tbl_supplier_payment",$data,$PurchaseMaster_InvoiceNo); */
        }
        redirect('Administrator/Purchase/purchase_update_form/' . $PurchaseMaster_SlNo, 'refresh');
        //$this->load->view('Administrator/sales/product_sales_update');
    }

    /*Purchase Record Update*/
    public function Purchase_order_update()
    {
        $purchInvoice = $this->input->post('purchInvoice');
        $purch_id = $this->input->post('PurchaseMaster_SlNo');
        /*Purchase Master Update*/
        $Purchase = array(
            "Supplier_SlNo" => $this->input->post('SupplierID'),
            "PurchaseMaster_InvoiceNo" => $purchInvoice,
            "PurchaseMaster_OrderDate" => $this->input->post('Purchase_date'),
            "PurchaseMaster_PurchaseFor" => $this->input->post('PurchaseFor'),
            "PurchaseMaster_Description" => $this->input->post('Notes'),
            "PurchaseMaster_TotalAmount" => $this->input->post('subTotal'),
            "PurchaseMaster_DiscountAmount" => $this->input->post('purchDiscount'),
            "PurchaseMaster_Tax" => $this->input->post('vatPersent'),
            "PurchaseMaster_Freight" => $this->input->post('purchFreight'),
            "PurchaseMaster_SubTotalAmount" => $this->input->post('purchTotal'),
            "PurchaseMaster_PaidAmount" => $this->input->post('PurchPaid'),
            "PurchaseMaster_DueAmount" => $this->input->post('purchaseDue'),
            "UpdateBy" => $this->session->userdata("FullName"),
            "PurchaseMaster_BranchID" => $this->session->userdata("BRANCHid"),
            "UpdateTime" => date("Y-m-d H:i:s")
        );
        $this->Billing_model->purchaseOrderUpdate($Purchase, $purchInvoice);

        /*Supplier Payment Update*/
        $data = array(
            "SPayment_date" => $this->input->post('Purchase_date', TRUE),
            "SPayment_invoice" => $purchInvoice,
            "SPayment_customerID" => $this->input->post('SupplierID', TRUE),
            "SPayment_amount" => $this->input->post('PurchPaid', TRUE),
            "SPayment_notes" => $this->input->post('Notes', TRUE),
            "SPayment_Addby" => $this->session->userdata("FullName"),
            "SPayment_brunchid" => $this->session->userdata("BRANCHid")
        );
        $this->Billing_model->update_supplier_payment_data("tbl_supplier_payment", $data, $purchInvoice);

        /*CartData Insert Or Update to purchase details */
        if ($cart = $this->cart->contents()) {
            foreach ($cart as $item) {
                $order_detail = array(
                    'PurchaseMaster_IDNo' => $purch_id,
                    'Product_IDNo' => $item['id'],
                    'PurchaseDetails_TotalQuantity' => $item['qty'],
                    'PurchaseDetails_Rate' => $item['price'],
                    'UpdateBy' => $this->session->userdata("FullName"),
                    'UpdateTime' => date('Y-m-d H:i:s')
                );

                $oldPurchaseDetail =  $this->db->where('PurchaseMaster_IDNo', $purch_id)->where('Product_IDNo', $item['id'])->get('tbl_purchasedetails')->row();
                if (count($oldPurchaseDetail) > 0) :

                    /*update old details*/
                    $this->db->where('PurchaseMaster_IDNo', $purch_id)->where('Product_IDNo', $item['id'])->update('tbl_purchasedetails', $order_detail);
                    $newQty  = $item['qty'] - $oldPurchaseDetail->PurchaseDetails_TotalQuantity;
                    $item['qty'] = $newQty;
                    $this->_addStock($item);

                else :

                    /*insert new details*/
                    $this->Billing_model->update_purchase_detail($order_detail);
                    $this->_addStock($item);

                endif;


                /*Update Product Purchase Rate*/
                $Pid = $item['id'];
                $Pfld = 'Product_SlNo';
                $ProductPrice = array('Product_Purchase_Rate' => $item['price'],);
                $this->mt->update_data("tbl_product", $ProductPrice, $Pid, $Pfld);
            } // end foreach
        } // end if

        $this->cart->destroy();
        $xx['purchaseforprint'] = $purch_id;
        $this->session->set_userdata($xx);
        echo json_encode(true);
    }

    /*Used in Purchase Update*/
    private function _addStock($item)
    {
        // Stock add
        $rox = $this->db->where('product_id', $item['id'])->get('tbl_currentinventory')->row();
        $id = $rox->inventory_id;
        $oldStock = $rox->purchase_quantity;

        if ($rox->product_id == $item['id']) {
            $addStock = array(
                'product_id'           => $item['id'],
                'purchase_quantity' => $oldStock + $item['qty']
            );
            $this->mt->update_data("tbl_currentinventory", $addStock, $id, 'inventory_id');
        } else {
            $addStock = array(
                'product_id'                     => $item['id'],
                'purchase_quantity' => $item['qty']
            );
            $this->mt->save_data("tbl_currentinventory", $addStock);
        }
    }

    function select_supplier()
    {
?>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right" for="Supplierid"> Select Supplier </label>
            <div class="col-sm-3">
                <select name="Supplierid" id="Supplierid" data-placeholder="Choose a Supplier..." class="chosen-select">
                    <option value=""></option>
                    <?php
                    $sql = $this->db->query("SELECT * FROM tbl_supplier where Supplier_brinchid='" . $this->brunch . "' order by Supplier_Name desc");
                    $row = $sql->result();
                    foreach ($row as $row) { ?>
                        <option value="<?php echo $row->Supplier_SlNo; ?>"><?php echo $row->Supplier_Name; ?>
                            (<?php echo $row->Supplier_Code; ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    <?php
    }

    function select_product()
    {
    ?>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right" for="Productid"> Select Product </label>
            <div class="col-sm-3">
                <select name="Productid" id="Productid" data-placeholder="Choose a Product..." class="chosen-select">
                    <option value=""></option>
                    <?php
                    $sql = $this->db->query("SELECT * FROM tbl_product order by Product_Name desc");
                    $row = $sql->result();
                    foreach ($row as $row) { ?>
                        <option value="<?php echo $row->Product_SlNo; ?>"><?php echo $row->Product_Name; ?>
                            (<?php echo $row->Product_Code; ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
<?php
    }
}
