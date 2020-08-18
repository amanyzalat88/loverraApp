<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="category_slack == ''">Add Category</span>
                        <span class="text-title" v-else>Edit Category</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="category_name_ar">Category Name <span style="color:red">Ar</span></label>
                        <input type="text" name="category_name_ar" v-model="category_name_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter category name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('category_name_ar') }">{{ errors.first('category_name_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category_name_en">Category Name <span style="color:red">En</span></label>
                        <input type="text" name="category_name_en" v-model="category_name_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter category name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('category_name_en') }">{{ errors.first('category_name_en') }}</span> 
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
                      <div class="form-group col-md-3">
                        <label for="parent">Parent</label>
                        <select name="parent" v-model="parent" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Parent..</option>
                            <option value="0"> -- Parent -- </option>
                            <option v-for="(parent, index) in categories" v-bind:value="parent.value" v-bind:key="index">
                               -- {{ parent.label_en }} --
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('parent') }">{{ errors.first('parent') }}</span> 
                    </div>
                     <div class="form-group col-md-3">
                        <label for="discount_code">Discount Code</label>
                        <select name="discount_code" v-model="discount_code" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Discount Code..</option>
                            <option v-for="(discount_code, index) in discount_codes" v-bind:value="discount_code.slack" v-bind:key="index">
                                {{ discount_code.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description_ar">Description <span style="color:red">Ar</span></label>
                        <textarea name="description_ar" v-model="description_ar" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter description"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description_ar') }">{{ errors.first('description_ar') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="description_en">Description <span style="color:red">En</span></label>
                        <textarea name="description_en" v-model="description_en" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" placeholder="Enter description"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description_en') }">{{ errors.first('description_en') }}</span>
                    </div>
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
                api_link        : (this.category_data == null)?'/api/add_category':'/api/update_category/'+this.category_data.slack,
                discount_code   : (this.category_data == null)?'':(this.category_data.discount_code == null)?'':this.category_data.discount_code.slack,
                category_slack  : (this.category_data == null)?'':this.category_data.slack,
                category_name_ar   : (this.category_data == null)?'':this.category_data.label_ar,
                category_name_en   : (this.category_data == null)?'':this.category_data.label_en,
                description_ar     : (this.category_data == null)?'':this.category_data.description_ar,
                description_en     : (this.category_data == null)?'':this.category_data.description_en,
                status          : (this.category_data == null)?'':(this.category_data.status == null)?'':this.category_data.status.value,
                parent          : (this.category_data == null)?'':(this.category_data.parent == null)?'':this.category_data.parent,
                photo           : (this.category_data == null)?'':(this.category_data.photo == null)?'':this.category_data.photo,
            }
        },
        props: {
            statuses: Array,
            categories: Array,
            discount_codes: Array,
            category_data: [Array, Object]
        },
        mounted() {
            console.log('Add category page loaded');
        },
        methods: {
            getPhoto(photo){
                if(photo)
            return base_url+photo;
            else
             return base_url+"images/4.jpg";
         },
            handleFileUpload(){
                this.file = this.$refs.file.files[0];
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
                             formData.append("discount_code", (this.discount_code == null)?'':this.discount_code);
                            formData.append("access_token", window.settings.access_token);
                            formData.append("category_name_en", (this.category_name_en == null)?'':this.category_name_en);
                            formData.append("description_en", (this.description_en == null)?'':this.description_en);
                            formData.append("category_name_ar", (this.category_name_ar == null)?'':this.category_name_ar);
                            formData.append("description_ar", (this.description_ar == null)?'':this.description_ar);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("parent", (this.parent == null)?'':this.parent);
                            formData.append('photo', (this.file == null)?'':this.file);
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