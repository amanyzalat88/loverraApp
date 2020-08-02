class Customers{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/customers',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'customers.name' },
                { name: 'customers.customer_type' },
                { name: 'customers.email' },
                { name: 'customers.phone' },
                { name: 'master_status.label' },
                { name: 'customers.created_at' },
                { name: 'customers.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 5, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [8] }
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