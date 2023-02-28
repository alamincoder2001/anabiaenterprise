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
</style>


<style type="text/css">
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
.modal-header{
    padding-bottom: 0 !important;
}
.modal-header h3 {
margin-top: 0;
color: #42b983;
}

.modal-body {
margin: 0px 0;
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
width: 700px;
margin: 0px auto;
background-color: #fff;
border-radius: 2px;
box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
transition: all .3s ease;
font-family: Helvetica, Arial, sans-serif;
}
.modal-header{
    padding-bottom: 0 !important;
}
.modal-header h3 {
margin-top: 0;
color: #42b983;
}

.modal-body {
margin: 0px 0;
     overflow-y: auto !important;
    height: 300px !important;
    margin: -8px -14px -44px !important;

}
.modal-default-button {
float: right;
}
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
</style>


<div id="stock">

	<div style="display: none;"   id="serial-modal" v-if="" v-bind:style="{display:serialModalStatus?'block':'none'}" >
<transition name="modal">
<div class="modal-mask">
<div class="modal-wrapper">
<div class="modal-container">

	<div class="modal-header">
	<slot name="header">
		<h3>IMEI Number List</h3>
	</slot>
	</div>

	<div class="modal-body" style="overflow: hidden;
height: 100%;
margin: -8px -14px -44px;">
	<slot name="body">
		<div class="form-group">
<div class="col-sm-12" style="display: flex;
margin-bottom: 10px;">


</div>

</div>
	</slot>
	<table class="table">
<thead>
<tr>
<th scope="col">SL</th>
<th scope="col">IMEI</th>
<th scope="col">Product</th>
<th scope="col">Purchase  Date</th>
</tr>
</thead>
<tbody>
  
<tr v-for="(imei, sl) in imeis">
	<td>{{sl+1}}</td>
	<td>{{imei.ps_imei_number}}</td>
    <td> {{imei.Product_Name}}</td>
    <td> {{imei.purchase_date}}</td>
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
		Close
		</button>
		
	</slot>
	</div>
</div>
</div>
</div>
</transition>
</div>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
			<div class="form-group" style="margin-top:10px;">
				<label class="col-sm-1 col-sm-offset-1 control-label no-padding-right"> Select Type </label>
				<div class="col-sm-2">
					<v-select v-bind:options="searchTypes" v-model="selectedSearchType" label="text" v-on:input="onChangeSearchType"></v-select>
				</div>
			</div>
	
			<div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'category'">
				<div class="col-sm-2" style="margin-left:15px;">
					<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
				</div>
			</div>
	
			<div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'product'">
				<div class="col-sm-2" style="margin-left:15px;">
					<v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
				</div>
			</div>

			<div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'brand'">
				<div class="col-sm-2" style="margin-left:15px;">
					<v-select v-bind:options="brands" v-model="selectedBrand" label="brand_name"></v-select>
				</div>
			</div>
	
			<div class="form-group">
				<div class="col-sm-2"  style="margin-left:15px;">
					<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getStock" style="margin-top:0px;border:0px;height:28px;">
				</div>
			</div>
		</div>
	</div>
	<div class="row" v-if="searchType != null" style="display:none" v-bind:style="{display: searchType == null ? 'none' : ''}">
		<div class="col-md-12">
			<a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive" id="stockContent">
				<table class="table table-bordered" v-if="searchType == 'current'" style="display:none" v-bind:style="{display: searchType == 'current' ? '' : 'none'}">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product Name</th>
							<th>Category</th>
							<th>Current Quantity</th>
							<th>Stock Value</th>
							<th>IMEI</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="product in stock">
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td>{{ product.ProductCategory_Name }}</td>
							<td>{{ product.current_quantity }} {{ product.Unit_Name }}</td>
							<td>{{ product.purchase_total_am }}</td>
							<td><button type="button" class="button edit" @click="viewIMEI(product.product_id)">
										<i class="fa fa-eye"></i>
									</button></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" style="text-align:right;">Total Stock Value</td>
							<td>{{ current_quantity }}</td>
							<td>{{ totalStockValue }}</td>
							<td><td>
						</tr>
					</tfoot>
				</table>

				<table class="table table-bordered" 
						v-if="searchType != 'current' && searchType != null" 
						style="display:none;" 
						v-bind:style="{display: searchType != 'current' && searchType != null ? '' : 'none'}">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product Name</th>
							<th>Category</th>
							<th>Purchased Quantity</th>
							<th>Purchase Returned Quantity</th>
<!-- 							<th>Damaged Quantity</th>
 -->							<th>Sold Quantity</th>
							<th>Sales Returned Quantity</th>
<!-- 							<th>Transfered In Quantity</th>
 -->							<!-- <th>Transfered Out Quantity</th> -->
							<th>Current Quantity</th>
							<th>Stock Value</th>
							<th>IMEI</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="product in stock">
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td>{{ product.ProductCategory_Name }}</td>
							<td>{{ product.purchased_quantity }}</td>
							<td>{{ product.purchase_returned_quantity }}</td>
<!-- 							<td>{{ product.damaged_quantity }}</td>
 -->							<td>{{ product.sold_quantity }}</td>
							<td>{{ product.sales_returned_quantity }}</td>
<!-- 							<td>{{ product.transfered_to_quantity}}</td>
 -->							<!-- <td>{{ product.transfered_from_quantity}}</td> -->
							<td>{{ product.current_quantity }} {{ product.Unit_Name }}</td>
							<td>{{ product.purchase_total_am }}</td>
							<td><button type="button" class="button edit" @click="viewIMEI(product.Product_SlNo)">
										<i class="fa fa-eye"></i>
									</button></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="7" style="text-align:right;">Total Stock Value</td>
							<td>{{ current_quantity }}</td>
							<td>{{ totalStockValue }}</td>
							<td><td>
							
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>





<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>

<script>
	Vue.component('modal', {
	template: '#serial-modal'
	})
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#stock',
		data(){
			return {
				searchTypes: [
					{text: 'Current Stock', value: 'current'},
					{text: 'Total Stock', value: 'total'},
					{text: 'Category Wise Stock', value: 'category'},
					{text: 'Product Wise Stock', value: 'product'},
					//{text: 'Brand Wise Stock', value: 'brand'}
				],
				selectedSearchType: {
					text: 'select',
					value: ''
				},
				searchType: null,
				categories: [],
				selectedCategory: null,
				products: [],
				selectedProduct: null,
				brands: [],
				selectedBrand: null,
				selectionText: '',
				serialModalStatus: false,
				stock: [],
				imeis:[],
				totalStockValue: 0.00,
				current_quantity:0
			}
		},
		created(){
		},
		methods:{
			serialShowModal(){

            this.serialModalStatus = true;
            console.log(this.serialModalStatus);
		    },
		    serialHideModal(){
	       this.serialModalStatus = false;
		   },
				getStock(){
				this.searchType = this.selectedSearchType.value;
				let url = '';
				if(this.searchType == 'current'){
					url = '/get_current_stock';
				} else {
					url = '/get_total_stock';
				}

				let parameters = null;
				this.selectionText = "";

				if(this.searchType == 'category' && this.selectedCategory == null){
					alert('Select a category');
					return;
				} else if(this.searchType == 'category' && this.selectedCategory != null) {
					parameters = {
						categoryId: this.selectedCategory.ProductCategory_SlNo
					}
					this.selectionText = "Category: " + this.selectedCategory.ProductCategory_Name;
				}
				
				if(this.searchType == 'product' && this.selectedProduct == null){
					alert('Select a product');
					return;
				} else if(this.searchType == 'product' && this.selectedProduct != null) {
					parameters = {
						productId: this.selectedProduct.Product_SlNo
					}
					this.selectionText = "product: " + this.selectedProduct.display_text;
				} 
				if(this.searchType == 'brand' && this.selectedBrand == null){
					alert('Select a brand');
					return;
				} else if(this.searchType == 'brand' && this.selectedBrand != null) {
					parameters = {
						brandId: this.selectedBrand.brand_SiNo
					}
					this.selectionText = "Brand: " + this.selectedBrand.brand_name;
				}


				axios.post(url, parameters).then(res => {
					this.stock = res.data.stock;
					this.totalStockValue = res.data.totalValue;
					this.current_quantity = res.data.current_quantity;
				})
			},
			async viewIMEI(prod_id){
				await axios.post('/get_IMEI_By_Prod',{prod_id:prod_id})
				.then((res)=>{
					this.imeis = res.data;
				});
				this.serialShowModal();
			},
			onChangeSearchType(){
				if(this.selectedSearchType.value == 'category' && this.categories.length == 0){
					this.getCategories();
				} else if(this.selectedSearchType.value == 'brand' && this.brands.length == 0){
					this.getBrands();
				} else if(this.selectedSearchType.value == 'product' && this.products.length == 0){
					this.getProducts();
				}
			},
			getCategories(){
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getProducts(){
				axios.get('/get_products').then(res => {
					this.products =  res.data;
				})
			},
			getBrands(){
				axios.get('/get_brands').then(res => {
					this.brands = res.data;
				})
			},
			async print(){
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">${this.selectedSearchType.text} Report</h4 style="text-align:center">
						<h6 style="text-align:center">${this.selectionText}</h6>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#stockContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				reportWindow.document.body.innerHTML += reportContent;

				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
		}
	})
</script>