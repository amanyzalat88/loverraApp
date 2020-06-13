class Categories{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/categories',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'category.label' },
                { name: 'category.category_code' },
                { name: 'master_status.label' },
                { name: 'category.created_at' },
                { name: 'category.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 4, "desc" ]],
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