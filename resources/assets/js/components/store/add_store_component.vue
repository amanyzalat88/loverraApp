<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="store_slack == ''">Add Store</span>
                        <span class="text-title" v-else>Edit Store</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">Name</label>
                        <input type="text" name="name" v-model="name" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter store name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="store_code">Store Code</label>
                        <input type="text" name="store_code" v-model="store_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" placeholder="Please enter store code"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('store_code') }">{{ errors.first('store_code') }}</span> 
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
                  <!--  <div class="form-group col-md-3">
                        <label for="tax_number">Tax Number or GST number</label>
                        <input type="text" name="tax_number" v-model="tax_number" v-validate="'max:50'" class="form-control form-control-custom" placeholder="Please enter tax number or GST number"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('tax_number') }">{{ errors.first('tax_number') }}</span> 
                    </div>-->
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Shipping Information</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                <div class="form-group col-md-3">
                        <label for="shipping">Shipping fees</label>
                        <input type="text" name="shipping" v-model="shipping" v-validate="required" class="form-control form-control-custom" placeholder="Please enter Shipping" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('primary_contact') }">{{ errors.first('primary_contact') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="free_shipping">Free Shipping</label>
                        <input type="text" name="free_shipping" v-model="free_shipping" v-validate="required" class="form-control form-control-custom" placeholder="Please enter Free Shipping" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('free_shipping') }">{{ errors.first('free_shipping') }}</span> 
                    </div>
                  <!--  <div class="form-group col-md-3">
                        <label for="primary_contact">Primary Contact No.</label>
                        <input type="text" name="primary_contact" v-model="primary_contact" v-validate="'min:10|max:15'" class="form-control form-control-custom" placeholder="Please enter primary contact number" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('primary_contact') }">{{ errors.first('primary_contact') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">Secondary Contact No.</label>
                        <input type="text" name="secondary_contact" v-model="secondary_contact" v-validate="'min:10|max:15'" class="form-control form-control-custom" placeholder="Please enter secondary contact number" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('secondary_contact') }">{{ errors.first('secondary_contact') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="primary_contact">Primary Email</label>
                        <input type="text" name="primary_email" v-model="primary_email" v-validate="'email'" class="form-control form-control-custom" placeholder="Please enter primary email" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('primary_email') }">{{ errors.first('primary_email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">Secondary Email</label>
                        <input type="text" name="secondary_email" v-model="secondary_email" v-validate="'email'" class="form-control form-control-custom" placeholder="Please enter secondary email" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('secondary_email') }">{{ errors.first('secondary_email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address">Address</label>
                        <textarea name="address" v-model="address" v-validate="'required|max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter store address"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('address') }">{{ errors.first('address') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pincode">Pincode</label>
                        <input type="text" name="pincode" v-model="pincode" v-validate="'max:15'" class="form-control form-control-custom" placeholder="Enter Pincode">
                        <span v-bind:class="{ 'error' : errors.has('pincode') }">{{ errors.first('pincode') }}</span>
                    </div>-->
                </div>

                <div v-if="store_slack != ''">
                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">Store wise Tax & Discount Information</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="form-group col-md-3">
                            <label for="tax_code">Tax Code</label>
                            <select name="tax_code" v-model="tax_code" v-validate="''" class="form-control form-control-custom custom-select">
                                <option value="">Choose Tax Code..</option>
                                <option v-for="(tax_code, index) in tax_codes" v-bind:value="tax_code.slack" v-bind:key="index">
                                    {{ tax_code.tax_code+' - '+tax_code.label }}
                                </option>
                            </select>
                            <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
                        </div>
                        <div class="form-group col-md-3">
                            <label for="discount_code">Discount Code</label>
                            <select name="discount_code" v-model="discount_code" v-validate="''" class="form-control form-control-custom custom-select">
                                <option value="">Choose Tax Code..</option>
                                <option v-for="(discount_code, index) in discount_codes" v-bind:value="discount_code.slack" v-bind:key="index">
                                    {{ discount_code.discount_code+' - '+discount_code.label }}
                                </option>
                            </select>
                            <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                        </div>
                    </div>
                </div>

               <!--    <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Status Information</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                  
                </div>

             <div class="form-row mb-2">
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Invoice Print & Currency Details</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                    <div class="form-group col-md-3">
                        <label for="print_type">Invoice Print Type</label>
                        <select name="print_type" v-model="print_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Invoice Print Type..</option>
                            <option v-for="(invoice_print_type, index) in invoice_print_types" v-bind:value="invoice_print_type.print_type_value" v-bind:key="index">
                                {{ invoice_print_type.print_type_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('print_type') }">{{ errors.first('print_type') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="currency_code">Currency</label>
                        <select name="currency_code" v-model="currency_code" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Currency..</option>
                            <option v-for="(currency_item, index) in currency_list" v-bind:value="currency_item.currency_code" v-bind:key="index">
                                {{ currency_item.currency_code }} - {{ currency_item.currency_name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('currency_code') }">{{ errors.first('currency_code') }}</span> 
                    </div>
                </div>-->
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">You are making the store inactive.</p>
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
                api_link        : (this.store_data == null)?'/api/add_store':'/api/update_store/'+this.store_data.slack,

                store_slack     : (this.store_data == null)?'':this.store_data.slack,
                name            : (this.store_data == null)?'':this.store_data.name,
                store_code      : (this.store_data == null)?'':this.store_data.store_code,
                tax_number      : (this.store_data == null)?'':this.store_data.tax_number,
                primary_contact : (this.store_data == null)?'':this.store_data.primary_contact,
                secondary_contact : (this.store_data == null)?'':this.store_data.secondary_contact,
                primary_email   : (this.store_data === null)?'':this.store_data.primary_email,
                secondary_email : (this.store_data == null)?'':this.store_data.secondary_email,
                address         : (this.store_data == null)?'':this.store_data.address,
                pincode         : (this.store_data == null)?'':this.store_data.pincode,
                tax_code        : (this.store_data == null)?'':(this.store_data.tax_code == null)?'':this.store_data.tax_code.slack,
                discount_code   : (this.store_data == null)?'':(this.store_data.discount_code == null)?'':this.store_data.discount_code.slack,
                print_type      : (this.store_data == null)?'':(this.store_data.invoice_type == null)?'':this.store_data.invoice_type.print_type_value,
                currency_code   : (this.store_data == null)?'':(this.store_data.currency_code == null)?'':this.store_data.currency_code,
                status          : (this.store_data == null)?'':this.store_data.status.value,
                shipping        : (this.store_data == null)?'':this.store_data.shipping,
                free_shipping   : (this.store_data == null)?'':this.store_data.free_shipping,
            }
        },
        props: {
            statuses: Array,
            tax_codes: Array,
            discount_codes: Array,
            invoice_print_types: Array,
            currency_list: Array,
            store_data: [Array, Object]
        },
        mounted() {
            console.log('Add store page loaded');
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
                            formData.append("name", (this.name == null)?'':this.name);
                            formData.append("store_code", (this.store_code == null)?'':this.store_code);
                            formData.append("tax_number", (this.tax_number == null)?'':this.tax_number);
                            formData.append("primary_contact", (this.primary_contact == null)?'':this.primary_contact);
                            formData.append("secondary_contact", (this.secondary_contact == null)?'':this.secondary_contact);
                            formData.append("primary_email", (this.primary_email == null)?'':this.primary_email);
                            formData.append("secondary_email", (this.secondary_email == null)?'':this.secondary_email);
                            formData.append("address", (this.address == null)?'':this.address);
                            formData.append("pincode", (this.pincode == null)?'':this.pincode);
                            formData.append("tax_code", (this.tax_code == null)?'':this.tax_code);
                            formData.append("discount_code", (this.discount_code == null)?'':this.discount_code);
                            formData.append("invoice_type", (this.print_type == null)?'':this.print_type);
                            formData.append("currency_code", (this.currency_code == null)?'':this.currency_code);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("free_shipping", (this.free_shipping == null)?'':this.free_shipping);
                            formData.append("shipping", (this.shipping == null)?'':this.shipping);

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