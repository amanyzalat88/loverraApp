class Slider{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/slider',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'slider.photo_ar' },
                { name: 'slider.photo_en' },
                { name: 'slider.created_at' },
                { name: 'slider.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 4, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [5] },
                {
                    "targets" :0 ,
                    "data": "img",
                    "render" : function ( url, type, full) {
                    return '<img height="75%" width="75%" src="public/'+full[0]+'"/>';
                    }
                },
                {
                    "targets" :1 ,
                    "data": "img",
                    "render" : function ( url, type, full) {
                    return '<img height="75%" width="75%" src="public/'+full[1]+'"/>';
                    }
                }
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