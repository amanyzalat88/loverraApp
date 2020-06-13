class Payment_methods{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/payment_methods',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'payment_methods.label' },
                { name: 'master_status.label' },
                { name: 'payment_methods.created_at' },
                { name: 'payment_methods.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 2, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [5] }
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