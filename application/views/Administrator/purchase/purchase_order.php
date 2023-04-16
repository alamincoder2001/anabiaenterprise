<style>
	.modal-mask {
		position: fixed;
		z-index: 9998;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, .5);
		display: table;
		transition: opacity .3s ease;
	}

	.modal-wrapper {
		display: table-cell;
		vertical-align: middle;
	}

	.modal-container {
		width: 400px;
		margin: 0px auto;
		background-color: #fff;
		border-radius: 2px;
		box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
		transition: all .3s ease;
		font-family: Helvetica, Arial, sans-serif;
	}

	.modal-header {
		padding-bottom: 0 !important;
	}

	.modal-header h3 {
		margin-top: 0;
		color: #42b983;
	}

	.modal-body {
		overflow-y: auto !important;
		height: 300px !important;
		margin: -8px -14px -44px !important;
	}

	.modal-default-button {
		float: right;
	}

	/*
* The following styles are auto-applied to elements with
* transition="modal" when their visibility is toggled
* by Vue.js.
*
* You can easily play with the modal transition by editing
* these styles.
*/

	.modal-enter {
		opacity: 0;
	}

	.modal-leave-active {
		opacity: 0;
	}

	.modal-enter .modal-container,
	.modal-leave-active .modal-container {
		-webkit-transform: scale(1.1);
		transform: scale(1.1);
	}

	.modal-footer {
		padding-top: 14px !important;
		margin-top: 30px !important;
	}

	.v-select {
		margin-bottom: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#branchDropdown .vs__actions button {
		display: none;
	}

	#branchDropdown .vs__actions .open-indicator {
		height: 15px;
		margin-top: 7px;
	}
</style>

<div class="row" id="purchase">

	<div style="display:none" id="serial-modal" v-if="" v-bind:style="{display:serialModalStatus?'block':'none'}">
		<transition name="modal">
			<div class="modal-mask">
				<div class="modal-wrapper">
					<div class="modal-container">

						<div class="modal-header">
							<slot name="header">
								<h3>IMEI Number Add</h3>
							</slot>
						</div>

						<div class="modal-body" style="overflow: hidden;
height: 100%;
margin: -8px -14px -44px;">
							<slot name="body">
								<form @submit.prevent="imei_add_action">
									<div class="form-group">

										<div class="col-sm-12" style="display: flex;
margin-bottom: 10px;">
											<input type="text" autocomplete="off" ref="imeinumberadd" id="imei_number" name="imei_number" v-model="get_imei_number" class="form-control" v-on:keyup="get_imei_enter" placeholder="please Enter IMEI Number" style="height: 30px;" />

											<input type="submit" class="btn btn-sm btn primary" style="border: none;
font-size: 13px;
line-height: 0.38;
margin-left: -1px;
background-color: #42b983 !important;
width: 7px;
height: 29px;
margin-left: 2px;" value="Add">
										</div>

								</form>

						</div>
						</slot>
						<table class="table">
							<thead>
								<tr>
									<th scope="col">SL</th>
									<th scope="col">IMEI</th>
									<th scope="col">Product</th>
									<th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>

								<tr v-for="(product, sl) in imei_cart">
									<th scope="row">{{sl+1}}</th>
									<td>{{product.imeiNumber}}</td>
									<td>{{product.name}}</td>
									<td @click="remove_imei_item(product.imeiNumber)"> <span class="badge badge-danger badge-pill" style="cursor:pointer"><i class="fa fa-times"></i></td>
								</tr>

							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<slot name="footer">
							<button class="modal-default-button" @click="serialHideModal" style="    background: #59b901;
border: none;
font-size: 18px;
color: white;">
								OK
							</button>
							<button class="modal-default-button" @click="serialHideModal" style="    background: rgb(255, 255, 255);
border: none;
font-size: 18px;
color: #de0000;
margin-right: 6px;">
								Close
							</button>
						</slot>
					</div>
				</div>
			</div>
	</div>
	</transition>
</div>
<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
	<div class="row">
		<div class="form-group">
			<label class="col-sm-1 control-label no-padding-right"> Invoice no </label>
			<div class="col-sm-2">
				<input type="text" id="invoice" name="invoice" v-model="purchase.invoice" readonly style="height:26px;" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-right"> Purchase For </label>
			<div class="col-sm-3">
				<v-select id="branchDropdown" v-bind:options="branches" v-model="selectedBranch" label="Brunch_name" disabled></v-select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label no-padding-right"> Date </label>
			<div class="col-sm-3">
				<input class="form-control" id="purchaseDate" name="purchaseDate" type="date" v-model="purchase.purchaseDate" v-bind:disabled="userType == 'u' ? true : false" style="border-radius: 5px 0px 0px 5px !important;padding: 4px 6px 4px !important;width: 230px;" />
			</div>
		</div>
		<div class="form-group" style="display: none;">

			<div class="col-sm-4" style="display: flex;margin-left: -12px;">
				<label class="col-sm-4 control-label no-padding-right"> Reference </label>
				<input class="form-control" id="reference" type="text" v-model="purchase.reference" />
			</div>
		</div>
	</div>
</div>

<div class="col-xs-9 col-md-9 col-lg-9">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">Supplier & Product Information</h4>
			<div class="widget-toolbar">
				<a href="#" data-action="collapse">
					<i class="ace-icon fa fa-chevron-up"></i>
				</a>

				<a href="#" data-action="close">
					<i class="ace-icon fa fa-times"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="widget-main">
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="col-sm-4 control-label no-padding-right"> Supplier </label>
							<div class="col-sm-7">
								<v-select v-bind:options="suppliers" v-model="selectedSupplier" v-on:input="onChangeSupplier" label="display_name"></v-select>
							</div>
							<div class="col-sm-1" style="padding: 0;">
								<a href="<?= base_url('supplier') ?>" title="Add New Supplier" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
							</div>
						</div>

						<div class="form-group" style="display:none;" v-bind:style="{display: selectedSupplier.Supplier_Type == 'G' ? '' : 'none'}">
							<label class="col-sm-4 control-label no-padding-right"> Name </label>
							<div class="col-sm-8">
								<input type="text" placeholder="Supplier Name" class="form-control" v-model="selectedSupplier.Supplier_Name" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label no-padding-right"> Mobile No </label>
							<div class="col-sm-8">
								<input type="text" placeholder="Mobile No" class="form-control" v-model="selectedSupplier.Supplier_Mobile" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' ? false : true" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label no-padding-right"> Address </label>
							<div class="col-sm-8">
								<textarea class="form-control" v-model="selectedSupplier.Supplier_Address" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' ? false : true"></textarea>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<form v-on:submit.prevent="addToCart">
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Product </label>
								<div class="col-sm-7">
									<v-select id="product" v-bind:options="products" v-model="selectedProduct" ref="product" label="display_text" v-on:input="onChangeProduct"></v-select>
								</div>
								<div class="col-sm-1" style="padding: 0;">
									<a href="<?= base_url('product') ?>" title="Add New Product" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
								</div>
							</div>
							<div class="form-group" style="display:none;">
								<label class="col-sm-4 control-label no-padding-right"> Group Name</label>
								<div class="col-sm-8">
									<input type="text" id="group" name="group" class="form-control" placeholder="Group name" readonly />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Pur. Rate </label>
								<div class="col-sm-8">
									<input type="text" id="purchaseRate" name="purchaseRate" class="form-control" placeholder="Pur. Rate" v-model="selectedProduct.Product_Purchase_Rate" v-on:input="productTotal" autocomplete="off" />
								</div>
							</div>
							<tr>
								<td>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right">Discount (%)</label>
										<div class="col-sm-8">
											<input type="number" step="0.01" id="discount" name="discount" class="form-control" value="0" v-model=" selectedProduct.discount" v-on:input="productTotal" autocomplete="off" />
										</div>
									</div>
								</td>
							</tr>

							<div class="form-group" v-if="selectedProduct.is_imei != 'true'">
								<label class="col-sm-4 control-label no-padding-right"> Qty </label>
								<div class="col-sm-8" style="display: flex;">
									<input type="text" step="0.01" id="quantity" autocomplete="off" name="quantity" class="form-control" placeholder="Quantity" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" />
								</div>
							</div>
							<div class="form-group" v-else>
								<label class="col-sm-4 control-label no-padding-right"> IMEI Qty </label>
								<div class="col-sm-8" style="display: flex;">
									<input type="text" step="0.01" id="quantity" autocomplete="off" name="quantity" class="form-control" placeholder="IMEI Quantity" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" />
									<button type="button" id="show-modal" @click="serialShowModal" style="background: rgb(210, 0, 0);color: white;border: none;font-size: 15px;height: 24px;margin-left: 1px;"><i class="fa fa-plus"></i></button>
								</div>
							</div>
							<div class="form-group" style="display:none;">
								<label class="col-sm-4 control-label no-padding-right"> Cost </label>
								<div class="col-sm-3">
									<input type="text" id="cost" name="cost" class="form-control" placeholder="Cost" readonly />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Total Amount </label>
								<div class="col-sm-8">
									<input type="text" id="productTotal" name="productTotal" class="form-control" readonly v-model="selectedProduct.total" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Selling Price </label>
								<div class="col-sm-8">
									<input type="text" id="sellingPrice" name="sellingPrice" class="form-control" v-model="selectedProduct.Product_SellingPrice" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> </label>
								<div class="col-sm-8">
									<button type="submit" class="btn btn-default pull-right">Add Cart</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
		<div class="table-responsive">
			<table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
				<thead>
					<tr>
						<th style="width:4%;color:#000;">SL</th>
						<th style="width:20%;color:#000;">Product Name</th>
						<th style="width:13%;color:#000;">Category</th>
						<th style="width:8%;color:#000;">Purchase Rate</th>
						<th style="width:5%;color:#000;">Quantity</th>
						<th style="width:5%;color:#000;">Discount</th>
						<th style="width:13%;color:#000;">Total Amount</th>
						<th style="width:20%;color:#000;">Action</th>
					</tr>
				</thead>
				<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
					<tr v-for="(product, sl) in cart">
						<td>{{ sl + 1}}</td>
						<td>{{ product.name }}
							<span v-if="product.is_imei == 'true'">
								<br>(<span v-for="(IMEICartStore, ind) in product.IMEICartStore ">{{IMEICartStore.imeiNumber }}<span v-if="(product.IMEICartStore.length  > -1) > ind">, </span>
								</span>)</span>
							</span>
						</td>
						<td>{{ product.categoryName }}</td>
						<td>{{ product.purchaseRate }}</td>
						<td>{{ product.quantity }}</td>
						<td>{{ product.discount }}</td>
						<td>{{ product.total }}</td>
						<td><a href="" v-on:click.prevent="removeFromCart(sl,product.productId,product)"><i class="fa fa-trash"></i></a></td>
					</tr>
					<tr>
						<td colspan="7"></td>
					</tr>
					<tr style="font-weight: bold;">
						<td colspan="4">Note</td>
						<td colspan="2" style="text-align:right">Total Qty = {{
								cart.reduce((prev, cur) => { return prev + parseFloat(cur.quantity)}, 0)
							 }}</td>
						<td colspan="3">Sub Total</td>
					</tr>
					<tr>
						<td colspan="6"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="purchase.note"></textarea></td>
						<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ purchase.subTotal }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">Amount Details</h4>
			<div class="widget-toolbar">
				<a href="#" data-action="collapse">
					<i class="ace-icon fa fa-chevron-up"></i>
				</a>

				<a href="#" data-action="close">
					<i class="ace-icon fa fa-times"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="widget-main">
				<div class="row">
					<div class="col-sm-12">
						<div class="table-responsive">
							<table style="color:#000;margin-bottom: 0px;">
								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Sub Total</label>
											<div class="col-sm-12">
												<input type="number" id="subTotal" name="subTotal" class="form-control" v-model="purchase.subTotal" readonly />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right"> Vat </label>
											<div class="col-sm-12">
												<input type="number" id="vatPercent" name="vatPercent" v-model="vatPercent" v-on:input="calculateTotal" style="width:50px;height:25px;" />
												<span style="width:20px;"> % </span>
												<input type="number" id="vat" name="vat" v-model="purchase.vat" readonly style="width:140px;height:25px;" />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Discount</label>
											<div class="col-sm-12">
												<input type="number" id="discount" name="discount" class="form-control" v-model="purchase.discount" v-on:input="calculateTotal" />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Transport / Labour Cost</label>
											<div class="col-sm-12">
												<input type="number" id="freight" name="freight" class="form-control" v-model="purchase.freight" v-on:input="calculateTotal" />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Total</label>
											<div class="col-sm-12">
												<input type="number" id="total" class="form-control" v-model="purchase.total" readonly />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Paid</label>
											<div class="col-sm-12">
												<input type="number" id="paid" class="form-control" v-model="purchase.paid" v-on:input="calculateTotal" />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Previous Due</label>
											<div class="col-sm-12">
												<input type="number" id="previousDue" name="previousDue" class="form-control" v-model="purchase.previousDue" readonly style="color:red;" />
											</div>
										</div>
									</td>
								</tr>

								<tr>
									<td>
										<div class="form-group">
											<label class="col-sm-12 control-label no-padding-right">Due</label>
											<div class="col-sm-12">
												<input type="number" id="due" name="due" class="form-control" v-model="purchase.due" readonly />
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form-group">
											<div class="col-sm-12">
												<input type="button" class="btn btn-success" value="Purchase" v-on:click="savePurchase" v-bind:disabled="purchaseOnProgress == true ? true : false" style="background:#000;color:#fff;padding:3px;margin-right: 15px;">
												<input type="button" class="btn btn-info" onclick="window.location = '<?php echo base_url(); ?>purchase'" value="New Purchase" style="background:#000;color:#fff;padding:3px;">
											</div>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!-- app -->
<script src="<?php echo base_url(); ?>assets/js/vue/vue.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script>
	Vue.component('v-select', VueSelect.VueSelect);
	// register modal component
	Vue.component('modal', {
		template: '#serial-modal'
	})
	new Vue({
		el: '#purchase',
		data() {
			return {
				purchase: {
					purchaseId: parseInt('<?php echo $purchaseId; ?>'),
					invoice: '<?php echo $invoice; ?>',
					purchaseFor: '',
					purchaseDate: moment().format('YYYY-MM-DD'),
					supplierId: '',
					subTotal: 0.00,
					vat: 0.00,
					discount: 0.00,
					freight: 0.00,
					total: 0.00,
					paid: 0.00,
					due: 0.00,
					previousDue: 0.00,
					note: '',
					reference: ''
				},
				serialModalStatus: false,
				get_imei_number: "",
				vatPercent: 0.00,
				branches: [],
				selectedBranch: {
					brunch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Brunch_name: "<?php echo $this->session->userdata('Brunch_name'); ?>"
				},
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: null,
					Supplier_Code: '',
					Supplier_Name: '',
					display_name: 'Select Supplier',
					Supplier_Mobile: '',
					Supplier_Address: '',
					Supplier_Type: ''
				},
				oldSupplierId: null,
				oldPreviousDue: 0,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: '',
					discount: 0
				},
				cart: [],
				imei_cart: [],
				IMEICartStore: [],
				purchaseOnProgress: false,
				userType: '<?php echo $this->session->userdata("accountType") ?>'
			}
		},
		created() {
			this.getBranches();
			this.getSuppliers();
			this.getProducts();

			if (this.purchase.purchaseId != 0) {
				this.getPurchase();
			}
		},
		methods: {
			get_imei_enter(event) {

			},
			serialShowModal() {
				this.serialModalStatus = true;
			},
			serialHideModal() {
				this.serialModalStatus = false;
			},
			async imei_add_action() {
				if (this.selectedProduct.Product_SlNo == '') {
					alert("Please select a product");
					return false;
				} else {
					if (this.selectedProduct.quantity <= 0 || this.selectedProduct.quantity == '' || this.selectedProduct.quantity == undefined) {
						alert("Please enter valid Quantity");
						return false;
					}
					await axios.post('/check_imei_number', {
						get_imei_number: this.get_imei_number
					}).then(res => {
						if (res.data > 0) {
							alert("Already Product Purchased !!");
							return false;


						}
					})
					if (this.selectedProduct.quantity <= this.imei_cart.length) {
						alert(`Invalid Quantity !! You have maximum quantity is ${this.selectedProduct.quantity}.`);
						this.get_imei_number = '';
						return false;
					}
					if (this.get_imei_number.trim() == '') {
						alert("IMEI Number is Required.");
						return false;
					}

					let cartInd = this.imei_cart.findIndex(p => p.imeiNumber == this.get_imei_number.trim());
					if (cartInd > -1) {
						alert('IMEI Number already exists in IMEI List');
						return false;
					} else {
						let imei_cart_obj = {
							productId: this.selectedProduct.Product_SlNo,
							name: this.selectedProduct.Product_Name,
							categoryId: this.selectedProduct.ProductCategory_ID,
							categoryName: this.selectedProduct.ProductCategory_Name,
							purchaseRate: this.selectedProduct.Product_Purchase_Rate,
							salesRate: this.selectedProduct.Product_SellingPrice,
							quantity: this.selectedProduct.quantity,
							total: this.selectedProduct.total,
							imeiNumber: this.get_imei_number
						}
						this.imei_cart.push(imei_cart_obj);
						this.get_imei_number = '';
					}

				}

				this.$refs.imeinumberadd.focus();
			},
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
					this.suppliers.unshift({
						Supplier_SlNo: 'S01',
						Supplier_Code: '',
						Supplier_Name: '',
						display_name: 'General Supplier',
						Supplier_Mobile: '',
						Supplier_Address: '',
						Supplier_Type: 'G'
					})
				})
			},
			getProducts() {
				axios.post('/get_products', {
					isService: 'false'
				}).then(res => {
					this.products = res.data;
				})
			},
			onChangeSupplier() {
				if (this.selectedSupplier.Supplier_SlNo == null) {
					return;
				}

				if (event.type == 'readystatechange') {
					return;
				}

				if (this.purchase.purchaseId != 0 && this.oldSupplierId != parseInt(this.selectedSupplier.Supplier_SlNo)) {
					let changeConfirm = confirm('Changing supplier will set previous due to current due amount. Do you really want to change supplier?');
					if (changeConfirm == false) {
						return;
					}
				} else if (this.purchase.purchaseId != 0 && this.oldSupplierId == parseInt(this.selectedSupplier.Supplier_SlNo)) {
					this.purchase.previousDue = this.oldPreviousDue;
					return;
				}
				axios.post('/get_supplier_due', {
					supplierId: this.selectedSupplier.Supplier_SlNo
				}).then(res => {
					if (res.data.length > 0) {
						this.purchase.previousDue = res.data[0].due;
					} else {
						this.purchase.previousDue = 0;
					}
				})

				this.calculateTotal();
			},
			onChangeProduct() {
				// this.selectedProduct.discount = 0
				// this.$refs.quantity.focus();
				if (this.selectedProduct.Product_Name != "") {
					this.$refs.quantity.focus();

				}

			},
			productTotal() {

				var price = this.selectedProduct.Product_Purchase_Rate * this.selectedProduct.quantity;
				var numVal2 = this.selectedProduct.discount / 100;
				var afterDiscount = price - (price * numVal2)
				this.selectedProduct.total = afterDiscount;
			},
			addToCart() {
				if (this.selectedProduct.is_imei == "true") {
					if (this.selectedProduct.quantity != this.imei_cart.length) {
						alert("Sorry !! your product quantity and Product IMEI quantity is not Valid.");
						return false;
					}
				}
				let cartInd = this.cart.findIndex(p => p.productId == this.selectedProduct.Product_SlNo);
				if (cartInd > -1) {
					alert('Product exists in cart');
					return;
				}
				let product = {
					productId: this.selectedProduct.Product_SlNo,
					name: this.selectedProduct.Product_Name,
					categoryId: this.selectedProduct.ProductCategory_ID,
					categoryName: this.selectedProduct.ProductCategory_Name,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate,
					salesRate: this.selectedProduct.Product_SellingPrice,
					is_imei: this.selectedProduct.is_imei,
					quantity: this.selectedProduct.quantity,
					total: this.selectedProduct.total,
					discount: this.selectedProduct.discount,
					IMEICartStore: []
				}
				this.imei_cart.forEach((obj) => {
					let getObj = Object.assign(obj, {
						discount: this.selectedProduct.discount
					});
					getObj = Object.assign(obj, {
						objLength: this.cart.length
					});
					product.IMEICartStore.push(getObj);
				});

				this.cart.push(product);
				this.imei_cart = [];
				this.get_imei_number = '';
				this.clearSelectedProduct();
				this.calculateTotal();

				document.querySelector('#product input[role="combobox"]').focus();

			},
			async remove_imei_item(imeiNumber) {
				var newImeiCart = this.imei_cart.filter((el) => {
					return el.imeiNumber != imeiNumber;
				});
				this.imei_cart = newImeiCart;
			},
			async removeFromCart(ind, prod_id, product) {
				if ((this.purchase.purchaseId != 0)) {
					await axios.post('/check_soldIMEI', {
						invoice: this.purchase.invoice,
						prodId: prod_id
					}).then(res => {
						if (res.data.trim() == 'no') {
							this.cart.splice(ind, 1);

							axios.post(`/delete_imeis`, {
								product: product.IMEICartStore
							}).then(res => {

							})


							var newImeiCartStore = this.IMEICartStore.filter((el) => {
								return el.productId != prod_id;
							});
							this.IMEICartStore = newImeiCartStore;




						} else {
							alert('You have already some IMEI Sold')
							return false;
						}
					});
				} else {
					this.cart.splice(ind, 1);

					var newImeiCartStore = this.IMEICartStore.filter((el) => {
						return el.productId != prod_id;
					});
					this.IMEICartStore = newImeiCartStore;


				}

				this.calculateTotal();
			},
			clearSelectedProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					Product_Code: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: '',
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					total: '',
					discount: 0
				}
			},
			calculateTotal() {
				this.purchase.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.total);
				}, 0);
				this.purchase.vat = (this.purchase.subTotal * this.vatPercent) / 100;
				this.purchase.total = (parseFloat(this.purchase.subTotal) + parseFloat(this.purchase.vat) + parseFloat(this.purchase.freight)) - this.purchase.discount;

				if (this.selectedSupplier.Supplier_Type == 'G') {
					this.purchase.paid = this.purchase.total;
					this.purchase.due = 0
				} else {
					this.purchase.paid = 0
					this.purchase.due = this.purchase.total - this.purchase.paid;
				}
			},
			savePurchase() {
				if (this.selectedSupplier.Supplier_SlNo == null) {
					alert('Select supplier');
					return;
				}

				if (this.purchase.purchaseDate == '') {
					alert('Enter purchase date');
					return;
				}

				if (this.cart.length == 0) {
					alert('Cart is empty');
					return;
				}

				this.purchase.supplierId = this.selectedSupplier.Supplier_SlNo;
				this.purchase.purchaseFor = this.selectedBranch.brunch_id;

				this.purchaseOnProgress = true;

				let data = {
					purchase: this.purchase,
					cartProducts: this.cart
				}

				if (this.selectedSupplier.Supplier_Type == 'G') {
					data.supplier = this.selectedSupplier;
				}

				let url = '/add_purchase';
				data.IMEICartStore = this.IMEICartStore;
				if (this.purchase.purchaseId != 0) {
					url = '/update_purchase';
				}

				axios.post(url, data).then(async res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						let conf = confirm('Do you want to view invoice?');
						if (conf) {
							window.open(`/purchase_invoice_print/${r.purchaseId}`, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/purchase';
						} else {
							window.location = '/purchase';
						}
					} else {
						this.purchaseOnProgress = false;
					}
				})
			},
			getPurchase() {
				axios.post('/get_purchases', {
					purchaseId: this.purchase.purchaseId
				}).then(res => {
					let r = res.data;
					let purchase = r.purchases[0];

					this.selectedSupplier.Supplier_SlNo = purchase.Supplier_SlNo;
					this.selectedSupplier.Supplier_Code = purchase.Supplier_Code;
					this.selectedSupplier.Supplier_Name = purchase.Supplier_Name;
					this.selectedSupplier.Supplier_Mobile = purchase.Supplier_Mobile;
					this.selectedSupplier.Supplier_Address = purchase.Supplier_Address;
					this.selectedSupplier.Supplier_Type = purchase.Supplier_Type;
					this.selectedSupplier.display_name = purchase.Supplier_Type == 'G' ? 'General Supplier' : `${purchase.Supplier_Code} - ${purchase.Supplier_Name}`;
					this.purchase.invoice = purchase.PurchaseMaster_InvoiceNo;
					this.purchase.purchaseFor = purchase.PurchaseMaster_PurchaseFor;
					this.purchase.purchaseDate = purchase.PurchaseMaster_OrderDate;
					this.purchase.supplierId = purchase.Supplier_SlNo;
					this.purchase.subTotal = purchase.PurchaseMaster_SubTotalAmount;
					this.purchase.vat = purchase.PurchaseMaster_Tax;
					this.purchase.discount = purchase.PurchaseMaster_DiscountAmount;
					this.purchase.freight = purchase.PurchaseMaster_Freight;
					this.purchase.total = purchase.PurchaseMaster_TotalAmount;
					this.purchase.paid = purchase.PurchaseMaster_PaidAmount;
					this.purchase.due = purchase.PurchaseMaster_DueAmount;
					this.purchase.previousDue = purchase.previous_due;
					this.purchase.note = purchase.PurchaseMaster_Description;
					this.purchase.reference = purchase.reference;

					this.oldSupplierId = purchase.Supplier_SlNo;
					this.oldPreviousDue = purchase.previous_due;

					this.vatPercent = (this.purchase.vat * 100) / this.purchase.subTotal;

					r.purchaseDetails.forEach(product => {
						let cartProduct = {
							productId: product.Product_IDNo,
							name: product.Product_Name,
							categoryId: product.ProductCategory_ID,
							categoryName: product.ProductCategory_Name,
							purchaseRate: product.PurchaseDetails_Rate,
							salesRate: product.Product_SellingPrice,
							quantity: product.PurchaseDetails_TotalQuantity,
							total: product.PurchaseDetails_TotalAmount,
							discount: product.PurchaseDetails_Discount,
							IMEICartStore: []
						}


						product.imei.forEach((obj) => {
							let imei_cart_obj = {
								productId: obj.ps_prod_id,
								name: product.Product_Name,
								categoryId: product.ProductCategory_ID,
								categoryName: product.ProductCategory_Name,
								purchaseRate: product.PurchaseDetails_Rate,
								salesRate: product.Product_SellingPrice,
								quantity: product.PurchaseDetails_TotalQuantity,
								total: product.PurchaseDetails_TotalAmount,
								imeiNumber: obj.ps_imei_number,
								discount: product.PurchaseDetails_Discount
							}
							cartProduct.IMEICartStore.push(imei_cart_obj);
						})
						this.cart.push(cartProduct);
					})
				})
			}
		}
	})
</script>