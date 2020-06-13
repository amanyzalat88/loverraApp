<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/authenticate', 'API\User@authenticate');
Route::post('/user/forgot_password', 'API\User@forgot_password');
Route::post('/user/reset_password', 'API\User@reset_password');

Route::group(['middleware' => ['token_auth']], function () {
    //user
    Route::post('/users', 'API\User@index');
    Route::post('/add_user', 'API\User@store');
    Route::post('/update_user/{slack}', 'API\User@update')->name('update_user');
    Route::post('/reset_user_password/{slack}', 'API\User@reset_user_password');

    //profile
    Route::post('/update_basic_profile', 'API\User@update_basic_profile')->name('update_basic_profile');
    Route::post('/update_password', 'API\User@update_password')->name('update_password');
    Route::post('/update_profile_image', 'API\User@update_profile_image')->name('update_profile_image');
    Route::post('/remove_profile_image', 'API\User@remove_profile_image')->name('remove_profile_image');
    Route::post('/update_profile_store', 'API\User@update_profile_store')->name('update_profile_store');

    //dashboard
    Route::post('/count_orders', 'API\Dashboard@count_orders');
    Route::post('/count_customers', 'API\Dashboard@count_customers');
    Route::post('/count_users', 'API\Dashboard@count_users');
    Route::post('/count_products', 'API\Dashboard@count_products');
    Route::post('/count_suppliers', 'API\Dashboard@count_suppliers');
    Route::post('/count_stores', 'API\Dashboard@count_stores');
    Route::post('/count_order_value', 'API\Dashboard@count_order_value');
    Route::post('/count_total_revenue', 'API\Dashboard@count_total_revenue');
    Route::post('/get_monthly_order_count', 'API\Dashboard@get_monthly_order_count');
    Route::post('/get_monthly_order_revenue', 'API\Dashboard@get_monthly_order_revenue');
    Route::post('/count_purchases', 'API\Dashboard@count_total_purchases');
    Route::post('/count_net_profit', 'API\Dashboard@count_net_profit');
    
    //role
    Route::post('/roles', 'API\Role@index');
    Route::post('/add_role', 'API\Role@store');
    Route::post('/update_role/{slack}', 'API\Role@update');

    //customer
    Route::post('/customers', 'API\Customer@index');
    Route::post('/add_customer', 'API\Customer@store');
    Route::post('/update_customer/{slack}', 'API\Customer@update');
    Route::post('/load_customers', 'API\Customer@load_customer_list');

    //category
    Route::post('/categories', 'API\Category@index');
    Route::post('/add_category', 'API\Category@store');
    Route::post('/update_category/{slack}', 'API\Category@update');

    //supplier
    Route::post('/suppliers', 'API\Supplier@index');
    Route::post('/add_supplier', 'API\Supplier@store');
    Route::post('/update_supplier/{slack}', 'API\Supplier@update');
    Route::post('/load_suppliers', 'API\Supplier@load_supplier_list');

    //product
    Route::post('/products', 'API\Product@index');
    Route::post('/add_product', 'API\Product@store');
    Route::post('/update_product/{slack}', 'API\Product@update');
    Route::post('/get_product', 'API\Product@get_product');
    Route::post('/generate_barcodes', 'API\Product@generate_barcodes');
    Route::post('/load_product_for_po', 'API\Product@load_product_for_po');

    //tax code
    Route::post('/tax_codes', 'API\Taxcode@index');
    Route::post('/add_tax_code', 'API\Taxcode@store');
    Route::post('/update_tax_code/{slack}', 'API\Taxcode@update');

    //order
    Route::post('/orders', 'API\Order@index');
    Route::post('/add_order', 'API\Order@store');
    Route::post('/update_order/{slack}', 'API\Order@update');
    Route::post('/delete_order/{slack}', 'API\Order@destroy');

    //store
    Route::post('/stores', 'API\Store@index');
    Route::post('/add_store', 'API\Store@store');
    Route::post('/update_store/{slack}', 'API\Store@update');

    //import
    Route::post('/import_data', 'API\Import@index');
    Route::post('/update_data', 'API\Import@update_data');
    Route::post('/download_reference_sheet', 'API\Import@generate_reference_sheet');

    //discount code
    Route::post('/discount_codes', 'API\Discountcode@index');
    Route::post('/add_discount_code', 'API\Discountcode@store');
    Route::post('/update_discount_code/{slack}', 'API\Discountcode@update');

    //payment method
    Route::post('/payment_methods', 'API\PaymentMethod@index');
    Route::post('/add_payment_method', 'API\PaymentMethod@store');
    Route::post('/update_payment_method/{slack}', 'API\PaymentMethod@update');

    //reports
    Route::post('/user_report', 'API\Report@user_report');
    Route::post('/category_report', 'API\Report@category_report');
    Route::post('/customer_report', 'API\Report@customer_report');
    Route::post('/supplier_report', 'API\Report@supplier_report');
    Route::post('/taxcode_report', 'API\Report@taxcode_report');
    Route::post('/discountcode_report', 'API\Report@discountcode_report');
    Route::post('/product_report', 'API\Report@product_report');
    Route::post('/store_report', 'API\Report@store_report');
    Route::post('/order_report', 'API\Report@order_report');
    Route::post('/purchase_order_report', 'API\Report@purchase_order_report');

    //purchase order
    Route::post('/purchase_orders', 'API\PurchaseOrder@index');
    Route::post('/add_purchase_order', 'API\PurchaseOrder@store');
    Route::post('/update_purchase_order/{slack}', 'API\PurchaseOrder@update');
    Route::post('/update_po_status/{slack}', 'API\PurchaseOrder@update_po_status');

    //setting
    Route::post('/add_setting_email', 'API\Setting@add_setting_email');
    Route::post('/update_setting_email/{slack}', 'API\Setting@update_setting_email');

    Route::post('/update_setting_app', 'API\Setting@update_setting_app');
    Route::post('/remove_company_logo', 'API\Setting@remove_company_image');

    //search
    Route::post('/filter_orders', 'API\Order@filter_orders');
    Route::post('/filter_customers', 'API\Customer@filter_customers');
    Route::post('/filter_purchase_orders', 'API\PurchaseOrder@filter_purchase_orders');
    Route::post('/filter_users', 'API\User@filter_users');
});