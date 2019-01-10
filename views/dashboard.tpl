{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-calculator"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    {$total_expired}
                                </div>
                                <div class="desc text-right"> Total Expired </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a class="dashboard-stat orange" href="#">
                            <div class="visual">
                                <i class="fa fa-calculator"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                   {$total_page_redeem}
                                </div>
                                <div class="desc text-right"> Total Page Redeem </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a class="dashboard-stat yellow" href="#">
                            <div class="visual">
                                <i class="fa fa-calculator"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    {$total_voucher_redeem}
                                </div>
                                <div class="desc text-right"> Total Voucher Redeem </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a class="dashboard-stat green" href="#">
                            <div class="visual">
                                <i class="fa fa-calculator"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    {$total_generated_voucher}
                                </div>
                                <div class="desc text-right"> Total Voucher Generated </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
     </div>

    <div class="row">
        <div class="col-md-6">
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
                                    &nbsp;<a href="{$_url}voucher/app/list_voucher_page/{$v['voucher_format_id']}/{$v['id']}/">{$v['prefix']}{$v['serial_number']}</a>
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: left;" colspan="6">
                                <ul class="pagination">
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
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
                                    &nbsp;<a href="{$_url}voucher/app/view_redeem_page/{$p['voucher_id']}/{$p['page_id']}/view/">{$p['page_title']}</a>
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: left;" colspan="6">
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
                    <div style="float: left">
                        <h4>Vouchers</h4>
                    </div>
                    <div class="ibox-tools">
                        <a href="{$base_url}voucher/app/list_voucher/" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> Vouchers</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table-bordered" width="100%">
                        <tr>
                            <td style="width:15%; height:40px; text-align: left;">
                                &nbsp; &nbsp; Unpaid({$count['unpaid']})
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-danger" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: {$percent['unpaid']}%" role="progressbar"> <span class="sr-only">{$percent['unpaid']}%</span> </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:15%; height:40px; text-align: left;">
                                &nbsp; &nbsp; Partially Paid({$count['partially']})
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-inverse" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:{$percent['partially']}%" role="progressbar"> <span class="sr-only">{$percent['partially']}%</span> </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:15%; height:40px; text-align: left;">
                                &nbsp; &nbsp; Paid({$count['paid']})
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-success" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:{$percent['paid']}%" role="progressbar"> <span class="sr-only">{$percent['paid']}%</span> </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="margin-left:20px">
                    Recent Vouchers
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered table-hover sys_table footable" data-page-size="10">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Account</th>
                                <th>Amount</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $recent_vouchers as $r}
                            <tr>
                                <td data-value="{$r['id']}">
                                    {$r['id']}
                                </td>

                                <td data-value="{$r['account']}">
                                    {if $r['account']}
                                        {$r['account']}
                                    {else}
                                        -
                                    {/if}
                                </td>

                                <td data-value="{$r['amount']}" class="amount" data-a-sign="{$config['currency_code']} ">{$r['amount']}</td>

                                <td data-value="{strtotime($r['expiry_date'])}">
                                    Actived from {date( $config['df'], strtotime($r['date']))} to {date( $config['df'], strtotime($r['expiry_date']))}
                                </td>

                                <td data-value="{$r['invoice_status']}">
                                    {if $r['invoice_status'] eq 'Paid'}
                                        <a href="{$_url}invoices/view/{$r['invoice_id']}/"><span class="btn btn-xs btn-success" style="width:85px">Paid</span></a>
                                    {elseif $r['invoice_status'] eq 'Unpaid' || $r['invoice_id'] eq '-1' || $r['invoice_id'] eq '0'}
                                        {if $r['invoice_id']}
                                            <a href="{$_url}invoices/view/{$r['invoice_id']}/"><span class="btn btn-xs btn-danger" style="width:85px">Unpaid</span></a>
                                        {else}
                                            <span class="btn btn-xs btn-danger" style="width:85px">Unpaid</span>
                                        {/if}
                                    {else}
                                        <a href="{$_url}invoices/view/{$r['invoice_id']}/"><span class="btn btn-xs btn-warning" style="width:85px">Partially Paid</span></a>
                                    {/if}
                                </td>

                            </tr>
                        {/foreach}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: left;" colspan="6">
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
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h4>Latest Income</h4>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered table-hover sys_table footable" data-page-size="10">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $latestincomes as $i}
                            <tr>
                                <td data-value="{$i['date']}">
                                    {$i['date']}
                                </td>

                                <td data-value="{$i['description']}">
                                    {$i['description']}
                                </td>

                                <td data-value="{$i['amount']}">
                                    {$i['amount']}
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="3">
                                <ul class="pagination">
                                </ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h4>Latest Expense</h4>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered table-hover sys_table footable" data-page-size="10">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $latestexpenses as $e}
                            <tr>
                                <td data-value="{$e['date']}">
                                    {$e['date']}
                                </td>

                                <td data-value="{$e['description']}">
                                    {$e['description']}
                                </td>

                                <td data-value="{$e['amount']}">
                                    {$e['amount']}
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="3">
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