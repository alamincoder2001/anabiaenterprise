<div class="row">
<div class="col-xs-12">
	<!-- PAGE CONTENT BEGINS -->
<div id="EditForm">
	<div class="form-horizontal">
				
		<form method="POST" id="assetsForm" action="#">
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="assetsname"> Assets Name </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-3">
					<input type="text" id="assetsname" required name="assetsname" placeholder="Assets Name" class="form-control" />
					<span id="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="rate"> Rate </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-3">
					<input type="number" id="rate" required name="rate" placeholder="Rate" class="form-control" />
					<span id="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="qty"> Quantity </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-3">
					<input type="number" id="qty" required name="qty" placeholder="Quantity" onblur="TotalAmount()" class="form-control" />
					<span id="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="amount"> Amount </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-3">
					<input type="number" id="amount" readonly name="amount" placeholder="Amount" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
				<label class="col-sm-1 control-label no-padding-right"></label>
				<div class="col-sm-8">
				    <button type="button" class="btn btn-sm btn-success" onclick="submitAssets()" name="btnSubmit">
						Submit
						<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
					</button>
				</div>
			</div>
		</form>
			
	</div>
</div>
</div>
</div>


			
<div class="row">
	<div class="col-xs-12">

		<div class="clearfix">
			<div class="pull-right tableTools-container"></div>
		</div>
		<div class="table-header">
			Assets Information
		</div>

		<!-- div.table-responsive -->

		<!-- div.dataTables_borderWrap -->
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>SL No</th>
						<th>Date</th>
						<th>Assets Name</th>
						<th>Rate</th>
						<th>Quantity</th>
						<th>Amount</th>
						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php 
						if(isset($assets) && $assets){
					$i=1; foreach($assets as $row){ ?>
					<tr>
						<td><?php echo $i++; ?></td>
						<td><?php echo $row->as_date; ?></td>
						<td><?php echo $row->as_name; ?></td>
						<td><?php echo $row->as_rate; ?></td>
						<td><?php echo $row->as_qty; ?></td>
						<td><?php echo $row->as_amount; ?></td>
						<td>
							<?php if($this->session->userdata('accountType') != 'u'){?>
							<div class="hidden-sm hidden-xs action-buttons">
								<a class="green" href="#" onclick="Edit_assets(<?php echo $row->as_id; ?>)"  title="Eidt">
									<i class="ace-icon fa fa-pencil bigger-130"></i>
								</a>

								<a class="red" href="#" onclick="deleteAssets(<?php echo $row->as_id; ?>)">
									<i class="ace-icon fa fa-trash-o bigger-130"></i>
								</a>
							</div>
							<?php }?>
						</td>

					</tr>
					
				<?php } }?>
				</tbody>
			</table>
	</div>
</div>
					

<script type="text/javascript">
	function TotalAmount()
	{
		var qty  = $('#qty').val();
		var rate = $('#rate').val();

		var amount = parseInt(qty) * parseInt(rate);
		$('#amount').val(amount);

	}

    function submitAssets(){

    	var isvalid = true;
        $('#assetsForm :input[required]').each(function () {
            var id = this.id; 
            $('#error' + id).remove(); //this code use in, required text no repeat
            if (this.value.trim() === '') {

                 $('#' + id).next('span').after("<span id='error" + id + "' class='errorTag' style='color:red; font-size:12px; font-weight:bold;'> &nbsp; Required this field ! </span>");
                isvalid = false; 
            }
        });
        if (isvalid) {
	        var urldata = "<?php echo base_url();?>insertassets";
	        $.ajax({
	            type: "POST",
	            url: urldata,
	            data: $('#assetsForm').serialize(),
	            success:function(data){
	                alert("Save Success");
					location.reload();
	            }
	        });
	    }
    }
</script>
<script type="text/javascript">
    function Edit_assets(id){
        var urldata = "<?php echo base_url();?>assetsEdit/"+id;
        $.ajax({
            type: "POST",
            url: urldata,
            data: {id:id},
            success:function(data){
                $("#EditForm").html(data);
            }
        });
    }
</script>
<script type="text/javascript">
    function UpdateAssets(id){
        var urldata = "<?php echo base_url();?>assetsUpdate/"+id;
        $.ajax({
            type: "POST",
            url: urldata,
            data: $('#assetsFormUpdate').serialize(),
            success:function(data){
				alert("Update Success");
				location.reload();
            }
        });
    }
</script>
<script type="text/javascript">
    function deleteAssets(id){
		if(confirm("Are You Sure Want to delete This?")){
	        var urldata = "<?php echo base_url();?>assetsDelete/"+id;
	        $.ajax({
	            type: "POST",
	            url: urldata,
	            data: {id:id},
	            success:function(data){
	                alert("Delete Success");
					location.reload();
	            }
	        });
		}
    }
</script>
