<style type="text/css">
	<style>.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
</style>
<div class="col-xs-12 col-md-12 col-lg-12" style="">
	<div class="form-group" style="margin-top:10px;">
		<label class="col-sm-1 control-label no-padding-right" for="searchtype"> Search Type </label>
		<div class="col-sm-2">
			<select id="searchtype" data-placeholder="Choose an Option..." class="chosen-select"  >
				<option value="0"></option>
				<option value="all"> All </option>
				<option value="date"> Date </option>
				<option value="emp"> Employee </option>
            </select>
		</div>
	</div>
	<div class="form-group" style="margin-top:10px;"  id="selectedEmployee">
		<label class="col-sm-1 control-label no-padding-right" for="searchtype"> Employee </label>
		<div class="col-sm-2">
			<select id="emp" data-placeholder="Choose an Option..." class="chosen-select"  >
				<option value="0">Select a Employee</option>
				<?php $employees =  $this->db->query("SELECT  * FROM tbl_employee  ")->result();
				  foreach ($employees as $key => $value) {?>
				  	<option value="<?= $value->Employee_SlNo?>"><?= $value->Employee_Name?></option>
				<?php    }?>
            </select>
		</div>
	</div>
	<span id="typeDate" >
		<div class="col-sm-2" >
			<div class="input-group">
				<input class="form-control date-picker" id="service_startdate" type="text" data-date-format="yyyy-mm-dd" style="border-radius: 5px 0px 0px 5px !important;" value="<?= date('Y-m-d');?>">
				<span class="input-group-addon" style="border-radius: 0px 4px 4px 0px !important;padding: 4px 6px 4px  !important;">
					<i class="fa fa-calendar bigger-110"></i>
				</span>
			</div>
		</div>
		<div class="col-sm-2">
			<div class="input-group">
				<input class="form-control date-picker" id="service_enddate" type="text" data-date-format="yyyy-mm-dd" style="border-radius: 5px 0px 0px 5px !important;" value="<?= date('Y-m-d');?>">
				<span class="input-group-addon" style="border-radius: 0px 4px 4px 0px !important;padding: 4px 6px 4px  !important;">
					<i class="fa fa-calendar bigger-110"></i>
				</span>
			</div>
		</div>
	</span>
	
	<div class="form-group">
		<div class="col-sm-1">
			<input type="button" class="btn btn-primary" id="searchforRecord" value="Show Report" style="margin-top:0px;border:0px;height:28px;">
		</div>
	</div>
	<div id="show_record_here">
	</div>
</div>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>

<script type="text/javascript">

	$(document).on('change','#searchtype',function(){
		if ($(this).val()=='all') {
			$('#typeDate').css('display','none')
		}else{
			$('#typeDate').css('display','block')
		}
		if ($(this).val()=='date') {
			$('#typeDate').css('display','block')
		}
		if ($(this).val()=='emp') {
			$('#selectedEmployee').css('display','block')
		}else{
			$('#selectedEmployee').css('display','none')
		}
	})
	$(document).on('click','#searchforRecord',function(){
		let searchtype = $('#searchtype').val();
		let service_startdate = $('#service_startdate').val();
		let service_enddate = $('#service_enddate').val();
		let emp             = $('#emp').val();
		let obj = {
			searchtype:searchtype,
			service_startdate:service_startdate,
			service_enddate:service_enddate,
			emp:emp
		}
		axios.post('/service_record',obj)
		.then((res)=>{
			$('#show_record_here').html(res.data);
		})
	});
</script>