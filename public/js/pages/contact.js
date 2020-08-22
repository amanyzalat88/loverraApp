class Contact{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/contact',
                type : 'post',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'contact.name' },
                { name: 'contact.phone' },
                
               
            ],
            order: [[ 2, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [2] }
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