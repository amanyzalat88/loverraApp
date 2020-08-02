<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="purchase_order_slack == ''">Add Purchase Order</span>
                        <span class="text-title" v-else>Edit Purchase Order</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="supplier">Supplier</label>
                        <cool-select type="text" name="supplier" v-validate="'required|max:250'" placeholder="Please choose supplier"  autocomplete="off" v-model="supplier" :items="supplier_list" item-text="label" itemValue='slack' @search='load_suppliers' ref="supplier">
                        </cool-select>
                        <span v-bind:class="{ 'error' : errors.has('supplier') }">{{ errors.first('supplier') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="po_number">PO Number</label>
                        <input type="text" name="po_number" v-model="po_number" v-validate="'required|max:50'" class="form-control form-control-custom" placeholder="Please enter PO Number"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('po_number') }">{{ errors.first('po_number') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="po_reference">PO Reference #</label>
                        <input type="text" name="po_reference" v-model="po_reference" v-validate="'max:30'" class="form-control form-control-custom" placeholder="Please enter PO Reference #"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('po_reference') }">{{ errors.first('po_reference') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="order_date">PO Order Date</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="order_date" v-validate="'date_format:yyyy-MM-dd'" input-class="form-control form-control-custom  bg-white" ref="order_date" name="order_date" placeholder="Please enter Order Date" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('order_date') }">{{ errors.first('order_date') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="order_due_date">PO Order Due Date</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="order_due_date" :disabled-date="not_before_order_date" name="order_due_date" v-validate="'date_format:yyyy-MM-dd'" input-class="form-control form-control-custom bg-white" placeholder="Please enter Order Due Date" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('order_due_date') }">{{ errors.first('order_due_date') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="currency">Currency as per Supplier</label>
                        <select name="currency" v-model="currency" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Currency..</option>
                            <option v-for="(currency_item, index) in currency_list" v-bind:value="currency_item.currency_code" v-bind:key="index">
                                {{ currency_item.currency_code }} - {{ currency_item.currency_name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('currency') }">{{ errors.first('currency') }}</span> 
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Products</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-4">
                        <label for="barcode">Search and Add Products</label>
                        <cool-select type="text" v-model="search_product"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="product_list" item-text="label" itemValue='label' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_products' @select='add_product_to_list'></cool-select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-5 mb-1">
                        <label for="name">Name & Description</label>
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="quantity">Quantity</label>  
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="unit_price">Unit Price</label>  
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="discount_percentage">Discount %</label>  
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="tax_percentage">Tax %</label>  
                    </div>
                     <div class="form-group col-md-2 mb-1">
                        <label for="amount">Amount</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(product, index) in products" :key="index">
                    <div class="form-group col-md-5">
                        <input type="text" v-bind:name="'product.name_'+index" v-model="product.name" v-validate="'required|max:250'" class="form-control form-control-custom" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('product.name_'+index) }">{{ errors.first('product.name_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.quantity_'+index" v-model="product.quantity" v-validate="'required|decimal|min:1'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                        <span v-bind:class="{ 'error' : errors.has('product.quantity_'+index) }">{{ errors.first('product.quantity_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.unit_price_'+index" v-model="product.unit_price" v-validate="'required|decimal'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                        <span v-bind:class="{ 'error' : errors.has('product.unit_price_'+index) }">{{ errors.first('product.unit_price_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.discount_percentage_'+index" v-model="product.discount_percentage" v-validate="'decimal'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                        <span v-bind:class="{ 'error' : errors.has('product.discount_percentage_'+index) }">{{ errors.first('product.discount_percentage_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.tax_percentage_'+index" v-model="product.tax_percentage" v-validate="'decimal'" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                        <span v-bind:class="{ 'error' : errors.has('product.tax_percentage_'+index) }">{{ errors.first('product.tax_percentage_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-2">
                        <input type="number" v-bind:name="'product.amount_'+index" v-model="product.amount" v-validate="'required|decimal'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" readonly="true">
                        <span v-bind:class="{ 'error' : errors.has('product.amount_'+index) }">{{ errors.first('product.amount_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1" v-if="products.length>1">
                        <button type="button" class="btn btn-outline-danger" @click="remove_product(index)"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" @click="add_new_product">Add More</button>

                <div class="form-row mb-3">
                    <div class="col-md-2 offset-md-7 text-right">
                        <span class="align-text-top">Shipping Charges</span>
                    </div>
                    <div class="col-md-2">
                        <input type="number" v-bind:name="shipping_charge" v-model="shipping_charge" v-validate="'decimal'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                    </div>
                </div>
                <div class="form-row mb-3">
                    <div class="col-md-2 offset-md-7 text-right">
                        <span class="align-text-top">Packing Charges</span>
                    </div>
                    <div class="col-md-2">
                        <input type="number" v-bind:name="packing_charge" v-model="packing_charge" v-validate="'decimal'" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" v-on:input="calculate_price">
                    </div>
                </div>
                <div class="form-row  mb-3">
                    <div class="col-md-2 offset-md-7 text-right">
                        Total
                    </div>
                    <div class="col-md-2">
                        {{ grand_total }}
                    </div>
                </div>
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                Are you sure you want to proceed?
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>
        
    </div>
</template>

<script>
    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import moment from "moment";
    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';

    export default {
        components: {
            CoolSelect
        },
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.purchase_order_data == null)?'/api/add_purchase_order':'/api/update_purchase_order/'+this.purchase_order_data.slack,

                supplier_list   : [],
                product_list   : [],
                search_product : '',
                
                supplier : (this.purchase_order_data == null)?'':this.purchase_order_data.supplier.slack,
                supplier_name : (this.purchase_order_data == null)?'':this.purchase_order_data.supplier.supplier_code+' - '+this.purchase_order_data.supplier.name,
                purchase_order_slack : (this.purchase_order_data == null)?'':this.purchase_order_data.slack,
                po_number : (this.purchase_order_data == null)?'':(this.purchase_order_data.po_number != null)?this.purchase_order_data.po_number:'',
                po_reference : (this.purchase_order_data == null)?'':(this.purchase_order_data.po_reference != null)?this.purchase_order_data.po_reference:'',
                order_date : (this.purchase_order_data == null)?'':(this.purchase_order_data.order_date != null)?new Date(this.purchase_order_data.order_date):'',
                order_due_date : (this.purchase_order_data == null)?'':(this.purchase_order_data.order_due_date != null)?new Date(this.purchase_order_data.order_due_date):'',
                currency : (this.purchase_order_data == null)?'':(this.purchase_order_data.currency_code != null)?this.purchase_order_data.currency_code:'',

                shipping_charge: (this.purchase_order_data == null)?'':(this.purchase_order_data.shipping_charge != null)?this.purchase_order_data.shipping_charge:'',
                packing_charge: (this.purchase_order_data == null)?'':(this.purchase_order_data.packing_charge != null)?this.purchase_order_data.packing_charge:'',
                grand_total : 0,

                products: [],
                products_template : {
                    slack: '',
                    name : '',
                    quantity : '',
                    unit_price : '',
                    discount_percentage : '',
                    tax_percentage : '',
                    amount : ''
                },

                po_product_list : (this.purchase_order_data != null)?this.purchase_order_data.products:[],

                today : new Date()
            }
        },

        props: {
            currency_list: Array,
            purchase_order_data: [Array, Object]
        },

        watch: {
            supplier: function(val) {
                if (val) {
                    this.product_list = [];
                    this.update_product_list();
                }
            }
        },

        mounted() {
            console.log('Add purchase order page loaded');
            console.log(this.$refs)
            this.$refs.supplier.setSearchData(this.supplier_name);
        },

        created() {
            this.update_product_list(this.po_product_list);
        },

        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
            },

            not_before_order_date(date) {
                return date < this.order_date;
            },

            load_suppliers (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);

                        axios.post('/api/load_suppliers', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.supplier_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            load_products (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("supplier", this.supplier);

                        axios.post('/api/load_product_for_po', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.product_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            add_product_to_list(item) {
                if( item.product_slack != '' ){
                    var current_product = {
                        slack : item.product_slack,
                        name : item.label,
                        quantity : 1,
                        unit_price : item.purchase_amount_excluding_tax,
                        discount_percentage : item.discount_percentage,
                        tax_percentage : item.tax_percentage,
                        amount : ''
                    };
                }

                var item_found = false;
                for(var i = 0; i < this.products.length; i++){   
                    if(this.products[i].slack == item.product_slack){
                        this.products[i].quantity++;
                        item_found = true;
                    }
                }

                if(this.products[0].name == '' && this.products[0].quantity == '' && this.products[0].unit_price == ''){
                    this.$set(this.products, 0, current_product);
                }else{
                    if(item_found == false){
                        this.products.push(current_product);
                    }
                }
                this.product_list = [];
                this.calculate_price();
            },

            calculate_tax(item_total, tax_percentage) {
                var tax_amount = (parseFloat(tax_percentage)/100)*parseFloat(item_total);
                return tax_amount.toFixed(2);
            },

            calculate_discount(item_total, discount_percentage) {
                var discount_amount = (parseFloat(discount_percentage)/100)*parseFloat(item_total);
                return discount_amount.toFixed(2);
            },

            calculate_price() {
                var grand_total = 0;
                for ( var index in this.products) {
                    var discount_amount = 0;
                    var tax_amount = 0;
                    var item_total = 0;

                    var quantity = this.products[index].quantity;
                    var unit_price = this.products[index].unit_price;
                    var discount_percentage = this.products[index].discount_percentage;
                    var tax_percentage = this.products[index].tax_percentage;

                    if(!isNaN(quantity) && quantity != null  && quantity != '' && !isNaN(unit_price) && unit_price != null && unit_price != '' ){
                        item_total = parseFloat(quantity)*parseFloat(unit_price);
                        
                        if( !isNaN(discount_percentage) && discount_percentage != null && tax_percentage != ''){
                            if(discount_percentage >= 0){
                                discount_amount = this.calculate_discount(item_total, discount_percentage);
                                item_total = parseFloat(item_total)-parseFloat(discount_amount);
                            }
                        }
                        if( !isNaN(tax_percentage) && tax_percentage != null && tax_percentage != ''){
                            if(tax_percentage >= 0){
                                tax_amount = this.calculate_tax(item_total, tax_percentage);
                            }
                        }

                        item_total = parseFloat(item_total)+parseFloat(tax_amount);
                        item_total = item_total.toFixed(2);
                        this.products[index].amount = item_total;
                        grand_total = parseFloat(grand_total)+parseFloat(item_total);
                    }else{
                        continue;
                    }
                }
                if(!isNaN(this.shipping_charge) && this.shipping_charge != ''){
                    grand_total = parseFloat(grand_total)+parseFloat(this.shipping_charge);
                }
                if(!isNaN(this.packing_charge) && this.packing_charge != ''){
                    grand_total = parseFloat(grand_total)+parseFloat(this.packing_charge);
                }
                this.grand_total = grand_total.toFixed(2);
            },

            add_new_product() {
                this.products.push({
                    slack: '',
                    name : '',
                    quantity : '',
                    unit_price : '',
                    discount_percentage : '',
                    tax_percentage : '',
                    amount : ''
                });
                this.calculate_price();
            },

            remove_product(index) {
                this.products.splice(index, 1);
                this.calculate_price();
            },

            update_product_list(purchase_order_products) {
                if(purchase_order_products != null && purchase_order_products.length > 0){
                    this.products = [];
                    for (let i = 0; i < purchase_order_products.length; i++) {
                        var individual_product = {
                            slack: purchase_order_products[i].product_slack,
                            name : purchase_order_products[i].name,
                            quantity : purchase_order_products[i].quantity,
                            unit_price : purchase_order_products[i].amount_excluding_tax,
                            discount_percentage : purchase_order_products[i].discount_percentage,
                            tax_percentage : purchase_order_products[i].tax_percentage,
                            amount : purchase_order_products[i].total_amount
                        };
                        this.products.push(individual_product);
                    }
                }else{
                    this.products = [];
                    this.products.push(this.products_template);
                }
                this.calculate_price();
            },

            submit_form () {

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("supplier", (this.supplier != null)?this.supplier:'');
                            formData.append("po_number", (this.po_number != null)?this.po_number:'');
                            formData.append("po_reference", (this.po_reference != null)?this.po_reference:'');
                            formData.append("order_date", this.convert_date_format(this.order_date));
                            formData.append("order_due_date", this.convert_date_format(this.order_due_date));
                            formData.append("currency", (this.currency != null)?this.currency:'');
                            formData.append("shipping_charge", this.shipping_charge);
                            formData.append("packing_charge", this.packing_charge);
                            formData.append("products", JSON.stringify(this.products));

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                }else{
                                    this.show_modal = false;
                                    this.processing = false;
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            
            }

        }
    }
</script>