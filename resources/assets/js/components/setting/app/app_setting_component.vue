<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">App Settings</span>
                </div>
                <div class="">
                    <a v-bind:href="edit_link" class="btn btn-primary"> Edit</a>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="company_name">Company Name</label>
                    <p>{{ company_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_date_format">App Date Time Format</label>
                    <p>{{ app_date_time_format }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_date_format">App Date Format</label>
                    <p>{{ app_date_format }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="app_date_format">Address Ar</label>
                    <p>{{ address_ar }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="app_date_format">Address En</label>
                    <p>{{ address_en }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="app_date_format">About Ar</label>
                    <p>{{ about_ar }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="app_date_format">About En</label>
                    <p>{{ about_en }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="app_date_format">Twitter</label>
                    <p>{{ twitter }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_date_format">Instagram</label>
                    <p>{{ insta }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (updated_by == null)?'-':updated_by['fullname']+' ('+updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ updated_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Company Invoice Print Logo</label>
                    <p><img :src="company_logo" class="company-logo-image"></p>
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
                edit_link       : '/edit_app_setting',
                
                company_name     : (this.app_setting.length == 0)?'-':this.app_setting.company_name,
                app_date_time_format  : (this.app_setting.length == 0)?'-':this.app_setting.app_date_time_format,
                app_date_format  : (this.app_setting.length == 0)?'-':this.app_setting.app_date_format,
                updated_by       : (this.app_setting.length == 0)?null:this.app_setting.updated_by,
                updated_at_label : (this.app_setting.length == 0)?'-':this.app_setting.updated_at_label,
                company_logo     : (this.app_setting.length == 0)?'-':this.app_setting.company_logo_path,
                address_ar     : (this.app_setting.length == 0)?'-':this.app_setting.address_ar,
                address_en     : (this.app_setting.length == 0)?'-':this.app_setting.address_en,
                about_ar     : (this.app_setting.length == 0)?'-':this.app_setting.about_ar,
                about_en     : (this.app_setting.length == 0)?'-':this.app_setting.about_en,
                twitter     : (this.app_setting.length == 0)?'-':this.app_setting.twitter,
                insta     : (this.app_setting.length == 0)?'-':this.app_setting.insta,
            }
        },
        props: {
            app_setting: [Array, Object]
        },
        mounted() {
            console.log('App setting page loaded');
        },
        methods: {
           
        }
    }
</script>