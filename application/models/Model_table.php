<?php
class Model_Table extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
   

    public function userAccess(){
        $currentUrl = $this->uri->uri_string();

        $userAccessQuery = $this->db->where('user_id', $this->session->userdata('userId'))->get('tbl_user_access');
        $access = [];
        if ($userAccessQuery->num_rows() != 0) {
            $userAccess = $userAccessQuery->row();
            $access = json_decode($userAccess->access);
        }

        $accountType = $this->session->userdata('accountType');
        if(array_search($currentUrl, $access) > -1 || $accountType == 'm' || $accountType == 'a'){
            return true;
        } else {
            return false;
        }
    }


    public function customerDue($clauses = "") {
        $branchId = $this->session->userdata('BRANCHid');
        $dueResult = $this->db->query("
            select
            c.Customer_SlNo,
            c.Customer_Name,
            c.Customer_Code,
            c.Customer_Address,
            c.Customer_Mobile,
            c.owner_name,
            (select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0.00) + ifnull(c.previous_due, 0.00)
                from tbl_salesmaster sm 
                where sm.SalseCustomer_IDNo = c.Customer_SlNo
                and sm.Status = 'a') as billAmount,

            (select ifnull(sum(sm.SaleMaster_PaidAmount), 0.00)
                from tbl_salesmaster sm
                where sm.SalseCustomer_IDNo = c.Customer_SlNo
                and sm.Status = 'a') as invoicePaid,

            (select ifnull(sum(cp.CPayment_amount), 0.00) 
                from tbl_customer_payment cp 
                where cp.CPayment_customerID = c.Customer_SlNo and cp.CPayment_TransactionType = 'CR'
                and cp.CPayment_status = 'a') as cashReceived,

            (select ifnull(sum(cp.CPayment_amount), 0.00) 
                from tbl_customer_payment cp 
                where cp.CPayment_customerID = c.Customer_SlNo 
                and cp.CPayment_TransactionType = 'CP'
                and cp.CPayment_status = 'a') as paidOutAmount,

            (select ifnull(sum(sr.SaleReturn_ReturnAmount), 0.00) 
                from tbl_salereturn sr 
                join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo 
                where smr.SalseCustomer_IDNo = c.Customer_SlNo) as returnedAmount,

            (select invoicePaid + cashReceived) as paidAmount,

            (select (billAmount + paidOutAmount) - (paidAmount + returnedAmount)) as dueAmount
            
            from tbl_customer c
            where c.Customer_brunchid = '$branchId' $clauses
        ")->result();

        return $dueResult;
    }

    /*---------------------------  Save Update Data  ------------------------------*/

    public function generateSalesInvoice(){
        $invoice = date('Y') . "000001";
        $year = date('Y');
        $sales = $this->db->query("select * from tbl_salesmaster sm where sm.SaleMaster_InvoiceNo like '$year%'");
        if($sales->num_rows() != 0){
            $newSalesId = $sales->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000', '00000');
            $invoice = date('Y') . (strlen($newSalesId) > count($zeros) ? $newSalesId : $zeros[count($zeros) - strlen($newSalesId)] . $newSalesId);
		}
		
		return $invoice;
    }
     public function generateServicesInvoice(){
        $invoice = date('Y') . "000001";
        $year = date('Y');
        $sales = $this->db->query("select * from tbl_services_master sv where sv.sv_invoice like '$year%'");
        if($sales->num_rows() != 0){
            $newSalesId = $sales->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000', '00000');
            $invoice = date('Y') . (strlen($newSalesId) > count($zeros) ? $newSalesId : $zeros[count($zeros) - strlen($newSalesId)] . $newSalesId);
        }
        
        return $invoice;
    }


    public function generateQuotationInvoice(){
		$invoice = 'Q-' . date('Y') . "00001";
        $year = date('Y');
        $quotations = $this->db->query("select * from tbl_quotation_master qm where qm.SaleMaster_InvoiceNo like 'Q-$year%'");
        if($quotations->num_rows() != 0){
            $newQuotationId = $quotations->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000');
            $invoice = 'Q-' . date('Y') . (strlen($newQuotationId) > count($zeros) ? $newQuotationId : $zeros[count($zeros) - strlen($newQuotationId)] . $newQuotationId);
		}
		
		return $invoice;
    }
    
    public function generatePurchaseInvoice(){
        $invoice = date('Y') . "000001";
        $year = date('Y');
        $purchases = $this->db->query("select * from tbl_purchasemaster pm where pm.PurchaseMaster_InvoiceNo like '$year%'");
        if($purchases->num_rows() != 0){
            $newPurchaseId = $purchases->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000', '00000');
            $invoice = date('Y') . (strlen($newPurchaseId) > count($zeros) ? $newPurchaseId : $zeros[count($zeros) - strlen($newPurchaseId)] . $newPurchaseId);
        }

        return $invoice;
    }

    public function generateCustomerCode(){
        $customerCode = "C00001";
        
        $lastCustomer = $this->db->query("select * from tbl_customer order by Customer_SlNo desc limit 1");
        if($lastCustomer->num_rows() != 0){
            $newCustomerId = $lastCustomer->row()->Customer_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $customerCode = 'C' . (strlen($newCustomerId) > count($zeros) ? $newCustomerId : $zeros[count($zeros) - strlen($newCustomerId)] . $newCustomerId);
        }

        return $customerCode;
    }

    public function generateProductCode(){
        $productCode = "P00001";
        
        $lastProduct = $this->db->query("select * from tbl_product order by Product_SlNo desc limit 1");
        if($lastProduct->num_rows() != 0){
            $newProductId = $lastProduct->row()->Product_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $productCode = 'P' . (strlen($newProductId) > count($zeros) ? $newProductId : $zeros[count($zeros) - strlen($newProductId)] . $newProductId);
        }

        return $productCode;
    }

    public function generateSupplierCode(){
        $supplierCode = "S00001";
        
        $lastSupplier = $this->db->query("select * from tbl_supplier order by Supplier_SlNo desc limit 1");
        if($lastSupplier->num_rows() != 0){
            $newSupplierId = $lastSupplier->row()->Supplier_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $supplierCode = 'S' . (strlen($newSupplierId) > count($zeros) ? $newSupplierId : $zeros[count($zeros) - strlen($newSupplierId)] . $newSupplierId);
        }

        return $supplierCode;
    }

    public function generateCustomerPaymentCode(){
        $paymentCode = "TR00001";
        
        $lastPayment = $this->db->query("select * from tbl_customer_payment order by CPayment_id desc limit 1");
        if($lastPayment->num_rows() != 0){
            $newPaymentId = $lastPayment->row()->CPayment_id + 1;
            $zeros = array('0', '00', '000', '0000');
            $paymentCode = 'TR' . (strlen($newPaymentId) > count($zeros) ? $newPaymentId : $zeros[count($zeros) - strlen($newPaymentId)] . $newPaymentId);
        }

        return $paymentCode;
    }

    public function generateSupplierPaymentCode(){
        $paymentCode = "TR00001";
        
        $lastPayment = $this->db->query("select * from tbl_supplier_payment order by SPayment_id desc limit 1");
        if($lastPayment->num_rows() != 0){
            $newPaymentId = $lastPayment->row()->SPayment_id + 1;
            $zeros = array('0', '00', '000', '0000');
            $paymentCode = 'TR' . (strlen($newPaymentId) > count($zeros) ? $newPaymentId : $zeros[count($zeros) - strlen($newPaymentId)] . $newPaymentId);
        }

        return $paymentCode;
    }

    public function generateCashTransactionCode(){
        $transactionCode = "TR00001";
        
        $lastTransaction = $this->db->query("select * from tbl_cashtransaction order by Tr_SlNo desc limit 1");
        if($lastTransaction->num_rows() != 0){
            $newTransactionId = $lastTransaction->row()->Tr_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $transactionCode = 'TR' . (strlen($newTransactionId) > count($zeros) ? $newTransactionId : $zeros[count($zeros) - strlen($newTransactionId)] . $newTransactionId);
        }

        return $transactionCode;
    }

    public function generateDamageCode(){
        $code = "D0001";
        
        $lastDamage = $this->db->query("select * from tbl_damage order by Damage_SlNo desc limit 1");
        if($lastDamage->num_rows() != 0){
            $newDamageCode = $lastDamage->row()->Damage_SlNo + 1;
            $zeros = array('0', '00', '000');
            $code = 'D' . (strlen($newDamageCode) > count($zeros) ? $newDamageCode : $zeros[count($zeros) - strlen($newDamageCode)] . $newDamageCode);
        }

        return $code;
    }

    public function generateAccountCode(){
        $code = "A0001";
        
        $lastRow = $this->db->query("select * from tbl_account order by Acc_SlNo desc limit 1");
        if($lastRow->num_rows() != 0){
            $newCode = $lastRow->row()->Acc_SlNo + 1;
            $zeros = array('0', '00', '000');
            $code = 'A' . (strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode);
        }

        return $code;
    }

    public function getTransactionSummary($date = null){
        $transactionSummary = $this->db->query("
            select
            /* Received */
            (
                select ifnull(sum(sm.SaleMaster_PaidAmount), 0) from tbl_salesmaster sm
                where sm.SaleMaster_branchid= " . $this->session->userdata('BRANCHid') . "
                and sm.Status = 'a'
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
            ) as received_sales,
            (
                select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                where cp.CPayment_TransactionType = 'CR'
                and cp.CPayment_status = 'a'
                and cp.CPayment_Paymentby != 'bank'
                and cp.CPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
            ) as received_customer,
            (
                select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                where sp.SPayment_TransactionType = 'CR'
                and sp.SPayment_status = 'a'
                and sp.SPayment_Paymentby != 'bank'
                and sp.SPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
            ) as received_supplier,
            (
                select ifnull(sum(ct.In_Amount), 0) from tbl_cashtransaction ct
                where ct.Tr_Type = 'In Cash'
                and ct.status = 'a'
                and ct.Tr_branchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
            ) as received_cash,
            (
                select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                where bt.transaction_type = 'withdraw'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as bank_withdraw,
            /* paid */
            (
                select ifnull(sum(pm.PurchaseMaster_PaidAmount), 0) from tbl_purchasemaster pm
                where pm.status = 'a'
                and pm.PurchaseMaster_BranchID= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
            ) as paid_purchase,
            (
                select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                where sp.SPayment_TransactionType = 'CP'
                and sp.SPayment_status = 'a'
                and sp.SPayment_Paymentby != 'bank'
                and sp.SPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
            ) as paid_supplier,
            (
                select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                where cp.CPayment_TransactionType = 'CP'
                and cp.CPayment_status = 'a'
                and cp.CPayment_Paymentby != 'bank'
                and cp.CPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
            ) as paid_customer,
            (
                select ifnull(sum(ct.Out_Amount), 0) from tbl_cashtransaction ct
                where ct.Tr_Type = 'Out Cash'
                and ct.status = 'a'
                and ct.Tr_branchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
            ) as paid_cash,
            (
                select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                where bt.transaction_type = 'deposit'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as bank_deposit,
            (
                select ifnull(sum(ep.payment_amount), 0) from tbl_employee_payment ep
                where ep.paymentBranch_id = " . $this->session->userdata('BRANCHid') . "
                and ep.status = 'a'
                " . ($date == null ? "" : " and ep.payment_date < '$date'") . "
            ) as employee_payment,
            /* total */
            (
                select received_sales + received_customer + received_supplier + received_cash + bank_withdraw
            ) as total_in,
            (
                select paid_purchase + paid_customer + paid_supplier + paid_cash + bank_deposit + employee_payment
            ) as total_out,
            (
                select total_in - total_out
            ) as cash_balance
        ")->row();

        return $transactionSummary;
    }

    public function getBankTransactionSummary($accountId = null, $date = null){
        $bankTransactionSummary = $this->db->query("
            select 
                ba.*,
                (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.account_id = ba.account_id
                    and bt.transaction_type = 'deposit'
                    and bt.status = 1
                    and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as total_deposit,
                (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.account_id = ba.account_id
                    and bt.transaction_type = 'withdraw'
                    and bt.status = 1
                    and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as total_withdraw,
                (
                    select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                    where cp.account_id = ba.account_id
                    and cp.CPayment_status = 'a'
                    and cp.CPayment_TransactionType = 'CR'
                    and cp.CPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                ) as total_received_from_customer,
                (
                    select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                    where cp.account_id = ba.account_id
                    and cp.CPayment_status = 'a'
                    and cp.CPayment_TransactionType = 'CP'
                    and cp.CPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                ) as total_paid_to_customer,
                (
                    select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                    where sp.account_id = ba.account_id
                    and sp.SPayment_status = 'a'
                    and sp.SPayment_TransactionType = 'CP'
                    and sp.SPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
                ) as total_paid_to_supplier,
                (
                    select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                    where sp.account_id = ba.account_id
                    and sp.SPayment_status = 'a'
                    and sp.SPayment_TransactionType = 'CR'
                    and sp.SPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
                ) as total_received_from_supplier,
                (
                    select (ba.initial_balance + total_deposit + total_received_from_customer + total_received_from_supplier) - (total_withdraw + total_paid_to_customer + total_paid_to_supplier)
                ) as balance
            from tbl_bank_accounts ba
            where ba.branch_id = " . $this->session->userdata('BRANCHid') . "
            " . ($accountId == null ? "" : " and ba.account_id = '$accountId'") . "
        ")->result();

        return $bankTransactionSummary;
    }

    public function payment_invoice($id){
        $sql = mysql_query("SELECT tbl_booking_bill.*, tbl_booking_customer.*, tbl_booking_customer.fld_id as cusID, tbl_cash_receive.fld_id as cashR_ID FROM tbl_booking_bill LEFT JOIN tbl_booking_customer ON tbl_booking_customer.fld_id=tbl_booking_bill.fld_customer_id left join tbl_cash_receive on tbl_booking_bill.fld_id =tbl_cash_receive.fld_order_id  where tbl_booking_bill.fld_id = '$id'");
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }
     public function ajax_cash_payment($key){
        $sql = mysql_query("SELECT tbl_booking_bill.*, tbl_booking_customer.*, tbl_booking_customer.fld_id as cusID, tbl_cash_receive.fld_id as cashR_ID FROM tbl_booking_bill LEFT JOIN tbl_booking_customer ON tbl_booking_customer.fld_id=tbl_booking_bill.fld_customer_id left join tbl_cash_receive on tbl_booking_bill.fld_id =tbl_cash_receive.fld_order_id  where tbl_booking_bill.fld_Serial = '$key'");
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }
    public function ajax_cash_receive($id){
        $sql = mysql_query("SELECT tbl_booking_bill.*, tbl_booking_customer.*, tbl_booking_customer.fld_id as cusID, tbl_cash_receive.fld_id as cashR_ID FROM tbl_booking_bill LEFT JOIN tbl_booking_customer ON tbl_booking_customer.fld_id=tbl_booking_bill.fld_customer_id left join tbl_cash_receive on tbl_booking_bill.fld_id =tbl_cash_receive.fld_order_id  where tbl_booking_bill.fld_id = '$id'");
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }
    public function add_product($data)
    {
        //untuk insert data ke table product
        $this->db->insert('product', $data);
    }
    public function save_data($table, $data){       
        $result= $this->db->insert($table, $data);
        if ($result) {                    
           $this->Id = $this->db->insert_id();
           return TRUE;
        } 
        $this->Err = mysql_error();
        return FALSE;
    }

    public function insert_payment($table, $data)
    {
        $this->db->insert($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;
    }
    

    public function save_date_id($table, $data){
        $this->db->insert($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;      
    }
    public function update_customer_data($table, $data, $id){       
        $this->db->where("fld_id", $id);
        $result= $this->db->update($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;      
     }
    public function update_data($table, $data, $id,$fld){       
        $this->db->where($fld, $id);
        $result= $this->db->update($table, $data);
        if (!$result) {                      
           return FALSE;
        } 
        return TRUE;
     }
     
    public function delete_data($table, $id, $fld){   
		$data['status'] = 'd';    
        $this->db->where($fld, $id);
       // $result= $this->db->delete($table);
        $result= $this->db->update($table, $data);
        if (!$result) {                      
           return FALSE;
        } 
        return TRUE;
    }

    public function select_by_Booking_id($id){
        $sql = mysql_query("SELECT tbl_booking_bill.*,tbl_booking_bill.fld_id as ordID, tbl_booking_customer.*, tbl_booking_customer.fld_id as cusID, tbl_cash_receive.*, tbl_cash_receive.fld_id as cashR_ID FROM tbl_booking_bill LEFT JOIN tbl_booking_customer ON tbl_booking_customer.fld_id=tbl_booking_bill.fld_customer_id left join tbl_cash_receive on tbl_booking_bill.fld_id =tbl_cash_receive.fld_order_id  where tbl_booking_bill.fld_id = '".$id."'");
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }
    public function edit_by_id($query){
        $sql = mysql_query($query);
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }

    public function select_by_id($table, $id, $fld){
        $sql = mysql_query("SELECT * from {$table} where {$fld} = '".$id."'");
        while($d = mysql_fetch_array($sql)){
            return $d;
        }
    }
    
    public function view_data($table){
        $a = array();
        $sql = mysql_query($table);
        while($d = mysql_fetch_array($sql)){
           $a[] = $d;
        }   
      return $a;
    }

    
    public function ccdata($data){
        $a = array();
        $sql = mysql_query($data);
        while($d = mysql_fetch_array($sql)){
           $a[] = $d;
        }	
      return $a;
    }
    
    
    public function mailcheck_availablity(){
        $mail = $this->input->post('usermail');
        
        $query = $this->db->query("SELECT fld_email from tbl_superadmin where fld_email = '$mail'");
        if($query->num_rows() > 0){
            return false;
        }
        else{
            return true;}
    }
	
	 // public function mailcheck_availablity(){
		  
	  //}


    public function getBrunchNameById($id)
    {
        $q = $this->db->where('brunch_id',$id)->get('tbl_brunch')->row();
        if ($q)
            return $q->Brunch_name;
        return false;
    }



    /************************************************************************************************************/

    /*Get Customer Due by SalseCustomer_IDNo*/
    public function getCustomerDueById($Custid)
    {
        // ====================
        $salesMaster = $this->db->where(['SalseCustomer_IDNo'=> $Custid, 'Status'=>'a'])->select_sum('SaleMaster_DueAmount')->get('tbl_salesmaster')->row();
        $dueAm = $salesMaster->SaleMaster_DueAmount;

        // ====================
        $salesPaid = $this->db->where('CPayment_customerID', $Custid)->where(['CPayment_TransactionType'=> '', 'CPayment_status'=>'a'])->select_sum('CPayment_amount')->get('tbl_customer_payment')->row();
        $salesPaidAm = $salesPaid->CPayment_amount;

        // ====================
        $paidAmount = $this->db->where('CPayment_customerID', $Custid)->where(['CPayment_TransactionType'=> 'CR', 'CPayment_status'=>'a'])->select_sum('CPayment_amount')->get('tbl_customer_payment')->row();
        $paidAm = $paidAmount->CPayment_amount;

        // ====================
        $payAmount = $this->db->where('CPayment_customerID', $Custid)->where(['CPayment_TransactionType'=> 'CP', 'CPayment_status'=>'a'])->select_sum('CPayment_amount')->get('tbl_customer_payment')->row();
        $payAm = $payAmount->CPayment_amount;

        // ====================
        $returnAmount = $this->db->where('CPayment_customerID', $Custid)->where(['CPayment_TransactionType'=> 'RP', 'CPayment_status'=>'a'])->select_sum('CPayment_amount')->get('tbl_customer_payment')->row();
        $returnAm = $returnAmount->CPayment_amount;

        // ====================
        $prevDueAmount = $this->db->where('Customer_SlNo', $Custid)->get('tbl_customer')->row();
        $prevDue = $prevDueAmount->previous_due;

        // ====================
        $salesReturnQuery = $this->db->query("
            select 
                ifnull(sum(sr.SaleReturn_ReturnAmount), 0.00) as salesReturn
            from tbl_salereturn sr 
            join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo 
            where smr.SalseCustomer_IDNo = ?
        ", $Custid)->row();
        $salesReturned = $salesReturnQuery->salesReturn;

        $due = ($dueAm + $payAm + $prevDue) - ($paidAm + $salesReturned);


        if ($due):
            return $due;
        else:
            return 0.00;
        endif;

    }


    /*Get Supplier Due by Supplier_SlNo*/

    /*Need to add supplier return amount*/
    public function getSupplierDueById($Suppid)
    {
        // ====================
        $purchaseMaster = $this->db->query("select ifnull(sum(pm.PurchaseMaster_TotalAmount - pm.PurchaseMaster_PaidAmount), 0.00) as dueAmount from tbl_purchasemaster pm
        where pm.Supplier_SlNo = ?", $Suppid)->row();
        $dueAm = $purchaseMaster->dueAmount;

        // ====================
        $paidAmount = $this->db->where('SPayment_customerID', $Suppid)->where('SPayment_TransactionType', 'CR')->select_sum('SPayment_amount')->get('tbl_supplier_payment')->row();
        $paidAm = $paidAmount->SPayment_amount;

        // ====================
        $payAmount = $this->db->where('SPayment_customerID', $Suppid)->where('SPayment_TransactionType', 'CP')->select_sum('SPayment_amount')->get('tbl_supplier_payment')->row();
        $payAm = $payAmount->SPayment_amount;

        // ====================
        $returnAmount = $this->db->where('Supplier_IDdNo', $Suppid)->select_sum('PurchaseReturn_ReturnAmount')->get('tbl_purchasereturn')->row();
        $returnAm = $returnAmount->PurchaseReturn_ReturnAmount;
     
        // ====================
        $prevDueAmount = $this->db->where('Supplier_SlNo', $Suppid)->get('tbl_supplier')->row();
        $prevDue = $prevDueAmount->previous_due;

        $due = ($paidAm + $dueAm + $prevDue) - ($payAm + $returnAm);

        if ($due):
            return $due;
        else:
            return 0.00;
        endif;

    }



    /*Used In Invoices*/
    public function convertNumberToWord($number = false)
    {
        error_reporting(E_ALL & ~E_NOTICE);
        if (!$number) {
            return false;
        }

        $no = round($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] .
                    //" " . $digits[$counter] . $plural . " " . $hundred
                    " " . $digits[$counter] . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    //. $digits[$counter] . $plural . " " . $hundred;
                    . $digits[$counter] . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
            "." . $words[$point / 10] . " " .
            $words[$point = $point % 10] : '';
        $r = $result . " Taka Only.";
        return strtoupper($r);

    }
	
  }
?>