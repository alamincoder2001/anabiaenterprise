
<div class="form-horizontal">
	<form method="POST" id="assetsFormUpdate" action="#">
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="assetsname"> Assets Name </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-3">
				<input type="text" id="assetsname" value="<?= $edit->as_name; ?>" required name="assetsname" placeholder="Assets Name" class="form-control" />
				<span id="error"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="rate"> Rate </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-3">
				<input type="number" id="rate" value="<?= $edit->as_rate; ?>" required name="rate" placeholder="Rate" class="form-control" />
				<span id="error"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="qty"> Quantity </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-3">
				<input type="number" id="qty" value="<?= $edit->as_qty; ?>" required name="qty" placeholder="Quantity" onblur="TotalAmount()" class="form-control" />
				<span id="error"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="amount"> Amount </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-3">
				<input type="number" id="amount"  value="<?= $edit->as_amount; ?>" readonly name="amount" placeholder="Amount" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
			<label class="col-sm-1 control-label no-padding-right"></label>
			<div class="col-sm-8">
			    <button type="button" class="btn btn-sm btn-success" onclick="UpdateAssets(<?= $edit->as_id; ?>)" name="btnSubmit">
					Update
					<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
				</button>
			</div>
		</div>
	</form>
</div>