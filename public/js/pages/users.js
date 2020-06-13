class Users{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/users',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'users.user_code' },
                { name: 'users.fullname' },
                { name: 'users.email' },
                { name: 'users.phone' },
                { name: 'roles.label' },
                { name: 'master_status.label' },
                { name: 'users.created_at' },
                { name: 'users.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 6, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [9] }
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