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
                                    2
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
                                    10
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
                                    3
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
                                    115
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

                                <td data-value="{strtotime($p['date'])}">
                                    {date( $config['df'], strtotime($p['date']))}
                                </td>

                                <td data-value="{$p['customer']}">
                                    {$p['customer']}
                                </td>

                                <td data-value="{$p['country_name']}" id="{$p['id']}">
                                    <a href="#">{$p['country_name']}</a>
                                </td>

                                <td data-value="{$p['category']}">
                                    {$p['category']}
                                </td>

                                <td data-value="{$p['title']}">
                                    &nbsp;{$p['title']}
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="6">
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
                                    {$v['customer']}
                                </td>

                                <td data-value="{$v['country_name']}" id="{$v['id']}">
                                    <a href="#">{$v['country_name']}</a>
                                </td>

                                <td data-value="{$v['category']}">
                                    {$v['category']}
                                </td>

                                <td data-value="{strtotime($v['expiry_date'])}">
                                    {date( $config['df'], strtotime($v['expiry_date']))}
                                </td>

                                <td data-value="{$v['serial_number']}">
                                    &nbsp;{$v['prefix']} {$v['serial_number']}
                                </td>

                            </tr>
                        {/foreach}

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right;" colspan="6">
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
                                &nbsp; &nbsp; Unpaid(1)
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-danger" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 30%" role="progressbar"> <span class="sr-only">30%</span> </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:15%; height:40px; text-align: left;">
                                &nbsp; &nbsp; Partially Unpaid(0)
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-inverse" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width:15%" role="progressbar"> <span class="sr-only">15%</span> </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:15%; height:40px; text-align: left;">
                                &nbsp; &nbsp; Paid(0)
                            </td>
                            <td>
                                <div class="progress progress-small mt-10" style="margin-left: 10px; margin-right: 10px">
                                    <div class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width:55%" role="progressbar"> <span class="sr-only">55%</span> </div>
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
                                    {$r['account']}
                                </td>

                                <td data-value="{$r['amount']}" class="amount" data-a-sign="{$config['currency_code']} ">
                                    {$r['amount']}
                                </td>

                                <td data-value="{strtotime($r['expiry_date'])}">
                                    {date( $config['df'], strtotime($r['expiry_date']))}
                                </td>

                                <td data-value="{$r['status']}">
                                    &nbsp;{$r['status']}
                                </td>

                            </tr>
                        {/foreach}
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