<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Boxes Card</span> {{ boxes.name_ar }}   </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="boxes.status.color">{{ boxes.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="name_ar">name Ar</label>
                    <p>{{ boxes.name_ar  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name_en">name En</label>
                    <p>{{ boxes.name_en }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="price">Price</label>
                    <p>{{ boxes.price }}</p>
                </div>
                 
                 <div class="form-group col-md-3">
                    <label for="photo">Photo</label>
                     <p><img :src="getPhoto(boxes.photo)"  height="150px" width="250px"/>  </p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (boxes.created_by == null)?'-':boxes.created_by['fullname']+' ('+boxes.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (boxes.updated_by == null)?'-':boxes.updated_by['fullname']+' ('+boxes.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ boxes.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ boxes.updated_at_label }}</p>
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
                boxes : this.boxes_data,
            }
        },
        props: {
            boxes_data: [Array, Object]
        },
        mounted() {
            console.log('boxes detail page loaded');
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