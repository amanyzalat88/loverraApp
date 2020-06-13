
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

"use strict";

require('./bootstrap');

window.Vue = require('vue');

import VeeValidate, { Validator } from 'vee-validate';

import {mixin} from './mixin';

import {event_bus} from './event_bus';

import { dictionary } from './validation_custom_message';

import Notifications from 'vue-notification';

Validator.localize('en', dictionary.en);

Vue.use(VeeValidate);

Vue.use(Notifications);

Vue.mixin(mixin);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
*/

//commons
Vue.component('modalcomponent', require('./components/commons/modal_component.vue'));
Vue.component('storeselectorcomponent', require('./components/commons/store_selector_component.vue'));
Vue.component('searchcomponent', require('./components/search/search_component.vue'));

Vue.component('signincomponent', require('./components/entry/sign_in_component.vue'));
Vue.component('forgotpasswordcomponent', require('./components/entry/forgot_password_component.vue'));
Vue.component('resetpasswordcomponent', require('./components/entry/reset_password_component.vue'));

Vue.component('dashboardcomponent', require('./components/dashboard/dashboard_component.vue'));

Vue.component('addusercomponent', require('./components/user/add_user_component.vue'));
Vue.component('userdetailcomponent', require('./components/user/user_detail_component.vue'));

Vue.component('profilecomponent', require('./components/user/profile_component.vue'));
Vue.component('editprofilecomponent', require('./components/user/edit_profile_component.vue'));

Vue.component('addrolecomponent', require('./components/role/add_role_component.vue'));
Vue.component('roledetailcomponent', require('./components/role/role_detail_component.vue'));

Vue.component('addcustomercomponent', require('./components/customer/add_customer_component.vue'));
Vue.component('customerdetailcomponent', require('./components/customer/customer_detail_component.vue'));

Vue.component('addcategorycomponent', require('./components/category/add_category_component.vue'));
Vue.component('categorydetailcomponent', require('./components/category/category_detail_component.vue'));

Vue.component('addproductcomponent', require('./components/product/add_product_component.vue'));
Vue.component('productdetailcomponent', require('./components/product/product_detail_component.vue'));
Vue.component('productbarcodecomponent', require('./components/product/product_barcode_component.vue'));

Vue.component('addsuppliercomponent', require('./components/supplier/add_supplier_component.vue'));
Vue.component('supplierdetailcomponent', require('./components/supplier/supplier_detail_component.vue'));

Vue.component('addtaxcodecomponent', require('./components/tax_code/add_tax_code_component.vue'));
Vue.component('taxcodedetailcomponent', require('./components/tax_code/tax_code_detail_component.vue'));

Vue.component('addordercomponent', require('./components/order/add_order_component.vue'));
Vue.component('orderdetailcomponent', require('./components/order/order_detail_component.vue'));

Vue.component('addstorecomponent', require('./components/store/add_store_component.vue'));
Vue.component('selectstorecomponent', require('./components/store/select_store_component.vue'));
Vue.component('storedetailcomponent', require('./components/store/store_detail_component.vue'));

Vue.component('adddiscountcodecomponent', require('./components/discount_code/add_discount_code_component.vue'));
Vue.component('discountcodedetailcomponent', require('./components/discount_code/discount_code_detail_component.vue'));

Vue.component('importcomponent', require('./components/import/import_component.vue'));
Vue.component('updatedatacomponent', require('./components/import/update_data_component.vue'));

Vue.component('addpaymentmethodcomponent', require('./components/payment_method/add_payment_method_component.vue'));
Vue.component('paymentmethoddetailcomponent', require('./components/payment_method/payment_method_detail_component.vue'));

Vue.component('reportcomponent', require('./components/report/report_component.vue'));

Vue.component('addpurchaseordercomponent', require('./components/purchase_order/add_purchase_order_component.vue'));
Vue.component('purchaseorderdetailcomponent', require('./components/purchase_order/purchase_order_detail_component.vue'));

Vue.component('emailsettingcomponent', require('./components/setting/email/email_setting_component.vue'));
Vue.component('editemailsettingcomponent', require('./components/setting/email/edit_email_setting_component.vue'));
Vue.component('appsettingcomponent', require('./components/setting/app/app_setting_component.vue'));
Vue.component('editappsettingcomponent', require('./components/setting/app/edit_app_setting_component.vue'));

const app = new Vue({
    el: '#app'
});
