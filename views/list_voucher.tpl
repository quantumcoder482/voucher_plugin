{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>List Voucher</h5>
                <div class="ibox-tools">
                    <a href="{$_url}voucher/app/add_voucher" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i>Add Voucher</a>
                </div>
            </div>
            <div class="ibox-content">
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
                <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="20">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Country</th>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Cost Price</th>
                        <th>Sales Price</th>
                        <th>Description</th>
                        <th>Generated</th>
                        <th>Pages</th>
                        <th class="text-center" width="230px">Manage</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $vouchers as $key => $v}
                        <tr>
                            <td data-value="{$v['id']}">
                                {$key+1}
                            </td>

                            <td data-value="{strtotime($v['created_date'])}">
                                {date( $config['df'], strtotime($v['created_date']))}
                            </td>

                            <td class="view_voucher" data-value="{$v['country_name']}" id="{$v['id']}">
                                <a href="#">{$v['country_name']}</a>
                            </td>

                            <td class="view_voucher" data-value="{$v['voucher_img']}" id="{$v['id']}" alt="{$v['voucher_img']}">

                                {if {$v['voucher_img']} eq ''}
                                    <a href="#"><img src="{$baseUrl}/apps/voucher/views/img/item_placeholder.png" width="40px" /></a>
                                {else}
                                    <a href="#"><img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['voucher_img']}" width="40px" /></a>
                                {/if}

                            </td>

                            <td class="view_voucher" data-value="{$v['category_name']}" id="{$v['id']}">
                                <a href="#">{$v['category_name']}</a>
                            </td>

                            <td data-value="{$v['cost_price']}" class="amount" data-a-sign="{if $v['currency_symbol'] eq ''} {$config['currency_code']} {else} {$v['currency_symbol']}{/if}">{$v['cost_price']}</td>

                            <td data-value="{$v['sales_price']}" class="amount" data-a-sign="{if $v['currency_symbol'] eq ''} {$config['currency_code']} {else} {$v['currency_symbol']}{/if}">{$v['sales_price']}</td>

                            <td data-value="{$v['description']}">
                                {$v['description']}
                            </td>

                            <td data-value="">
                                {$generated_voucher[$v['id']]}
                                <span style="color: #CAA931;">({$active_voucher[$v['id']]})</span>
                            </td>

                            <td data-value="">
                                <span style="color:#CAA931">{$pages[$v['id']]}</span>
                            </td>

                            <td class="text-center">
                                <a href="{$_url}voucher/app/list_voucher_page/{$v['id']}" class="btn btn-primary btn-xs add_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Add Page">
                                    + Page
                                </a>
                                {*{if $v['pay_status'] neq 0 && $v['expired'] neq 1}*}
                                    <a href="#" class="btn btn-xs generate" id="{$v['id']}" style="background-color:#4B0082; border-color:#4B0082; color:#f8f8f8"
                                       data-toggle="tooltip" data-placement="top" title="generate">
                                        Generate
                                    </a>
                                {*{elseif $v['expired'] eq 1}*}
                                    {*<a href="#" class="btn btn-xs" id="{$v['id']}" style="background-color:#A9A9A9; border-color:#A9A9A9; color:#f8f8f8" disabled*}
                                       {*data-toggle="tooltip" data-placement="top" title="{$_L['Renew']}">*}
                                        {*{$_L['Renew']}*}
                                    {*</a>*}
                                {*{/if}*}

                                <a href="#" class="btn btn-primary btn-xs view_voucher" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                    <i class="fa fa-file-text-o"></i>
                                </a>
                                <a href="#" class="btn btn-info btn-xs edit_voucher" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-xs cdelete" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Delete']}">
                                    <i class="fa fa-trash"></i>
                                </a>
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
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h3 style="font-weight: 600">Recent Redeem Voucher</h3>
            </div>
            <div class="ibox-content">
                <table class="table table-bordered table-hover sys_table footable" data-page-size="10">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Country</th>
                        <th>Cat</th>
                        <th>Expiry</th>
                        <th>Serial No.</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $redeem_vouchers as $v}
                        <tr>
                            <td data-value="{$v['id']}">
                                {$v['id']}
                            </td>

                            <td data-value="{$v['customer']}">
                                <a href="{$_url}contacts/view/{$v['contact_id']}/summary/">{$v['customer']}</a>
                            </td>

                            <td data-value="{$v['country_name']}" id="{$v['id']}">
                                <a href="#">{$v['country_name']}</a>
                            </td>

                            <td data-value="{$v['category']}">
                                {$v['category']}
                            </td>

                            <td data-value="{strtotime($v['expiry_date'])}">
                                {if $voucher_status[$v['id']] eq 'Expired'}
                                    <span style="color:red">{date( $config['df'], strtotime($v['expiry_date']))}</span>
                                {elseif $voucher_status[$v['id']] eq 'Limit'}
                                    <span style="color:darkorange">{date( $config['df'], strtotime($v['expiry_date']))}</span>
                                {else}
                                    {date( $config['df'], strtotime($v['expiry_date']))}
                                {/if}
                            </td>

                            <td data-value="{$v['serial_number']}">
                                {if $v['status'] eq 'Active'}
                                    <a href="{$_url}voucher/app/list_voucher_page/{$v['voucher_format_id']}/{$v['id']}/">{$v['prefix']}{$v['serial_number']}</a>
                                {else}
                                    {$v['prefix']}{$v['serial_number']}
                                {/if}
                            </td>
                            <td data-value="{$v['invoice_status']}">
                                <a href="{$_url}invoices/view/{$v['invoice_id']}/" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                    <i class="fa fa-file-text-o"></i>
                                </a>
                                {if $v['invoice_status'] eq 'Paid'}
                                    <div class="label-success" style="display:inline-block; margin:0 auto; font-size:85%; width:85px">Paid</div>
                                {elseif $v['invoice_status'] eq 'Unpaid'}
                                    <div class="label-danger" style="display:inline-block; margin:0 auto; font-size:85%; width:85px">Unpaid</div>
                                {elseif $v['invoice_status'] eq 'Partially Paid'}
                                    <div class="label-warning" style="display:inline-block; margin:0 auto; font-size:85%; width:85px">Partially Paid</div>
                                {else}
                                    -
                                {/if}
                            </td>

                        </tr>
                    {/foreach}

                    </tbody>
                    <tfoot>
                    <tr>
                        <td style="text-align: right;" colspan="7">
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
{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/list_voucher.js"></script>
{/block}