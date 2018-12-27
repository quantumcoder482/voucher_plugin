
{extends file="$layouts_client"}

{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}

{block name="content"}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Voucher Pages</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$voucher_img}" width="450px" />
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

                            {foreach $voucher_pages as $v}
                                <tr>
                                    <td data-value="{$v['id']}">
                                        {$v['id']}
                                    </td>

                                    <td data-value="{$v['front_img']}" alt="voucher front image">

                                        {if $v['front_img'] eq ''}
                                            <img src="{$baseUrl}/apps/voucher/views/img/item_placeholder.png" width="40px" />
                                        {else}
                                            <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['front_img']}" width="40px" />
                                        {/if}

                                    </td>

                                    <td data-value="{$v['title']}" id="{$v['id']}">
                                        <a href="#">{$v['title']}</a>
                                    </td>

                                    <td data-value="{$v['description']}">
                                        {$v['description']}
                                    </td>

                                    <td data-value="{$page_status[$v['id']]}">
                                        {if $page_status[$v['id']] neq 'redeem'}
                                            <span style="color: #2bb673;"><i class="fa fa-check"></i> </span>
                                        {/if}
                                    </td>

                                    <td class="text-center">
                                        {if $page_status[$v['id']] eq 'redeem' || $page_status[$v['id']] eq ''}
                                            <a href="{$_url}voucher/client/redeem_voucher_page/{$voucher_id}/{$v['id']}" class="btn btn-xs square-redeem" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Redeem">
                                                Redeem
                                            </a>
                                        {elseif $page_status[$v['id']] eq 'confirm'}
                                            <a href="#" class="btn btn-xs square-active" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Confirm">
                                                Confirm
                                            </a>
                                        {elseif $page_status[$v['id']] eq 'pending'}
                                            <a href="#" class="btn btn-xs square-deactive" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="Pending">
                                                Pending
                                            </a>
                                            {if $setting['voucher_edit_enable'] neq 1}
                                                <a href="#" class="btn btn-info btn-xs edit_redeem" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            {/if}
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
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/list_voucher_pages.js"></script>

    <script>

        $(document).ready(function(){

        });

    </script>
{/block}