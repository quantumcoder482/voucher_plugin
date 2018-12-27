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

                {if $view_type == 'filter'}
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
                {/if}

                <table class="table table-bordered table-hover sys_table footable" {if $view_type=='filter' } data-filter="#foo_filter" data-page-size="20"{/if}>
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
                        <th class="text-center" width="210px">Manage</th>
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

                            <td data-value="{$v['country_name']}" id="{$v['id']}">
                                <a href="#">{$v['country_name']}</a>
                            </td>

                            <td data-value="{$v['voucher_img']}" alt="{$v['voucher_img']}">

                                {if {$v['voucher_img']} eq ''}
                                    <img src="{$baseUrl}/apps/voucher/views/img/item_placeholder.png" width="40px" />
                                {else}
                                    <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['voucher_img']}" width="40px" />
                                {/if}

                            </td>

                            <td data-value="{$v['category']}" id="{$v['id']}">
                                <a href="#">{$v['category']}</a>
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

                    {if $view_type == 'filter'}
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="11">
                                <ul class="pagination">
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    {/if}

                </table>
                {$paginator['contents']}
            </div>
        </div>
    </div>
</div>
{/block}
{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/list_voucher.js"></script>
{/block}