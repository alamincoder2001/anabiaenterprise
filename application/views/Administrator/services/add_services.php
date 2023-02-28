<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#branchDropdown .vs__actions button{
		display:none;
	}
	#branchDropdown .vs__actions .open-indicator{
		height:15px;
		margin-top:7px;
	}
</style>
<div id="sales" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
		<div class="row">
			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right"> Service No </label>
				<div class="col-sm-2">
					<input type="text" id="invoiceNo" class="form-control" v-model="sales.invoiceNo" readonly />
				</div>
			</div>
			<div class="form-group" style="display: none;">
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
					<input class="form-control" id="salesDate" type="date" v-model="sales.salesDate" v-bind:disabled="userType == 'u' ? true : false"/>
				</div>
			</div>
		</div>
	</div>


	<div class="col-xs-9 col-md-9 col-lg-9">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Services Entry Information</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<form v-on:submit.prevent="addToCart">
			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
						<div class="col-sm-5">
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Employee </label>
								<div class="col-sm-7">
									<v-select v-bind:options="employees" label="display_name" v-model="selectedEmployee" v-on:input="employeeOnChange"></v-select>
								</div>
								<div class="col-sm-1" style="padding: 0;">
									<a href="<?= base_url('employee')?>" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Employee"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
								</div>
							</div>
							<div class="form-group"  v-bind:style="">
								<label class="col-sm-4 control-label no-padding-right"> Name </label>
								<div class="col-sm-8">
									<input type="text" id="employeeName" placeholder="Employee Name" class="form-control" v-model="selectedEmployee.Employee_Name"  />
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Mobile No </label>
								<div class="col-sm-8">
									<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedEmployee.Employee_ContactNo"/>
								</div>
							</div>
                            
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right"> Address </label>
								<div class="col-sm-8">
									<textarea id="address" placeholder="Address" class="form-control" v-model="selectedEmployee.Employee_PrasentAddress" ></textarea>
								</div>
							</div>
						</div>

						<div class="col-sm-5">
							
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> IMEI  </label>
									<div class="col-sm-9">
										<v-select v-bind:options="IMEIStore" v-model="IMEI" label="ps_imei_number" v-on:input="IMEINumber"></v-select>
									</div>
								</div>

									<!-- <div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> IMEI </label>
									<div class="col-sm-9">
										<input type="text" id="productTotal"  placeholder="IMEI " autocomplete="off" class="form-control" v-model="IMEI"  v-bind:style=" imeiCheck?'border: 1px solid red;' : '' " v-on:input="IMEI_Number_exist_heck"  />
									</div>
								</div> -->
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Product </label>
									<div class="col-sm-8">
										<v-select v-bind:options="products" v-model="selectedProduct" label="display_text" v-on:input="productOnChange"></v-select>
									</div>
									<div class="col-sm-1" style="padding: 0;">
										<a href="/product" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> P D. </label>
									<div class="col-sm-9">
										<input type="text" id="IMEI_purchase_date" placeholder="IMEI purchase date" class="form-control" autocomplete="off"  v-model="selectedIEMI.purchase_date"  />
									</div>
								</div>
									<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Customer  </label>
									<div class="col-sm-9">
										<v-select v-bind:options="customers" v-model="selectedCustomer" label="Customer_Name" v-on:input="onChangeCustomer"></v-select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> C. Name  </label>
									<div class="col-sm-9">
										<input type="text" id="c_name" placeholder="Customer Name" class="form-control" v-model="c_name" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Address </label>
									<div class="col-sm-9">
										<input type="text" id="c_address" placeholder="Customer address" class="form-control" autocomplete="off"  v-model="c_address"/>
									</div>
								</div>

								<div class="form-group" style="display: none;">
									<label class="col-sm-3 control-label no-padding-right"> Brand </label>
									<div class="col-sm-9">
										<input type="text" id="brand" placeholder="Group" class="form-control" />
									</div>
								</div>
								
								<div class="form-group" style="display: none;">
									<label class="col-sm-3 control-label no-padding-right"> Sale Rate </label>
									<div class="col-sm-9">
										<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedIEMI.Product_SellingPrice" v-on:input="calCulateCart"/>
									</div>
								</div>
								<div class="form-group" style="display: none;">
									<label class="col-sm-3 control-label no-padding-right">Discount%</label>
									<div class="col-sm-9">
										<input type="number" id="discount" placeholder="discount" step="0.01" class="form-control" v-model="selectedIEMI.SaleDetails_Discount" v-on:input="calCulateCart" value="0"  />
									</div>
								</div>
								<div class="form-group" style="display:none">
									<label class="col-sm-3 control-label no-padding-right"> Quantity </label>
									<div class="col-sm-9">
										<input type="number" step="0.01" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedIEMI.quantity" v-on:input="calCulateCart" autocomplete="off" value="1"/>
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Amount </label>
									<div class="col-sm-9">
										<input type="text" id="productTotal"  placeholder="Amount" class="form-control" autocomplete="off" v-model="total"  />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right"> Description </label>
									<div class="col-sm-9">
										<input type="text" id="productTotal"  placeholder="Note Here" class="form-control" autocomplete="off" v-model="desc"  />
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
							<th style="width:15%;color:#000;"> Customer Name</th>
							<th style="width:15%;color:#000;"> Customer Address</th>
							<th style="width:20%;color:#000;">Product Name</th>
							<th style="width:15%;color:#000;"> IMEI</th>
							<th style="width:15%;color:#000;">Amount</th>
							<th style="width:15%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart"> 
							<td>{{ sl + 1 }}</td>
							<td>{{ product.c_name }}</td>
							<td>{{ product.c_address }}</td>
							<td>{{ product.Product_Name }}</td>
							<td>{{ product.IMEI }}</td>
							<td>{{ product.total }}</td>
							
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="7"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="4">Note</td>
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
													<input type="number" id="vatPercent" class="form-control" step="2" v-model="vatPercent" v-on:input="calculateTotal"/>
												</div>
												<label class="col-sm-1 control-label no-padding-right">%</label>
												<div class="col-sm-7">
													<input type="number" id="vat" readonly="" class="form-control" v-model="sales.vat"/>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Discount Persent</label>

												<div class="col-sm-4">
													<input type="number" id="discountPercent" class="form-control" v-model="discountPercent" v-on:input="calculateTotal"/>
												</div>

												<label class="col-sm-1 control-label no-padding-right">%</label>

												<div class="col-sm-7">
													<input type="number" id="discount" class="form-control" v-model="sales.discount" v-on:input="calculateTotal"/>
												</div>

											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label no-padding-right">Transport Cost</label>
												<div class="col-sm-12">
													<input type="number" class="form-control" v-model="sales.transportCost" v-on:input="calculateTotal"/>
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
													<input type="number" id="paid" class="form-control" v-model="sales.paid" v-on:input="calculateTotal" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? true : false"/>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<label class="col-sm-12 control-label">Due</label>
												<div class="col-sm-6">
													<input type="number" id="due" class="form-control" v-model="sales.due" readonly/>
												</div>
												<div class="col-sm-6">
													<input type="number" id="previousDue" class="form-control" v-model="sales.previousDue" readonly style="color:red;"  />
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<div class="col-sm-6">
													<input type="button" class="btn btn-default btn-sm" value="Service" v-on:click="saveSales" v-bind:disabled="saleOnProgress ? true : false" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">
												</div>
												<div class="col-sm-6">
													<a class="btn btn-info btn-sm" v-bind:href="`/sales/${sales.isService == 'true' ? 'service' : 'product'}`" style="color: black!important;margin-top: 0px;width:100%;padding:5px;font-weight:bold;">New Service</a>
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

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	<?php 
			$salesId = empty($salesId)?0:$salesId;

	?>
	new Vue({
		el: '#sales',
		data(){
			return {
				sales:{
					salesId: parseInt('<?php echo $salesId;?>'),
					invoiceNo: '<?php echo $invoice;?>',
					salesBy: '<?php echo $this->session->userdata("FullName"); ?>',
					salesType: 'retail',
					salesFrom: '',
					salesDate: '',
					customerId: '',
					employeeId: null,
					subTotal: 0.00,
					discount: 0.00,
					vat: 0,
					transportCost: 0.00,
					total: 0.00,
					paid: 0.00,
					previousDue: 0.00,
					due: 0.00,
					isService: "",
					note: ''
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
				selectedCustomer:{
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: '',
					display_name: 'Select Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: ''
				},
				selectedEmployee:{
					Employee_SlNo: '',
					Employee_ID: '',
					Employee_Name: '',
					display_name: 'Select Employee',
					Employee_ContactNo: '',
					Employee_PrasentAddress: ''
				},
				
				oldCustomerId: null,
				oldPreviousDue: 0,
				products: [],
				IMEIStore:[],
				customers:[],
				selectedIEMI:{
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0.00,
					vat: 0.00,
					total: 0.00,
					ps_prod_id:'',
					ps_imei_number:'',
					SaleDetails_Discount:0.00,
					ProductCategory_Name:'',
					ProductCategory_ID:'',
					IMEI_purchase_date:'',
					purchase_date:''
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
					total: 0.00,

				},
				c_name:'',
				c_address:'',
				IMEI:'',
				total:0.00,
				desc:'',
				imeiCheck:false,
				productPurchaseRate: '',
				productStockText: '',
				productStock: '',
				saleOnProgress: false,
				userType: '<?php echo $this->session->userdata("accountType");?>'
			}
		},
		created(){
			this.sales.salesDate = moment().format('YYYY-MM-DD');
			this.getEmployees();
			this.getBranches();
			this.getCustomers();
			this.getProducts();
			this.GetIMEIList();
			if(this.sales.salesId != 0){
				this.getSales();
			}
		},
		methods:{
			onChangeCustomer(){

			


				this.c_name = this.selectedCustomer.Customer_Name
				this.c_address = this.selectedCustomer.Customer_Address
				
			},
			async getCustomers(){
				await axios.get('/get_customers').then(res=>{
                   this.customers = res.data;
                   console.log(res.data)
			  })
			},
			async  GetIMEIList(){
              await axios.get('/GetIMEIListService').then(res=>{
                   this.IMEIStore = res.data;
			  })
			},
			async IMEI_Number_exist_heck(){

				await axios.post('/IMEI_number_exist_check',{IMEI:this.IMEI}).then(res=>{
                   console.log(res.data);
                   if (res.data>0) {
                       this.imeiCheck = true;
                   }else{
                   	 this.imeiCheck = false;
                   }
			  })
			},
			calCulateCart(){
			if(this.selectedIEMI.Product_SlNo<=0){
					alert("please select a product");
					return false;
		    }
			var numVal1 = this.selectedIEMI.Product_SellingPrice*1;
            var numVal2 = this.selectedIEMI.SaleDetails_Discount/ 100;
            var totalValue = numVal1- (numVal1 * numVal2)
			this.selectedIEMI.total  = totalValue.toFixed(2);
             
			},
			get_employees(){
				// this.amount = this.select.amount;
				this.amount = this.select.amount;

			},
			getEmployees(){
				axios.get('/get_employees').then(res => {
						this.employees = res.data;
				})

			},
			getBranches(){
				axios.get('/get_branches').then(res=>{
					this.branches = res.data;
				});
			},
			getCustomers(){
				axios.post('/get_customers', {customerType: this.sales.salesType}).then(res=>{
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
			
			getProducts(){
				axios.post('/get_products', {isService: this.sales.isService}).then(res=>{
					if(this.sales.salesType == 'wholesale'){
						this.products = res.data.filter((product) => product.Product_WholesaleRate > 0);
						this.products.map((product) => {
							return product.Product_SellingPrice = product.Product_WholesaleRate;
						})
					} else {
						this.products = res.data;
					}
				})
			},
			productTotal(){
				this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
			},
			onSalesTypeChange(){
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
			employeeOnChange(){
				if(this.selectedCustomer.Customer_SlNo == ''){
					return;
				}
				if(event.type == 'readystatechange'){
					return;
				}

				if(this.sales.salesId != 0 && this.oldCustomerId != parseInt(this.selectedCustomer.Customer_SlNo)){
					let changeConfirm = confirm('Changing customer will set previous due to current due amount. Do you really want to change customer?');
					if(changeConfirm == false){
						return;
					}
				} else if(this.sales.salesId != 0 && this.oldCustomerId == parseInt(this.selectedCustomer.Customer_SlNo)){
					this.sales.previousDue = this.oldPreviousDue;
					return;
				}
				axios.post('/get_customer_due',{customerId: this.selectedCustomer.Customer_SlNo}).then(res=>{
					if(res.data.length > 0){
						this.sales.previousDue = res.data[0].dueAmount;
					} else {
						this.sales.previousDue = 0;
					}
				})
			},
			async productOnChange(){
				if((this.selectedProduct.Product_SlNo != '' || this.selectedProduct.Product_SlNo != 0) && this.sales.isService == 'false'){
					this.productStock = await axios.post('/get_product_stock', {productId: this.selectedProduct.Product_SlNo}).then(res => {
						return res.data;
					})

					this.productStockText = this.productStock > 0 ? "Available Stock" : "Stock Unavailable";
				}

				this.$refs.quantity.focus();
			},
			toggleProductPurchaseRate(){
				//this.productPurchaseRate = this.productPurchaseRate == '' ? this.selectedProduct.Product_Purchase_Rate : '';
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			addToCart(){
				if ( this.selectedProduct.Product_SlNo==0) {
					alert("please select a product")
					return false;
				}
				let addToCart = {
					Product_SlNo:this.selectedProduct.Product_SlNo,
					Product_Name:this.selectedProduct.Product_Name,
					ProductCategory_ID:this.selectedProduct.ProductCategory_ID,
					ProductCategory_Name:this.selectedProduct.ProductCategory_Name,
					c_name:this.c_name,
				    c_address:this.c_address,
				    IMEI:this.IMEI.ps_imei_number,
				    total:this.total,
				    desc:this.desc
				}
				this.cart.push(addToCart);
                this.selectedProduct.Product_SlNo = 0;
				this.selectedProduct.Product_Name = '';
				this.selectedProduct.ProductCategory_ID =0;
				this.selectedProduct.ProductCategory_Name = '';
				this.c_name = '';
				this.c_address = '';
				this.total = 0;
				this.desc = '';
				this.calculateTotal();

			},
			removeFromCart(ind,imei){

				this.cart.splice(ind, 1);

				this.calculateTotal();
				
			},
			async IMEINumber(){
				let IMEI = this.IMEI.ps_imei_number;
				this.selectedIEMI.purchase_date = this.IMEI.purchase_date
				await axios.post('/GetServiceStatus',{IMEI:IMEI})
				.then((res)=>{
					if (res.data>0) {
						console.log(res.data+' JH')
                        alert('IMEI already serviced')
					}else{
						return true;
					}
				})
				await axios.post('/IMEIWiseCustomer',{IMEI:IMEI})
				.then((res)=>{
					this.c_name = res.data[0].Customer_Name;
				    this.c_address = res.data[0].Customer_Address;

				    this.selectedProduct.Product_SlNo = this.IMEI.Product_SlNo
				    this.selectedProduct.display_text = this.IMEI.Product_Name
				    this.selectedProduct.Product_Name = this.IMEI.Product_Name

				})
			},
			clearProduct(){
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
			calculateTotal(){
				console.log(this.sales.total)
				this.sales.subTotal = this.cart.reduce((prev, curr) => { return prev + parseFloat(curr.total)}, 0).toFixed(2);


				this.sales.vat = ((parseFloat(this.sales.subTotal) * parseFloat(this.vatPercent)) / 100).toFixed(2);
				if(event.target.id == 'discountPercent'){
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2); 
				} else {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(2);
				}

				let vat = isNaN(this.sales.vat)?0: parseFloat(this.sales.vat);

				this.sales.total = ((parseFloat(this.sales.subTotal) +vat) - parseFloat(this.sales.discount)).toFixed(2);
				if(this.selectedCustomer.Customer_Type == 'G'){
					this.sales.paid = this.sales.total;
				} else {
					this.sales.due = (parseFloat(this.sales.total) - parseFloat(this.sales.paid)).toFixed(2);
				}

				console.log(parseFloat(this.sales.vat)+'y')


			},
			saveSales(){
				
				if(this.selectedEmployee.Employee_SlNo == ''){
					alert('Select Employee');
					return;
				}
				if(this.cart.length == 0){
					alert('Cart is empty');
					return;
				}
                
				if(this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null){
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}
                
				let url = "/add_services";
				if(this.sales.salesId != 0 ){
				
					url  = "/update_services";
				}
                
				this.sales.salesFrom = this.selectedBranch.brunch_id;

				this.saleOnProgress = true;

				let data = {
					sales: this.sales,
					cart: this.cart
				}
				
				axios.post(url, data).then(async res=> {
					let r = res.data;
					if(r.success){
						let conf = confirm('Service success, Do you want to view invoice?');
						if(conf){
							window.open('/serviceInvoicePrint/'+r.serviseId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/services';
						} else {
							window.location = '/services';
						}
					} else {
						alert(r.message);
						this.saleOnProgress = false;
					}
				})
			},
			getSales(){
				    axios.post('/servicesinvoices', {salesId: this.sales.salesId}).then(res=>{
					let r = res.data;
					let sales = r.sales[0];
					this.sales.salesFrom = sales.sv_branch;
					this.sales.salesDate = sales.SaleMaster_SaleDate;
					this.sales.salesType = sales.sv_date;
					// this.sales.customerId = sales.SalseCustomer_IDNo;
				
                     
					

					this.sales.subTotal = sales.sv_sale_subtotal;
					this.sales.discount = sales.sv_sale_total_discount;
					this.sales.vat = sales.sv_vat_amount;
					this.sales.transportCost = sales.sv_transport_cost;
					this.sales.total = sales.sv_total_sale_amount; 

					this.sales.paid = sales.sv_paid_amount;
					// this.sales.previousDue = sales.SaleMaster_Previous_Due;
					this.sales.due = sales.sv_sale_due;
					this.sales.note = sales.sv_desc;


					// this.oldCustomerId = sales.SalseCustomer_IDNo;
					// this.oldPreviousDue = sales.SaleMaster_Previous_Due;

					this.vatPercent = parseFloat(this.sales.sv_vat_amount) * 100 / parseFloat(this.sales.sv_sale_subtotal);
					this.discountPercent = parseFloat(this.sales.sv_sale_total_discount) * 100 / parseFloat(this.sales.sv_sale_subtotal);

					this.selectedEmployee = {
							Employee_SlNo : sales.Employee_SlNo,
							Employee_Name : sales.Employee_Name,
							Employee_ContactNo : sales.Employee_ContactNo,
							Employee_PrasentAddress : sales.Employee_PrasentAddress,
							display_name:sales.Employee_Name
					}


					r.saleDetails.forEach(product => {
						let cartProduct = {
							Product_SlNo:product.Product_SlNo,
							Product_Name:product.Product_Name,
							ProductCategory_ID:product.ProductCategory_ID,
							// ProductCategory_Name:'o',
							c_name:product.sv_d_customer_name,
						    c_address:product.sv_d_customer_address,
						    IMEI:product.s_d_imei,
						    total:product.sv_d_total,
						    desc:product.sv_d_desc
						}

						this.cart.push(cartProduct);

					})


					////




					////

					this.getProducts();
				})

				
			}
		}
	})
</script>