
{extends file="$layouts_client"}

{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div style="text-align: center">
                                <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$page_setting['front_img']}" width="650x" />
                            </div>
                            <div style="text-align: center">
                                <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$page_setting['back_img']}" width="650px" />
                            </div>

                        </div>
                        <div class="col-md-6 ib_right_panel">
                            <div>
                                <h2>
                                    {$page_setting['title']}
                                </h2>
                            </div>

                            <form class="form-horizontal" id="frm_redeem">

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="voucher_number">Voucher No.</label>
                                    <div class="col-md-10">
                                        <input type="text" name="voucher_number" class="form-control" id="voucher_number" value="{$voucher_info['prefix']} {$voucher_info['serial_number']}" disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="country_name">Country</label>
                                    <div class="col-md-4">
                                        <input type="text" name="country_name" class="form-control" id="country_name" value="{$voucher_info['country_name']}" disabled>
                                    </div>

                                    <label class="col-md-2 control-label" for="category">Category</label>
                                    <div class="col-md-4">
                                        <input type="text" name="category" class="form-control" id="category" value="{$voucher_info['category']}" disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="customer_name">Name</label>
                                    <div class="col-md-10">
                                        <input type="text" name="customer_name" class="form-control" id="customer_name" value="{$customer_data['account']}" disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="customer_address">Address</label>
                                    <div class="col-md-10">
                                        <textarea id="customer_address" name="customer_address" class="form-control" rows="3">{$customer_addr}</textarea>
                                    </div>
                                    <span class="col-md-offset-3 help-block">    Your address is editable from your profile page.</span>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="departure_date">Departure Date</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" value=" " name="departure_date" id="departure_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                                    </div>

                                    <label class="col-md-2 control-label" for="category">Return Date</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" value=" " name="return_date" id="return_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="customer_name">Total Days</label>
                                    <div class="col-md-4">
                                        <input type="text" name="total_days" class="form-control" id="total_days" value=" " disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="remark">Remark</label>
                                    <div class="col-md-10">
                                        <textarea id="remark" name="remark" class="form-control" rows="3">
                                        </textarea>
                                    </div>
                                </div>

                                {foreach $fs as $f}

                                    <div class="form-group">
                                        <label class="col-lg-2 control-label" for="cf{$f['id']}">{$f['fieldname']}</label>
                                        {if ($f['fieldtype']) eq 'text'}


                                            <div class="col-lg-10">
                                                <input type="text" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control"> {if ($f['description']) neq ''}
                                                    <span class="help-block">{$f['description']}</span>
                                                {/if}

                                            </div>

                                        {elseif ($f['fieldtype']) eq 'password'}

                                            <div class="col-lg-10">
                                                <input type="password" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control"> {if ($f['description']) neq ''}
                                                    <span class="help-block">{$f['description']}</span>
                                                {/if}
                                            </div>

                                        {elseif ($f['fieldtype']) eq 'dropdown'}
                                            <div class="col-lg-10">
                                                <select id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" style="width:100%">
                                                    <option value=""></option>
                                                    {foreach explode(',',$f['fieldoptions']) as $fo}
                                                        <option value="{$fo}">{$fo}</option>
                                                    {/foreach}
                                                </select>
                                                {if ($f['description']) neq ''}
                                                    <span class="help-block">{$f['description']}</span>
                                                {/if}
                                            </div>


                                        {elseif ($f['fieldtype']) eq 'textarea'}

                                            <div class="col-lg-10">
                                                <textarea id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" rows="3"></textarea> {if ($f['description']) neq ''}
                                                    <span class="help-block">{$f['description']}</span>
                                                {/if}
                                            </div>

                                        {elseif ($f['fieldtype']) eq 'date'}

                                            <div class="col-lg-10">
                                                <input type="text" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true"> {if ($f['description']) neq ''}
                                                    <span class="help-block">{$f['description']}</span>
                                                {/if}
                                            </div>
                                        {else} {/if}
                                    </div>
                                {/foreach}


                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-md-4" style="text-align: center">
                                        <span style="font-size: 12pt; font-weight: 600">{$product_data['name']}</span>
                                    </div>
                                    <div class="col-md-8" style="text-align: right">
                                        <span class="amount" style="font-weight: 600" autocomplete="off" data-a-sign="{$config['currency_code']} " data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">{$product_data['sales_price']}</span>
                                    </div>
                                </div>

                                <input type="hidden" id="voucher_id" name="voucher_id" value="{$voucher_id}">
                                <input type="hidden" id="page_id" name="page_id" value="{$page_id}">


                                <div class="hr-line-dashed"></div>

                                <div class="form-group">
                                    <div class="col-md-offset-10 col-md-2" style="text-align: right">
                                        <button type="submit" id="redeem_submit" class="btn btn-primary">Redeem</button>
                                    </div>
                                </div>

                                <br>
                            </form>

                        </div>
                    </div>


                </div>
            </div>

        </div>


    </div> <!-- Row end-->


{/block}

{block name=script}
    <script>
        $(function() {

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


            $('#redeem_submit').click(function(e) {
                e.preventDefault();



            })''

        });

    </script>

{/block}