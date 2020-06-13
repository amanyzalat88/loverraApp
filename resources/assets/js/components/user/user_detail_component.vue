<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>User</span> {{ user.fullname }} ({{ user.user_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="user.status.color">{{ user.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="user_code">User Code</label>
                    <p>{{ user.user_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="fullname">Fullname</label>
                    <p>{{ user.fullname }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Email</label>
                    <p>{{ user.email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">Phone</label>
                    <p>{{ user.phone }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="role">Role</label>
                    <p>{{ user.role.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (user.created_by == null)?'-':user.created_by['fullname']+' ('+user.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (user.updated_by == null)?'-':user.updated_by['fullname']+' ('+user.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ user.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ user.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Store Access</span>
            </div>
            <div class="table-responsive table-sm" v-if="user.stores !=''">
                <table class="table display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Store Code</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Pincode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(store, key, index) in user.stores" v-bind:value="store.slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ store.store_code }}</td>
                            <td>{{ store.name }}</td>
                            <td>{{ store.address }}</td>
                            <td>{{ (store.pincode == null)?'-':store.pincode }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-3" v-else>No store access</div>
        </div>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                user            : this.user_data,
                stores          : this.user_data.stores,
            }
        },
        props: {
            user_data: [Array, Object]
        },
        mounted() {
            console.log('User detail page loaded');
        },
        methods: {
           
        }
    }
</script>