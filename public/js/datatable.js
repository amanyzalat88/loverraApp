"use strict";

$.extend( $.fn.dataTable.defaults, {
    'pagingType': 'numbers',
    'processing': true,
    'serverSide': true,
    'responsive': true,
    'pageLength': 25,
    'dom'       : '<"d-flex mb-3"<"mr-auto"i> <"mr-4"l><f>><"clearfix">r<t><"mt-3"p><"clearfix">',
    'scrollX'   : true
} );

$('#menu-toggle').on('click', function () {
    "use strict";
    setTimeout(function(){
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    }, 300);
});
