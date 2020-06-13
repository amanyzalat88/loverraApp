<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title">Reports</span>
                        </div>
                    </div>
                </div>
                <div class="">

                </div>
            </div>

            <form @submit.prevent="download_user_report('user_report_form')" data-vv-scope="user_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">User Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="user_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="user_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="user_report_form.server_errors" v-bind:class="[user_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="user_report_form.from_created_date"  input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format" v-model="user_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="role">Role</label>
                        <select name="role" v-model="user_report_form.role" class="form-control form-control-custom custom-select">
                            <option value="">Choose Role..</option>
                            <option v-for="(role, index) in roles" v-bind:value="role.slack" v-bind:key="index">
                                {{ role.role_code }} - {{ role.label }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="user_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in user_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>
            
            <hr class='mb-4'>

            <form @submit.prevent="download_product_report('product_report_form')" data-vv-scope="product_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Product Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="product_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="product_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="product_report_form.server_errors" v-bind:class="[product_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="product_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="product_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier">Supplier</label>
                        <select name="supplier" v-model="product_report_form.supplier" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Supplier..</option>
                            <option v-for="(supplier, index) in suppliers" v-bind:value="supplier.slack" v-bind:key="index">
                               {{ supplier.supplier_code }} - {{ supplier.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('supplier') }">{{ errors.first('supplier') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category">Category</label>
                        <select name="category" v-model="product_report_form.category" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Category..</option>
                            <option v-for="(category, index) in categories" v-bind:value="category.slack" v-bind:key="index">
                                {{ category.category_code }} - {{ category.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('category') }">{{ errors.first('category') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code</label>
                        <select name="tax_code" v-model="product_report_form.tax_code" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Tax Code..</option>
                            <option v-for="(taxcode, index) in taxcodes" v-bind:value="taxcode.slack" v-bind:key="index">
                                {{ taxcode.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <select name="discount_code" v-model="product_report_form.discount_code" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Discount Code..</option>
                            <option v-for="(discount_code, index) in discountcodes" v-bind:value="discount_code.slack" v-bind:key="index">
                                {{ discount_code.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="product_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in user_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_order_report('order_report_form')" data-vv-scope="order_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Order Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="order_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="order_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="order_report_form.server_errors" v-bind:class="[order_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="order_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="order_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="order_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in order_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_purchase_order_report('purchase_order_report_form')" data-vv-scope="purchase_order_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Purchase Order Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="purchase_order_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="purchase_order_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="purchase_order_report_form.server_errors" v-bind:class="[purchase_order_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="purchase_order_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="purchase_order_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="purchase_order_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in purchase_order_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_customer_report('customer_report_form')" data-vv-scope="customer_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Customer Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="customer_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="customer_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="customer_report_form.server_errors" v-bind:class="[customer_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format" v-model="customer_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="customer_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="customer_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in customer_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_taxcode_report('taxcode_report_form')" data-vv-scope="taxcode_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Tax Code Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="taxcode_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="taxcode_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="taxcode_report_form.server_errors" v-bind:class="[taxcode_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="taxcode_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="taxcode_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="taxcode_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in taxcode_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_discountcode_report('discountcode_report_form')" data-vv-scope="discountcode_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Discount Code Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="discountcode_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="discountcode_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="discountcode_report_form.server_errors" v-bind:class="[discountcode_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="discountcode_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="discountcode_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="discountcode_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in discountcode_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_supplier_report('supplier_report_form')" data-vv-scope="supplier_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Supplier Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="supplier_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="supplier_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="supplier_report_form.server_errors" v-bind:class="[supplier_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="supplier_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="supplier_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="supplier_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in supplier_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

            <hr class='mb-4'>

            <form @submit.prevent="download_category_report('category_report_form')" data-vv-scope="category_report_form" class="mb-2">
                <div class="d-flex flex-wrap mb-2">
                    <div class="mr-auto">
                    <div class="d-flex">
                            <div>
                                <span class="text-subhead">Category Report</span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="category_report_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="category_report_form.processing == true"></i> Download</button>
                    </div>
                </div>

                <p v-html="category_report_form.server_errors" v-bind:class="[category_report_form.error_class]"></p>

                <div class="form-row mb-1">
                    <div class="form-group col-md-3">
                        <label for="from_created_date">From Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="category_report_form.from_created_date" input-class="form-control bg-white" placeholder="Select from created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_created_date">To Created Date</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="category_report_form.to_created_date" input-class="form-control bg-white" placeholder="Select to created date"></date-picker>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="category_report_form.status" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in category_statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select> 
                    </div>
                </div>
            </form>

        </div>
    </div>
</template>  

<script>
    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import moment from "moment";

    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },

                user_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    role : '',
                    status : ''
                },

                product_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    supplier : '',
                    category : '',
                    tax_code : '',
                    discount_code : '',
                    status : ''
                },

                order_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                purchase_order_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                customer_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                store_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                taxcode_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                discountcode_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                supplier_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },

                category_report_form: {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,

                    from_created_date : '',
                    to_created_date : '',
                    status : ''
                },
            }
        },
        components: {
            DatePicker
        },
        props: {
            user_statuses: Array,
            roles: Array,
            
            product_statuses: Array,
            suppliers: Array,
            categories: Array,
            taxcodes: Array,
            discountcodes: Array,

            order_statuses: Array,

            purchase_order_statuses: Array,

            customer_statuses: Array,
            
            category_statuses: Array,

            supplier_statuses: Array,

            taxcode_statuses: Array,

            discountcode_statuses: Array,
        },
        mounted() {
            console.log('Report page loaded');
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
            },

            download_user_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.user_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.user_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.user_report_form.to_created_date));
                        formData.append("role", this.user_report_form.role);
                        formData.append("status", this.user_report_form.status);

                        axios.post('/api/user_report', formData).then((response) => {
                            this.user_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.user_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.user_report_form.server_errors = response.data.msg;
                                }
                                this.user_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_product_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.product_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.product_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.product_report_form.to_created_date));
                        formData.append("supplier", this.product_report_form.supplier);
                        formData.append("category", this.product_report_form.category);
                        formData.append("tax_code", this.product_report_form.tax_code);
                        formData.append("discount_code", this.product_report_form.discount_code);
                        formData.append("status", this.product_report_form.status);

                        axios.post('/api/product_report', formData).then((response) => {
                            this.product_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.product_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.product_report_form.server_errors = response.data.msg;
                                }
                                this.product_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_order_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.order_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.order_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.order_report_form.to_created_date));
                        formData.append("status", this.order_report_form.status);

                        axios.post('/api/order_report', formData).then((response) => {
                            this.order_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.order_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.order_report_form.server_errors = response.data.msg;
                                }
                                this.order_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_purchase_order_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.purchase_order_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.purchase_order_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.purchase_order_report_form.to_created_date));
                        formData.append("status", this.purchase_order_report_form.status);

                        axios.post('/api/purchase_order_report', formData).then((response) => {
                            this.purchase_order_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.purchase_order_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.purchase_order_report_form.server_errors = response.data.msg;
                                }
                                this.purchase_order_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_customer_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.customer_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.customer_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.customer_report_form.to_created_date));
                        formData.append("status", this.customer_report_form.status);

                        axios.post('/api/customer_report', formData).then((response) => {
                            this.customer_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.customer_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.customer_report_form.server_errors = response.data.msg;
                                }
                                this.customer_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_store_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.store_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.store_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.store_report_form.to_created_date));
                        formData.append("status", this.store_report_form.status);

                        axios.post('/api/store_report', formData).then((response) => {
                            this.store_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.store_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.store_report_form.server_errors = response.data.msg;
                                }
                                this.store_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_taxcode_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.taxcode_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.taxcode_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.taxcode_report_form.to_created_date));
                        formData.append("status", this.taxcode_report_form.status);

                        axios.post('/api/taxcode_report', formData).then((response) => {
                            this.taxcode_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.taxcode_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.taxcode_report_form.server_errors = response.data.msg;
                                }
                                this.taxcode_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_discountcode_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.discountcode_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.discountcode_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.discountcode_report_form.to_created_date));
                        formData.append("status", this.discountcode_report_form.status);

                        axios.post('/api/discountcode_report', formData).then((response) => {
                            this.discountcode_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.discountcode_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.discountcode_report_form.server_errors = response.data.msg;
                                }
                                this.discountcode_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_supplier_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.supplier_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.supplier_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.supplier_report_form.to_created_date));
                        formData.append("status", this.supplier_report_form.status);

                        axios.post('/api/supplier_report', formData).then((response) => {
                            this.supplier_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.supplier_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.supplier_report_form.server_errors = response.data.msg;
                                }
                                this.supplier_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            download_category_report(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.category_report_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("from_created_date", this.convert_date_format(this.category_report_form.from_created_date));
                        formData.append("to_created_date", this.convert_date_format(this.category_report_form.to_created_date));
                        formData.append("status", this.category_report_form.status);

                        axios.post('/api/category_report', formData).then((response) => {
                            this.category_report_form.processing = false;
                            if(response.data.status_code == 200) {
                                if(response.data.link != ""){
                                    
                                    const link = document.createElement('a');
                                    link.href = response.data.link;
                                    document.body.appendChild(link);
                                    link.click();

                                }else{
                                    location.reload();
                                }
                            }else{
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.category_report_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.category_report_form.server_errors = response.data.msg;
                                }
                                this.category_report_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            }
        }
    }
</script>