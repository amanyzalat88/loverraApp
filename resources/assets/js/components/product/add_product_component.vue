<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="product_slack == ''">Add Product</span>
                        <span class="text-title" v-else>Edit Product</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Save</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Product Information</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name_ar">Name <span style="color:red">Ar</span></label>
                        <input type="text" name="name_ar" v-model="product_name_ar" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter product name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name_ar') }">{{ errors.first('name_ar') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="name_en">Name <span style="color:red">En</span></label>
                        <input type="text" name="name_en" v-model="product_name_en" v-validate="'required|max:250'" class="form-control form-control-custom" placeholder="Please enter product name"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name_en') }">{{ errors.first('name_en') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="product_code">Product Code</label>
                        <input type="text" name="product_code" v-model="product_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" placeholder="Please enter product code"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('product_code') }">{{ errors.first('product_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier">Supplier</label>
                        <select name="supplier" v-model="supplier" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Supplier..</option>
                            <option v-for="(supplier, index) in suppliers" v-bind:value="supplier.slack" v-bind:key="index">
                               {{ supplier.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('supplier') }">{{ errors.first('supplier') }}</span> 
                    </div>
                   
                </div>

                <div class="form-row mb-2">
                 <div class="form-group col-md-3">
                        <label for="category">Category</label>
                        <select name="category" v-model="category" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Category..</option>
                            <option v-for="(category, index) in categories" v-bind:value="category.slack" v-bind:key="index">
                                {{ category.label_en }}
                            </option>
                           
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('category') }">{{ errors.first('category') }}</span> 
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
                <hr>
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Price, Quantity and Tax Information</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="purchase_price">Purchase Price  </label>
                        <input type="number" name='purchase_price' v-model="purchase_price" v-validate="'required|decimal'" class="form-control form-control-custom" placeholder="Please enter purchase price excluding tax"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('purchase_price') }">{{ errors.first('purchase_price') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="sale_price">Sale Price </label>
                        <input type="number" name='sale_price' v-model="sale_price" v-validate="'decimal'" class="form-control form-control-custom" placeholder="Please enter sale price excluding tax"  autocomplete="off" step="0.01" min="0">
                       
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Quantity</label>
                        <input type="number" name='quantity' v-model="quantity" v-validate="'required'" class="form-control form-control-custom" placeholder="Please enter quantity"  autocomplete="off" step="1" min="0">
                        <span v-bind:class="{ 'error' : errors.has('quantity') }">{{ errors.first('quantity') }}</span> 
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="tax_code">Tax Code</label>
                        <select name="tax_code" v-model="tax_code"  class="form-control form-control-custom custom-select">
                            <option value="">Choose Tax Code..</option>
                            <option v-for="(taxcode, index) in taxcodes" v-bind:value="taxcode.slack" v-bind:key="index">
                                {{ taxcode.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
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
                    <div class="form-group col-md-3">
                        <label for="soldout">Sold out</label>
                        <input type="checkbox" name="soldout" v-model="soldout"   true-value="1" false-value="0"  class="form-control form-control-custom" />
                        <span v-bind:class="{ 'error' : errors.has('soldout') }">{{ errors.first('soldout') }}</span>
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
                        <input type="file" id="photo"  ref="file"  v-on:change="handleFileUpload()" class="form-control form-control-custom" />
                         
                       
                    </div>
                      <div class="form-group col-md-3">
                          <img :src="getPhoto(photo)"  height="150px" width="250px"/> 
                      </div>
                </div>
                <hr>
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">Add Multi Photo</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="photos">Choose Photos</label>
                        <input type="file"  id="photos" multiple ref="file_input" @change="uploadFiles">

                       
                    </div>
                     
                    
                </div>
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">Product status is inactive.</p>
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
                api_link        : (this.product_data == null)?'/api/add_product':'/api/update_product/'+this.product_data.slack,

                product_slack   : (this.product_data == null)?'':this.product_data.slack,
                product_name_ar    : (this.product_data == null)?'':this.product_data.name_ar,
                product_name_en    : (this.product_data == null)?'':this.product_data.name_en,
                product_code    : (this.product_data == null)?'':this.product_data.product_code,
                description_ar    : (this.product_data == null)?'':this.product_data.description_ar,
                description_en    : (this.product_data == null)?'':this.product_data.description_en,
                supplier        : (this.product_data == null)?'':(this.product_data.supplier == null)?'':this.product_data.supplier.slack,
                category        : (this.product_data == null)?'':(this.product_data.category == null)?'':this.product_data.category.slack,
                tax_code        : (this.product_data == null)?'':(this.product_data.tax_code == null)?'':this.product_data.tax_code.slack,
                discount_code   : (this.product_data == null)?'':(this.product_data.discount_code == null)?'':this.product_data.discount_code.slack,
                quantity        : (this.product_data == null)?'':this.product_data.quantity,
                sale_price      : (this.product_data == null)?'':this.product_data.sale_amount_excluding_tax,
                purchase_price  : (this.product_data == null)?'':this.product_data.purchase_amount_excluding_tax,    
                status          : (this.product_data == null)?'':this.product_data.status.value,
                soldout         :(this.product_data == null)?0:this.product_data.soldout,
                photo           : (this.product_data == null)?'':(this.product_data.photo == null)?'':this.product_data.photo,
            }
        },
        props: {
            statuses: Array,
            suppliers: Array,
            categories: Array,
            taxcodes: Array,
            discount_codes: Array,
            product_data: [Array, Object]
        },
        mounted() {
            console.log('Add product page loaded');
        },
        methods: {
             getPhoto(photo){
          if(photo)
            return base_url+photo;
            else
             return base_url+"images/4.jpg";
         },
         uploadFiles () {
        // get the input
        this.files = this.$refs.file_input.files
            //console.log(this.files);
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

                            formData.append("access_token", window.settings.access_token);
                            formData.append("product_name_ar", (this.product_name_ar == null)?'':this.product_name_ar);
                            formData.append("product_name_en", (this.product_name_en == null)?'':this.product_name_en);
                            formData.append("product_code", (this.product_code == null)?'':this.product_code);
                            formData.append("supplier", (this.supplier == null)?'':this.supplier);
                            formData.append("category", (this.category == null)?'':this.category);
                            formData.append("tax_code", (this.tax_code == null)?'':this.tax_code);
                            formData.append("discount_code", (this.discount_code == null)?'':this.discount_code);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("quantity", (this.quantity == null)?'':this.quantity);
                            formData.append("sale_price", (this.sale_price == null)?'':this.sale_price);
                            formData.append("purchase_price", (this.purchase_price == null)?'':this.purchase_price);
                            formData.append("description_en", (this.description_en == null)?'':this.description_en);
                            formData.append("description_ar", (this.description_ar == null)?'':this.description_ar);
                            formData.append("soldout", (this.soldout == null)?'':this.soldout);
                            formData.append('photo', (this.file == null)?'':this.file);
                            if(this.files!=null){
                                for (let i = 0; i < this.files.length; i++) {
                                    formData.append('photos[]', this.files[i])
                                    console.log(this.files[i]);
                                }
                            }
                           
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