<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>Category</span> {{ category.label }} ({{ category.category_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="category.status.color">{{ category.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">Basic Information</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="category_code">category Code</label>
                    <p>{{ category.category_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">Name</label>
                    <p>{{ category.label_en }}</p>
                </div>
                 <div class="form-group col-md-3">
                    <label for="label">الاسم</label>
                    <p>{{ category.label_ar }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">Created By</label>
                    <p>{{ (category.created_by == null)?'-':category.created_by['fullname']+' ('+category.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">Updated By</label>
                    <p>{{ (category.updated_by == null)?'-':category.updated_by['fullname']+' ('+category.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">Created On</label>
                    <p>{{ category.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">Updated On</label>
                    <p>{{ category.updated_at_label }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">Description Ar</label>
                    <p>{{ category.description_ar }}</p>
                </div>
                 <div class="form-group col-md-6">
                    <label for="description">Description En</label>
                    <p>{{ category.description_en }}</p>
                </div>
            </div>

        </div>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                category : this.category_data,
            }
        },
        props: {
            category_data: [Array, Object]
        },
        mounted() {
            console.log('Category detail page loaded');
        },
        methods: {
           
        }
    }
</script>