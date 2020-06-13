<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">Email Settings</span>
                </div>
                <div class="">
                    <a v-bind:href="edit_link" class="btn btn-primary"> Edit</a>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="email">Type</label>
                    <p>{{ type }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Driver</label>
                    <p>{{ driver }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Host</label>
                    <p>{{ host }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Port</label>
                    <p>{{ port }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Username</label>
                    <p>{{ username }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Password</label>
                    <p>{{ password }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Encryption</label>
                    <p>{{ encryption }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">From Email</label>
                    <p>{{ from_email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">From Email Name</label>
                    <p>{{ from_email_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">Status</label>
                    <p><span v-bind:class="status_color">{{ status_label }}</span></p>
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
                server_errors   : '',
                error_class     : '',
                processing      : false,
                edit_link       : (this.email_setting.length == 0)?'/edit_email_setting':'/edit_email_setting/'+this.email_setting.slack,
                
                type            : (this.email_setting.length == 0)?'-':this.email_setting.type,
                driver          : (this.email_setting.length == 0)?'-':this.email_setting.driver,
                host            : (this.email_setting.length == 0)?'-':this.email_setting.host,
                port            : (this.email_setting.length == 0)?'-':this.email_setting.port,
                username        : (this.email_setting.length == 0)?'-':this.email_setting.username,
                password        : (this.email_setting.length == 0)?'-':this.email_setting.password,
                encryption      : (this.email_setting.length == 0)?'-':this.email_setting.encryption,
                from_email      : (this.email_setting.length == 0)?'-':this.email_setting.from_email,
                from_email_name : (this.email_setting.length == 0)?'-':this.email_setting.from_email_name,
                status_label    : (this.email_setting.length == 0)?'-':this.email_setting.status.label,
                status_color    : (this.email_setting.length == 0)?'':this.email_setting.status.color,
            }
        },
        props: {
            email_setting: [Array, Object]
        },
        mounted() {
            console.log('Email setting page loaded');
        },
        methods: {
           
        }
    }
</script>