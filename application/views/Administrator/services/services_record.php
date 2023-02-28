<div class="row" style="margin-top: 15px;">
    <div class="col-md-12" style="margin-bottom: 10px;">
    <div class="col-md-12">
        <div id="reportContent" class="table-responsive">
            <table class="record-table" style="margin-top: 20px;width: 100%;">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Employee Name</th>
                        <th>VAT</th>
                        <th>Discount</th>
                        <th>Transport Cost</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Total Qty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                    $BRANCHid = $this->session->userdata('BRANCHid');
                    if ($searchtype=='all') {

$sqlstmt =  $this->db->query("SELECT  svm.*,emp.Employee_Name
FROM tbl_services_master svm
INNER JOIN tbl_employee emp
ON svm.sv_emp_id = emp.Employee_SlNo  WHERE svm.sv_branch='$BRANCHid'  ORDER BY svm.sv_m_id DESC ")->result();
                    }elseif($searchtype=='emp'){
                         $sqlstmt =  $this->db->query("SELECT  svm.*,emp.Employee_Name
FROM tbl_services_master svm
INNER JOIN tbl_employee emp
ON svm.sv_emp_id = emp.Employee_SlNo  WHERE  svm.sv_branch='$BRANCHid' AND  emp.Employee_SlNo='$emp' AND svm.sv_date BETWEEN '$service_startdate' AND '$service_enddate' ")->result();
                    }else{
                         $sqlstmt =  $this->db->query("SELECT  svm.*,emp.Employee_Name
FROM tbl_services_master svm
INNER JOIN tbl_employee emp
ON svm.sv_emp_id = emp.Employee_SlNo WHERE  svm.sv_branch='$BRANCHid' AND  svm.sv_date BETWEEN '$service_startdate' AND '$service_enddate' ORDER BY svm.sv_m_id DESC ")->result();
                    }
                    $vat = 0;
                    $sv_paid_amount =0;
                    $sv_sale_total_discount =0;
                    $sv_transport_cost =0;
                    $sv_total_sale_amount =0;
                    $sv_paid_amount =0;
                    $sv_sale_due =0;
                    $totalQty=0;
                    foreach ($sqlstmt as $key => $value) {?>
                         <tr>
                        <td><?= $value->sv_invoice?></td>
                        <td><?= $value->sv_date?></td>
                        <td style="text-align: right;"><?= $value->Employee_Name?></td>
                        <td style="text-align: right;"><?= $value->sv_vat_amount?></td>
                        <td style="text-align: right;"><?= $value->sv_sale_total_discount?></td>
                        <td style="text-align: right;"><?= $value->sv_transport_cost?></td>
                        <td style="text-align: right;"><?= $value->sv_total_sale_amount?></td>
                        <td style="text-align: right;"><?= $value->sv_paid_amount?></td>
                        <td style="text-align: right;"><?= $value->sv_sale_due?></td>
                        <td style="text-align: right;"> 
                        <?php $totalQtyresult =  $this->db->query("select *   from tbl_services_details where sv_m_id=?",$value->sv_m_id)->num_rows();
                             echo $totalQtyresult;
                             $totalQty =$totalQty+$totalQtyresult;
                        ?>
                         </td>
                        <td style="text-align: center;"><a href="/serviceInvoicePrint/<?= $value->sv_m_id?>" title="Services Invoice" target="_blank"><i class="fa fa-file"></i></a> 
                         <a  href="/services/<?= $value->sv_m_id?>" title="Edit Services" style="cursor: pointer;color: green;font-size: 18px" onClick="editRecord(<?= $value->sv_m_id?>)">
                            <i class="fa fa-edit"></i></a>
                         <span style="cursor: pointer;color: red;font-size: 18px"  onClick="deleteRecod(<?= $value->sv_m_id?>)" title="Delete Services"><i class="fa fa-trash"></i></span></td>
                    </tr>
                    <?php
                    $vat = $vat +$value->sv_vat_amount;
                    $sv_sale_total_discount = $sv_sale_total_discount +$value->sv_sale_total_discount;
                    $sv_transport_cost = $sv_transport_cost +$value->sv_transport_cost;
                    $sv_total_sale_amount = $sv_total_sale_amount +$value->sv_total_sale_amount;
                    $sv_paid_amount = $sv_paid_amount +$value->sv_paid_amount;
                    $sv_sale_due = $sv_sale_due +$value->sv_sale_due;
                } 
                   ?>
                   
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="2" style="text-align: right;">Total</td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"><?= $vat?></td>
                        <td style="text-align: right;"><?= $sv_sale_total_discount?></td>
                        <td style="text-align: right;"><?= $sv_transport_cost?></td>
                        <td style="text-align: right;"><?= $sv_total_sale_amount?></td>
                        <td style="text-align: right;"><?= $sv_paid_amount?></td>
                        <td style="text-align: right;"><?= $sv_sale_due?></td>
                        <td style="text-align: right;"><?= $totalQty?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
     function  deleteRecod(id){
        if (confirm('Are you sure delete this?')) {
             axios.post('/service_record_delete',{id:id})
             .then((res)=>{
                alert("Delete Success !!!");
              location.reload("<?php base_url()?>s_record");
             })
        }else{
            return false;
        
        }
        
    }


</script>