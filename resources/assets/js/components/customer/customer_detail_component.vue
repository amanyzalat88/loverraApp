<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Customer</span> {{ customer.name }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="customer.status.color">{{ customer.status.label }}</span>
                </div>
            </div>

 

          


            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="fullname">Fullname</label>
                    <p>{{ (customer.name == '' || customer.name == null)?'-':customer.name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Email</label>
                    <p>{{ (customer.email == '' || customer.email == null)?'-':customer.email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">Phone</label>
                    <p>{{ (customer.phone == '' || customer.phone == null)?'-':customer.phone }}</p>
                </div>
              
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (customer.created_by == null)?'-':customer.created_by['fullname']+' ('+customer.created_by['customer_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (customer.updated_by == null)?'-':customer.updated_by['fullname']+' ('+customer.updated_by['customer_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ customer.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ customer.updated_at_label }}</p>
                </div>
            </div>

              <div class="mb-2">
                <span class="text-subhead">Address Details</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Delivery Area</th>
                        <th scope="col">Building / Villa</th>
                        <th scope="col" class="text-right">Street</th>
                        <th scope="col" class="text-right">Flat Number</th>
                        <th scope="col" class="text-right">Nearest Landmark</th>
                        <th scope="col" class="text-right">City</th>
                        <th scope="col" class="text-right">Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(customer_addresses, key, index) in addresses" v-bind:value="customer_addresses.slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ customer_addresses.delivery }}</td>
                            <td>{{ customer_addresses.building }}</td>
                            <td class="text-right">{{ customer_addresses.street }}</td>
                            <td class="text-right">{{ customer_addresses.flatnumber }}</td>
                            <td class="text-right">{{ customer_addresses.landmark }}</td>
                            <td class="text-right">{{ customer_addresses.city }}</td>
                            <td class="text-right">{{ customer_addresses.country }}</td>
                            
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
                customer  : this.customer_data,
                addresses : this.customer_data.addresses
            }
        },
        props: {
            customer_data: [Array, Object]
        },
        mounted() {
            console.log('Customer detail page loaded');
        },
        methods: {
           
        }
    }
</script>