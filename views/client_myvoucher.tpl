
{extends file="$layouts_client"}

{block name="style"}
  <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

{if $setting['show_alert'] eq 1 && $active_status eq 'No'}
<div class="row">
    <div class="col-md-12">
        <div class="ibox-content round-alert">
            <p style="text-align:center">
                <span style="color:#ffff00;font-size:15pt"><i class="glyphicon glyphicon-warning-sign"></i></span>
                <span style="color:white;">{$setting['alert_msg']}</span>
                <span class="alert"><a href="{$active_invoice_url}">[Click Here]</a></span>
            </p>
        </div>
    </div>
</div>
{/if}

{if $setting['redeem'] eq 1 && $active_status eq 'Yes'}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content" style="text-center; background-color:#555555; color:white" >
               <form class="form-horizontal" id="rform">
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="serial_number">Redeem Voucher</small></label>
                        <div class="col-md-8" style="color:black">
                            <input type="text" id="serial_number" name="serial_number" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-md-2" >
                            <button class="btn btn-primary redeem_submit" type="button" id="redeem_submit">{$_L['Submit']}</button>
                        </div>
                        <span class="col-md-offset-2 col-md-8 help-block" style="font-size: x-small; color:white">Enter a valid voucher code from your voucher booklet. </span>
                    </div>
               </form>
            </div>
        </div>
    </div>
</div>
{else}
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content" style="text-center" >
               <form class="form-horizontal" id="">
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="serial_number">Redeem Voucher</small></label>
                        <div class="col-md-8">
                            <input type="text" id="serial_number" name="serial_number" class="form-control" disabled autocomplete="off">
                        </div>
                        <div class="col-md-2" >
                            <span class="btn btn-primary">{$_L['Submit']}</span>
                        </div>
                        <span class="col-md-offset-2 col-md-8 help-block" style="font-size: x-small">Enter a valid voucher code from your voucher booklet. </span>
                    </div>
               </form>
            </div>
        </div>
    </div>
</div>
{/if}

<input type="hidden" id="require_agree" name="require_agree" value="{$setting['require_agree']}">
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

                            <td data-value="{$v['country_name']}" id="{$v['id']}">
                                {if $voucher_status[$v['id']] eq 'Active'}
                                    <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}" class="view_voucherpage">{$v['country_name']}</a>
                                {else}
                                    {$v['country_name']}
                                {/if}
                            </td>

                            <td class="text-center" data-value="{$v['voucher_img']}" id="{$v['id']}" >
                                {if $voucher_status[$v['id']] eq 'Active'}
                                    <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}" class="view_voucherpage">
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
                                {if $voucher_status[$v['id']] eq 'Active'}
                                    <div class="label-success" style="margin:0 auto;font-size:85%;width:65px">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {elseif $voucher_status[$v['id']] eq 'Expired'}
                                    <div class="label-danger" style="color:#ff2222;margin:0 auto;font-size:85%;width:65px">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {else}
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

            var require_agree = $('#require_agree').val();

            console.log(require_agree);

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

            $('.redeem_submit').on('click', function(e){
                e.preventDefault();

                if(require_agree != 1) {

                    $.post(_url + 'voucher/client/redeem_voucher', $("#rform").serialize())
                        .done(function (data) {
                            if (data == 'reload') {
                                window.location.reload();
                            }
                            else {
                                // window.open(data);
                                // toastr.error(data);
                                window.location.href = data;
                            }
                        });
                }else {
                    $('body').modalmanager('loading');

                    $modal.load(_url + 'voucher/client/confirm_redeem', '', function () {

                        $modal.modal();
                        $modal.css("width", "800px");
                        $modal.css("margin-left", "-349px");

                    });
                }

            });


            $modal.on('click', '.modal_submit', function (e) {

                e.preventDefault();

                if($('#modal_agree_check').prop('checked') ){

                    $modal.modal('loading');

                    $.post(_url + 'voucher/client/redeem_voucher', $("#rform").serialize())
                        .done(function (data) {
                            if (data == 'reload') {
                                window.location.reload();
                            }
                            else {

                                window.location.href = data;
                                // window.open(data);

                            }
                        });
                } else {
                    window.location.reload();
                }

            });
          

        });

    </script>

{/block}