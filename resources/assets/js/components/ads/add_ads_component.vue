<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="ads_slack == ''">Add Ads</span>
                        <span class="text-title" v-else>Edit Ads</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <label for="title_ar">Title <span style="color:red">Ar</span></label>
                        <input type="text" name="title_ar" v-model="title_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter tilte ar"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('title_ar') }">{{ errors.first('title_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-6">
                        <label for="title_en">Title <span style="color:red">En</span></label>
                        <input type="text" name="title_en" v-model="title_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter tilte en"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('title_en') }">{{ errors.first('title_en') }}</span> 
                    </div>
                     
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description_ar">Description <span style="color:red">Ar</span></label>
                        <input type="text" name="description_ar" v-model="description_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter description ar"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('description_ar') }">{{ errors.first('description_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="description_en">Description <span style="color:red">En</span></label>
                        <input type="text" name="description_en" v-model="description_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter description en"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('description_en') }">{{ errors.first('description_en') }}</span> 
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
                        <label for="category">Category</label>
                        <select name="category" v-model="category_id" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Category..</option>
                            <option v-for="(category, index) in categories" v-bind:value="category.value" v-bind:key="index">
                                {{ category.label_en }}
                            </option>
                           
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('category') }">{{ errors.first('category') }}</span> 
                    </div>-->
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
                api_link        : (this.ads_data == null)?'/api/add_ads':'/api/update_ads/'+this.ads_data.slack,

                ads_slack       : (this.ads_data == null)?"":this.ads_data.slack,
                title_ar        : (this.ads_data == null)?"":this.ads_data.title_ar,
                title_en        : (this.ads_data == null)?"":this.ads_data.title_en,
                description_ar  : (this.ads_data == null)?"":this.ads_data.description_ar,
                description_en  : (this.ads_data == null)?"":this.ads_data.description_en,
                category_id     : (this.ads_data == null)?"":this.ads_data.category_id,
                status          : (this.ads_data == null)?"":(this.ads_data.status == null)?'':this.ads_data.status.value,
                photo           : (this.ads_data == null)?'':(this.ads_data.photo == null)?'':this.ads_data.photo,
            }
        },
        props: {
            statuses: Array,
            categories: Array,
            ads_data: [Array, Object]
        },
        mounted() {
            console.log('Add ads page loaded');
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
                            formData.append("title_ar", (this.title_ar == null)?'':this.title_ar);
                            formData.append("title_en", (this.title_en == null)?'':this.title_en);
                            formData.append("description_ar", (this.description_ar == null)?'':this.description_ar);
                            formData.append("description_en", (this.description_en == null)?'':this.description_en);
                            formData.append("category", (this.category == null)?'':this.category);
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