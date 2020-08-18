<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Ads</span> {{ ads.title_ar }}   </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="ads.status.color">{{ ads.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="title_ar">title Ar</label>
                    <p>{{ ads.title_ar  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="title_en">title En</label>
                    <p>{{ ads.title_en }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="description_ar">Description ar</label>
                    <p>{{ ads.description_ar }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="description_en">Description en</label>
                    <p>{{ ads.description_en }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="catrgory_id">Catrgory Name</label>
                    <p>{{ ads.catrgory_id }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="photo">Photo</label>
                     <p><img :src="getPhoto(ads.photo)"  height="150px" width="250px"/>  </p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (ads.created_by == null)?'-':ads.created_by['fullname']+' ('+ads.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (ads.updated_by == null)?'-':ads.updated_by['fullname']+' ('+ads.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ ads.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ ads.updated_at_label }}</p>
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
                ads : this.ads_data,
            }
        },
        props: {
            ads_data: [Array, Object]
        },
        mounted() {
            console.log('ads detail page loaded');
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