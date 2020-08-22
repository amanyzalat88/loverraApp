<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="discount_code_slack == ''">Add Discount Code</span>
                        <span class="text-title" v-else>Edit Discount Code</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="discount_name">Discount Name</label>
                        <input type="text" name="discount_name" v-model="discount_name" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter discount name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('discount_name') }">{{ errors.first('discount_name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <input type="text" name="discount_code" v-model="discount_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" placeholder="Please enter discount code"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">Discount Percentage</label>
                        <input type="number" name='discount_percentage' v-model="discount_percentage" v-validate="'required|decimal'" class="form-control form-control-custom" placeholder="Please enter discount percentage"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('discount_percentage') }">{{ errors.first('discount_percentage') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('status') }">{{ errors.first('status') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description">Desctiption</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter description"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description') }">{{ errors.first('description') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_type">Discount Type</label>
                        <select name="discount_type"    v-model="discount_type"  v-validate="'required'"  class="form-control form-control-custom">
                            <option value="" disabled >Choose Discount Type..</option>
                            <option value="1">Product</option>
                            <option value="2">Category</option>
                            <option value="3">Invoice</option>
                        </select>
                      
                        <span v-bind:class="{ 'error' : errors.has('discount_type') }">{{ errors.first('discount_type') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_num">Discount Number</label>
                        <input type="number" name="discount_num" min="1" v-model="discount_num" v-validate="'required'" class="form-control form-control-custom" placeholder="Please enter discount code"  autocomplete="off">
                       
                    </div>
                    </div>
                    <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="discount_from">Discount From</label>
                         <date-picker :lang='date.lang' :format="date.format"  v-model="discount_from"  input-class="form-control bg-white" placeholder="Select Discount From Date"></date-picker>
                       
                      
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_to">Discount To</label>
                        <date-picker :lang='date.lang' :format="date.format"  v-model="discount_to"  input-class="form-control bg-white" placeholder="Select Discount To Date"></date-picker>
                       
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
    import DatePicker from 'vue2-datepicker';
     import moment from "moment";
    export default {
        data(){
            return{
                  date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.discount_code_data == null)?'/api/add_discount_code':'/api/update_discount_code/'+this.discount_code_data.slack,

                discount_code_slack  : (this.discount_code_data == null)?'':this.discount_code_data.slack,
                discount_name        : (this.discount_code_data == null)?'':this.discount_code_data.label,
                discount_code        : (this.discount_code_data == null)?'':this.discount_code_data.discount_code,
                discount_percentage  : (this.discount_code_data == null)?'':this.discount_code_data.discount_percentage,
                status               : (this.discount_code_data == null)?'':(this.discount_code_data.status == null)?'':this.discount_code_data.status.value,
                description          : (this.discount_code_data == null)?[]:this.discount_code_data.description,
                discount_type  : (this.discount_code_data == null)?'':this.discount_code_data.discount_type,
                discount_num  : (this.discount_code_data == null)?'':this.discount_code_data.discount_num,
                discount_from  : (this.discount_code_data == null)?'':this.discount_code_data.discount_from,
                discount_to  : (this.discount_code_data == null)?'':this.discount_code_data.discount_to,
            }
        },
        components: {
            DatePicker
        },
        props: {
            statuses: Array,
            discount_code_data: [Array, Object],
        },
        mounted() {
            console.log('Add Discount Code page loaded');
        },
        methods: {
             convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
                
            },
            submit_form(){

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("label", (this.discount_name == null)?'':this.discount_name);
                            formData.append("discount_code", ( this.discount_code == null)?'':this.discount_code);
                            formData.append("discount_percentage", (this.discount_percentage == null)?'':this.discount_percentage);
                            formData.append("description", (this.description == null)?'': this.description);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("discount_type", (this.discount_type == null)?'':this.discount_type);
                            formData.append("discount_num", (this.discount_num == null)?'':this.discount_num);
                            formData.append("discount_from", (this.discount_from == null)?'':this.convert_date_format(this.discount_from));
                            formData.append("discount_to", (this.discount_to == null)?'':this.convert_date_format(this.discount_to));

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
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            }
        }
    }
</script>