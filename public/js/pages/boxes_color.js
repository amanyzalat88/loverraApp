class BoxesColor{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/boxescolor',
                type : 'post',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'boxes.name_ar' },
                { name: 'boxes.name_en' },
                { name: 'boxes.price' },
            
                { name: 'boxes.created_at' },
                { name: 'boxes.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[5, "desc" ]],
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