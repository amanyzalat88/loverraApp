<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="boxes_slack == ''">Add Boxes Color</span>
                        <span class="text-title" v-else>Edit Boxes Color</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <label for="name_ar">Name <span style="color:red">Ar</span></label>
                        <input type="text" name="name_ar" v-model="name_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter tilte ar"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name_ar') }">{{ errors.first('name_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name_en">Name <span style="color:red">En</span></label>
                        <input type="text" name="name_en" v-model="name_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter tilte en"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name_en') }">{{ errors.first('name_en') }}</span> 
                    </div>
                     
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="price">Price</label>
                        <input type="number" nim="1" name="price" v-model="price" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter description ar"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('price') }">{{ errors.first('price') }}</span> 
                    </div>
                  <div class="form-group col-md-3">
                        <label for="box_id">Boxes</label>
                        <select name="box_id" v-model="box_id" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Boxes..</option>
                            <option v-for="(box, index) in boxes" v-bind:value="box.value" v-bind:key="index">
                                {{ box.name_en }}
                            </option>
                           
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('box') }">{{ errors.first('box') }}</span> 
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
                        <label for="photo">Photo</label>
                        <input type="file" id="photo" ref="file" v-validate="'required|max:250'" v-on:change="handleFileUpload()" class="form-control form-control-custom" />
                        <span v-bind:class="{ 'error' : errors.has('photo') }">{{ errors.first('photo') }}</span>
                       
                    </div>
                      <div class="form-group col-md-3">
                          <img :src="getPhoto(photo)"  height="150px" width="250px"/> 
                      </div>
                    
                </div>

               
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If ads is inactive all the products with this ads will get affected.</p>
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
     let base_url= "/public/";
    export default {
        data(){
            return{
                server_errors   : "",
                error_class     : "",
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.boxes_data == null)?'/api/add_boxescolor':'/api/update_boxescolor/'+this.boxes_data.slack,
              
                boxes_slack       : (this.boxes_data == null)?"":this.boxes_data.slack,
                name_ar        : (this.boxes_data == null)?"":this.boxes_data.name_ar,
                name_en        : (this.boxes_data == null)?"":this.boxes_data.name_en,
                price  : (this.boxes_data == null)?"":this.boxes_data.price,
                box_id  : (this.boxes_data == null)?"":this.boxes_data.box_id,
             
                status          : (this.boxes_data == null)?"":(this.boxes_data.status == null)?'':this.boxes_data.status.value,
                photo           : (this.boxes_data == null)?'':(this.boxes_data.photo == null)?'':this.boxes_data.photo,
            }
        },
        props: {
            statuses: Array,
            boxes: Array,
            boxes_data: [Array, Object]
        },
        mounted() {
            console.log('Add boxes page loaded');
        },
        methods: {
              getPhoto(photo){
                
                if(photo)
                    return base_url+photo;
                    else
                    return base_url+"images/4.jpg";
                },
                handleFileUpload () {
                // get the input
                  this.file = this.$refs.file.files[0];
                },
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("name_ar", (this.name_ar == null)?'':this.name_ar);
                            formData.append("name_en", (this.name_en == null)?'':this.name_en);
                            formData.append("price", (this.price == null)?'':this.price);
                            formData.append("box_id", (this.box_id == null)?'':this.box_id);
                           
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append('photo', (this.file == null)?'':this.file);
                            axios.post(this.api_link, formData,{
                                            headers: {
                                                'Content-Type': 'multipart/form-data'
                                            }
                                             }).then((response) => {

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
            }
        }
    }
</script>