<style>
	.v-select{
		margin-bottom: 5px;
		float:right;
		min-width: 200px;
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
	#table1{
		border-collapse: collapse;
		width: 100%;
	}

	#table1 td, #table1 th{
		padding: 5px;
		border: 1px solid #909090;
	}

	#table1 th{
		text-align: center;
	}

	#table1 thead{
		background-color: #cbd6e7;
	}
</style>
<div id="profitLoss">
	<div class="row" style="border-bottom: 1px solid #ccc;">
		<div class="col-md-12">
			<form class="form-inline" v-on:submit.prevent="getProfitLoss">
				<div class="form-group" style="margin-right: 15px;">
					<label>Customer &nbsp;</label>
					<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select Customer"></v-select>
				</div>

				<div class="form-group">
					<label>Date from </label>
					<input type="date" class="form-control" v-model="filter.dateFrom">
				</div>

				<div class="form-group">
					<label>to </label>
					<input type="date" class="form-control" v-model="filter.dateTo">
				</div>

				<div class="form-group">
					<input type="submit" class="btn btn-info btn-xs" value="Search" style="padding-top:0px;padding-bottom:0px;margin-top:-4px;">
				</div>
			</form>
		</div>
	</div>
	<div class="row" style="padding: 10px 0;display:none;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
		<div class="col-md-12">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
	</div>

	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12">
			<div class="table-responsive" id="reportTable">
				<table id="table1">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product</th>
							<th>Sold Quantity</th>
							<th>Purchase Rate</th>
							<th>Purchased Total</th>
							<th>Sold Amount</th>
							<th>Profit/Loss</th>
						</tr>
					</thead>
					<tbody v-for="data in reportData" style="display:none;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
						<tr>
							<td colspan="7" style="background-color: #e3eae7;">
								<strong>Invoice: </strong> {{ data.SaleMaster_InvoiceNo }} | 
								<strong>Sales Date: </strong> {{ data.SaleMaster_SaleDate }} | 
								<strong>Customer: </strong> {{ data.Customer_Name }} |
								<strong>Discount: </strong> {{ data.SaleMaster_TotalDiscountAmount }}
								<strong>Refenence: </strong> {{ data.reference
								 }}
							</td>
						</tr> 
						<tr v-for="product in data.saleDetails">
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td style="text-align:right;">{{ product.SaleDetails_TotalQuantity }}</td>
							<td style="text-align:right;">{{ parseFloat(product.Purchase_Rate).toFixed(2) }}</td>
							<td style="text-align:right;">{{  parseFloat(product.purchased_amount).toFixed(2) }}</td>
							<td style="text-align:right;">{{  parseFloat(product.SaleDetails_TotalAmount).toFixed(2) }}</td>
							<td style="text-align:right;">{{ parseFloat(product.profit_loss).toFixed(2) }}</td>
						</tr>
						<tr style="background-color: #f0f0f0;font-weight: bold;">
							<td colspan="4" style="text-align:right;">Total</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.purchased_amount) }, 0) }}</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.SaleDetails_TotalAmount) }, 0) }}</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.profit_loss) }, 0) }}</td>
						</tr>
					</tbody>
					<tfoot style="display:none;font-weight:bold;background-color:#e9dcdc;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
						<tr>
							<td style="text-align:right;" colspan="4">Total Profit</td>
							<td style="text-align:right;">
								{{ 
									reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.purchased_amount) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.SaleDetails_TotalAmount) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									totalProfit = reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.profit_loss) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Other Income (+)</td>
							<td colspan="3"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.income | decimal }}</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Total Discount (-)</td>
							<td colspan="3"></td>
							<td style="text-align:right;">{{ totalDiscount = reportData.reduce((prev, cur) => { return prev + parseFloat(cur.SaleMaster_TotalDiscountAmount) }, 0).toFixed(2) }}</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Total Damaged (-)</td>
							<td colspan="3"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.damaged_amount | decimal }}</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Cash Transaction (-)</td>
							<td colspan="3"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.expense | decimal }}</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Employee Payment (-)</td>
							<td colspan="3"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.employee_payment | decimal }}</td>
						</tr>

						<tr>
							<td colspan="3" style="text-align:right;">Profit</td>
							<td colspan="3"></td>
							<td style="text-align:right;">
								{{  ((parseFloat(totalProfit) + parseFloat(otherIncomeExpense.income)) - 
									(parseFloat(totalDiscount) + parseFloat(otherIncomeExpense.damaged_amount) + parseFloat(otherIncomeExpense.expense) + parseFloat(otherIncomeExpense.employee_payment))).toFixed(2) }}
							</td>
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
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#profitLoss',
		data(){
			return {
				filter: {
					customer: null,
					dateFrom: moment().format('YYYY-MM-DD'),
					dateTo: moment().format('YYYY-MM-DD')
				},
				customers: [],
				selectedCustomer: null,
				reportData: [],
				otherIncomeExpense: {
					income: 0,
					expense: 0,
					employee_payment: 0
				}
			}
		},
		filters: {
			decimal(value) {
				return value ==  null || value == undefined ? '0.00' : parseFloat(value).toFixed(2);
			}
		},
		created(){
			this.getCustomers();
		},
		methods: {
			getCustomers(){
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},

			async getProfitLoss(){
				if(this.selectedCustomer != null){
					this.filter.customer = this.selectedCustomer.Customer_SlNo;
				} else {
					this.filter.customer = null;
				}
				this.reportData = await axios.post('/get_profit_loss', this.filter).then(res => {
					return res.data;
				})

				this.otherIncomeExpense = await axios.post('/get_other_income_expense', this.filter).then(res => {
					return res.data;
				})
				
			},

			async print(){
				let customerText = '';
				if(this.selectedCustomer != null){
					customerText = `
						<strong>Customer Id: </strong> ${this.selectedCustomer.Customer_Code}<br>
						<strong>Name: </strong> ${this.selectedCustomer.Customer_Name}<br>
						<strong>Address: </strong> ${this.selectedCustomer.Customer_Address}<br>
						<strong>Mobile: </strong> ${this.selectedCustomer.Customer_Mobile}
					`;
				}

				let dateText = '';
				if(this.filter.dateFrom != '' && this.filter.dateTo != ''){
					dateText = `
						Statement from <strong>${this.filter.dateFrom}</strong> to <strong>${this.filter.dateTo}</strong>
					`;
				}
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Profit/Loss Report</h4 style="text-align:center">
						<div class="row">
							<div class="col-md-6">${customerText}</div>
							<div class="col-md-6 text-right">${dateText}</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				mywindow.document.head.innerHTML += `
					<style>
						#table1{
							border-collapse: collapse;
							width: 100%;
						}

						#table1 td, #table1 th{
							padding: 5px;
							border: 1px solid #909090;
						}

						#table1 th{
							text-align: center;
						}

						#table1 thead{
							background-color: #cbd6e7;
						}
					</style>
				`;
				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>