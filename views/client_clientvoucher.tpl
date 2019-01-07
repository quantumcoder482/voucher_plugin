
{extends file="$layouts_client"}

{block name="style"}
  <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
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
                </form>

                <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="10">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Country</th>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Expiry</th>
                        <th>Prefix + Serial No.</th>
                        <th>Redeem(Balance)</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $voucher_data as $key=>$v}
                        <tr>
                            <td data-value="{$v['id']}">
                               {$total_vouchers - $key}
                            </td>

                            <td data-value="{strtotime($v['date'])}">
                                {date( $config['df'], strtotime($v['date']))}
                            </td>

                            <td data-value="{$v['customer']}">
                                {if $v['customer'] == ''}
                                    -
                                {else}
                                    {$v['customer']}
                                {/if}
                            </td>

                            <td data-value="{$v['country_name']}" id="{$v['id']}">
                                {if $voucher_status[$v['id']] eq 'Redeem'}
                                    <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}/view" class="view_voucherpage">{$v['country_name']}</a>
                                {else}
                                    {$v['country_name']}
                                {/if}
                            </td>

                            <td class="text-center" data-value="{$v['voucher_img']}" id="{$v['id']}" >
                                {if $voucher_status[$v['id']] eq 'Redeem'}
                                    <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}/view" class="view_voucherpage">
                                        <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['voucher_img']}" width="40px" />
                                    </a>
                                {else}
                                    <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['voucher_img']}" width="40px" />
                                {/if}

                            </td>

                            <td data-value="{$v['category']}" id="{$v['id']}">
                                {$v['category']}
                            </td>

                            <td data-value="{strtotime($v['expiry_date'])}" >
                                <span {if $voucher_status[$v['id']] eq 'Expired'} style="color:red" {elseif $voucher_status[$v['id']] eq 'Limit' } style = "color:orange"{/if}> 
                                    {date( $config['df'], strtotime($v['expiry_date']))}
                                </span>
                            </td>

                            <td data-value="{$v['prefix']} {$v['serial_number']}">
                                {$v['prefix']} {$v['serial_number']}
                            </td>   

                            <td>
                               {$voucher_pages[$v['id']]} <span style="color:orange">({$voucher_pages[$v['id']]-$redeem_pages[$v['id']]})</span>
                            </td>

                            <td class="text-center" data-value="{$voucher_status[$v['id']]}">
                                {if $voucher_status[$v['id']] eq 'Redeem'}
                                    <div class="label-success" style="margin:0 auto;font-size:85%;width:65px">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {elseif $voucher_status[$v['id']] eq 'Expired'}
                                    <div class="label-danger" style="color:#ff2222;margin:0 auto;font-size:85%;width:65px">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {elseif $voucher_status[$v['id']] eq 'UnRedeem'}
                                    <div class="label-default" style="margin:0 auto;font-size:85%;width:65px;">
                                        {$voucher_status[$v['id']]}
                                    </div>
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
   
{/block}

{block name=script}
    <script>

        $(function() {

            $('.footable').footable();
            var _url = $('#_url').val();

            $.fn.modal.defaults.width = '800px';
            $('[data-toggle="tooltip"]').tooltip();
            var $modal = $('#ajax-modal');

            $('.amount').autoNumeric('init', {

                aSign: '{$config['currency_code']} ',
                dGroup: {$config['thousand_separator_placement']},
                aPad: {$config['currency_decimal_digits']},
                pSign: '{$config['currency_symbol_position']}',
                aDec: '{$config['dec_point']}',
                aSep: '{$config['thousands_sep']}',
                vMax: '9999999999999999.00',
                vMin: '-9999999999999999.00'

            });


        });

    </script>

{/block}