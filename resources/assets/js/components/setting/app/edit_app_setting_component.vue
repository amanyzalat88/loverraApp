<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">Edit App Setting</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="company_name">Company Name</label>
                        <input type="text" name="company_name" v-model="company_name" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter Company Name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('company_name') }">{{ errors.first('company_name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="twitter">Twitter </label>
                        <input type="text" name="twitter" v-model="twitter" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter Company Name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('twitter') }">{{ errors.first('twitter') }}</span> 
                       
                    </div>
                     <div class="form-group col-md-3">
                        <label for="insta">Instagram </label>
                        <input type="text" name="insta" v-model="insta" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter Company Name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('insta') }">{{ errors.first('insta') }}</span> 
                       
                    </div>
                     <div class="form-group col-md-3">
                        <label for="phone">Phone </label>
                        <input type="text" name="phone" v-model="phone" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter Company Name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span> 
                       
                    </div>
                   <!-- <div class="form-group col-md-3">
                        <label for="app_date_time_format">Date Time format</label>
                        <select name="app_date_time_format" v-model="app_date_time_format" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Date Time format..</option>
                            <option v-for="(date_time_format, index) in date_time_formats" v-bind:value="date_time_format.date_format_value" v-bind:key="index">
                                {{ date_time_format.date_format_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('app_date_time_format') }">{{ errors.first('app_date_time_format') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="app_date_format">Date Format</label>
                        <select name="app_date_format" v-model="app_date_format" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Date Format..</option>
                            <option v-for="(date_format, index) in date_formats" v-bind:value="date_format.date_format_value" v-bind:key="index">
                                {{ date_format.date_format_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('app_date_format') }">{{ errors.first('app_date_format') }}</span> 
                    </div>-->
                </div>

               <div class="form-row mb-2">
               <div class="form-group col-md-3">
                        <label for="email">Email </label>
                        <input type="text" name="email" v-model="email" v-validate="'required|email|max:250'" class="form-control form-control-custom" placeholder="Please enter Company Name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                       
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address_ar">Address <span style="color:red">Ar</span></label>
                        <textarea name="address_ar" v-model="address_ar" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter Address"></textarea>
                        
                        <span v-bind:class="{ 'error' : errors.has('address_ar') }">{{ errors.first('address_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address_en">Address <span style="color:red">En</span></label>
                          <textarea name="address_en" v-model="address_en" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter Address"></textarea>
                       
                        <span v-bind:class="{ 'error' : errors.has('address_en') }">{{ errors.first('address_en') }}</span> 
                       
                    </div>
                     
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="about_ar">About <span style="color:red">Ar</span></label>
                          <textarea name="about_ar" v-model="about_ar" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter About"></textarea>
                     
                        <span v-bind:class="{ 'error' : errors.has('about_ar') }">{{ errors.first('about_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="about_en">About <span style="color:red">En</span></label>
                         <textarea name="about_en" v-model="about_en" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter About"></textarea>
                        
                        <span v-bind:class="{ 'error' : errors.has('about_en') }">{{ errors.first('about_en') }}</span> 
                       
                    </div>
                    </div>
               
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="company_logo">Company Logo (jpeg, jpg, png)</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="company_logo" ref="company_logo" accept="image/x-png,image/jpeg" v-validate="'ext:jpg,jpeg,png'">
                        <span v-bind:class="{ 'error' : errors.has('company_logo') }">{{ errors.first('company_logo') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="company_logo">Current Company Logo</label>
                        <div class="d-block">
                            <img :src="company_logo" class="company-logo-image">
                            <span class="btn-label ml-3" v-show="company_logo_exists == true" @click="remove_company_logo()">Remove</span>
                        </div>
                    </div>
                 </div>
            </form>
                
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
                modal           : false,
                show_modal      : false,
                api_link        : '/api/update_setting_app',
phone:(this.setting_data.length == 0)?'':this.setting_data.phone,
email:(this.setting_data.length == 0)?'':this.setting_data.email,
address_ar:(this.setting_data.length == 0)?'':this.setting_data.address_ar,
address_en:(this.setting_data.length == 0)?'':this.setting_data.address_en,
about_ar:(this.setting_data.length == 0)?'':this.setting_data.about_ar,
about_en:(this.setting_data.length == 0)?'':this.setting_data.about_en,
twitter:(this.setting_data.length == 0)?'':this.setting_data.twitter,
insta:(this.setting_data.length == 0)?'':this.setting_data.insta,
                company_name    : (this.setting_data.length == 0)?'':this.setting_data.company_name,
                app_date_time_format     : (this.setting_data.length == 0)?'':this.setting_data.app_date_time_format,
                app_date_format     : (this.setting_data.length == 0)?'':this.setting_data.app_date_format,
                company_logo     : (this.setting_data.length == 0)?'-':this.setting_data.company_logo_path,
                company_logo_exists : (this.setting_data.length == 0)?false:((this.setting_data.company_logo != '')?true:false)
            }
        },
        props: {
            setting_data: [Array, Object],
            date_time_formats: Array,
            date_formats: Array,
        },
        mounted() {
            console.log('Edit App setting page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();
                            var file = this.$refs.company_logo.files[0];
                            
                            formData.append("access_token", window.settings.access_token);
                            formData.append("company_name", (this.company_name == null)?'':this.company_name);
                            formData.append("date_time_format", (this.app_date_time_format == null)?'':this.app_date_time_format);
                            formData.append("date_format", (this.app_date_format == null)?'':this.app_date_format);
                            formData.append("phone", (this.phone == null)?'':this.phone);
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("about_ar", (this.about_ar == null)?'':this.about_ar);
                            formData.append("about_en", (this.about_en == null)?'':this.about_en);
                            formData.append("address_en", (this.address_en == null)?'':this.address_en);
                            formData.append("address_ar", (this.address_ar == null)?'':this.address_ar);
                             formData.append("twitter", (this.twitter == null)?'':this.twitter);
                              formData.append("insta", (this.insta == null)?'':this.insta);
                            formData.append("company_logo", file);

                            axios.post(this.api_link, formData).then((response) => {
                                
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
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
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                            this.$off("submit");
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                            this.$off("close");
                        });
                    }
                });
            },

            remove_company_logo(){
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);

                axios.post('/api/remove_company_logo', formData).then((response) => {
                    this.processing = false;
                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.server_errors = this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>