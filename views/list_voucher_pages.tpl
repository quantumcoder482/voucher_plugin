{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>
                    Voucher Pages
                    {if $gid neq ''}
                        - {$voucher_info['category_name']} {$voucher_info['prefix']}-{$voucher_info['serial_number']}
                    {else}
                        - {$voucher_format['country_name']} {$voucher_format['category_name']}
                    {/if}
                </h5>

                {*if $gid eq ''*}
                <div class="ibox-tools">
                    <a href="{$_url}voucher/app/add_page/{$voucher_id}" class="btn btn-primary btn-xs add_page"><i class="fa fa-plus"></i>Add Page</a>
                </div>
                {*/if*}

            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{$baseUrl}/storage/system/{$voucher_img}" width="100%" />
                        <br>
                        {if $gid neq ''}
                        <div class="ibox-title">
                            <h5>Voucher Details</h5>
                        </div>
                        <div class="ibox-content">
                            <table style="text-align: left" width="100%">
                                <tr>
                                    <td width="40%" style="text-align: left">Date Activated:</td>
                                    <td style="text-align: left">{if $voucher_info['date'] neq '0000-00-00'}{$voucher_info['date']}{/if}</td>
                                </tr>
                                <tr>
                                    <td width="40%" style="text-align: left">Expire Date:</td>
                                    <td style="text-align: left">{if $voucher_info['expiry_date'] neq '0000-00-00'}{$voucher_info['expiry_date']}{/if}</td>
                                </tr>
                                <tr>
                                    <td width="40%" style="text-align: left">Type:</td>
                                    <td style="text-align: left">{$voucher_info['category_name']}</td>
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
                        {/if}
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
                                {if $gid neq ''}<th>Status</th>{/if}
                                <th class="text-center" width="210px">Manage</th>
                            </tr>
                            </thead>
                            <tbody>

                            {foreach $voucher_pages as $key => $v}
                                <tr>
                                    <td data-value="{$v['id']}">
                                        {$key+1}
                                    </td>

                                    <td data-value="{$v['front_img']}" alt="voucher front image">
                                        {if $gid neq ''}
                                            <a href="#" class="{if $page_status[$v['id']] neq 'redeem'}view_redeem_page{/if}" id="{$v['id']}">
                                                <img src="{$baseUrl}/storage/system/{$v['front_img']}" width="40px" />
                                            </a>
                                        {else}
                                            {if {$v['front_img']} eq ''}
                                                <img src="{$baseUrl}/apps/voucher/views/img/item_placeholder.png" width="40px" />
                                            {else}
                                                <img src="{$baseUrl}/storage/system/{$v['front_img']}" width="40px" />
                                            {/if}
                                        {/if}

                                    </td>

                                    <td data-value="{$v['title']}" id="{$v['id']}">
                                        {if $gid neq ''}
                                            <a href="#" class="{if $page_status[$v['id']] neq 'redeem'}view_redeem_page{/if}" id="{$v['id']}">{$v['title']}</a>
                                        {else}
                                            <a href="#" class="view_page" id="{$v['id']}">{$v['title']}</a>
                                        {/if}

                                    </td>

                                    <td data-value="{$v['description']}">
                                        {$v['description']}
                                    </td>

                                    {if $gid neq ''}
                                    <td data-value="{$page_status[$v['id']]}">
                                        {if $page_status[$v['id']] eq 'Redeem' || $page_status[$v['id']] eq ''}
                                        <a href="#" class="btn btn-xs square-redeem redeem_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Redeem">
                                            Redeem
                                        </a>
                                        {elseif $page_status[$v['id']] eq 'Confirmed'}
                                        <a href="#" class="btn btn-xs square-active" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Confirmed">
                                            Confirmed
                                        </a>
                                        {elseif $page_status[$v['id']] eq 'Processing'}
                                        <a href="#" class="btn btn-xs square-deactive" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Processing">
                                            Processing
                                        </a>
                                        {elseif $page_status[$v['id']] eq 'Cancelled'}
                                        <a href="#" class="btn btn-xs square-deactive" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Cancelled">
                                            Cancelled
                                        </a>
                                        {/if}
                                    </td>
                                    {/if}

                                    {if $gid neq ''}
                                        {if $page_status[$v['id']] neq 'Redeem'}
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-xs view_redeem_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            <a href="#" class="btn btn-info btn-xs edit_redeem_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        </td>
                                        {else}
                                            <td></td>
                                        {/if}
                                    {else}
                                        <td class="text-center">
                                            <a href="#" class="btn btn-primary btn-xs view_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-xs clone_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Clone']}">
                                                <i class="fa fa-files-o"></i>
                                            </a>
                                            <a href="#" class="btn btn-info btn-xs edit_page" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-xs cdelete" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Delete']}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    {/if}
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
                    <input type="hidden" id="vid" name="vid" value="{$voucher_id}">
                    <input type="hidden" id="gid" name="gid" value="{$gid}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{if $gid neq ''}
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
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>

                                {foreach $recent_transaction as $t}
                                    <tr>
                                        <td data-value="{$t['invoice_id']}">
                                            {if $t['invoice_id'] eq '0' || $t['invoice_id'] eq ''}
                                                -
                                            {else}
                                                <a href="{$invoice_url[$t['id']]}" >{$t['invoice_id']}</a>
                                            {/if}

                                        </td>
                                        <td data-value="{strtotime($t['createdon'])}">
                                            {date( $config['df'], strtotime($t['createdon']))}
                                        </td>
                                        <td data-value="{$t['page_title']}">
                                           {$t['page_title']}
                                        </td>
                                        <td data-value="{$t['customer_name']}">
                                            <a href="{$account_url[$t['id']]}">{$t['customer_name']}</a>
                                        </td>
                                        <td data-value="{$t['invoice_amount']}" class="amount" data-a-sign="{$config['currency_code']} ">{$t['invoice_amount']}</td>
                                        <td data-value="{$t['invoice_status']}">
                                            <a href="{$invoice_url[$t['id']]}" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                                <i class="fa fa-file-text-o"></i>
                                            </a>
                                            {if $t['invoice_status'] eq 'Paid'}
                                                <div class="label-success" style="display:inline-block; margin:0 auto; font-size:85%; width:85px">Paid</div>
                                            {elseif $t['invoice_status'] eq 'Unpaid'}
                                                <div class="label-danger" style="display:inline-block; margin:0 auto; font-size:85%; width:85px">Unpaid</div>
                                            {elseif $t['invoice_status'] eq 'Partially Paid'}
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
{/if}

{/block}
{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/list_voucher_pages.js"></script>
{/block}