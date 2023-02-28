<div class="row">
<div class="col-xs-12">
	<?php if($selected){ ?>
	<form class="form-vertical" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>company_profile_Update">
	<div class="col-sm-4 col-sm-offset-1">
		<div class="form-group">
			<label class="col-sm-12 control-label" for="">Company Logo</label>
			<div class="col-sm-12">
			<div class="left"> 
				<?php if($selected->Company_Logo_thum != "") {?>
				<img id="hideid" src="<?php echo base_url().'uploads/company_profile_thum/'.$selected->Company_Logo_thum; ?>" alt="" style="width:100px">
				<?php } else{ ?>
				<img id="hideid" src="<?php echo base_url();?>images/No-Image-.jpg" alt="" style="width:200px">
				<?php } ?>
					<img id="preview" src="#" style="width:100px;height:100px" hidden>
			</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-12 control-label" for="companyLogo">Change Logo</label>
			<div >
				<input name="companyLogo" id="companyLogo" type="file"  onchange="readURL(this)" class="form-control" style="height:35px;" />
			</div>
		</div>
		
		<div class="control-group" style="margin-top:30px;">
		<label class="col-sm-12 control-label bolder blue">Invoice Print Type</label>
		<div class="radio">
			<label>
				<input name="inpt" id="a4"  type="radio" value="1" <?php if($selected->print_type==1){ echo "checked"; } ?>  class="ace" />
				<span class="lbl"> A4 Size</span>
			</label>
		</div>
		
		<div class="radio">
			<label>
				<input name="inpt" id="a42" type="radio" value="2" <?php if($selected->print_type==2){ echo "checked"; } ?>  class="ace" />
				<span class="lbl"> 1/2 of A4 Size</span>
			</label>
		</div>
		
		<div class="radio">
			<label>
				<input name="inpt" id="pos" type="radio" value="3" <?php if($selected->print_type==3){ echo "checked"; } ?>  class="ace" />
				<span class="lbl"> POS </span>
			</label>
		</div>
	</div>
	</div>
	
		
	<div class="col-sm-5 col-sm-offset-1">
		<div class="form-group">
			<label class="col-sm-12 control-label" for="form-field-1"> Company Name </label>
			<div class="col-sm-12">
				<input name="Company_name" type="text" id="Company_name" value="<?php echo $selected->Company_Name; ?>" class="form-control" />
                <input name="iidd" type="hidden" id="iidd" value="<?php echo $selected->Company_SlNo; ?>" class="txt" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-12 control-label" for="form-field-1"> Description  </label>
			<div class="col-sm-12">
				<textarea id="Description" name="Description" class="form-control"><?php echo $selected->Repot_Heading; ?></textarea>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label" for=""> </label>
			<label class="col-sm-1 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" name="btnSubmit" title="Update" class="btn btn-sm btn-info pull-left">
						Update
						<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
				</button>
				
			</div>
		</div>
	</div>
	</form>
	<?php 
	}else{
	?>
	
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>company_profile_insert">
	<div class="col-sm-4 col-sm-offset-1">
		<div class="form-group">
			<label class="col-sm-12 control-label" for="pro_Name">Company Logo</label>
			<div class="col-sm-12">
                  <img id="hideid" src="<?php echo base_url();?>images/No-Image-.jpg" alt="" style="width:100px">
				  <img id="preview" src="#" style="width:100px;height:100px" hidden>
			</div>
		</div>

		<!-- <div class="form-group"> -->
			<label class="col-sm-12 control-label" for="pro_Name">Change Logo</label>
			<!-- <div class="col-sm-12"> -->
				<input name="companyLogo" required id="companyLogo" type="file" class="form-control" style="height:35px;" />
			<!-- </div> -->
		<!-- </div> -->
	
		<div class="control-group">
		<label class="col-sm-12 control-label bolder blue">Invoice Print Type</label>
		<div class="radio">
			<label>
				<input name="inpt" id="a4"  type="radio" value="1" class="ace" />
				<span class="lbl"> A4 Size</span>
			</label>
		</div>
		
		<div class="radio">
			<label>
				<input name="inpt" id="a42" type="radio" value="2" class="ace" />
				<span class="lbl"> 1/2 of A4 Size</span>
			</label>
		</div>
		
		<div class="radio">
			<label>
				<input name="inpt" id="pos" type="radio" value="3" class="ace" />
				<span class="lbl"> POS </span>
			</label>
		</div>
	</div>
	
	</div>
	
		
	<div class="col-sm-5 col-sm-offset-1">
		<div class="form-group">
			<label class="col-sm-12 control-label" for="form-field-1"> Company Name </label>
			<div class="col-sm-12">
				<input name="Company_name" type="text" id="Company_name" value="" class="form-control" />
                 <input name="iidd" type="hidden" id="iidd" value="" class="txt" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-12 control-label" for="form-field-1"> Description  </label>
			<div class="col-sm-12">
				<textarea id="Description" name="Description" class="form-control"></textarea>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label" for=""> </label>
			<label class="col-sm-1 control-label"></label>
			<div class="col-sm-6">
				<button type="submit" name="btnSubmit" title="Update" class="btn btn-sm btn-success pull-left">
						Save
						<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
				</button>
				
			</div>
		</div>
	</div>
	</form>
	<?php
	}
	?>
</div>
</div>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview').src=e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            $("#hideid").hide();
            $("#preview").show();
        }
    }
</script>

<script type="text/javascript">
    function Employee_submit(){
    	var logo = $('#companyLogo').val();
    	alert(logo);
    	if(logo == ""){
           alert('Please Select a logo')
            return false;
        }
        var Company_name = $("#Company_name").val();
		var inpt=$('input[name=inpt]:checked').val();
		//alert(inpt);
        if(Company_name == ""){
            $("#Company_name").css('border-color','red');
            return false;
        }
        var fd = new FormData();
        var Description = CKEDITOR.instances['Description'].getData();
        var Description=encodeURIComponent(Description);
          fd.append('companyLogo', $('#companyLogo')[0].files[0]);
          fd.append('Company_name', $('#Company_name').val());
          fd.append('Description',Description );
          fd.append('inpt',inpt );
          fd.append('iidd', $('#iidd').val());
         

          var x = $.ajax({
            url: "<?php echo base_url();?>Administrator/page/company_profile_Update/",
            type: "POST",
            data: fd,
            enctype: 'multipart/form-data',
            processData: false, 
            contentType: false,
            success:function(data){         
            //$("#Company").html(data);
            alert("Update Success");
            //setTimeout( function() {$.fancybox.close(); },1200);
            location.reload();
            } 
          });
    }



    $(function() {
      $('.froala-editor').froalaEditor()
    });
</script>
   <!-- TextArea editor -->
<script type='text/javascript' src='<?php echo base_url(); ?>ckeditor/ckeditor.js'></script>

<script type="text/javascript">
    CKEDITOR.replace('Description');
    
</script>        
<!-- end TextArea editor -->