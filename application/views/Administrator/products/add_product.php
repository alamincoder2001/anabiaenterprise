<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select.open .dropdown-toggle{
		border-bottom: 1px solid #ccc;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
		height: 25px;
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
	#products label{
		font-size:13px;
	}
	#products select{
		border-radius: 3px;
	}
	#products .add-button{
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display:block;
		text-align: center;
		color: white;
	}
	#products .add-button:hover{
		background-color: #41add6;
		color: white;
	}
</style>
<div id="products"> 
		<form @submit.prevent="saveProduct">
		<div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
			<div class="col-md-6">
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Product Id:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Code" readonly>
					</div> 
				</div> 
				<div class="form-group clearfix"> 
					<label class="control-label col-md-4">Category:</label>
					<div class="col-md-7">
						<select class="form-control" v-if="categories.length == 0"></select>
						<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name" v-if="categories.length > 0"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/category" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix" style="display:none;">
					<label class="control-label col-md-4">Brand:</label>
					<div class="col-md-7">
						<select class="form-control" v-if="brands.length == 0"></select>
						<v-select v-bind:options="brands" v-model="selectedBrand" label="brand_name" v-if="brands.length > 0"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Product Name:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Name" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Unit:</label>
					<div class="col-md-7">
						<select class="form-control" v-if="units.length == 0"></select>
						<v-select v-bind:options="units" v-model="selectedUnit" label="Unit_Name" v-if="units.length > 0"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/unit" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-md-4">VAT:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.vat">
					</div>
				</div>
			</div>	

			<div class="col-md-6">
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Re-order level:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_ReOrederLevel" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Purchase Rate:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Purchase_Rate" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Sales Rate:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_SellingPrice" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Wholesale Rate:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_WholesaleRate" required>
					</div>
				</div>
				<div class="form-group clearfix">
					<label for="imei" class="control-label col-md-4">Is IMEI:</label>
					<div class="col-md-7">
						<input type="checkbox" v-model="product.is_imei" id="imei">
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Is Service:</label>
					<div class="col-md-7">
						<input type="checkbox" v-model="product.is_service">
					</div>
				</div>
				
				<div class="form-group clearfix">
					<div class="col-md-7 col-md-offset-4">
						<input type="submit" class="btn btn-success btn-sm" value="Save">
					</div>
				</div>
			</div>	
		</div>
		</form>

		<div class="row">
			<div class="col-sm-12 form-inline">
				<div class="form-group">
					<label for="filter" class="sr-only">Filter</label>
					<input type="text" class="form-control" v-model="filter" placeholder="Filter">
				</div>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<datatable :columns="columns" :data="products" :filter-by="filter">
						<template scope="{ row }">
							<tr>
								<td>{{ row.Product_Code }}</td>
								<td>{{ row.Product_Name }}</td>
								<td>{{ row.ProductCategory_Name }}</td>
								<td>{{ row.Product_Purchase_Rate }}</td>
								<td>{{ row.Product_SellingPrice }}</td>
								<td>{{ row.Product_WholesaleRate }}</td>
								<td>{{ row.vat }}</td>
								<td>{{ row.is_service }}</td>
								<td>{{ row.Unit_Name }}</td>
								<td>{{ row.is_imei }}</td>
								<td>
									<?php if($this->session->userdata('accountType') != 'u'){?>
									<button type="button" class="button edit" @click="editProduct(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deleteProduct(row.Product_SlNo)">
										<i class="fa fa-trash"></i>
									</button>
									<?php }?>
									<button type="button" class="button" @click="window.location = `/Administrator/products/barcodeGenerate/${row.Product_SlNo}`">
										<i class="fa fa-barcode"></i>
									</button>
								</td>
							</tr>
						</template>
					</datatable>
					<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
				</div>
			</div>
		</div>


</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#products',
		data(){
			return {
				product: {
					Product_SlNo: '',
					Product_Code: "<?php echo $productCode;?>",
					Product_Name: '',
					ProductCategory_ID: '',
					brand: '',
					Product_ReOrederLevel: '',
					Product_Purchase_Rate: '',
					Product_SellingPrice: '',
					Product_WholesaleRate: 0,
					Unit_ID: '',
					vat: 0,
					is_service: false,
					is_imei: false,
				},
				products: [],
				categories: [],
				selectedCategory: null,
				brands: [],
				selectedBrand: null,
				units: [],
				selectedUnit: null,

				columns: [
                    { label: 'Product Id', field: 'Product_Code', align: 'center', filterable: false },
                    { label: 'Product Name', field: 'Product_Name', align: 'center' },
                    { label: 'Category', field: 'ProductCategory_Name', align: 'center' },
                    { label: 'Purchase Price', field: 'Product_Purchase_Rate', align: 'center' },
                    { label: 'Sales Price', field: 'Product_SellingPrice', align: 'center' },
                    { label: 'Wholesale Price', field: 'Product_WholesaleRate', align: 'center' },
                    { label: 'VAT', field: 'vat', align: 'center' },
                    { label: 'Is Service', field: 'is_service', align: 'center' },
                    { label: 'Unit', field: 'Unit_Name', align: 'center' },
                    { label: 'Is_IMEI', align: 'center', filterable: false },
                    { label: 'Action', align: 'center', filterable: false }
                ],
                page: 1,
                per_page: 10,
                filter: ''
			}
		},
		created(){
			this.getCategories();
			this.getBrands();
			this.getUnits();
			this.getProducts();
		},
		methods:{
			getCategories(){
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getBrands(){
				axios.get('/get_brands').then(res => {
					this.brands = res.data;
				})
			},
			getUnits(){
				axios.get('/get_units').then(res => {
					this.units = res.data;
				})
			},
			getProducts(){
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			saveProduct(){
				if(this.selectedCategory == null){
					alert('Select category');
					return;
				}
				if(this.selectedUnit == null){
					alert('Select unit');
					return;
				}
				if(this.selectedBrand != null){
					this.product.brand = this.selectedBrand.brand_SiNo;
				}

				this.product.ProductCategory_ID = this.selectedCategory.ProductCategory_SlNo;
				this.product.Unit_ID = this.selectedUnit.Unit_SlNo;

				let url = '/add_product';
				if(this.product.Product_SlNo != 0){
					url = '/update_product';
				}
				axios.post(url, this.product)
				.then(res=>{
					let r = res.data;
					alert(r.message);
					if(r.success){
						this.clearForm();
						this.product.Product_Code = r.productId;
						this.getProducts();
					}
				})
				
			},
			editProduct(product){
				let keys = Object.keys(this.product);
				keys.forEach(key => {
					this.product[key] = product[key];
				})

				this.product.is_service = product.is_service == 'true' ? true : false;
				this.product.is_imei = product.is_imei == 'true' ? true : false;

				this.selectedCategory = {
					ProductCategory_SlNo: product.ProductCategory_ID,
					ProductCategory_Name: product.ProductCategory_Name
				}

				this.selectedUnit = {
					Unit_SlNo: product.Unit_ID,
					Unit_Name: product.Unit_Name
				}
			},
			deleteProduct(productId){
				let deleteConfirm = confirm('Are you sure?');
				if(deleteConfirm == false){
					return;
				}
				axios.post('/delete_product', {productId: productId}).then(res => {
					let r = res.data;
					alert(r.message);
					if(r.success){
						this.getProducts();
					}
				})
			},
			clearForm(){
				let keys = Object.keys(this.product);
				keys.forEach(key => {
					if(typeof(this.product[key]) == "string"){
						this.product[key] = '';
					} else if(typeof(this.product[key]) == "number"){
						this.product[key] = 0;
					}
				})
				this.product.is_imei = false;
			}
		}
	})
</script>