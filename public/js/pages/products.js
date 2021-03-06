class Products{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/products',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'products.product_code' },
                { name: 'products.name_en' },
                { name: 'products.name_ar' },
               // { name: 'suppliers.name' },
                { name: 'category.label_ar' },
                
              //  { name: 'discount_codes.label' },
                { name: 'products.quantity' },
                { name: 'products.purchase_amount_excluding_tax' },
               // { name: 'master_status.label' },
              //  { name: 'products.created_at' },
             //   { name: 'products.updated_at' },
             //   { name: 'user_created.fullname' },
            ],
            order: [[ 5, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] },
                 
                
            ]
        });

        listing_table.on( 'draw', function () {
            if(typeof listing_table != "undefined" && listing_table.page.info().recordsDisplay == 1){
                $('div.dataTables_scrollHead').css('overflow', 'visible');
                $('div.dataTables_scrollBody').css('overflow', 'visible');
            }
        });
    }
}