
{extends file="$layouts_client"}

{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}

{block name="content"}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>
                    Voucher Pages - {$serial_number}
                </h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$voucher_img}" width="100%" />
                        <br>

                            <div class="ibox-title">
                                <h5>Voucher Details</h5>
                            </div>
                            <div class="ibox-content">
                                <table style="text-align: left" width="100%">
                                    <tr>
                                        <td width="40%" style="text-align: left">Date Activated:</td>
                                        <td style="text-align: left">{$voucher_info['date']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Expire Date:</td>
                                        <td style="text-align: left">{$voucher_info['expiry_date']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Type:</td>
                                        <td style="text-align: left">{$voucher_info['category']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Country:</td>
                                        <td style="text-align: left">{$voucher_info['country_name']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Description:</td>
                                        <td style="text-align: left">{$voucher_info['description']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Voucher Number:</td>
                                        <td style="text-align: left">{$voucher_info['prefix']}{$voucher_info['serial_number']}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align: left">Status:</td>
                                        <td style="text-align: left">{if $voucher_info['status'] neq ''}{$voucher_info['status']}{else}Inactive{/if}</td>
                                    </tr>
                                </table>
                            </div>

                    </div>
                    <div class="col-md-8">
                        <form class="form-horizontal" method="post" action="">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="fa fa-search"></span>
                                        </div>
                                        <input type="text" name="name" id="foo_filter" class="form-control" placeholder="{$_L['Search']}..." />

                                    </div>
                                </div>

                            </div>
                            <input type="hidden" id="sure_msg" value="{$_L['are_you_sure']}" />
                        </form>
                        <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="10" >
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-center" width="210px">Manage</th>
                            </tr>
                            </thead>
                            <tbody>

                            {foreach $voucher_pages as $key=>$v}
                                <tr>
                                    <td data-value="{$v['id']}">
                                        {$key+1}
                                    </td>

                                    <td data-value="{$v['front_img']}" alt="voucher front image">

                                        {if $v['front_img'] eq ''}
                                            <img src="{$baseUrl}/apps/voucher/views/img/item_placeholder.png" width="40px" />
                                        {else}
                                            {if $page_status[$v['id']] eq 'confirm'}
                                                <a href="#" class="view_page" id="{$t_id[$v['id']]}"><img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['front_img']}" width="40px" /></a>
                                            {else}
                                                <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['front_img']}" width="40px" />
                                            {/if}
                                        {/if}

                                    </td>

                                    <td data-value="{$v['title']}" id="{$v['id']}">
                                        {if $page_status[$v['id']] eq 'confirm'}
                                            <a href="#" class="view_page" id="{$t_id[$v['id']]}">{$v['title']}</a>
                                        {else}
                                            {$v['title']}
                                        {/if}
                                    </td>

                                    <td data-value="{$v['description']}">
                                        {$v['description']}
                                    </td>

                                    <td data-value="{$page_status[$v['id']]}">
                                        {if $v['void_days']}
                                            {if $page_status[$v['id']] eq 'processing' || $page_status[$v['id']] eq 'confirm'}
                                                <span style="color: #2bb673;"><i class="fa fa-check"></i> </span>
                                            {elseif $page_status[$v['id']] eq 'void'}
                                                <button class="btn btn-xs btn-danger" style="width:85px;color:white">Void</button>
                                            {else}
                                                <button class="btn btn-xs" style="background-color: #FBB040; width:85px; color:white">{$page_status[$v['id']]}</button>
                                            {/if}
                                        {else}
                                            {if $page_status[$v['id']] neq 'redeem'}
                                                <span style="color: #2bb673;"><i class="fa fa-check"></i> </span>
                                            {/if}
                                        {/if}
                                    </td>

                                    <td class="text-center">
                                        {if $page_status[$v['id']] eq 'void'}
                                            <button class="btn btn-xs square-deactive">Redeem</button>
                                        {elseif $page_status[$v['id']] eq 'redeem' || $page_status[$v['id']] eq ''}
                                            {if $view_type eq 'view'}
                                                <a href="#" class="btn btn-xs square-redeem" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Redeem">
                                                    Redeem
                                                </a>
                                            {else}
                                                <a href="{$_url}voucher/client/redeem_voucher_page/{$voucher_id}/{$v['id']}" class="btn btn-xs square-redeem" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Redeem">
                                                    Redeem
                                                </a>
                                            {/if}
                                        {elseif $page_status[$v['id']] eq 'confirm'}
                                            <a href="#" class="btn btn-primary btn-xs view_page" id="{$t_id[$v['id']]}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            <a href="#" class="btn btn-xs square-active" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Confirmed">
                                                Confirmed
                                            </a>
                                        {elseif $page_status[$v['id']] eq 'processing'}
                                            <a href="#" class="btn btn-xs square-deactive" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Processing">
                                                Processing
                                            </a>
                                            {if $setting['voucher_edit_enable'] neq 1 && $view_type neq 'view'}
                                                <a href="#" class="btn btn-info btn-xs edit_redeem" id="{$t_id[$v['id']]}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            {/if}
                                        {else}
                                            <a href="{$_url}voucher/client/redeem_voucher_page/{$voucher_id}/{$v['id']}" class="btn btn-xs square-redeem" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Redeem">
                                                Redeem
                                            </a>
                                        {/if}
                                    </td>

                                </tr>
                            {/foreach}

                            </tbody>

                                <tfoot>
                                <tr>
                                    <td style="text-align: right;" colspan="11">
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
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>
                    Recent Transaction
                </h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="10" >
                            <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Redeem Date</th>
                                <th>Page Title</th>
                                <th>Account</th>
                                <th>Amount</th>
                                <th>Payment</th>
                            </tr>
                            </thead>
                            <tbody>

                            {foreach $recent_transaction as $t}
                                <tr>
                                    <td data-value="{$t['invoice_id']}">
                                        {if $t['invoice_id'] eq '0' || $t['invoice_id'] eq ''}
                                            -
                                        {else}
                                            <a href="{$invoice_url[$t['id']]}" > {$t['invoice_id']}</a>
                                        {/if}

                                    </td>
                                    <td data-value="{strtotime($t['createdon'])}">
                                        {date( $config['df'], strtotime($t['createdon']))}
                                    </td>
                                    <td data-value="{$t['page_title']}">
                                        {$t['page_title']}
                                    </td>
                                    <td data-value="{$t['customer_name']}">
                                        {$t['customer_name']}
                                    </td>
                                    <td data-value="{$t['invoice_amount']}" class="amount" data-a-sign="{$config['currency_code']} ">{$t['invoice_amount']}</td>
                                    <td data-value="{$t['invoice_status']}">

                                        {if $t['invoice_status'] eq 'Paid'}
                                            <a href="{$invoice_url[$t['id']]}" style="" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            <div class="label-success" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Paid</div>
                                        {elseif $t['invoice_status'] eq 'Unpaid'}
                                            <a href="{$invoice_url[$t['id']]}" style="" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            <div class="label-danger" style="display:inline-block; margin:0 auto; font-size:100%; width:85px">Unpaid</div>
                                        {elseif $t['invoice_status'] eq 'Partially Paid'}
                                            <a href="{$invoice_url[$t['id']]}" style="" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
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
                                <td style="text-align: right;" colspan="11">
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
    </div>
</div>
{/block}
{block name=script}
    {*<script type="text/javascript" src="{$app_url}apps/voucher/views/js/list_voucher_pages.js"></script>*}
    <script>

        $(document).ready(function(){

            $.fn.modal.defaults.width = '800px';
            $('[data-toggle="tooltip"]').tooltip();

            var $modal = $('#ajax-modal');
            var _url = $("#_url").val();

            $('.footable').footable();

            $('.edit_redeem').on('click', function (e) {

                var id = this.id;

                e.preventDefault();

                $('body').modalmanager('loading');

                $modal.load(_url + 'voucher/client/modal_edit_redeem/'+id, '', function () {

                    $modal.modal();
                    $modal.css("width", "800px");
                    $modal.css("margin-left", "-349px");

                });
            });

            $('.view_page').on('click', function (e) {

                var id = this.id;

                e.preventDefault();

                $('body').modalmanager('loading');

                $modal.load(_url + 'voucher/client/modal_edit_redeem/'+id +'/view', '', function () {

                    $modal.modal();
                    $modal.css("width", "800px");
                    $modal.css("margin-left", "-349px");

                });
            });




            $modal.on('click', '.modal_submit', function (e) {

                $('#voucher_number').prop('disabled', false);
                $('#country_name').prop('disabled', false);
                $('#category').prop('disabled', false);
                $('#customer_name').prop('disabled', false);
                $('#customer_address').prop('disabled', false);
                $('#total_days').prop('disabled', false);

                var tid = $('#tid').val();
                e.preventDefault();

                $modal.modal('loading');

                $.post(_url + 'voucher/client/post_redeem_page/'+tid, $("#frm_redeem").serialize())
                    .done(function (data) {
                        if(data == 'page_list') {
                            var voucher_id = $('#voucher_id').val();
                            window.location = base_url + 'voucher/client/voucher_page/'+voucher_id;

                        }else if(data == 'reload') {
                            window.location.reload();
                        }
                    });

            });

        });

    </script>
{/block}