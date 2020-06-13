<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">Generate Barcode</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Generate Barcodes</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <label for="product_code">Product Codes (csv of product codes)</label>
                        <textarea name="product_code" v-model="product_code" v-validate="'required|max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter product codes separated by comma"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('product_code') }">{{ errors.first('product_code') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="no_of_barcodes">No of Barcodes per Product</label>
                        <input type="number" name="no_of_barcodes" v-model="no_of_barcodes" v-validate="'required|integer|min_value:1|max_value:200'" class="form-control form-control-custom" placeholder="Please enter no. of barcodes per product"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('no_of_barcodes') }">{{ errors.first('no_of_barcodes') }}</span> 
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
                api_link        : '/api/generate_barcodes',

                product_code    : '',
                no_of_barcodes  : '',
            }
        },
        mounted() {
            console.log('Generate barcode page loaded');
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
                            formData.append("product_code", this.product_code);
                            formData.append("no_of_barcodes", this.no_of_barcodes);

                            axios.post(this.api_link, formData).then((response) => {
                                
                                this.show_modal = false;
                                this.processing = false;

                                if(response.data.status_code == 200) {
                                    if(response.data.link != ""){
                                        window.open(response.data.link, '_blank');
                                    }else{
                                        location.reload();
                                    }
                                }else{
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                    this.show_modal = false;
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