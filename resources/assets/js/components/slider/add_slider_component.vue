<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="slider_slack == ''">Add photo</span>
                        <span class="text-title" v-else>Edit Category</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>


                <div class="form-row mb-2">
                      <div class="form-group col-md-3">
                        <label for="photo_ar">Photo <span style="color:red">Ar</span></label>
                        <input type="file" id="photo_ar" ref="file_ar" v-validate="'required|max:250'" v-on:change="FileUpload()" class="form-control form-control-custom" />
                        <span v-bind:class="{ 'error' : errors.has('photo_ar') }">{{ errors.first('photo_ar') }}</span>
                       
                    </div>
                      <div class="form-group col-md-3">
                          <img :src="getPhoto(photo_ar)"  height="150px" width="250px"/> 
                      </div>
                      
                      </div>
                   <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="photo_en">Photo <span style="color:red">En</span></label>
                        <input type="file" id="photo_en" ref="file_en" v-validate="'required|max:250'" v-on:change="handleFileUpload()" class="form-control form-control-custom" />
                        <span v-bind:class="{ 'error' : errors.has('photo_en') }">{{ errors.first('photo_en') }}</span>
                       
                    </div>
                      <div class="form-group col-md-3">
                          <img :src="getPhoto(photo_en)"  height="150px" width="250px"/> 
                      </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If category is inactive all the products under this catgeory will get affected.</p>
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
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.slider_data == null)?'/api/add_slider':'/api/update_slider/'+this.slider_data.slack,

                slider_slack  : (this.slider_data == null)?'':this.slider_data.slack,
                photo_ar   : (this.slider_data == null)?'':this.slider_data.photo_ar,
                photo_en   : (this.slider_data == null)?'':this.slider_data.photo_en,
                
            }
        },
        props: {
            
            slider_data: [Array, Object]
        },
        mounted() {
            console.log('Add slider page loaded');
        },
        methods: {
            getPhoto(photo){
                if(photo)
            return base_url+photo;
            else
             return base_url+"images/4.jpg";
         },
           FileUpload(){
                this.file_ar = this.$refs.file_ar.files[0];
            },
             handleFileUpload(){
                this.file_en = this.$refs.file_en.files[0];
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
                            formData.append('photo_ar', (this.file_ar == null)?'':this.file_ar);
                            
                            formData.append('photo_en', (this.file_en == null)?'':this.file_en);
                            axios.post(this.api_link, formData,{
                                            headers: {
                                                'Content-Type': 'multipart/form-data'
                                            }
                                             }
                            ).then((response) => {
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