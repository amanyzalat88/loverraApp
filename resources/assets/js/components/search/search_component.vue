<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="fire_filter_requests" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">Search</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>
                
                <div class="form-row mb-2">
                    <div class="form-group col-md-4">
                        <input type="text" v-model="search_query" class="form-control form-control-custom" placeholder="Search by keyword"  autocomplete="off">
                    </div>
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-primary" :disabled="search_query.length == 0"> Search</button>
                    </div>
                </div>

                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#orders" role="tab" aria-controls="pills-home" aria-selected="true">Orders [{{ order.count }}]</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#customers" role="tab" aria-controls="pills-profile" aria-selected="false">Customers [{{ customer.count }}]</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pos" role="tab" aria-controls="pills-contact" aria-selected="false">Purchase Orders [{{ purchase_order.count }}]</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#users" role="tab" aria-controls="pills-contact" aria-selected="false">Users [{{ user.count }}]</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="card filter-card mb-2" v-for="(order, index) in order.list" v-bind:value="order.slack" v-bind:key="index">
                            <div class="card-body p-3">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="order_number">Order Number</label>
                                        <p class="m-0">{{ order.order_number }}&nbsp;&nbsp;<a v-if="order.detail_link != ''" v-bind:href="order.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                                    </div> 
                                    <div class="form-group col-md-3">
                                        <label for="email">Email</label>
                                        <p class="m-0">{{ order.customer_email }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="email">Phone</label>
                                        <p class="m-0">{{ order.customer_phone }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="email">Amount</label>
                                        <p class="m-0">{{ order.total_order_amount }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="created_on">Created On</label>
                                        <p class="m-0">{{ order.created_at_label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="status">Status</label>
                                        <p class="m-0"><span v-bind:class="order.status.color">{{ order.status.label }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="card filter-card mb-2" v-for="(customer, index) in customer.list" v-bind:value="customer.slack" v-bind:key="index">
                            <div class="card-body p-3">
                                <div class="form-row mb-2">
                                    <div class="form-group col-md-3">
                                        <label for="fullname">Fullname</label>
                                        <p class="m-0">{{ customer.name }}&nbsp;&nbsp;<a v-if="customer.detail_link != ''" v-bind:href="customer.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="email">Email</label>
                                        <p class="m-0">{{ customer.email }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="phone">Phone</label>
                                        <p class="m-0">{{ customer.phone }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="created_on">Created On</label>
                                        <p class="m-0">{{ customer.created_at_label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="status">Status</label>
                                        <p class="m-0"><span v-bind:class="customer.status.color">{{ customer.status.label }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="pos" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <div class="card filter-card mb-2" v-for="(purchase_order, index) in purchase_order.list" v-bind:value="purchase_order.slack" v-bind:key="index">
                            <div class="card-body p-3">
                                <div class="form-row mb-2">
                                    <div class="form-group col-md-3">
                                        <label for="po_reference">PO Number</label>
                                        <p class="m-0">{{ purchase_order.po_number }}&nbsp;&nbsp;<a v-if="purchase_order.detail_link != ''" v-bind:href="purchase_order.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="po_reference">Reference Number</label>
                                        <p class="m-0">{{ purchase_order.po_reference }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="order_date">Order Date</label>
                                        <p class="m-0">{{ purchase_order.order_date }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="order_due_date">Order Due Date</label>
                                        <p class="m-0">{{ purchase_order.order_due_date }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="created_by">Created By</label>
                                        <p class="m-0">{{ (purchase_order.created_by == null)?'-':purchase_order.created_by['fullname']+' ('+purchase_order.created_by['user_code']+')' }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="created_on">Created On</label>
                                        <p class="m-0">{{ purchase_order.created_at_label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="updated_on">Updated On</label>
                                        <p class="m-0">{{ purchase_order.updated_at_label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="updated_on">Status</label>
                                        <p class="m-0"><span v-bind:class="purchase_order.status.color">{{ purchase_order.status.label }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <div class="card filter-card mb-2" v-for="(user, index) in user.list" v-bind:value="user.slack" v-bind:key="index">
                            <div class="card-body p-3">
                                <div class="form-row mb-2">
                                    <div class="form-group col-md-3">
                                        <label for="user_code">User Code</label>
                                        <p class="m-0">{{ user.user_code }}&nbsp;&nbsp;<a v-if="user.detail_link != ''" v-bind:href="user.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="fullname">Fullname</label>
                                        <p class="m-0">{{ user.fullname }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="email">Email</label>
                                        <p class="m-0">{{ user.email }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="phone">Phone</label>
                                        <p class="m-0">{{ user.phone }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="role">Role</label>
                                        <p class="m-0">{{ user.role.label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="created_on">Created On</label>
                                        <p class="m-0">{{ user.created_at_label }}</p>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="updated_on">Status</label>
                                        <p class="m-0"><span v-bind:class="user.status.color">{{ user.status.label }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
                
        </div>
        
    </div>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,

                search_query    : '',
                
                order           : {
                    link : '/api/filter_orders',
                    list : [],
                    count : 0
                },

                customer        : {
                    link : '/api/filter_customers',
                    list : [],
                    count : 0
                },

                purchase_order  : {
                    link : '/api/filter_purchase_orders',
                    list : [],
                    count : 0
                },

                user           : {
                    link : '/api/filter_users',
                    list : [],
                    count : 0
                }

                
            }
        },
        props: {
            
        },
        mounted() {
            console.log('Search page loaded');
        },
        methods: {
            fire_filter_requests(){
                this.filter_orders();
                this.filter_customers();
                this.filter_pos();
                this.filter_users();
            },

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("keyword", this.search_query);
                return formData;
            },

            filter_orders(){
                var formData = this.set_form_data();

                axios.post(this.order.link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.order.list =  response.data.data;
                        this.order.count = response.data.data.length;
                    }else{
                        this.order.list = [];
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            filter_customers(){
                var formData = this.set_form_data();

                axios.post(this.customer.link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.customer.list =  response.data.data;
                        this.customer.count = response.data.data.length;
                    }else{
                        this.customer.list = [];
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            filter_pos(){
                var formData = this.set_form_data();

                axios.post(this.purchase_order.link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.purchase_order.list =  response.data.data;
                        this.purchase_order.count = response.data.data.length;
                    }else{
                        this.purchase_order.list = [];
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            filter_users(){
                var formData = this.set_form_data();

                axios.post(this.user.link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.user.list =  response.data.data;
                        this.user.count = response.data.data.length;
                    }else{
                        this.user.list = [];
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>