<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="payment_method_slack == ''">Add Payment Method</span>
                        <span class="text-title" v-else>Edit Payment Method</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="payment_method_ar">Payment Method <span style="color:red">Ar</span></label>
                        <input type="text" name="payment_method_ar" v-model="payment_method_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter payment method"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('payment_method_ar') }">{{ errors.first('payment_method_ar') }}</span> 
                    </div>
                     <div class="form-group col-md-3">
                        <label for="payment_method_en">Payment Method <span style="color:red">En</span></label>
                        <input type="text" name="payment_method_en" v-model="payment_method_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter payment method"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('payment_method_en') }">{{ errors.first('payment_method_en') }}</span> 
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
                        <label for="description">Description</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter description"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description') }">{{ errors.first('description') }}</span>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">You are making the payment method inactive.</p>
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
                api_link        : (this.payment_method_data == null)?'/api/add_payment_method':'/api/update_payment_method/'+this.payment_method_data.slack,

                payment_method_slack  : (this.payment_method_data == null)?'':this.payment_method_data.slack,
                payment_method_en   : (this.payment_method_data == null)?'':this.payment_method_data.label_en,
                payment_method_ar   : (this.payment_method_data == null)?'':this.payment_method_data.label_ar,
                description     : (this.payment_method_data == null)?'':this.payment_method_data.description,
                status          : (this.payment_method_data == null)?'':this.payment_method_data.status.value,
            }
        },
        props: {
            statuses: Array,
            payment_method_data: [Array, Object]
        },
        mounted() {
            console.log('Add payment method page loaded');
        },
        methods: {
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
                            formData.append("payment_method_name_en", (this.payment_method == null)?'':this.payment_method_en);
                            formData.append("payment_method_name_ar", (this.payment_method == null)?'':this.payment_method_ar);
                            formData.append("description", (this.description == null)?'':this.description);
                            formData.append("status", (this.status == null)?'':this.status);

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