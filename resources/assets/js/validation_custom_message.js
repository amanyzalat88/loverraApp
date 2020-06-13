"use strict";

export const dictionary = {
    en: {
      attributes: {

        email: 'Email',
        password: 'Password',

        fullname: 'Fullname',
        phone: 'Contact No',
        role: 'Role',
        status: 'Status',
        current_password: 'Current Password',
        new_password: 'New Password',
        new_password_confirmation: 'New Password Confirmation',

        customer_number: 'Customer Contact No',
        customer_email : 'Customer Email',

        description: 'Description',

        name: 'Name',
        product_code: 'Product Code',
        supplier: 'Supplier',
        category: 'Category',
        tax_code: 'Tax Code',
        purchase_price: 'Purchase Price',
        sale_price: 'Sale Price',
        quantity: 'Quantity',
        no_of_barcodes: 'No of Barcodes per Product',

        category_name: 'Category Name',
        
        supplier_name: 'Supplier Name',
        address: 'Address',
        pincode: 'Pincode',

        tax_code_label: 'Tax Code Name',

        discount_name: 'Discount Name',
        discount_code: 'Discount Code',
        discount_percentage: 'Discount Percentage',

        store_code: 'Store Code',
        tax_number: 'Tax No or GST No.',
        primary_contact: 'Primary Contact No',
        secondary_contact: 'Secondary Contact No',
        primary_email: 'Primary Email',
        secondary_email: 'Secondary Email',
        print_type: 'Invoice Print Type',
        currency_code: 'Currency Code',

        payment_method: 'Payment Method',

        driver: 'Driver',
        host: 'Host',
        port: 'Port',
        username: 'Username',
        encryption: 'Encryption',
        from_email: 'From Email',
        from_email_name: 'From Email Name'
        
      },
      messages: {
        required: (field) => field+ ` is required`,
        email:  (field) => `Provide a valid `+field,
        min: (field, params) => field+ ` must be at least ${params[0]} characters`,
        max: (field, params) => field+ ` must not be more than ${params[0]} characters`,
        min_value: (field, params) => field+ ` must be more than ${params[0]}`,
        between: (field, params) => field+ ` must be between ${params[0]} and ${params[1]}`,
        confirmed: (field) => `Passwords doesn't match`,
      }
    }
  };