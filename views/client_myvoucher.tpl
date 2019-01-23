
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
                <span class="alert"><a href="{$active_invoice_url}"><button class="btn btn-xs btn-warning" style="background-color:#f1c40f; width: 85px ">Activate</button></a></span>
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
                                <input type="text" name="name" id="global_filter" class="form-control global_filter" placeholder="{$_L['Search']}..." />

                            </div>
                        </div>

                    </div>
                </form>

                <table class="table table-bordered table-hover sys_table default" id="vouchers" data-filter="#foo_filter">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Country</th>
                        <th>Image</th>
                        <th>Type</th>
                        <th>Expiry</th>
                        <th>Serial No.</th>
                        <th>Redeem(Balance)</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach $voucher_data as $key=>$v}
                        <tr>
                            <td data-value="{$v['id']}">
                               {$total_vouchers - $key}
                            </td>

                            <td data-value="{strtotime($v['date'])}">
                                {if $v['date'] neq '0000-00-00'}
                                    {date( $config['df'], strtotime($v['date']))}
                                {else}
                                    -
                                {/if}
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
                                {if $v['expiry_date'] neq '0000-00-00'}
                                    <span {if $voucher_status[$v['id']] eq 'Expired'} style="color:red" {elseif $voucher_status[$v['id']] eq 'Limit' } style = "color:orange"{/if}>
                                        {date( $config['df'], strtotime($v['expiry_date']))}
                                    </span>
                                {else}
                                    -
                                {/if}
                            </td>

                            <td data-value="{$v['prefix']}{$v['serial_number']}">
                                {if $setting['set_status_manually'] eq '0'}
                                    {if $voucher_status[$v['id']] eq 'Active' && $v['invoice_status'] eq 'Paid'}
                                        <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}" class="view_voucherpage">{$v['prefix']}{$v['serial_number']}</a>
                                    {else}
                                        {$v['prefix']}{$v['serial_number']}
                                    {/if}
                                {else}
                                    {if $voucher_status[$v['id']] eq 'Active'}
                                        <a href="{{$_url}}voucher/client/voucher_page/{$v['id']}" class="view_voucherpage">{$v['prefix']}{$v['serial_number']}</a>
                                    {else}
                                        {$v['prefix']}{$v['serial_number']}
                                    {/if}
                                {/if}
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
                                {elseif $voucher_status[$v['id']] eq 'Processing'}
                                    <div class="label-default" style="margin:0 auto;font-size:85%;width:65px;">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {elseif $voucher_status[$v['id']] eq 'Cancelled'}
                                    <div class="label-default" style="margin:0 auto;font-size:85%;width:65px;">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {else}
                                    <div class="label-warning" style="margin:0 auto;font-size:85%;width:65px;">
                                        {$voucher_status[$v['id']]}
                                    </div>
                                {/if} 
                            </td>
                            <td>
                                <a href="{{$_url}}voucher/client/download_generated_voucher/{$v['id']}" class="btn btn-primary btn-xs" style="background-color: #92278F; border-color:#92278F" id="vid{$v['id']}"><i class="fa fa-file-pdf-o"></i> </a>
                            </td>
                        </tr>
                    {/foreach}

                    </tbody>
                        {*<tfoot>*}
                        {*<tr>*}
                            {*<td style="text-align: right;" colspan="11">*}
                                {*<ul class="pagination">*}
                                {*</ul>*}
                            {*</td>*}
                        {*</tr>*}
                        {*</tfoot>*}
                </table>
            </div>

            <div class="ibox-title">
                Recent Transaction
            </div>
            <div class="ibox-content">
                <table class="table table-bordered table-hover sys-table" id="recent_transactions">
                    <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Activation Date</th>
                        <th>Account</th>
                        <th>Country (Type)</th>
                        <th>Serial No.</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        {foreach $recent_transaction as $key=>$t}
                            <tr>
                                <td data-value="{$t['invoice_id']}">
                                    {$t['invoice_id']}
                                </td>

                                <td data-value="{strtotime($t['date'])}">
                                    {date( $config['df'], strtotime($t['date']))}
                                </td>

                                <td data-value="{$t['account']}">
                                    {$t['account']}
                                </td>

                                <td data-value="{$t['country_name']}({$t['category']})">
                                    {$t['country_name']} ({$t['category']})
                                </td>

                                <td data-value="{$t['prefix']}{$t['serial_number']}">
                                    {if $t['invoice_status'] eq 'Paid'}
                                        <a href="{{$_url}}voucher/client/voucher_page/{$t['id']}" class="view_voucherpage">{$t['prefix']}{$t['serial_number']}</a>
                                    {else}
                                        {$t['prefix']}{$t['serial_number']}
                                    {/if}
                                </td>

                                <td data-value="{$t['invoice_amount']}" class="amount" data-a-sign="{$config['currency_code']} ">{$t['invoice_amount']}</td>

                                <td class="text-center" data-value="{$t['invoice_status']}">
                                    <a href="{$invoice_url[$t['id']]}" style="" class="btn btn-primary btn-xs view_invoice" id="{$v['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['View']}">
                                        <i class="fa fa-file-text-o"></i>
                                    </a>
                                    {if $t['invoice_status'] eq 'Paid'}
                                        <div class="label-success" style="display:inline-block; margin:0 auto;font-size:100%;width:65px">
                                            {$t['invoice_status']}
                                        </div>
                                    {elseif $t['invoice_status'] eq 'Unpaid'}
                                        <div class="label-danger" style="display:inline-block;color:#ff2222;margin:0 auto; font-size:100%;width:65px">
                                            {$t['invoice_status']}
                                        </div>
                                    {else}
                                        <div class="label-warning" style="display:inline-block;margin:0 auto; font-size:85%;width:100px;">
                                            {$t['invoice_status']}
                                        </div>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                    {*<tfoot>*}
                    {*<tr>*}
                        {*<td style="text-align: right;" colspan="11">*}
                            {*<ul class="pagination">*}
                            {*</ul>*}
                        {*</td>*}
                    {*</tr>*}
                    {*</tfoot>*}
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

            $.fn.dataTable.ext.classes.sPageButton = 'button button-primary';
            $.fn.DataTable.ext.pager.numbers_length = 5;

            $('#vouchers').DataTable({
                "processing": false,
                "paging": true,
                "pageLength": 10,
                // "bFilter": false,
                "bInfo": false,
                "bLengthChange": false,
                "order": [[0, "asc"]],
                "pagingType": "simple_numbers",
                "responsive": false,
                "dom": 'tp'
            });

            $('#recent_transactions').DataTable({
                "processing": false,
                "paging": true,
                "pageLength": 10,
                "bFilter": false,
                "bInfo": false,
                "bLengthChange": false,
                "order": [[0, "asc"]],
                "pagingType": "simple_numbers",
                "responsive": false,
            });


            $('input.global_filter').on( 'keyup click', function(){
                filterGlobal();
            });

            function filterGlobal () {
                $('#vouchers').DataTable().search(
                       $('#global_filter').val()
                 ).draw();
            }

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