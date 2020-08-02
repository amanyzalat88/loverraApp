<template>
    <div class="row m-0">
        
        <div class="col-md-8 p-0 bg-white border-right">
            
            <div class="p-0 border-bottom">
                <div class="d-flex flex-wrap p-3">
                    <div class="mr-auto">
                        <span class="text-title" v-if="order_slack == ''">New Order</span>
                        <span class="text-title" v-else>Order # {{ order_number }}</span>
                    </div>
                    <div class="text-secondary">
                        {{ current_date_time }}
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column p-3 border-bottom product-info-form">
                <form @submit.prevent="product_info_form">
                    <div class="form-row mb-2">
                        <div class="form-group col-md-3">
                            <label for="barcode">Barcode</label>
                            <input type="text" name="barcode" v-model="barcode" class="form-control form-control-lg" ref="barcode" placeholder="Scan Barcode"  autocomplete="off">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="product_title">Product Title</label>
                            <input type="text" name="product_title" v-model="product_title" class="form-control form-control-lg" placeholder="Product Title"  autocomplete="off">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="category">Category</label>
                        <select name="category" v-model="category" class="form-control custom-select custom-select-lg">
                            <option value="">Filter by Category..</option>
                            <option v-for="(category, index) in categories" v-bind:value="category.slack" v-bind:key="index">
                                {{ category.label }}
                            </option>
                        </select>
                        </div>
                        <div class="form-group col-md-3">
                            <button type="submit" class="btn btn-primary btn-block btn-lg find-product-btn" v-bind:disabled="product_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="product_processing == true"></i> Go</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex flex-wrap mb-5 p-3 product-list bg-light">
                <div class="col-md-12">
                <div class="row">
                    <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-4 bg-light product" v-for="(product_list_item, index) in product_list" v-bind:value="product_list_item.product_slack" v-bind:key="index" v-on:click='add_to_cart(product_list_item)'>
                        <div class="col-12 p-3 bg-white product-grid">
                            <div class="product-code">
                                <span class="small text-secondary text-break">Product Code : {{ product_list_item.product_code}}</span>
                            </div>
                            <div class="text-bold text-break overflow-hidden product-title">
                                {{ product_list_item.product_name | truncate(60) }}
                            </div>
                            <div class="text-bold text-break overflow-hidden" v-show="product_list_item.remaining_quantity<=10">
                                <span class="text-warning text-caption">Only {{ product_list_item.remaining_quantity }} stock(s) left</span>
                            </div>
                            <div class="text-bold text-break overflow-hidden">
                                <span v-if="product_list_item.discount_percentage>0" class="text-success text-caption">Discount {{ product_list_item.discount_percentage }}%</span>
                            </div>
                            <div class="mt-auto ml-auto pt-3 text-break product-price">
                                <span class="product-price-currency">{{ store_currency }}</span> {{ product_list_item.sale_amount_excluding_tax }}
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>

        <div class="col-md-4 p-0 full-height">
            
            <div class="cart_form">
                <div class="p-0 border-bottom">
                    <div class="d-flex flex-wrap p-3">
                        <span class="mr-auto text-title text-black-50">Cart</span>
                        <span class="btn-label" v-on:click="show_customer_modal = true"><i class="fas fa-user-edit"></i> Customer: {{ customer_label | truncate(26) }}</span>
                    </div>
                </div>

                <div class="p-0 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between p-3">
                        <span>{{ item_count }} Items ({{ quantity_count }} Qty)</span>
                        <span>Order Level Tax : {{ store_level_total_tax_amount }}</span>
                        <span class="text-success">Order Level Discount : {{ store_level_total_discount_amount }}</span>
                    </div>
                </div>

                <div class="p-0 cart-list border-left">
                
                    <div class="d-flex flex-column pl-3 pt-3 pb-3 border-bottom" v-for="(cart_item, key, index) in cart" v-bind:value="cart_item.product_slack" v-bind:key="index">
                        <div class="d-flex mb-2">
                            <span class="small text-secondary">Product Code : {{ cart_item.product_code }}</span>
                            
                            <button type="button" v-on:click="remove_from_cart(cart_item.product_slack)" class="close cart-item-remove bg-light mr-2 ml-auto" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-bold text-break cart-item-title">
                                {{ cart_item.name }}
                            </span>
                            <input type="number" v-model="cart_item.quantity" v-on:input="validate_quantity(cart_item.product_slack, $event)" class="form-control form-control-custom cart-product-quantity mr-2 ml-3" autocomplete="off" min=0>
                        </div>

                        <div class="d-flex flex-row justify-content-between mr-2 cart-item-summary">
                            <div class="">
                                <div class="d-flex flex-column">
                                    <div class="text-success">Discount Amount: {{ cart_item.total_discount }}</div>
                                    <div class="">Tax Amount: {{ cart_item.total_tax }}</div>
                                </div>
                            </div>
                            <div class="">
                                <div class="d-flex flex-column">
                                    <div class="text-right">Price: {{ cart_item.quantity }} x {{ cart_item.price }}</div>
                                    <div class="text-right">Total: {{ cart_item.total_price }}</div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                <div class="d-flex flex-column p-3 ml-auto fixed-bottom col-md-4 border-top cart-summary">
                    <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                        <span class="">Sub total</span>
                        <span class="">{{ sub_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                        <span class="">Total Discount</span>
                        <span class="">{{ discount_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                        <span class="">Total After Discount</span>
                        <span class="">{{ total_after_discount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 cart-summary-label">
                        <span class="">Total Tax</span>
                        <span class="">{{ tax_total }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 cart-total">
                        <span class="">Total</span>
                        <span class="">{{ total }}</span>
                    </div>
                    <div class="d-flex mt-2" v-if="processing == false">
                        <div class="mr-2">
                            <button type="submit" class="btn btn-light btn-lg btn-block" @click.stop.prevent="create_order('HOLD')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Hold Order</button>
                        </div>
                        <div class="flex-grow-1">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" @click.stop.prevent="create_order('CLOSE')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Close Order</button>
                        </div>
                    </div>
                    <div class="d-flex mt-2" v-if="processing == true">
                        <span><i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> </span>  Processing your order...
                    </div>
                </div>
            </div>

        </div>

        <modalcomponent v-show="show_customer_modal" v-on:close="show_customer_modal = false" ref='customer'>
            <template v-slot:modal-header>
                Provide Customer Details
            </template>
            <template v-slot:modal-body>
                <div class="form-group">
                    <label for="customer_number">Contact Number</label>
                    <cool-select type="text" name="customer_number" v-model="customer_number" v-validate="'integer|min:10'" class="" placeholder="Provide Contact Number"  autocomplete="off" :items="customer_list" item-text="phone" itemValue='phone' :resetSearchOnBlur='false' ref="customer_number" disable-filtering-by-search @search="load_customers($event, 'phone')" @select="set_filter_customer_number()" :search-text.sync="filter_customer_number">
                        <template #item="{ item }">
                            <div class='d-flex justify-content-start'>
                            <div>
                                {{ item.name }} - {{ item.phone }}, {{ item.email }}
                            </div>
                            </div>
                        </template>
                    </cool-select>
                   <span v-bind:class="{ 'error' : errors.has('customer_number') }">{{ errors.first('customer_number') }}</span>
                </div>
                <div class="form-group">
                    <label for="customer_email">Email</label>
                    <cool-select type="text" name="customer_email" v-model="customer_email" v-validate="'email'" class="" placeholder="Provide Email"  autocomplete="off" :items="customer_list" item-text="email" itemValue='email' :resetSearchOnBlur='false' ref="customer_email" disable-filtering-by-search @search="load_customers($event, 'email')"  @select="set_filter_customer_email()" :search-text.sync="filter_customer_email" >
                        <template #item="{ item }">
                            <div class='d-flex justify-content-start'>
                            <div>
                                {{ item.name }} - {{ item.email }}, {{ item.phone }}
                            </div>
                            </div>
                        </template>
                    </cool-select>
                    <span v-bind:class="{ 'error' : errors.has('customer_email') }">{{ errors.first('customer_email') }}</span>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="select_customer('skip')">Skip</button>
                <button type="button" class="btn btn-primary" @click="select_customer('proceed')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Proceed</button>
            </template>
        </modalcomponent>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-html="server_errors" v-bind:class="[error_class]"></p>
                <form data-vv-scope="confirmation_form">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" v-model="payment_method" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Payment Method..</option>
                            <option v-for="(payment_method, index) in payment_methods" v-bind:value="payment_method.slack" v-bind:key="index">
                                {{ payment_method.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('confirmation_form.payment_method') }">{{ errors.first('confirmation_form.payment_method') }}</span> 
                    </div>
                </form>
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
    
    import moment from "moment";
    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';

    export default {
        components: {
            CoolSelect
        },
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                product_processing      : false,
                show_modal      : false,
                show_customer_modal : false,
                api_link        : (this.order_data == null)?'/api/add_order':'/api/update_order/'+this.order_data.slack,
                default_label   : 'Walkin Customer',
                current_date_time : moment().format('MMM Do YYYY, h:mm:ss a'),

                customer_label  : '-',
                barcode         : '',
                product_title   : '',
                category        : '',

                order_slack    : (this.order_data == null)?'':this.order_data.slack,
                order_number   : (this.order_data == null)?'':this.order_data.order['order_number'],

                sub_total       : (this.order_data == null)?0.00:this.order_data.order['sale_amount_subtotal_excluding_tax'],

                store_level_total_tax_percentage :  this.store_tax_percentage,
                store_level_total_tax_amount     :  (this.order_data == null)?0.00:this.order_data.order['store_level_total_tax_amount'],
                product_level_total_tax_amount   :  (this.order_data == null)?0.00:this.order_data.order['product_level_total_tax_amount'],
                tax_total       : (this.order_data == null)?0.00:this.order_data.order['tax_total'],

                store_level_total_discount_percentage :  this.store_discount_percentage,
                store_level_total_discount_amount     :  (this.order_data == null)?0.00:this.order_data.order['store_level_total_discount_amount'],
                product_level_total_discount_amount   :  (this.order_data == null)?0.00:this.order_data.order['product_level_total_discount_amount'],
                discount_total  : (this.order_data == null)?0.00:this.order_data.order['discount_amount'],

                total_after_discount : (this.order_data == null)?0.00:this.order_data.order['total'],

                total           : (this.order_data == null)?0.00:this.order_data.order['total'],

                product_list    : [],
                customer_number : (this.order_data == null)?'':this.order_data.order['customer_number'],
                customer_email  : (this.order_data == null)?'':this.order_data.order['customer_email'],
                order_status    : '',
                payment_method  : (this.order_data == null)?'':this.order_data.order['payment_method'],
                cart            : (this.order_data == null)?{}:(JSON.parse(this.order_data.cart).length == 0)?{}:JSON.parse(this.order_data.cart),
                item_count      : 0,
                quantity_count  : 0,

                customer_list   : [],
                filter_customer_number : '',
                filter_customer_email  : ''
            }
        },
        props: {
            store_tax_percentage: String,
            store_discount_percentage: String,
            payment_methods: Array,
            categories: Array,
            order_data: [Array, Object],
            store_currency: String,
        },
        filters: {
            truncate: function(value, limit) {
                if (!value) return '';
                if (value.length > limit) {
                    value = value.substring(0, (limit - 3)) + '...';
                }
                return value;
            }
        },
        mounted() {
            console.log('Add order page loaded');
            this.run_clock();
            if(this.order_data !== null){
                this.update_prices();
                this.update_customer();
            }else{
                this.show_customer_modal = true;
            }
        },
        methods: {
            product_info_form(){
                this.product_processing = true;
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("barcode", this.barcode);
                formData.append("product_title", this.product_title);
                formData.append("product_category", this.category);

                axios.post('/api/get_product', formData).then((response) => {
                    this.product_processing = false;
                    this.product_list = response.data.data;
                    if(this.barcode !='' && this.product_list.length == 1){
                        this.add_to_cart(this.product_list[0]);
                        this.barcode = '';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            add_to_cart(product){

                var quantity = (this.cart[product.product_slack] != null)?parseFloat(this.cart[product.product_slack].quantity)+1:1;
                var total_price = parseFloat(quantity)*parseFloat(product.sale_amount_excluding_tax);

                var product_data = { 
                    "product_slack" : product.product_slack,
                    "product_code"  : product.product_code,
                    "name"          : product.product_name,
                    "price"         : product.sale_amount_excluding_tax,
                    "quantity"      : quantity,
                    "tax_percentage": (product.tax_percentage!= null)?product.tax_percentage:0.00,
                    "discount_percentage": (product.discount_percentage!= null)?product.discount_percentage:0.00,
                    "total_price"   : total_price.toFixed(2)
                };

                this.$set(this.cart, product.product_slack, product_data);
                this.update_prices();
            },

            remove_from_cart(product_slack){
                delete this.cart[product_slack];
                this.update_prices();
            },

            validate_quantity :  _.debounce(function (product_slack, event) {
                var entered_quantity = event.target.value;
                if(entered_quantity >0 || entered_quantity == ""){
                    this.update_prices();
                }else{
                    delete this.cart[product_slack];
                    this.update_prices();
                }
            },2000),

            calculate_tax(item_total, tax_percentage){
                var tax_amount = (parseFloat(tax_percentage)/100)*parseFloat(item_total);
                return tax_amount.toFixed(2);
            },

            calculate_store_level_tax(item_total, store_tax_percentage){
                var store_level_tax_percentage = (store_tax_percentage != null)?store_tax_percentage:0; 
                var store_level_tax_amount = (parseFloat(store_level_tax_percentage)/100)*parseFloat(item_total);
                return store_level_tax_amount.toFixed(2);
            },

            calculate_discount(item_total, discount_percentage){
                var discount_amount = (parseFloat(discount_percentage)/100)*parseFloat(item_total);
                return discount_amount.toFixed(2);
            },

            calculate_store_level_discount(item_total, store_discount_percentage){
                var store_level_discount_percentage = (store_discount_percentage != null)?store_discount_percentage:0; 
                var store_level_discount_amount = (parseFloat(store_level_discount_percentage)/100)*parseFloat(item_total);
                return store_level_discount_amount.toFixed(2);
            },

            update_prices(){

                this.sub_total = 0.00;
                this.tax_total = 0.00;
                this.discount_total = 0.00;
                this.total = 0.00;
                this.quantity_count = 0;

                for ( var product_slack in this.cart) {
                    const product_data = this.cart[product_slack];

                    if(product_data.quantity != ""){
                        
                        var quantity = parseFloat(product_data.quantity);
                        if(!isNaN(quantity)){
                            var total_price = parseFloat(quantity)*parseFloat(product_data.price);
                            var total_discount = this.calculate_discount(total_price, product_data.discount_percentage);
                            var total_after_discount = parseFloat(total_price)-parseFloat(total_discount);
                            var total_tax = this.calculate_tax(total_after_discount, product_data.tax_percentage);
                            

                            this.$set(this.cart[product_slack], 'total_price', total_price.toFixed(2));
                            this.$set(this.cart[product_slack], 'total_tax', total_tax);
                            this.$set(this.cart[product_slack], 'total_discount', total_discount);
                            
                            this.sub_total = this.sub_total+parseFloat(total_price);
                            this.tax_total = this.tax_total+parseFloat(total_tax);
                            this.discount_total = this.discount_total+parseFloat(total_discount);

                            this.quantity_count = this.quantity_count+quantity;
                        }
                    }
                }
                this.sub_total = this.sub_total.toFixed(2);
                
                this.store_level_total_discount_amount = this.calculate_store_level_discount(this.sub_total, this.store_level_total_discount_percentage);
                this.discount_total = parseFloat(this.store_level_total_discount_amount)+parseFloat(this.discount_total);
                this.discount_total = this.discount_total.toFixed(2);

                this.total_after_discount = parseFloat(this.sub_total)-parseFloat(this.discount_total);
                this.total_after_discount = this.total_after_discount.toFixed(2);

                this.store_level_total_tax_amount = this.calculate_store_level_tax(this.total_after_discount, this.store_level_total_tax_percentage);
                this.tax_total = parseFloat(this.store_level_total_tax_amount)+parseFloat(this.tax_total);
                this.tax_total = this.tax_total.toFixed(2);

                this.total = parseFloat(this.total_after_discount)+parseFloat(this.tax_total);
                this.total = this.total.toFixed(2);

                this.item_count = Object.keys(this.cart).length;
            },

            create_order(order_status) {

                this.$off("submit");
                this.$off("close");
                if(Object.keys(this.cart).length <= 0){
                    return;
                }
                this.order_status = order_status;
                this.show_modal = true;

                this.$on("submit",function () {
                    
                    this.$validator.validateAll('confirmation_form').then((isValid) => {
                        if (isValid) {
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("order_status", this.order_status);
                            formData.append("customer_number", this.customer_number);
                            formData.append("customer_email", this.customer_email);
                            formData.append("payment_method", this.payment_method);
                            formData.append("cart", JSON.stringify(this.cart));

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                    if(response.data.link != ""){
                                        window.open(response.data.link, '_blank');
                                        setTimeout(function(){
                                            location.reload();
                                        }, 1000);
                                    }else{
                                        setTimeout(function(){
                                            location.reload();
                                        }, 1000);
                                    }
                                }else{
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
                        }
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            load_customers (keywords, type) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("type", type);

                        axios.post('/api/load_customers', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.customer_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                    if(type == 'phone'){
                        this.customer_number = keywords;
                    }else{
                        this.customer_email = keywords;
                    }
                }
            },

            select_customer(action){
                switch(action){
                    case 'skip':
                        this.customer_number = '';
                        this.customer_email = '';
                        this.customer_label = this.default_label;
                        this.set_customer_autocomplete_empty();
                        this.show_customer_modal = false;
                    break;
                    case 'proceed':
                        this.$validator.validateAll();
                        if (!this.errors.any()) {
                            if(this.customer_number == '' && this.customer_email == '' && this.filter_customer_number == '' && this.filter_customer_email == ''){
                                this.customer_label = this.default_label;
                                this.set_customer_autocomplete_empty();
                            }else{
                                if((this.customer_number != '' && this.customer_number != null) || (this.filter_customer_number != '')){
                                    var c_number = (this.customer_number != '' && this.customer_number != null)?this.customer_number:this.filter_customer_number;
                                    this.customer_number = c_number;
                                    this.$refs.customer_number.setSearchData(this.customer_number); 
                                    this.customer_label = this.customer_number;
                                }else if((this.customer_email != '' && this.customer_email != null) || (this.filter_customer_email != '')){
                                    var c_email = (this.customer_email != '' && this.customer_email != null)?this.customer_email:this.filter_customer_email;
                                    this.customer_email = c_email;
                                    this.$refs.customer_email.setSearchData(this.customer_email); 
                                    this.customer_label = this.customer_email;
                                }
                            }
                            this.show_customer_modal = false;
                        }
                    break;
                }
                this.$refs.barcode.focus();
            },

            update_customer(){
                if(this.customer_number == '' && this.customer_email == ''){
                    this.customer_label = this.default_label;
                }else{
                    if(this.customer_number != ''){
                        this.customer_label = this.customer_number;
                        this.$refs.customer_number.setSearchData(this.customer_number);
                    }else if(this.customer_email != ''){
                        this.customer_label = this.customer_email;
                        this.$refs.customer_email.setSearchData(this.customer_email);
                    }
                }
            },

            set_filter_customer_number(){
                if(this.customer_number != ''){
                    this.filter_customer_number = this.customer_number;
                }
            },

            set_filter_customer_email(){
                if(this.customer_email != ''){
                    this.filter_customer_email = this.customer_email;
                }
            },

            set_customer_autocomplete_empty(){
                this.$refs.customer_number.setSearchData(''); 
                this.$refs.customer_email.setSearchData(''); 
            },

            run_clock(){
                setInterval(() => {
                    this.current_date_time = moment().format('MMM Do YYYY, h:mm:ss a');
                }, 1000);
            }
        }
    }
</script>