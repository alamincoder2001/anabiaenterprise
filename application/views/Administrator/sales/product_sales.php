<style>
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
<div id="sales" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
		<div class="row">
			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Invoice no </label>
				<div class="col-sm-2">
					<input type="text" id="invoiceNo" class="form-control" v-model="sales.invoiceNo" readonly />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Sales By </label>
				<div class="col-sm-2">
					<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" placeholder="Select Employee"></v-select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Sales From </label>
				<div class="col-sm-2">
					<v-select id="branchDropdown" v-bind:options="branches" label="Brunch_name" v-model="selectedBranch" disabled></v-select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<input class="form-control" id="salesDate" type="date" v-model="sales.salesDate" v-bind:disabled="userType == 'u' ? true : false" />
				</div>
			</div>
			<div class="form-group" style="display: none;">

				<div class="col-sm-4" style="display: flex;margin-left: -12px;">
					<label class="col-sm-4 control-label no-padding-right"> Reference : </label>
					<input class="form-control" id="reference" type="text" v-model="sales.reference" />
				</div>
			</div>
		</div>
	</div>

	<div class="col-xs-9 col-md-9 col-lg-9">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Sales Information</h4>
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
						<div class="col-sm-5">
							<div class="form-group clearfix" style="margin-bottom: 8px;">
								<label class="col-sm-4 control-label no-padding-right"> Sales Type </label>
								<div class="col-sm-8">
									<input type="radio" name="salesType" value="retail" v-model="sales.salesType" v-on:change="onSalesTypeChange"> Retail &nbsp;
									<input type="radio" name="salesType" value="wholesale" v-model="sales.salesType" v-on:change="onSalesTypeChange"> Wholesale
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Customer </label>
								<div class="col-sm-7">
									<v-select v-bind:options="customers" label="display_name" v-model="selectedCustomer" v-on:input="customerOnChange"></v-select>
								</div>
								<div class="col-sm-1" style="padding: 0;">
									<a href="<?= base_url('customer') ?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
								</div>
							</div>

							<div class="form-group" style="display:none;" v-bind:style="{display: selectedCustomer.Customer_Type == 'G' ? '' : 'none'}">
								<label class="col-sm-4 control-label no-padding-right"> Name </label>
								<div class="col-sm-8">
									<input type="text" id="customerName" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Mobile No </label>
								<div class="col-sm-8">
									<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Address </label>
								<div class="col-sm-8">
									<textarea id="address" placeholder="Address" class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true"></textarea>
								</div>
							</div>
						</div>

						<div class="col-xs-12 col-lg-5">
							<form v-on:submit.prevent="addToCart">
								<div class="form-group clearfix" style="margin-bottom: 8px;">
									<label class="col-xs-3 control-label no-padding-right"></label>
									<div class="col-xs-9">
										<input type="radio" value="false" v-model="Is_IMEI" id="product"> <label for="product">Product</label> &nbsp;
										<input type="radio" value="true" v-model="Is_IMEI" id="imei"> <label for="imei">IMEI Serial</label>
									</div>
								</div>
								<div class="form-group" v-if="Is_IMEI != 'true'">
									<label class="col-sm-3 control-label no-padding-right"> Product </label>
									<div class="col-sm-9">
										<v-select v-bind:options="IMEIStore" v-model="selectedIEMI" label="display_text" v-on:input="onChangeIMEI"></v-select>
									</div>
								</div>
								<div class="form-group" v-else>
									<label class="col-sm-3 control-label no-padding-right"> IMEI </label>
									<div class="col-sm-9">
										<v-select v-bind:options="IMEIStore" v-model="selectedIEMI" label="ps_imei_number" v-on:input="onChangeIMEI"></v-select>
									</div>
								</div>
								<div class="form-group" style="display: none;">
									<label class="col-sm-3 control-label no-padding-right"> Brand </label>
									<div class="col-sm-9">
										<input type="text" id="brand" placeholder="Group" class="form-control" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Sale Rate </label>
									<div class="col-sm-9">
										<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedIEMI.Product_SellingPrice" v-on:input="calCulateCart" />

									</div>
								</div>
								<div class="form-group" v-if="selectedIEMI.is_imei != 'true' && Is_IMEI != 'true'">
									<label class="col-sm-3 control-label no-padding-right"> Quantity </label>
									<div class="col-sm-9">
										<input type="number" min="0" step="0.01" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedIEMI.quantity" v-on:input="productTotal" autocomplete="off" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right">Discount%</label>
									<div class="col-sm-9">
										<input type="number" id="discount" placeholder="discount" step="0.01" class="form-control" v-model="selectedIEMI.SaleDetails_Discount" v-on:input="productTotal" value="0" autocomplete="off" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Amount </label>
									<div class="col-sm-9">
										<input type="text" placeholder="Amount" class="form-control" v-model="selectedIEMI.total" readonly="" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> </label>
									<div class="col-sm-9">
										<button type="submit" class="btn btn-default pull-right">Add to Cart</button>
									</div>
								</div>
							</form>

						</div>
						<div class="col-sm-2">
							<div style="display:none;" v-bind:style="{display:sales.isService == 'true' ? 'none' : ''}">
								<div class="text-center" style="display:none;" v-bind:style="{color: productStock > 0 ? 'green' : 'red', display: this.selectedIEMI.ps_prod_id == '' ? 'none' : ''}"> {{ productStockText }}</div class="text-center">

								<input type="text" id="productStock" v-model="productStock" readonly style="border:none;font-size:20px;width:100%;text-align:center;color:green"><br>
								<input type="text" id="stockUnit" v-model="selectedProduct.Unit_Name" readonly style="border:none;font-size:12px;width:100%;text-align: center;"><br><br>
							</div>
							<input type="password" ref="productPurchaseRate" v-model="selectedIEMI.purchase_rate" v-on:mousedown="toggleProductPurchaseRate" v-on:mouseup="toggleProductPurchaseRate" readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
					<thead>
						<tr class="">
							<th style="width:10%;color:#000;">Sl</th>
							<th style="width:25%;color:#000;">Category</th>
							<th style="width:20%;color:#000;">Product Name</th>
							<th style="width:7%;color:#000;">Qty</th>
							<th style="width:8%;color:#000;">Rate</th>
							<th style="width:8%;color:#000;">Discount</th>
							<th style="width:15%;color:#000;">Total Amount</th>
							<th style="width:15%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart">
							<td>{{ sl + 1 }}</td>
							<td>{{ product.categoryName }}</td>
							<td>{{ product.name }}
								<span v-if="product.is_imei == 'true'">
									<br>
									(<span v-for="(IMEICart, ind) in product.IMEIStore ">{{IMEICart.ps_imei_number}}<span v-if="(product.IMEIStore.length  > -1) > ind">, </span>
									</span>)</span>
								</span>
							</td>
							<td>{{ product.quantity }}</td>
							<td>{{ product.salesRate }}</td>
							<td>{{ product.SaleDetails_Discount }}</td>
							<td>{{ product.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl,product.IMEIStore)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="7"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="2">Note</td>
							<td colspan="2" style="text-align:right">Total Qty = {{
								cart.reduce((prev, cur) => { return prev + parseFloat(cur.quantity)}, 0).toFixed(2)
							 }}</td>
							<td colspan="4">Total</td>
						</tr>

						<tr>
							<td colspan="4"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="sales.note"></textarea></td>
							<td colspan="5" style="padding-top: 15px;font-size:18px;">{{ sales.total }}</td>
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
								<table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Sub Total</label>
												<div class="col-sm-12">
													<input type="number" id="subTotal" class="form-control" v-model="sales.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right"> Vat </label>
												<div class="col-sm-4">
													<input type="number" id="vatPercent" class="form-control" v-model="vatPercent" v-on:input="calculateTotal" />
												</div>
												<label class="col-sm-1 control-label no-padding-right">%</label>
												<div class="col-sm-7">
													<input type="number" id="vat" readonly="" class="form-control" v-model="sales.vat" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Discount Persent</label>

												<div class="col-sm-4">
													<input type="number" id="discountPercent" class="form-control" v-model="discountPercent" v-on:input="calculateTotal" />
												</div>

												<label class="col-sm-1 control-label no-padding-right">%</label>

												<div class="col-sm-7">
													<input type="number" id="discount" class="form-control" v-model="sales.discount" v-on:input="calculateTotal" />
												</div>

											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Transport Cost</label>
												<div class="col-sm-12">
													<input type="number" class="form-control" v-model="sales.transportCost" v-on:input="calculateTotal" />
												</div>
											</div>
										</td>
									</tr>

									<tr style="display:none;">
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Round Of</label>
												<div class="col-sm-12">
													<input type="number" id="roundOf" class="form-control" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Total</label>
												<div class="col-sm-12">
													<input type="text" id="total" class="form-control" v-model="sales.total" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Paid</label>
												<div class="col-sm-12">
													<input type="number" id="paid" class="form-control" v-model="sales.paid" v-on:input="calculateTotal" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? true : false" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label">Due</label>
												<div class="col-sm-6">
													<input type="number" id="due" class="form-control" v-model="sales.due" readonly />
												</div>
												<div class="col-sm-6">
													<input type="number" id="previousDue" class="form-control" v-model="sales.previousDue" readonly style="color:red;" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<div class="col-sm-6">
													<input type="button" class="btn btn-default btn-sm" value="Sale" v-on:click="saveSales" v-bind:disabled="saleOnProgress ? true : false" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">
												</div>
												<div class="col-sm-6">
													<a class="btn btn-info btn-sm" v-bind:href="`/sales/${sales.isService == 'true' ? 'service' : 'product'}`" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">New Sale</a>
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

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#sales',
		data() {
			return {
				sales: {
					salesId: parseInt('<?php echo $salesId; ?>'),
					invoiceNo: '<?php echo $invoice; ?>',
					salesBy: '<?php echo $this->session->userdata("FullName"); ?>',
					salesType: 'retail',
					salesFrom: '',
					salesDate: '',
					customerId: '',
					employeeId: null,
					subTotal: 0.00,
					discount: 0.00,
					vat: 0.00,
					transportCost: 0.00,
					total: 0.00,
					paid: 0.00,
					previousDue: 0.00,
					due: 0.00,
					isService: '<?php echo $isService; ?>',
					note: '',
					reference: ''
				},
				vatPercent: 0,
				discountPercent: 0,
				cart: [],
				employees: [],
				selectedEmployee: null,
				branches: [],
				selectedBranch: {
					brunch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Brunch_name: "<?php echo $this->session->userdata('Brunch_name'); ?>"
				},
				customers: [],
				selectedCustomer: {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: '',
					display_name: 'Select Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: ''
				},
				oldCustomerId: null,
				oldPreviousDue: 0,
				products: [],
				IMEIStore: [],
				selectedIEMI: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00,
					ps_prod_id: '',
					ps_imei_number: '',
					SaleDetails_Discount: 0,
					ProductCategory_Name: '',
					ProductCategory_ID: ''
				},
				selectedProduct: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00
				},
				productPurchaseRate: '',
				productStockText: '',
				productStock: '',
				saleOnProgress: false,
				Is_IMEI: false,
				userType: '<?php echo $this->session->userdata("accountType"); ?>'
			}
		},
		watch: {
			Is_IMEI() {
				if (this.Is_IMEI == "false") {
					this.IMEIStore = [];
					this.IMEIStore = this.products;
					this.selectedIEMI = {
						Product_SlNo: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: 0,
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0.00,
						vat: 0.00,
						total: 0.00,
						ps_prod_id: '',
						ps_imei_number: '',
						SaleDetails_Discount: 0,
						ProductCategory_Name: '',
						ProductCategory_ID: ''
					}
				} else {
					this.IMEIStore = [];
					this.GetIMEIList();
					this.selectedIEMI = {
						Product_SlNo: '',
						display_text: '',
						Product_Name: '',
						Unit_Name: '',
						quantity: 1,
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0.00,
						vat: 0.00,
						total: 0.00,
						ps_prod_id: '',
						ps_imei_number: 'Select Product IMEI',
						SaleDetails_Discount: 0,
						ProductCategory_Name: '',
						ProductCategory_ID: ''
					}
				}
			}
		},
		created() {
			this.sales.salesDate = moment().format('YYYY-MM-DD');
			this.getEmployees();
			this.getBranches();
			this.getCustomers();
			this.getProducts();
			if (this.sales.salesId != 0) {
				this.getSales();
			}
		},
		methods: {
			async GetIMEIList() {
				await axios.get('/GetIMEIList').then(res => {
					this.IMEIStore = res.data;
				})
			},

			calCulateCart() {
				this.productTotal()
				// 	alert()

				// if(this.selectedIEMI.Product_SlNo<=0){
				// 		alert("please select a product");
				// 		return false;
				//    }
				// var numVal1 = this.selectedIEMI.Product_SellingPrice*1;
				//          var numVal2 = this.selectedIEMI.SaleDetails_Discount/ 100;
				//          var totalValue = numVal1- (numVal1 * numVal2)
				// this.selectedIEMI.total  = totalValue.toFixed(2);
				//           console.log(this.selectedIEMI.total)
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})

			},
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				});
			},
			getCustomers() {
				axios.post('/get_customers', {
					customerType: this.sales.salesType
				}).then(res => {
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: 'C01',
						Customer_Code: '',
						Customer_Name: '',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G'
					})
				})
			},
			getProducts() {
				axios.post('/get_products', {
					isService: this.sales.isService
				}).then(res => {
					if (this.sales.salesType == 'wholesale') {
						this.products = res.data.filter((product) => product.Product_WholesaleRate > 0 && product.is_imei == 'false');
						this.products.map((product) => {
							return product.Product_SellingPrice = product.Product_WholesaleRate;
						})
					} else {
						this.products = res.data.filter(p => p.is_imei == 'false');
					}
					this.IMEIStore = this.products
				})
			},
			productTotal() {
				// this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
				if (this.Is_IMEI != "true") {
					var numVal1 = this.selectedIEMI.Product_SellingPrice * parseFloat(this.selectedIEMI.quantity);
				} else {
					var numVal1 = this.selectedIEMI.Product_SellingPrice * 1;
				}
				var numVal2 = this.selectedIEMI.SaleDetails_Discount / 100;
				var totalValue = numVal1 - (numVal1 * numVal2)
				this.selectedIEMI.total = totalValue.toFixed(2);

			},
			onSalesTypeChange() {
				this.selectedCustomer = {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: '',
					display_name: 'Select Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: ''
				}
				this.getCustomers();

				this.clearProduct();
				this.getProducts();
			},
			customerOnChange() {
				if (this.selectedCustomer.Customer_SlNo == '') {
					return;
				}
				if (event.type == 'readystatechange') {
					return;
				}

				if (this.sales.salesId != 0 && this.oldCustomerId != parseInt(this.selectedCustomer.Customer_SlNo)) {
					let changeConfirm = confirm('Changing customer will set previous due to current due amount. Do you really want to change customer?');
					if (changeConfirm == false) {
						return;
					}
				} else if (this.sales.salesId != 0 && this.oldCustomerId == parseInt(this.selectedCustomer.Customer_SlNo)) {
					this.sales.previousDue = this.oldPreviousDue;
					return;
				}
				axios.post('/get_customer_due', {
					customerId: this.selectedCustomer.Customer_SlNo
				}).then(res => {
					if (res.data.length > 0) {
						this.sales.previousDue = res.data[0].dueAmount;
					} else {
						this.sales.previousDue = 0;
					}
				})
				if (this.selectedCustomer != null && this.selectedCustomer.Customer_Type == "G") {
					this.sales.due = 0;
					this.sales.paid = this.sales.total;
				}else{
					this.sales.due = this.sales.total;
					this.sales.paid = 0;
				}
			},
			async productOnChange() {
				this.productStock = await axios.post('/get_product_stock', {
					productId: this.Is_IMEI != true ? this.selectedIEMI.Product_SlNo : this.selectedIEMI.ps_prod_id
				}).then(res => {
					return res.data;
				})
				this.productStockText = this.productStock > 0 ? "Available Stock" : "Stock Unavailable";

				if (this.Is_IMEI != "true") {
					this.$refs.quantity.focus();
				}
			},
			toggleProductPurchaseRate() {
				//this.productPurchaseRate = this.productPurchaseRate == '' ? this.selectedProduct.Product_Purchase_Rate : '';
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			addToCart() {
				if (this.selectedIEMI.quantity == null || this.selectedIEMI.quantity == 0) {
					alert("Quantity is required")
					return
				}
				let addNewObj = {
					productId: this.selectedIEMI.Product_SlNo,
					categoryName: this.selectedIEMI.ProductCategory_Name,
					ProductCategory_ID: this.selectedIEMI.ProductCategory_ID,
					name: this.selectedIEMI.Product_Name,
					salesRate: this.selectedIEMI.Product_SellingPrice,
					vat: this.selectedIEMI.vat,
					is_imei: this.selectedIEMI.is_imei,
					quantity: this.Is_IMEI != "true" ? this.selectedIEMI.quantity : 1,
					total: this.selectedIEMI.total,
					purchaseRate: this.selectedIEMI.Product_Purchase_Rate,
					purchase_discount: this.selectedIEMI.purchase_discount,
					SaleDetails_Discount: this.selectedIEMI.SaleDetails_Discount,
					IMEIStore: [{
						ps_imei_number: this.selectedIEMI.ps_imei_number
					}]
				}

				let checkCart = this.cart.filter((item, ind) => {
					return (item.productId == this.selectedIEMI.Product_SlNo) && (item.salesRate == this.selectedIEMI.Product_SellingPrice) && (item.SaleDetails_Discount == this.selectedIEMI.SaleDetails_Discount);
				});
				let getCurrentInd = this.cart.findIndex((item) => {
					return (item.productId == this.selectedIEMI.Product_SlNo) && (item.salesRate == this.selectedIEMI.Product_SellingPrice) && (item.SaleDetails_Discount == this.selectedIEMI.SaleDetails_Discount);
				});
				if (checkCart.length > 0) {
					checkCart.map((item) => {
						let storeObj = item.IMEIStore;
						let checkSameI = item.IMEIStore.findIndex((item) => {
							return addNewObj.IMEIStore[0]['ps_imei_number'] == item.ps_imei_number;
						})
						if (checkSameI > -1) {
							alert("Already Added !!");
							return false;
						}
						storeObj.push(addNewObj.IMEIStore[0]);
						this.cart[getCurrentInd].quantity = storeObj.length;
						this.cart[getCurrentInd].total = parseFloat(this.selectedIEMI.total) + parseFloat(this.cart[getCurrentInd].total);

					})
				} else {
					this.cart.push(addNewObj);
				}


				this.calculateTotal()


			},
			removeFromCart(ind, imei) {
				if (this.sales.salesId != 0) {
					axios.post('/remove_cart_imei', imei)
						.then((res) => {
							if (res.data == 'deleted') {
								this.cart.splice(ind, 1);
								this.calculateTotal();
							}
						})
				} else {
					this.cart.splice(ind, 1);
					this.calculateTotal();
				}
			},
			clearProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00
				}
				this.productStock = '';
				this.productStockText = '';
			},
			calculateTotal() {
				this.sales.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.total)
				}, 0).toFixed(2);
				this.sales.vat = ((parseFloat(this.sales.subTotal) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				if (event.target.id == 'discountPercent') {
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2);
				} else {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(2);
				}
				this.sales.total = ((parseFloat(this.sales.subTotal) + parseFloat(this.sales.vat) + parseFloat(this.sales.transportCost)) - parseFloat(this.sales.discount)).toFixed(2);
				if (this.selectedCustomer.Customer_Type == 'G') {
					this.sales.paid = this.sales.total;
				} else {
					this.sales.due = (parseFloat(this.sales.total) - parseFloat(this.sales.paid)).toFixed(2);
				}
			},
			onChangeIMEI() {

				///this.selectedIEMI.SaleDetails_Discount = 0;
				// this.selectedIEMI.total = (parseFloat(this.selectedIEMI.quantity.quantity) * parseFloat(this.selectedIEMI.Product_SellingPrice)).toFixed(2);
				this.productOnChange()
				this.productTotal()

				// 	this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
				// 	console.log(this.selectedProduct.total)

			},
			saveSales() {


				if (this.selectedCustomer.Customer_SlNo == '') {
					alert('Select Customer');
					return;
				}
				if (this.cart.length == 0) {
					alert('Cart is empty');
					return;
				}

				if (parseFloat(this.selectedCustomer.Customer_Credit_Limit) < (parseFloat(this.sales.due) + parseFloat(this.sales.previousDue))) {
					alert(`Customer credit limit (${this.selectedCustomer.Customer_Credit_Limit}) exceeded`);
					return;
				}

				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null) {
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}

				let url = "/add_sales";
				if (this.sales.salesId != 0) {
					url = "/update_sales";
				}

				this.sales.customerId = this.selectedCustomer.Customer_SlNo;
				this.sales.salesFrom = this.selectedBranch.brunch_id;

				this.saleOnProgress = true;

				let data = {
					sales: this.sales,
					cart: this.cart
				}

				if (this.selectedCustomer.Customer_Type == 'G') {
					data.customer = this.selectedCustomer;
				}
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm('Sale success, Do you want to view invoice?');
						if (conf) {
							window.open('/sale_invoice_print/' + r.salesId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
						} else {
							window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
						}
					} else {
						alert(r.message);
						this.saleOnProgress = false;
					}
				})
			},
			getSales() {
				axios.post('/get_sales', {
					salesId: this.sales.salesId
				}).then(res => {
					let r = res.data;
					let sales = r.sales[0];
					this.sales.salesBy = sales.AddBy;
					this.sales.salesFrom = sales.SaleMaster_branchid;
					this.sales.salesDate = sales.SaleMaster_SaleDate;
					this.sales.salesType = sales.SaleMaster_SaleType;
					this.sales.customerId = sales.SalseCustomer_IDNo;
					this.sales.employeeId = sales.Employee_SlNo;
					this.sales.subTotal = sales.SaleMaster_SubTotalAmount;
					this.sales.discount = sales.SaleMaster_TotalDiscountAmount;
					this.sales.vat = sales.SaleMaster_TaxAmount;
					this.sales.transportCost = sales.SaleMaster_Freight;
					this.sales.total = sales.SaleMaster_TotalSaleAmount;
					this.sales.paid = sales.SaleMaster_PaidAmount;
					this.sales.previousDue = sales.SaleMaster_Previous_Due;
					this.sales.due = sales.SaleMaster_DueAmount;
					this.sales.note = sales.SaleMaster_Description;
					this.sales.reference = sales.reference;

					this.oldCustomerId = sales.SalseCustomer_IDNo;
					this.oldPreviousDue = sales.SaleMaster_Previous_Due;

					this.vatPercent = parseFloat(this.sales.vat) * 100 / parseFloat(this.sales.subTotal);
					this.discountPercent = parseFloat(this.sales.discount) * 100 / parseFloat(this.sales.subTotal);

					this.selectedEmployee = {
						Employee_SlNo: sales.employee_id,
						Employee_Name: sales.Employee_Name
					}

					this.selectedCustomer = {
						Customer_SlNo: sales.SalseCustomer_IDNo,
						Customer_Code: sales.Customer_Code,
						Customer_Name: sales.Customer_Name,
						display_name: sales.Customer_Type == 'G' ? 'General Customer' : `${sales.Customer_Code} - ${sales.Customer_Name}`,
						Customer_Mobile: sales.Customer_Mobile,
						Customer_Address: sales.Customer_Address,
						Customer_Type: sales.Customer_Type
					}

					r.saleDetails.forEach(product => {
						let cartProduct = {
							productId: product.Product_IDNo,
							categoryName: product.ProductCategory_Name,
							name: product.Product_Name,
							salesRate: product.SaleDetails_Rate,
							vat: product.SaleDetails_Tax,
							quantity: product.SaleDetails_TotalQuantity,
							total: product.SaleDetails_TotalAmount,
							purchaseRate: product.Purchase_Rate,
							purchase_discount: product.Discount_amount,
							SaleDetails_Discount: product.SaleDetails_Discount,
							IMEIStore: product.imei

						}

						this.cart.push(cartProduct);
						console.log(this.cart)

					})

					this.getCustomers();
					this.getProducts();
				})


			}
		}
	})
</script>