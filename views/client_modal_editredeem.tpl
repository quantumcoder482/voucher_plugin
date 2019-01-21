{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
       {$t_data['page_title']}
    </h3>
</div>

<div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" id="frm_redeem">

                <div class="form-group">
                    <label class="col-md-2 control-label" for="voucher_number">Voucher No.</label>
                    <div class="col-md-10">
                        <input type="text" name="voucher_number" class="form-control" id="voucher_number" value="{$t_data['voucher_number']}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label" for="country_name">Country</label>
                    <div class="col-md-4">
                        <input type="text" name="country_name" class="form-control" id="country_name" value="{$t_data['country_name']}" disabled>
                    </div>

                    <label class="col-md-2 control-label" for="category">Category</label>
                    <div class="col-md-4">
                        <input type="text" name="category" class="form-control" id="category" value="{$t_data['category']}" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label" for="customer_name">Name</label>
                    <div class="col-md-10">
                        <input type="text" name="customer_name" class="form-control" id="customer_name" value="{$t_data['customer_name']}" disabled>
                    </div>
                </div>

                {if $page_setting['address'] eq '1'}
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="customer_address">Address</label>
                        <div class="col-md-10">
                            <textarea id="customer_address" name="customer_address" class="form-control" rows="3" disabled>{$t_data['customer_address']}</textarea>
                        </div>
                        <span class="col-md-offset-2 help-block" style="padding-left: 30px">Your address is editable from your profile page.</span>
                    </div>
                {/if}

                {if $page_setting['date_range'] eq '1'}
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="departure_date">Departure Date</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control datepicker" value="{$t_data['departure_date']}" name="departure_date" id="departure_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                        </div>

                        <label class="col-md-2 control-label" for="category">Return Date</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control datepicker" value="{$t_data['return_date']}" name="return_date" id="return_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="total_days">Total Days</label>
                        <div class="col-md-4">
                            <input type="text" name="total_days" class="form-control" id="total_days" value="{$t_data['total_days']}" disabled>
                        </div>
                    </div>
                {/if}
                <div class="form-group">
                    <label class="col-md-2 control-label" for="remark">Remark</label>
                    <div class="col-md-10">
                        <textarea id="remark" name="remark" class="form-control" rows="3">{$t_data['remark']}</textarea>
                    </div>
                </div>

                {* if isset($customfield) *}
                {foreach $fs as $f}

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="cf{$f['id']}">{$f['fieldname']}</label>
                        {if ($f['fieldtype']) eq 'text'}


                            <div class="col-md-10">
                                <input type="text" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" value={$cf_value[$f['id']]}> {if ($f['description']) neq ''}
                                    <span class="help-block">{$f['description']}</span>
                                {/if}

                            </div>

                        {elseif ($f['fieldtype']) eq 'password'}

                            <div class="col-md-10">
                                <input type="password" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control"> {if ($f['description']) neq ''}
                                    <span class="help-block">{$f['description']}</span>
                                {/if}
                            </div>

                        {elseif ($f['fieldtype']) eq 'dropdown'}
                            <div class="col-md-10">
                                <select id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" style="width:100%">
                                    {if ($cf_value[$f['id']])}
                                        <option value="{$cf_value[$f['id']]}" selected>{$cf_value[$f['id']]}</option>
                                    {else}
                                        <option value=""></option>
                                    {/if}
                                    {foreach explode(',',$f['fieldoptions']) as $fo}
                                        <option value="{$fo}">{$fo}</option>
                                    {/foreach}
                                </select>
                                {if ($f['description']) neq ''}
                                    <span class="help-block">{$f['description']}</span>
                                {/if}
                            </div>


                        {elseif ($f['fieldtype']) eq 'textarea'}

                            <div class="col-md-10">
                                <textarea id="cf{$f['id']}" name="cf{$f['id']}" class="form-control" rows="3">{$cf_value[$f['id']]}</textarea> {if ($f['description']) neq ''}
                                    <span class="help-block">{$f['description']}</span>
                                {/if}
                            </div>

                        {elseif ($f['fieldtype']) eq 'date'}

                            <div class="col-md-10">
                                <input type="text" id="cf{$f['id']}" name="cf{$f['id']}" class="form-control datepicker" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" value="{$cf_value[$f['id']]}"> {if ($f['description']) neq ''}
                                    <span class="help-block">{$f['description']}</span>
                                {/if}
                            </div>
                        {else} {/if}
                    </div>
                {/foreach}
                {*/if*}

                {if $page_setting['payment_req'] eq '1' && $t_data['product_name'] neq ''}
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-6" style="text-align: left">
                        <span style="font-size: 12pt;">{$t_data['product_name']}</span>
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" id="product_quantity" name="product_quantity" class="form-control" value="{$t_data['product_quantity']}">
                        <span class="help-block">
                            &nbsp;&nbsp;&nbsp;&nbsp;Quantity
                        </span>
                    </div>
                    <div class="col-md-2" style="text-align: right">
                        <span class="amount product_price" style="font-weight: 600" autocomplete="off" data-a-sign="{$config['currency_code']} " data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">{$t_data['product_price']}</span>
                    </div>
                </div>
                {/if}

                {if $page_setting['payment_req'] eq '1' && $t_data['sub_product_name'] neq ''}
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-md-2" style="text-align: right">
                        <input type="checkbox" class="i-checks" id="sub_product_req" name="sub_product_req" value="1" {if $t_data['sub_product_req'] eq '1'}checked{/if}>
                    </div>
                    <div class="col-md-6" style="text-align: left">
                        <span style="font-size: 12pt;">{$t_data['sub_product_name']}</span>
                    </div>
                    <div class="col-md-2">
                        <input type="number" min="1" id="sub_product_quantity" name="sub_product_quantity" class="form-control" value="{$t_data['sub_product_quantity']}" >
                        <span class="help-block">
                            &nbsp;&nbsp;&nbsp;&nbsp;Quantity
                        </span>
                    </div>
                    <div class="col-md-2" style="text-align: right">
                        <span class="amount sub_product_price" style="font-weight: 600" autocomplete="off" data-a-sign="{$config['currency_code']} " data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">{$t_data['sub_product_price']}</span>
                    </div>
                </div>
                {/if}

                {if $page_setting['payment_req'] eq '1' && $t_data['product_name'] neq ''}
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-md-10" style="text-align: right">
                        <span style="font-size: 12pt; font-weight: 600">Total :</span>
                    </div>
                    <div class="col-md-2" style="text-align: right">
                        <span class="amount total_price" style="font-weight: 600" autocomplete="off" data-a-sign="{$config['currency_code']} " data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">{if $t_data['sub_product_req'] eq '1'}{$t_data['product_price']+$t_data['sub_product_price']}{else}{$t_data['product_price']}{/if}</span>
                    </div>
                </div>
                {/if}

                <input type="hidden" id="tid" name="tid" value="{$t_id}">
                <input type="hidden" id="voucher_id" name="voucher_id" value="{$t_data['voucher_id']}">
                <input type="hidden" id="page_id" name="page_id" value="{$t_data['page_id']}">
                <input type="hidden" id="page_title" name="page_title" value="{$t_data['page_title']}">
                <input type="hidden" id="product_name" name="product_name" value="{$t_data['product_name']}">
                <input type="hidden" id="product_price" name="product_price" value="{$t_data['product_price']}">
                <input type="hidden" id="sub_product_name" name="sub_product_name" value="{$t_data['sub_product_name']}">
                <input type="hidden" id="sub_product_price" name="sub_product_price" value="{$t_data['sub_product_price']}">
                <input type="hidden" id="invoice_id" name="invoice_id" value="{$t_data['invoice_id']}">
                <input type="hidden" id="currency_code" name="currency_code" value="{$config['currency_code']}">


                <br>
            </form>
        </div>
    </div> <!-- Row end-->

</div>

<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>

    {if $view_type neq 'view'}
    <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">
        <i class="fa fa-check"></i> {$_L['Update']}</button>
    {/if}
</div>


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

            $('.datepicker').datepicker();

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue'
            });


            var product_price;
            var sub_product_price;

            var change_prices = function(){
                product_price = parseFloat($('#product_price').val()* $('#product_quantity').val()+0.00);
                sub_product_price = parseFloat($('#sub_product_price').val() * $('#sub_product_quantity').val()+0.00);

                $('.product_price').html($('#currency_code').val() + ' ' + product_price);
                $('.sub_product_price').html($('#currency_code').val() + ' ' + sub_product_price);

                var isChecked = $("#sub_product_req").prop("checked");

                if(isChecked == true){
                    var total_price = product_price + sub_product_price;
                }else{
                    var total_price = product_price;
                }

                // total_price = total_price.toLocaleString();

                var total_price = $('#currency_code').val() + ' ' + total_price;
                $('.total_price').html(total_price);

            };

            change_prices();

            $('#product_quantity').on('change', function(){
                change_prices();
            });

            $('#sub_product_quantity').on('change', function(){
                change_prices();
            });


            $('#sub_product_req').on('ifChanged', function(e) {
                e.preventDefault();

                var isChecked = e.currentTarget.checked;

                if(isChecked == true){
                    var total_price = product_price + sub_product_price;
                }else{
                    var total_price = product_price;
                }

                // total_price = total_price.toLocaleString();
                var total_price = $('#currency_code').val() + ' ' + total_price;
                $('.total_price').html(total_price);

            });


            var calc_totaldays = function(){
                var departure_date = $('#departure_date').val();
                var return_date = $('#return_date').val();
                var total_days = '';

                if(departure_date != '' && return_date != '' ){
                    departure_date = departure_date.split('-');
                    departure_date = new Date(departure_date[0], departure_date[1]-1, departure_date[2]);

                    return_date = return_date.split('-');
                    return_date = new Date(return_date[0], return_date[1]-1, return_date[2]);

                    if(return_date>departure_date){
                        total_days = (return_date-departure_date)/(1000*60*60*24);
                    }
                    $('#total_days').val(total_days);
                }

            }

            $('#departure_date').on('change', function(e) {
                e.preventDefault();
                var departure_date = $('#departure_date').val();
                departure_date = departure_date.split('-');

                var min_date = new Date(departure_date[0], departure_date[1]-1, departure_date[2]);

                calc_totaldays();

            });

            $('#return_date').on('change', function(e) {
                e.preventDefault();
                calc_totaldays();
            });


        });

    </script>

{/block}