class Discountcodes{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/discount_codes',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'discount_codes.label' },
                { name: 'discount_codes.discount_code' },
                { name: 'discount_codes.discount_percentage' },
                { name: 'master_status.label' },
                { name: 'discount_codes.created_at' },
                { name: 'discount_codes.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 4, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [7] }
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