
$(document).ready(function() {

    var $ib_data_panel = $("#ib_data_panel");

    $ib_data_panel.block({ message:block_msg });

    var $customer = $('#customer_id');
    var $category = $("#category_id");
    var $country=$("#country_id");
    var $status=$("#status");

    $customer.select2({
        theme: "bootstrap"
    });

    $category.select2({
        theme: "bootstrap"
    });

    $country.select2({
        theme:"bootstrap"
    });

    $status.select2({
        theme:"bootstrap"
    });


    var _url = $("#_url").val();

    var selected = [];
    var ib_act_hidden = $("#ib_act_hidden");
    function ib_btn_trigger() {
        if(selected.length > 0){
            ib_act_hidden.show(200);
        }
        else{
            ib_act_hidden.hide(200);
        }
    }


    $(".cdelete").click(function (e) {

        e.preventDefault();
        id = this.id;
        var sure_msg = $('#sure_msg').val();

        bootbox.confirm(sure_msg, function (result) {

            if (result) {

                var _url = $("#_url").val();

                window.location.href = _url + "voucher/app/delete_page_transaction/" + id;
            }
        });
    });

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    var $reportrange = $("#reportrange");

    $reportrange.daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'YYYY/MM/DD'
        }
    }, cb);

    cb(start, end);

    var ib_dt = $('#ib_dt').DataTable( {

        "serverSide": true,
        "ajax": {
            "url": base_url + "voucher/app/page_tr_list/",
            "type": "POST",
            "data": function ( d ) {
                d.customer = $customer.val();
                d.category = $category.val();
                d.country = $country.val();
                d.status = $status.val();
                d.reportrange = $reportrange.val();

                d.filter_customer = $('#filter_customer').val();
                d.filter_title = $('#filter_title').val();
                d.filter_category = $('#filter_category').val();
                d.filter_country = $('#filter_country').val();
                d.filter_serialnumber = $('#filter_serialnumber').val();
            }
        },
        "pageLength": 10,
        responsive: false,
        dom: "<'row'<'col-sm-6'i><'col-sm-6'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
        fixedHeader: {
            headerOffset: 50
        },
        lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        buttons: [
            {
                extend:    'colvis',
                text:      '<i class="fa fa-columns"></i>',
                titleAttr: 'Columns'
            },
            {
                extend:    'copyHtml5',
                text:      '<i class="fa fa-files-o"></i>',
                titleAttr: 'Copy'
            },
            {
                extend:    'excelHtml5',
                text:      '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-text-o"></i>',
                titleAttr: 'CSV'
            },
            {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF'
            },
            {
                extend:    'print',
                text:      '<i class="fa fa-print"></i>',
                titleAttr: 'Print'
            },
            {
                extend:    'pageLength',
                text:      '<i class="fa fa-bars"></i>',
                titleAttr: 'Entries'
            }
        ],
        "orderCellsTop": true,
        "columnDefs": [
            { "orderable": false, "targets":9 }
        ],
        "order": [[ 1, 'desc' ]],
        "scrollX": true,
        "initComplete": function () {
            $ib_data_panel.unblock();
            listen_change();
        }
    } );

    // var init_data = function () {
    //     $.post(_url + "transactions/get_tr_searchdata/", $("#frm_search").serialize())
    //         .done(function (data) {
    //             if (data) {
    //                 // console.log(data);
    //                 $('#total_entries').html(data.total_records);
    //                 $('#debit').html(data.total_dr);
    //                 $('#credit').html(data.total_cr);
    //             }
    //
    // };
    //
    // init_data();
    //         });


    var $ib_filter = $("#ib_filter");
    var $ib_inner_filter = $("#inner_filter");

    $ib_filter.on('click', function(e) {
        e.preventDefault();

        $ib_data_panel.block({ message:block_msg });

        ib_dt.ajax.reload(
            function () {
                $ib_data_panel.unblock();
                listen_change();
            }
        );
    });

    $ib_inner_filter.on('click', function(e) {
        e.preventDefault();

        $ib_data_panel.block({ message:block_msg });

        ib_dt.ajax.reload(
            function () {
                $ib_data_panel.unblock();
                listen_change();
            }
        );
    });


    $ib_data_panel.on('click', '.cdelete', function(e){

        e.preventDefault();
        var id = this.id;
        bootbox.confirm(_L['are_you_sure'], function(result) {
            if(result){

                $.get( _url + "voucher/app/delete_page_transaction/"+id, function( data ) {
                    $ib_data_panel.block({ message:block_msg });

                    ib_dt.ajax.reload(
                        function () {
                            $ib_data_panel.unblock();
                            listen_change();
                            $('.i-checks').iCheck('uncheck');
                        }
                    );
                });


            }
        });

    });


    function listen_change() {

        var i_checks = $('.i-checks');
        i_checks.iCheck({
            checkboxClass: 'icheckbox_square-blue'
        });

        i_checks.on('ifChanged', function (event) {

            var id = $(this)[0].id;

            var index = $.inArray(id, selected);

            if($(this).iCheck('update')[0].checked){

                if(id == 'd_select_all'){

                    //   ib_dt.rows().select();

                    i_checks.iCheck('check');

                    return;
                }

                selected.push( id );

                //  $(this).closest('tr').toggleClass('selected');

                ib_btn_trigger();

            }
            else{

                if(id == 'd_select_all'){

                    i_checks.iCheck('uncheck');

                    return;
                }

                selected.splice( index, 1 );

                //  $(this).closest('tr').toggleClass('selected');

                ib_btn_trigger();

            }

        });
    }

    listen_change();

    $("#delete_multiple_vouchers").click(function(e){
        e.preventDefault();
        bootbox.confirm(_L['are_you_sure'], function(result) {
            if(result){
                $.redirect(_url + "voucher/app/delete_page_transactions/",{ ids: selected});
            }
        });

    });


});

