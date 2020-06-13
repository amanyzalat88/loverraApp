<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> Order #{{ order_basic.order_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="order_basic.status.color">{{ order_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4" v-if="order_basic.status.value == 1">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <button type="submit" class="btn btn-danger mr-1" v-if="delete_order_access == true" v-on:click="delete_order()" v-bind:disabled="order_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="order_processing == true"></i> Delete Order</button>

                    <a class="btn btn-outline-primary" v-bind:href="'/print_order/'+slack" target="_blank">Print</a>

                </div>

            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="email">Email</label>
                    <p>{{ order_basic.customer_email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Phone</label>
                    <p>{{ order_basic.customer_phone }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Payment Mode</label>
                    <p>{{ order_basic.payment_method }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (order_basic.created_by == null)?'-':order_basic.created_by['fullname']+' ('+order_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (order_basic.updated_by == null)?'-':order_basic.updated_by['fullname']+' ('+order_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ order_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ order_basic.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">Order Level Tax Information</span>
                </div>
                <div class="form-row mb-2" v-if="order_basic.order_level_tax_percentage >0">
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code</label>
                        <p>{{ order_basic.order_level_tax_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">Tax Percentage</label>
                        <p>{{ order_basic.order_level_tax_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_amount">Tax Amount</label>
                        <p>{{ order_basic.order_level_tax_amount }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="order_basic.order_level_tax_percentage >0">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">Tax Type</th>
                                    <th scope="col">Tax Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in order_basic.order_level_tax_components" v-bind:key="index">
                                        <td>{{ tax_component.tax_type }}</td>
                                        <td>{{ tax_component.tax_percentage }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span class="mb-2" v-else>No Order Level Tax Components</span>
                    </div>
                </div>
                
            </div>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">Order Level Discount Information</span>
                </div>
                <div class="form-row mb-2" v-if="order_basic.order_level_discount_percentage >0">
                    <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <p>{{ order_basic.order_level_discount_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">Discount Percentage</label>
                        <p>{{ order_basic.order_level_discount_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_label">Discount Amount</label>
                        <p>{{ order_basic.order_level_discount_amount }}</p>
                    </div>
                </div>
                <div class="mb-3" v-else>No Order Level Discount Information</div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Product Information</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Product Code</th>
                        <th scope="col">Product</th>
                        <th scope="col" class="text-right">Quantity</th>
                        <th scope="col" class="text-right">Price (EXCL Tax)</th>
                        <th scope="col" class="text-right">Discount %</th>
                        <th scope="col" class="text-right">Discount Amount</th>
                        <th scope="col" class="text-right">Tax %</th>
                        <th scope="col" class="text-right">Tax Amount</th>
                        <th scope="col" class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(order_product, key, index) in products" v-bind:value="order_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ order_product.product_code }}</td>
                            <td>{{ order_product.name }}</td>
                            <td class="text-right">{{ order_product.quantity }}</td>
                            <td class="text-right">{{ order_product.price }}</td>
                            <td class="text-right">{{ order_product.discount_percentage }}</td>
                            <td class="text-right">{{ order_product.discount_amount }}</td>
                            <td class="text-right">{{ order_product.tax_percentage }}</td>
                            <td class="text-right">{{ order_product.tax_amount }}</td>
                            <td class="text-right">{{ order_product.total_price }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Sub Total (EXCL Tax)</td>
                            <td class="text-right">{{ order_basic.sale_amount_subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total Discount</td>
                            <td class="text-right">{{ order_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total After Discount</td>
                            <td class="text-right">{{ order_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total Tax</td>
                            <td class="text-right">{{ order_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total (INCL Tax)</td>
                            <td class="text-right">{{ order_basic.total_order_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                show_modal      : false,
                delete_order_api_link : '/api/delete_order/'+this.order_data.slack,
                
                slack           : this.order_data.slack,
                order_basic     : this.order_data,
                products        : this.order_data.products
            }
        },
        props: {
            order_data: [Array, Object],
            delete_order_access: Boolean
        },
        mounted() {
            console.log('Order detail page loaded');
        },
        methods: {
           delete_order(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_order_api_link, formData).then((response) => {

                        if(response.data.status_code == 200) {
                            if(response.data.link != ""){
                                window.location.href = response.data.link;
                            }else{
                                location.reload();
                            }
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
                        this.order_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            }
        }
    }
</script>