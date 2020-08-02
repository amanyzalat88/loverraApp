<template>
    <div class="row">
        <div class="col-md-12">
           
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="customer_slack == ''">Add Customer</span>
                        <span class="text-title" v-else>Edit Customer</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="mb-2">
                    <span class="text-subhead">Basic Information</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">Fullname</label>
                        <input type="text" name="name" v-model="name" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter fullname"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">Email</label>
                        <input type="text" name="email" v-model="email" v-validate="{ required: this.email_required, email: true, max: 150 }" class="form-control form-control-custom" placeholder="Please enter email"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">Contact No.</label>
                        <input type="text" name="phone" v-model="phone" v-validate="{ required: this.phone_required, min: 10, max: 15 }" class="form-control form-control-custom" placeholder="Please enter Contact Number" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span> 
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
               <div class="mb-2">
                    <span class="text-subhead">Address Details</span>

                </div>
                <div class="form-row mb-2">
                
                   
                    <div class="form-group col-md-3">
                            <label for="delivery">Delivery Area</label>
                              <input type="text" name="delivery" v-model="delivery" class="form-control form-control-custom" placeholder="Please enter Delivery Area" autocomplete="off">
                           
                            
                       </div>
                        <div class="form-group col-md-3">
                            <label for="building">Building  / Villa</label>
                              <input type="text" name="building"  v-model="building"   class="form-control form-control-custom" placeholder="Please enter Building" autocomplete="off">
                           
                          
                       </div>
                       <div class="form-group col-md-3">
                            <label for="street">Street</label>
                              <input type="text" name="street" v-model="street"    class="form-control form-control-custom" placeholder="Please enter Street" autocomplete="off">
                           
                           
                       </div>
                        <div class="form-group col-md-3">
                            <label for="flatnumber">Flat Number</label>
                              <input type="text" name="flatnumber" v-model="flatnumber"  class="form-control form-control-custom" placeholder="Please enter Flat Number" autocomplete="off">
                           
                       </div>
                        <div class="form-group col-md-3">
                            <label for="landmark">Nearest Landmark</label>
                              <input type="text" name="landmark" v-model="landmark"  class="form-control form-control-custom" placeholder="Please enter Landmark" autocomplete="off">
                           
                   </div>
                   <div class="form-group col-md-3">
                            <label for="city">City</label>
                        <select name="city" v-model="city" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose City..</option>
                             <option value="1">Cairo</option>
                        </select>
                           
                   </div>
                   <div class="form-group col-md-3">
                            <label for="country">Country  </label>
                        <select name="country" v-model="country" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Country..</option>
                             <option value="1">Egypt</option>
                        </select>
                           
                   </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">You are making the customer inactive.</p>
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
                api_link        : (this.customer_data == null)?'/api/add_customer':'/api/update_customer/'+this.customer_data.slack,

                customer_slack  : (this.customer_data == null)?'':this.customer_data.slack,
                email           : (this.customer_data == null)?'':this.customer_data.email,
                name            : (this.customer_data == null)?'':this.customer_data.name,
                phone           : (this.customer_data == null)?'':this.customer_data.phone,
                address:'',
                status          : (this.customer_data == null)?'':(this.customer_data.status == null)?'':this.customer_data.status.value,
                country         : (this.customer_data.address == null)?'':(this.customer_data.address.country == null)?'':this.customer_data.address.country,
                city:(this.customer_data.address == null)?'':(this.customer_data.address.city == null)?'':this.customer_data.address.city,
                delivery:(this.customer_data.address == null)?'':(this.customer_data.address.delivery_area == null)?'':this.customer_data.address.delivery_area,
                building:(this.customer_data.address == null)?'':(this.customer_data.address.building == null)?'':this.customer_data.address.building,
                street:(this.customer_data.address == null)?'':(this.customer_data.address.street == null)?'':this.customer_data.address.street,
                landmark:(this.customer_data.address == null)?'':(this.customer_data.address.landmark == null)?'':this.customer_data.address.landmark,
                flatnumber:(this.customer_data.address == null)?'':(this.customer_data.address.flatnumber == null)?'':this.customer_data.address.flatnumber,
                address_slack  : (this.customer_data.address == null)?'':(this.customer_data.address.slack == null)?'':this.customer_data.address.slack,
            }
        },
        props: {
            statuses: Array,
            customer_data: [Array, Object]
        },
        mounted() {
            console.log('Add customer page loaded');
        },
        computed: {
            email_required(){
                if(this.phone === '')
                    return true;
                return false;
            },
            phone_required(){
                if(this.email === '')
                    return true;
                return false;
            }
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
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("phone", (this.phone == null)?'':this.phone);
           
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("country", (this.country == null)?'':this.country);
                            formData.append("city", (this.city == null)?'':this.city);
                            formData.append("delivery", (this.delivery == null)?'':this.delivery);
                            formData.append("building", (this.building == null)?'':this.building);
                            formData.append("landmark", (this.landmark == null)?'':this.landmark);
                            formData.append("street", (this.street == null)?'':this.street);
                            formData.append("flatnumber", (this.flatnumber == null)?'':this.flatnumber);
                            formData.append("address_slack", (this.address_slack == null)?'':this.address_slack);
                            
                            axios.post(this.api_link, formData).then((response) => {
                                
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                }else{
                                    this.processing = false;
                                    this.show_modal = false;
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