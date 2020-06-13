<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Store</span> {{ store.name }} ({{ store.store_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="store.status.color">{{ store.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="store_code">Store Code</label>
                    <p>{{ store.store_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">Name</label>
                    <p>{{ store.name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="tax_number">Tax Number or GST number</label>
                    <p>{{ store.tax_number }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (store.created_by == null)?'-':store.created_by['fullname']+' ('+store.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (store.updated_by == null)?'-':store.updated_by['fullname']+' ('+store.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ store.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ store.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Contact Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="primary_contact">Primary Contact No.</label>
                    <p>{{ (store.primary_contact != null)?store.primary_contact:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="secondary_contact">Secondary Contact No.</label>
                    <p>{{  (store.secondary_contact != null)?store.secondary_contact:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="primary_email">Primary Email</label>
                    <p>{{  (store.secondary_contact != null)?store.primary_email:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="secondary_email">Secondary Email</label>
                    <p>{{  (store.secondary_contact != null)?store.secondary_email:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="address">Address</label>
                    <p>{{ store.address }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="pincode">Pincode</label>
                    <p>{{ store.pincode }}</p>
                </div>
            </div>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">Tax Information</span>
                </div>
                <div class="form-row mb-2" v-if="store.tax_code != null">
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code</label>
                        <p>{{ store.tax_code.tax_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">Tax Percentage</label>
                        <p>{{ store.tax_code.tax_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_label">Tax Name</label>
                        <p>{{ store.tax_code.label }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="store.tax_code != null">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">Tax Type</th>
                                    <th scope="col">Tax Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in store.tax_code.tax_components" v-bind:key="index">
                                        <td>{{ tax_component.tax_type }}</td>
                                        <td>{{ tax_component.tax_percentage }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span class="mb-2" v-else>No Tax Components</span>
                    </div>
                </div>
                
            </div>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">Discount Information</span>
                </div>
                <div class="form-row mb-2" v-if="store.discount_code != null">
                    <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <p>{{ store.discount_code.discount_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">Discount Percentage</label>
                        <p>{{ store.discount_code.discount_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_label">Discount Name</label>
                        <p>{{ store.discount_code.label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_description">Discount Description</label>
                        <p>{{ store.discount_code.description }}</p>
                    </div>
                </div>
                <div class="mb-3" v-else>No Discount Information</div>
            </div>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">Invoice Print Type Details</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="invoice_type">Invoice Print Type</label>
                        <p>{{ store.invoice_type.print_type_label }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                store : this.store_data
            }
        },
        props: {
            store_data: [Array, Object]
        },
        mounted() {
            console.log('Store detail page loaded');
        },
        methods: {
           
        }
    }
</script>