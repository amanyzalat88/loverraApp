<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="tax_code_slack == ''">Add Tax Code</span>
                        <span class="text-title" v-else>Edit Tax Code</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="tax_code_label">Tax Code Name</label>
                        <input type="text" name="tax_code_label" v-model="tax_code_label" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter tax code name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('tax_code_label') }">{{ errors.first('tax_code_label') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code or HSN Code</label>
                        <input type="text" name="tax_code" v-model="tax_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" placeholder="Please enter tax code"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
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
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Tax Types</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                 <div class="form-row">
                    <div class="form-group col-md-3 mb-1">
                        <label for="tax_type">Tax Type</label>
                    </div>
                    <div class="form-group col-md-3 mb-1">
                        <label for="tax_percentage">Tax Percentage</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(tax_type_item, index) in tax_types" :key="index">
                    <div class="form-group col-md-3">
                        <input type="text" v-bind:name="'tax_type_item.tax_type_'+index" data-vv-as="Tax Type" v-model="tax_type_item.tax_type" v-validate="'required'" class="form-control form-control-custom" placeholder="Please enter tax type"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('tax_type_item.tax_type_'+index) }">{{ errors.first('tax_type_item.tax_type_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <input type="number" v-bind:name="'tax_type_item.tax_percentage_'+index" data-vv-as="Tax Percentage" v-model="tax_type_item.tax_percentage" v-validate="'required|decimal'" class="form-control form-control-custom" placeholder="Please enter tax percentage"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('tax_type_item.tax_percentage_'+index) }">{{ errors.first('tax_type_item.tax_percentage_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-3" v-if="tax_types.length>1">
                        <button type="button" class="btn btn-outline-danger" @click="remove_tax_type(index)"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-primary" @click="add_new_tax_type">Add More</button>
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If tax code is inactive all the products with this tax code will get affected.</p>
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
                api_link        : (this.tax_code_data == null)?'/api/add_tax_code':'/api/update_tax_code/'+this.tax_code_data.slack,

                tax_code_slack  : (this.tax_code_data == null)?'':this.tax_code_data.slack,
                tax_code_label  : (this.tax_code_data == null)?'':this.tax_code_data.label,
                tax_code        : (this.tax_code_data == null)?'':this.tax_code_data.tax_code,
                total_tax_percentage  : (this.tax_code_data == null)?'':this.tax_code_data.total_tax_percentage,
                status          : (this.tax_code_data == null)?'':(this.tax_code_data.status == null)?'':this.tax_code_data.status.value,
                description     : (this.tax_code_data == null)?'':this.tax_code_data.description,

                tax_code_type_data : (this.tax_code_data == null)?[]:(this.tax_code_data.tax_components == null)?[]:this.tax_code_data.tax_components,
                tax_types       : [
                    {
                        tax_type : '',
                        tax_percentage : ''
                    }
                ]
            }
        },
        props: {
            statuses: Array,
            tax_code_data: [Array, Object]
        },
        mounted() {
            console.log('Add Tax Code page loaded');
        },
        created(){
            this.update_tax_code_type();
        },
        methods: {
            add_new_tax_type(){
                this.tax_types.push({
                    tax_type: '',
                    tax_percentage: ''
                });
            },
            remove_tax_type(index){
                this.tax_types.splice(index, 1);
            },
            update_tax_code_type(){
                if(this.tax_code_type_data.length>0){
                    this.tax_types = [];
                    for(var i=0; i< this.tax_code_type_data.length; i++){
                        var tax_code_type_array = {
                            tax_type : this.tax_code_type_data[i].tax_type,
                            tax_percentage : this.tax_code_type_data[i].tax_percentage
                        }
                        this.tax_types.push(tax_code_type_array);
                    }
                }
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
                            formData.append("tax_code_name", (this.tax_code_label == null)?'':this.tax_code_label);
                            formData.append("tax_code", (this.tax_code == null)?'':this.tax_code);
                            formData.append("tax_percentage", null);
                            formData.append("description", (this.description == null)?'':this.description);
                            formData.append("status", (this.status== null)?'':this.status);
                            formData.append("tax_types", JSON.stringify(this.tax_types));

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