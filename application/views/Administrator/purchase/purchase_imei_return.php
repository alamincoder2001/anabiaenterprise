<div class="row" id="purchaseReturn">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
		<div class="form-group" style="margin-top:10px;">
			<label class="col-sm-1 col-sm-offset-1 control-label no-padding-right" for="purchaseInvoiceno"> Invoice no </label>
			<div class="col-sm-2">
                <select class="chosen-select" id="imei_number">
                <option value="0" >Select a IMEI</option>
                <?php $returning_imeis =  $this->db->query("SELECT ps_imei_number,ps_prod_id FROM  tbl_product_serial_numbers WHERE ps_brunch_id=?",$this->session->userdata('BRANCHid'))->result(); 
                foreach($returning_imeis as $key=>$value){?>
                    <option value="<?= $value->ps_imei_number?>"><?= $value->ps_imei_number?></option>
                <?php } ?>
                </select>
 			</div>
             <div class="col-sm-2">
             </div>
		</div>

        <span id="render_html">
        </span>
	</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script>
     $(document).on('change','#imei_number',function(){
        var imei_number = $(this).val();
        $.ajax({
            url:"<?= base_url()?>Administrator/Purchase/get_return_imei_number",
            method:"POST",
            data:{imei_number:imei_number},
            success:function(data){
                $('#render_html').html(data);
            }
        })
     })

     $(document).on('click','#parchase_return_btn',function(){
            let imei_number = $(this).attr('data-id');
            let return_amout = $(this).attr('data-amount');
            let prod_id = $(this).attr('data-prod_id');
           
            axios.post('/purchase_imei_return',{imei_number:imei_number,return_amout:return_amout,prod_id:prod_id})
            .then((res)=>{
                if (res.data=='return') {
                    alert('Return Success')
                   window.location.reload()
                }else{
                    alert("Somthing Wrong")
                    return false;
                }
            })
     });
</script>