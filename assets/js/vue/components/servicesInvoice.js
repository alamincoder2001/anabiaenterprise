const salesInvoice = Vue.component('sales-invoice', {
    template: `
        <div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            
            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            Services Invoice
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-xs-7">
                        <strong>Employee Id:</strong> {{ sales.Employee_ID }}<br>
                        <strong>Employee Name:</strong> {{ sales.Employee_Name }}<br>
                        <strong>Present Address:</strong> {{ sales.Employee_PrasentAddress }}<br>
                        <strong>Permanent Address:</strong> {{ sales.Employee_PermanentAddress }}<br>
                        <strong>Employee Mobile:</strong> {{ sales.Employee_ContactNo }}
                    </div>
                    <div class="col-xs-5 text-right">
                        <strong>Invoice No.:</strong> {{ sales.sv_invoice }}<br>
                        <strong> Date:</strong> {{ sales.sv_date }} <br>
                        <strong> Services  By :</strong> {{ sales.service_by }} 
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div _d9283dsc></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td>Sl.</td>
                                    <td>IMEI</td>
                                    <td>Product Name</td>
                                    <td>Customer Name</td>
                                    <td>Customer Address</td>
                                    <td> Note</td>
                                    <td align="right">Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(svd, sl) in cart">
                                    <td>{{ sl + 1 }}</td>
                                    <td>{{ svd.s_d_imei }}
                                    <td>{{ svd.Product_Name }}</td>
                                    <td>{{ svd.sv_d_customer_name }}</td>
                                    <td>{{ svd.sv_d_customer_address }}</td>
                                    <td>{{ svd.sv_d_desc }}</td>
                                    <td align="right">{{ svd.sv_d_total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-xs-12">
                        <table _t92sadbc2>
                            <tr style="float:right">
                                <td><strong>Total :</strong></td>
                                <td style="text-align:right">{{sales.sv_total_sale_amount}} </td>
                            </tr>
                          
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    `,
    props: ['sales_id'],
    data(){
        return {
            sales:{
                SaleMaster_InvoiceNo: null,
                SalseCustomer_IDNo: null,
                SaleMaster_SaleDate: null,
                Customer_Name: null,
                Customer_Address: null,
                Customer_Mobile: null,
                SaleMaster_TotalSaleAmount: null,
                SaleMaster_TotalDiscountAmount: null,
                SaleMaster_TaxAmount: null,
                SaleMaster_Freight: null,
                SaleMaster_SubTotalAmount: null,
                SaleMaster_PaidAmount: null,
                SaleMaster_DueAmount: null,
                SaleMaster_Previous_Due: null,
                SaleMaster_Description: null,
                AddBy: null
            },
            cart: [],
            style: null,
            companyProfile: null,
            currentBranch: null
        }
    },
    filters: {
        formatDateTime(dt, format) {
            return dt == '' || dt == null ? '' : moment(dt).format(format);
        }
    },
    created(){
        this.setStyle();
        this.getSales();
        this.getCurrentBranch();
    },
    methods:{
        getSales(){
            console.log(this.sales_id+' bb')
            axios.post('/servicesinvoices', {salesId: this.sales_id}).then(res=>{
                console.log(res.data)
                this.sales = res.data.sales[0];
                this.cart = res.data.saleDetails;
               
            })
        },
        getCurrentBranch() {
            axios.get('/get_current_branch').then(res => {
                this.currentBranch = res.data;
            })
        },
        setStyle(){
            this.style = document.createElement('style');
            this.style.innerHTML = `
                div[_h098asdh]{
                    /*background-color:#e0e0e0;*/
                    font-weight: bold;
                    font-size:15px;
                    margin-bottom:15px;
                    padding: 5px;
                    border-top: 1px dotted #454545;
                    border-bottom: 1px dotted #454545;
                }
                div[_d9283dsc]{
                    padding-bottom:25px;
                    border-bottom: 1px solid #ccc;
                    margin-bottom: 15px;
                }
                table[_a584de]{
                    width: 100%;
                    text-align:center;
                }
                table[_a584de] thead{
                    font-weight:bold;
                }
                table[_a584de] td{
                    padding: 3px;
                    border: 1px solid #ccc;
                }
                table[_t92sadbc2]{
                    width: 100%;
                }
                table[_t92sadbc2] td{
                    padding: 2px;
                }
            `;
            document.head.appendChild(this.style);
        },
        convertNumberToWords(amountToWord) {
            var words = new Array();
            words[0] = '';
            words[1] = 'One';
            words[2] = 'Two';
            words[3] = 'Three';
            words[4] = 'Four';
            words[5] = 'Five';
            words[6] = 'Six';
            words[7] = 'Seven';
            words[8] = 'Eight';
            words[9] = 'Nine';
            words[10] = 'Ten';
            words[11] = 'Eleven';
            words[12] = 'Twelve';
            words[13] = 'Thirteen';
            words[14] = 'Fourteen';
            words[15] = 'Fifteen';
            words[16] = 'Sixteen';
            words[17] = 'Seventeen';
            words[18] = 'Eighteen';
            words[19] = 'Nineteen';
            words[20] = 'Twenty';
            words[30] = 'Thirty';
            words[40] = 'Forty';
            words[50] = 'Fifty';
            words[60] = 'Sixty';
            words[70] = 'Seventy';
            words[80] = 'Eighty';
            words[90] = 'Ninety';
            amount = amountToWord == null ? '0.00' : amountToWord.toString();
            var atemp = amount.split(".");
            var number = atemp[0].split(",").join("");
            var n_length = number.length;
            var words_string = "";
            if (n_length <= 9) {
                var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                var received_n_array = new Array();
                for (var i = 0; i < n_length; i++) {
                    received_n_array[i] = number.substr(i, 1);
                }
                for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                    n_array[i] = received_n_array[j];
                }
                for (var i = 0, j = 1; i < 9; i++, j++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        if (n_array[i] == 1) {
                            n_array[j] = 10 + parseInt(n_array[j]);
                            n_array[i] = 0;
                        }
                    }
                }
                value = "";
                for (var i = 0; i < 9; i++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        value = n_array[i] * 10;
                    } else {
                        value = n_array[i];
                    }
                    if (value != 0) {
                        words_string += words[value] + " ";
                    }
                    if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Crores ";
                    }
                    if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Lakhs ";
                    }
                    if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Thousand ";
                    }
                    if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                        words_string += "Hundred and ";
                    } else if (i == 6 && value != 0) {
                        words_string += "Hundred ";
                    }
                }
                words_string = words_string.split("  ").join(" ");
            }
            return words_string + ' only';
        },
        async print(){
            let invoiceContent = document.querySelector('#invoiceContent').innerHTML;
            let printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}, left=0, top=0`);
            if (this.currentBranch.print_type == '3') {
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Invoice</title>
                            <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                            <style>
                                body, table{
                                    font-size:11px;
                                     width:500px!important;
                                margin: 0px auto!important;
                                }
                            </style>
                        </head>
                        <body>
                            <div style="text-align:center;">
                                <img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:80px;margin:0px;" /><br>
                                <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                                <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                            </div>
                            ${invoiceContent}
                        </body>
                    </html>
                `);
            } else if (this.currentBranch.print_type == '2') {
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                        <style>
                            html, body{
                                width:500px!important;
                                margin: 0px auto!important;
                            }
                            body, table{
                                font-size: 13px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="row">
                            <div class="col-xs-2"><img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:80px;
    margin-top: 36px;" /></div>
                            <div class="col-xs-10" style="padding-top:20px;text-align:center">
                                <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                                <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                ${invoiceContent}
                            </div>
                        </div>
                    </body>
                    </html>
				`);
            } else {
				printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                        <style>
                            body, table{
                                font-size: 13px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-2"><img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_thum}" alt="Logo" style="height:80px;
    margin-top: 36px;" /></div>
                                <div class="col-xs-10" style="padding-top:20px;text-align:center">
                                    <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                                    <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12">
                                    ${invoiceContent}
                                </div>
                            </div>
                        </div>
                        <div class="container" style="position:fixed;bottom:15px;width:100%;">
                            <div class="row" style="border-bottom:1px solid #ccc;margin-bottom:5px;padding-bottom:6px;">
                                <div class="col-xs-6">
                                    <span style="text-decoration:overline;">Received by</span><br><br>
                                    ** THANK YOU FOR YOUR BUSINESS **
                                </div>
                                <div class="col-xs-6 text-right">
                                    <span style="text-decoration:overline;">Authorized Signature</span>
                                </div>
                            </div>

                            <div class="row" style="font-size:12px;">
                                <div class="col-xs-6">
                                    Print Date: ${moment().format('DD-MM-YYYY h:mm a')}, Printed by: ${this.sales.AddBy}
                                </div>
                                <div class="col-xs-6 text-right">
                                    Developed by: Link-Up Technologoy, Contact no: 01911978897
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
				`);
            }
            let invoiceStyle = printWindow.document.createElement('style');
            invoiceStyle.innerHTML = this.style.innerHTML;
            printWindow.document.head.appendChild(invoiceStyle);
            printWindow.moveTo(0, 0);
            printWindow.focus();
            await new Promise(resolve => setTimeout(resolve, 1000));
            printWindow.print();
            printWindow.close();
        }
    }
})