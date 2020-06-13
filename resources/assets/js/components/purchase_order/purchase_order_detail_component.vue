<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> Purchase Order #{{ po_basic.po_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="po_basic.status.color">{{ po_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4" v-if="po_statuses != ''">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <a class="btn btn-outline-primary" v-bind:href="'/print_purchase_order/'+slack" target="_blank">Print</a>

                    <div class="dropdown d-inline">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="po_action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Change Status
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="po_action">
                            <button class="dropdown-item" type="button" v-for="(po_status, key, index) in po_statuses" v-bind:value="po_status.value_constant" v-bind:key="index" v-on:click="change_po_status(po_status.value_constant)">Mark as {{ po_status.label }}</button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="po_reference">Reference Number</label>
                    <p>{{ po_basic.po_reference }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_date">Order Date</label>
                    <p>{{ po_basic.order_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_due_date">Order Due Date</label>
                    <p>{{ po_basic.order_due_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (po_basic.created_by == null)?'-':po_basic.created_by['fullname']+' ('+po_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (po_basic.updated_by == null)?'-':po_basic.updated_by['fullname']+' ('+po_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ po_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ po_basic.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">Supplier Information</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="supplier_name">Supplier Name</label>
                        <p>{{ po_basic.supplier_name }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">Address</label>
                        <p>{{ po_basic.supplier_address }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="currency_name">Currency</label>
                        <p>{{ po_basic.currency_name }} ({{ po_basic.currency_code }})</p>
                    </div>
                </div>
                
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
                        <tr v-for="(po_product, key, index) in products" v-bind:value="po_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ po_product.product_code }}</td>
                            <td>{{ po_product.name }}</td>
                            <td class="text-right">{{ po_product.quantity }}</td>
                            <td class="text-right">{{ po_product.amount_excluding_tax }}</td>
                            <td class="text-right">{{ po_product.discount_percentage }}</td>
                            <td class="text-right">{{ po_product.discount_amount }}</td>
                            <td class="text-right">{{ po_product.tax_percentage }}</td>
                            <td class="text-right">{{ po_product.tax_amount }}</td>
                            <td class="text-right">{{ po_product.subtotal_amount_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Sub Total (EXCL Tax)</td>
                            <td class="text-right">{{ po_basic.subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total Discount</td>
                            <td class="text-right">{{ po_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total After Discount</td>
                            <td class="text-right">{{ po_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total Tax</td>
                            <td class="text-right">{{ po_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Shipping Charge</td>
                            <td class="text-right">{{ po_basic.shipping_charge }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Packaging Charge</td>
                            <td class="text-right">{{ po_basic.packing_charge }}</td>
                        </tr>
                        <tr>
                            <td colspan="9" class="text-right">Total (INCL Tax)</td>
                            <td class="text-right">{{ po_basic.total_order_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                
                change_po_link  : '/api/update_po_status/'+this.purchase_order_data.slack,
                slack           : this.purchase_order_data.slack,
                po_basic        : this.purchase_order_data,
                products        : this.purchase_order_data.products
            }
        },
        props: {
            purchase_order_data: [Array, Object],
            po_statuses: Array
        },
        mounted() {
            console.log('PO detail page loaded');
        },
        methods: {
            change_po_status(po_status){
                this.processing = true;
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("status", po_status);

                axios.post(this.change_po_link, formData).then((response) => {
                    
                    this.show_modal = false;
                    this.processing = false;

                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                        this.show_modal = false;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>