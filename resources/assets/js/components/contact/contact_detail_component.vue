<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Contact Us</span>     </span>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="name">name</label>
                    <p>{{ contact.name  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">phone</label>
                    <p>{{ contact.phone }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="description_ar">email</label>
                    <p>{{ contact.email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="description_en">message</label>
                    <p>{{ contact.message }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="catrgory_id">photo</label>
                    <p><img :src="getPhoto(contact.photo)"  height="150px" width="250px"/>  </p>
                </div>
                 
            </div>

           

        </div>
    </div>
</template>  

<script>
    'use strict';
     let base_url= "/public/";
    export default {
        data(){
            return{
                contact : this.contact_data,
            }
        },
        props: {
           contact_data: [Array, Object]
        },
        mounted() {
            console.log('contact detail page loaded');
        },
        methods: {
              getPhoto(photo){
                
                if(photo)
                    return base_url+photo;
                    else
                    return base_url+"images/4.jpg";
                },
        }
    }
</script>