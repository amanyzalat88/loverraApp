class Stores{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/stores',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'stores.store_code' },
                { name: 'stores.name' },
                { name: 'master_status.label' },
                { name: 'stores.created_at' },
                { name: 'stores.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
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