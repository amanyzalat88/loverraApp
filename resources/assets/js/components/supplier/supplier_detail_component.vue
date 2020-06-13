<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Supplier</span> {{ supplier.name }} ({{ supplier.supplier_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="supplier.status.color">{{ supplier.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="supplier_code">supplier Code</label>
                    <p>{{ supplier.supplier_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">Name</label>
                    <p>{{ supplier.name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Email</label>
                    <p>{{ supplier.email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">Contact No</label>
                    <p>{{ supplier.phone }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (supplier.created_by == null)?'-':supplier.created_by['fullname']+' ('+supplier.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (supplier.updated_by == null)?'-':supplier.updated_by['fullname']+' ('+supplier.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ supplier.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ supplier.updated_at_label }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="address">Address</label>
                    <p>{{ supplier.address }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="pincode">Pincode</label>
                    <p>{{ supplier.pincode }}</p>
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
                supplier : this.supplier_data,
            }
        },
        props: {
            supplier_data: [Array, Object]
        },
        mounted() {
            console.log('Supplier detail page loaded');
        },
        methods: {
           
        }
    }
</script>