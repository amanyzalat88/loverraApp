<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">

                    <div class="modal-header">
                        <span>
                            <slot name="modal-header">
                            Confirm
                            </slot>
                        </span>
                    </div>

                    <div class="modal-body">
                        <slot name="modal-body">
                        Are you sure you want to proceed?
                        </slot>
                    </div>

                    <div class="modal-footer">
                        <slot name="modal-footer">
                            <button type="button" class="btn btn-light" @click="$emit('closemodal')">Cancel</button>
                        </slot>
                    </div>

                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    'use strict';
    
    export default {
        
    }
</script>
