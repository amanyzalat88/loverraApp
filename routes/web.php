<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['demo_check']], function () {
    Route::get('/', "Entry@sign_in")->name('home');
    Route::get('/logout', "Entry@logout")->name('logout');
    Route::get('/forgot_password', "Entry@forgot_password")->name('forgot_password');
    Route::get('/reset_password/{user_slack}/{forgot_password_token}', "Entry@reset_password")->name('reset_password');
    Route::get('/generate_lockout_password/{password_string?}', "Entry@generate_lockout_password")->name('generate_lockout_password');
});

Route::group(['middleware' => ['token_auth', 'user_menu']], function () {

    //search 
    Route::get('/search', "Search@index")->name('search');

    //dashboard
    Route::get('/dashboard', "Dashboard@index")->name('dashboard');

    //user
    Route::get('/users', "User@index")->name('users');
    Route::get('/user/{slack}', "User@detail")->name('user');
    Route::get('/add_user', "User@add_user")->name('add_user');
    Route::get('/edit_user/{slack?}', "User@add_user")->name('edit_user');
    Route::get('/profile/{slack}', "User@profile")->name('profile');
    Route::get('/edit_profile', "User@edit_profile")->name('edit_profile');

    //role
    Route::get('/roles', "Role@index")->name('roles');
    Route::get('/role/{slack}', "Role@detail")->name('role');
    Route::get('/add_role', "Role@add_role")->name('add_role');
    Route::get('/edit_role/{slack?}', "Role@add_role")->name('edit_role');

    //customer
    Route::get('/customers', "Customer@index")->name('customers');
    Route::get('/customer/{slack}', "Customer@detail")->name('customer');
    Route::get('/add_customer', "Customer@add_customer")->name('add_customer');
    Route::get('/edit_customer/{slack?}', "Customer@add_customer")->name('edit_customer');

    //product
    Route::get('/products', "Product@index")->name('products');
    Route::get('/product/{slack}', "Product@detail")->name('product');
    Route::get('/add_product', "Product@add_product")->name('add_product');
    Route::get('/edit_product/{slack?}', "Product@add_product")->name('edit_product');
    Route::get('/generate_barcode', "Product@generate_barcode")->name('generate_barcode');

    //category
    Route::get('/categories', "Category@index")->name('categories');
    Route::get('/category/{slack}', "Category@detail")->name('category');
    Route::get('/add_category', "Category@add_category")->name('add_category');
    Route::get('/edit_category/{slack?}', "Category@add_category")->name('edit_category');

    //supplier
    Route::get('/suppliers', "Supplier@index")->name('suppliers');
    Route::get('/supplier/{slack}', "Supplier@detail")->name('supplier');
    Route::get('/add_supplier', "Supplier@add_supplier")->name('add_supplier');
    Route::get('/edit_supplier/{slack?}', "Supplier@add_supplier")->name('edit_supplier');

    //tax code
    Route::get('/tax_codes', "Taxcode@index")->name('tax_codes');
    Route::get('/tax_code/{slack}', "Taxcode@detail")->name('tax_code');
    Route::get('/add_tax_code', "Taxcode@add_tax_code")->name('add_tax_code');
    Route::get('/edit_tax_code/{slack?}', "Taxcode@add_tax_code")->name('edit_tax_code');

    //order
    Route::get('/orders', "Order@index")->name('orders');
    Route::get('/order/{slack}', "Order@detail")->name('order_detail');
    Route::get('/add_order', "Order@add_order")->name('add_order');
    Route::get('/edit_order/{slack?}', "Order@add_order")->name('edit_order');
    Route::get('/print_order/{slack}', "Order@print_order")->name('print_order');

    //store
    Route::get('/stores', "Store@index")->name('stores');
    Route::get('/store/{slack}', "Store@detail")->name('store');
    Route::get('/add_store', "Store@add_store")->name('add_store');
    Route::get('/edit_store/{slack?}', "Store@add_store")->name('edit_store');
    Route::get('/select_store', "Store@select_store")->name('select_store');

    //uploads
    Route::get('/import_data', "Import@index")->name('import_data');
    Route::get('/update_data', "Import@update_data")->name('update_data');

    //discount code
    Route::get('/discount_codes', "Discountcode@index")->name('discount_codes');
    Route::get('/discount_code/{slack}', "Discountcode@detail")->name('discount_code');
    Route::get('/add_discount_code', "Discountcode@add_discount_code")->name('add_discount_code');
    Route::get('/edit_discount_code/{slack?}', "Discountcode@add_discount_code")->name('edit_discount_code');

    //payment methods
    Route::get('/payment_methods', "PaymentMethod@index")->name('payment_methods');
    Route::get('/payment_method/{slack}', "PaymentMethod@detail")->name('payment_method');
    Route::get('/add_payment_method', "PaymentMethod@add_payment_method")->name('add_payment_method');
    Route::get('/edit_payment_method/{slack?}', "PaymentMethod@add_payment_method")->name('edit_payment_method');

    //reports
    Route::get('/reports', "Report@index")->name('reports');

    //setting email
    Route::get('/email_setting', "Setting@email_setting")->name('email_setting');
    Route::get('/edit_email_setting/{slack?}', "Setting@edit_email_setting")->name('edit_email_setting');

    //setting app
    Route::get('/app_setting', "Setting@app_setting")->name('app_setting');
    Route::get('/edit_app_setting', "Setting@edit_app_setting")->name('edit_app_setting');

    //Purchase Order
    Route::get('/purchase_orders', "PurchaseOrder@index")->name('purchase_orders');
    Route::get('/purchase_order/{slack}', "PurchaseOrder@detail")->name('purchase_order_detail');
    Route::get('/add_purchase_order', "PurchaseOrder@add_purchase_order")->name('add_purchase_order');
    Route::get('/edit_purchase_order/{slack?}', "PurchaseOrder@add_purchase_order")->name('edit_purchase_order');
    Route::get('/print_purchase_order/{slack}', "PurchaseOrder@print_purchase_order")->name('print_purchase_order');
});