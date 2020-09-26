<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Product</span> {{ product.name }} ({{ product.product_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="product.status.color">{{ product.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="product_code">Product Code</label>
                    <p>{{ product.product_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">الاسم</label>
                    <p>{{ product.name_ar }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">Name</label>
                    <p>{{ product.name_en }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Supplier</label>
                    <p>{{ product.supplier.name }} ({{product.supplier.supplier_code}})</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">Category</label>
                    <p>{{ product.category.label }} ({{product.category.category_code}})</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (product.created_by == null)?'-':product.created_by['fullname']+' ('+product.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (product.updated_by == null)?'-':product.updated_by['fullname']+' ('+product.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ product.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ product.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Price and Quantity Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="purchase_amount_excluding_tax">Purchase Price Excluding Tax</label>
                    <p>{{ product.purchase_amount_excluding_tax }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="sale_amount_excluding_tax">Sale Price Excluding Tax</label>
                    <p>{{ product.sale_amount_excluding_tax }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="quantity">Quantity</label>
                    <p>{{ product.quantity }}</p>
                </div>
            </div>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">Tax Information</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code</label>
                        <p>{{ product.tax_code.tax_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">Tax Percentage</label>
                        <p>{{ product.tax_code.total_tax_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_label">Tax Name</label>
                        <p>{{ product.tax_code.label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_description">Tax Description</label>
                        <p>{{ product.tax_code.description }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="product.tax_code.total_tax_percentage > 0">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">Tax Type</th>
                                    <th scope="col">Tax Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in product.tax_code.tax_components" v-bind:key="index">
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
                <div class="form-row mb-2" v-if="product.discount_code != null">
                    <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <p>{{ product.discount_code.discount_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">Discount Percentage</label>
                        <p>{{ product.discount_code.discount_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_label">Discount Name</label>
                        <p>{{ product.discount_code.label_en }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_description">Discount Description</label>
                        <p>{{ product.discount_code.description }}</p>
                    </div>
                </div>
                <div class="mb-3" v-else>No Discount Information</div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">Product Description en</label>
                    <p>{{ product.description_en }}</p>
                </div>
                 <div class="form-group col-md-6">
                    <label for="description">Product Description ar</label>
                    <p>{{ product.description_ar }}</p>
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
                product : this.product_data
            }
        },
        props: {
            product_data: [Array, Object]
        },
        mounted() {
            console.log('Product detail page loaded');
        },
        methods: {
           
        }
    }
</script>