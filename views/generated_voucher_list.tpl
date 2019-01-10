{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
    <div class="row">
        {*<div class="col-md-12">*}
            {*<div class="panel panel-default">*}

                {*<div class="panel-body" style="text-align: right">*}

                    {*<a href="{$_url}contacts/add/" class="btn btn-primary"><i class="fa fa-plus"></i> Add Page</a>*}

                {*</div>*}
            {*</div>*}
        {*</div>*}

        <div class="col-md-12">
            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="table-responsive" id="ib_data_panel">

                        <table class="table table-bordered table-hover display" id="ib_dt">  <!--width="100%" -->
                            <thead>
                            <tr class="heading">
                                <th>#</th>
                                <th>Image</th>
                                <th style="width: 80px;">Date</th>
                                <th>Prefix</th>
                                <th>Serial No.</th>
                                <th>Contact</th>
                                <th>Agent</th>
                                <th style="width: 80px;">Expiry</th>
                                <th>Redeem</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-right" style="width: 80px;">Manage</th>
                            </tr>

                            <tr class="heading">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><input type="text" id="filter_prefix" name="filter_prefix" class="form-control" width="30px"></td>
                                <td><input type="text" id="filter_serialnumber" name="filter_serialnumber" class="form-control"></td>
                                <td><input type="text" id="filter_contact" name="filter_contact" class="form-control"></td>
                                <td><input type="text" id="filter_agent" name="filter_agent" class="form-control"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right" style="width: 80px;"><button type="submit" id="ib_filter" class="btn btn-primary">{$_L['Filter']}</button></td>
                            </tr>
                            </thead>

                        </table>
                    </div>

                </div>
            </div>
        </div>
        <input type="hidden" id="vid" name="vid" value="{$vid}">
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3 style="font-weight: 600">Recent Redeem Voucher Page</h3>
                </div>

                <div class="ibox-content">
                    <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="10">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Country</th>
                            <th>Cat</th>
                            <th>Page Title</th>
                            <th>Serial No.</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $redeem_voucher_pages as $p}
                            <tr>
                                <td data-value="{$p['id']}">
                                    {$p['id']}
                                </td>

                                <td data-value="{strtotime($p['createdon'])}">
                                    {date( $config['df'], strtotime($p['createdon']))}
                                </td>

                                <td data-value="{$p['customer_name']}">
                                    <a href="{$_url}contacts/view/{$p['contact_id']}/summary/">{$p['customer_name']}</a>
                                </td>

                                <td data-value="{$p['country_name']}" id="{$p['id']}">
                                    <a href="#">{$p['country_name']}</a>
                                </td>

                                <td data-value="{$p['category']}">
                                    {$p['category']}
                                </td>

                                <td data-value="{$p['page_title']}">
                                    <a href="{$_url}voucher/app/view_redeem_page/{$p['voucher_id']}/{$p['page_id']}/view/">{$p['page_title']}</a>
                                </td>

                                <td data-value="{$p['voucher_number']}">
                                    <a href="{$_url}voucher/app/list_voucher_page/{$p['voucher_format_id']}/{$p['voucher_id']}/">{$p['voucher_number']}</a>
                                </td>

                                <td data-value="{$p['invoice_status']}">
                                    {if $p['invoice_status'] eq 'Paid'}
                                        <a href="{$_url}invoices/view/{$p['invoice_id']}/" style="" class="btn btn-primary btn-xs view_invoice" id="{$p['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                            <i class="fa fa-file-text-o"></i>
                                        </a>
                                        <div class="label-success" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Paid</div>
                                    {elseif $p['invoice_status'] eq 'Unpaid'}
                                        <a href="{$_url}invoices/view/{$p['invoice_id']}/" style="" class="btn btn-primary btn-xs view_invoice" id="{$p['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                            <i class="fa fa-file-text-o"></i>
                                        </a>
                                        <div class="label-danger" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Unpaid</div>
                                    {elseif $p['invoice_status'] eq 'Partially Paid'}
                                        <a href="{$_url}invoices/view/{$p['invoice_id']}/" style="" class="btn btn-primary btn-xs view_invoice" id="{$p['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                            <i class="fa fa-file-text-o"></i>
                                        </a>
                                        <div class="label-warning" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Partially Paid</div>
                                    {else}
                                        <div class="label-success" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Confirmed</div>
                                    {/if}
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="8">
                                <ul class="pagination">
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
{/block}

{block name="script"}
    <script>
        Dropzone.autoDiscover = false;
        $(function() {

            var _url = $("#_url").val();

            $.fn.modal.defaults.width = '850px';
            var $modal = $('#ajax-modal');
            $('[data-toggle="tooltip"]').tooltip();

            $('.footable').footable();

            $modal.on('click', '.generate_modal_submit', function (e) {

                $('#serial_number').prop('disabled', false);
                $('#total_voucher').prop('disabled', false);

                e.preventDefault();

                $modal.modal('loading');

                $.post(_url + 'voucher/app/post_generate_voucher', $("#mrform").serialize())
                    .done(function (data) {
                        if ($.isNumeric(data)) {
                            window.location.reload();
                        }
                        else {
                            $modal.modal('loading');
                            toastr.error(data);
                        }
                    });
            });

            var $ib_data_panel = $("#ib_data_panel");

            $ib_data_panel.block({ message:block_msg });


            $('[data-toggle="tooltip"]').tooltip();

            var ib_dt = $('#ib_dt').DataTable( {

                "serverSide": true,
                "ajax": {
                    "url": _url + "voucher/app/json_voucher_list/{$vid}",
                    "type": "POST",
                    "data": function ( d ) {

                        d.prefix = $('#filter_prefix').val();
                        d.serial_number = $('#filter_serialnumber').val();
                        d.contact = $('#filter_contact').val();
                        d.agent = $('#filter_agent').val();

                    }
                },
                "pageLength": 10,
                responsive: false,
                dom: "<'row'<'col-sm-6'i><'col-sm-6'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'><'col-sm-7'p>>",
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    {
                        extend:    'pageLength',
                        text:      '<i class="fa fa-bars"></i>',
                        titleAttr: 'Entries'
                    },
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
                    }

                ],
                "orderCellsTop": true,
                "columnDefs": [
                    // {
                    //     "render": function ( data, type, row ) {
                    //         return '<a href="' + _url +'voucher/app/list_voucher_page/'+ row[12] +'/'+row[0]+'">'+ data +'</a>';
                    //     },
                    //     "targets": 1
                    // },
                    // {
                    //     "render": function (data, type, row) {
                    //         return '<a href="' + _url +'voucher/app/list_voucher_page/'+ row[12] +'/'+row[0]+'">'+ data +'</a>';
                    //     },
                    //     "targets": 4
                    // },
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": 9 },
                    { "orderable": false, "targets": 11 },
                    { className: "text-center", "targets": [ 1 ] },
                    { "type": "html-num", "targets": 1 }
                ],
                "order": [[ 0, 'desc' ]],
                "scrollX": true,
                "initComplete": function () {
                    $ib_data_panel.unblock();
                    //
                    // listen_change();
                },
                select: {
                    info: false
                }
            } );

            var $ib_filter = $("#ib_filter");

            $ib_filter.on('click', function(e) {
                e.preventDefault();

                $ib_data_panel.block({ message:block_msg });

                ib_dt.ajax.reload(
                    function () {
                        $ib_data_panel.unblock();
                        // listen_change();
                    }
                );


            });

            $ib_data_panel.on('click', '.cdelete', function(e){

                e.preventDefault();
                var lid = this.id;
                bootbox.confirm(_L['are_you_sure'], function(result) {
                    if(result){

                        $.get( _url + "voucher/app/delete_generated_voucher/"+lid, function( data ) {
                            $ib_data_panel.block({ message:block_msg });

                            ib_dt.ajax.reload(
                                function () {
                                    $ib_data_panel.unblock();
                                    // listen_change();
                                    // $('.i-checks').iCheck('uncheck');
                                }
                            );
                        });


                    }
                });

            });

            $ib_data_panel.on('click', '.cedit', function(e) {

                e.preventDefault();
                var id = this.id;
                var vid = $('#vid').val();

                e.preventDefault();

                $('body').modalmanager('loading');

                $modal.load(_url + 'voucher/app/modal_generate_voucher/'+vid+'/'+id, '', function () {

                    $modal.modal();

                });
            });




        });
    </script>
{/block}
