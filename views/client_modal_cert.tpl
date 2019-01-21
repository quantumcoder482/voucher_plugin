{block name="style"}
    {*<link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />*}
{/block}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
       Checkout
    </h3>
</div>

<div class="modal-body">

    <div class="row">

        <div class="col-md-12">
            <form id="frm_voucher">
                <table id="cart_summary" class="table table-bordered stock-management-off">
                    <thead>
                    <tr>
                        <th width="120px;">Voucher</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th width="100px;">Quantity</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img class="img-responsive" src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$voucher['voucher_img']}">
                            </td>
                            <td>
                               <textarea id="description" name="description" rows="5" style="width: 100%; border-style: none; border-color: Transparent; overflow: auto;outline: none;"></textarea>
                            </td>
                            <td>
                                {ib_money_format($voucher['sales_price'],$config)}
                            </td>

                            <td class="cart_quantity text-center">

                                <input class="form-control" size="2" type="text" id="total_voucher" name="total_voucher" autocomplete="off"  value="1" disabled>
                                {*<div style="margin-top: 10px;">*}
                                    {*<a class="btn btn-primary btn-xs" href="#" id="add" > <span><i class="fa fa-plus"></i></span> </a>*}
                                    {*<a class="btn btn-danger btn-xs" href="#" id="deduct"> <span><i class="fa fa-minus"></i></span> </a>*}

                                {*</div>*}
                            </td>
                            <td> <span class="amount total_price" id="total_price" data-a-sign="{$config['currency_code']} ">{$voucher['sales_price']}</span> </td>

                        </tr>

                    </tbody>

                    <tfoot>

                    <tr class="cart_total_price">
                        <td rowspan="3" colspan="3" id="cart_voucher" class="cart_voucher">
                        </td>
                        <td class="text-right"><strong>Total</strong></td>
                        <td ><strong><span class="amount total_price" id="total_price" data-a-sign="{$config['currency_code']} ">{$voucher['sales_price']}</span></strong></td>
                    </tr>
                    </tfoot>

                </table>

                <input type="hidden" id="serial_number" name="serial_number" value="">
                <input type="hidden" id="vid" name="vid" value="{$voucher['id']}">
                <input type="hidden" id="sales_price" name="sales_price" value="{$voucher['sales_price']}">
                <input type="hidden" id="currency_code" name="currency_code" value="{$config['currency_code']}">
                <input type="hidden" id="template_id" name="template_id" value="{$voucher['template_id']}">

            </form>

                <p class="cart_navigation clearfix">
                    <a href="#" class="btn btn-primary pull-right checkout" title="Proceed to checkout">
                        <span><i class="fa fa-shopping-cart"></i> Process to checkout</span>
                    </a>
                </p>

        </div>

    </div>

</div>

<div class="modal-footer">
    <button type="button" id="btn_close" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
</div>


{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/voucher_codes.js"></script>

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


            // voucher code generate

            $('#total_voucher').val(1);

            var voucher_code_list = voucher_codes.generate({
                length: 11,
                count: 5000,
                charset: "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"
            });

            if($('#serial_number').val() == ''){
                $('#serial_number').val(voucher_code_list[0]);
            }


            function generate_voucher(){

                var voucher_num = Number($('#total_voucher').val());
                var price = Number($('#sales_price').val());
                var voucher_code = [];
                if(voucher_num == 1){
                    voucher_code = voucher_code_list[0];
                    $('#serial_number').val(voucher_code);
                }else{
                    for(var i=0;i<= voucher_num-1;i++){
                        voucher_code.push(voucher_code_list[i]);
                    }
                    $('#serial_number').val(voucher_code.join(','));
                }

                var total_price = voucher_num * price;
                total_price = total_price.toLocaleString();
                var str = $('#currency_code').val() + ' ' + total_price;
                $('.total_price').html(str);

                console.log($('#serial_number').val());
            }


            $('#add').on('click', function(e) {
                e.preventDefault();
                var total_voucher = Number($('#total_voucher').val());
                $('#total_voucher').val(total_voucher+1);
                generate_voucher();

            });

            $('#deduct').on('click', function(e){
                e.preventDefault();
                var total_voucher = Number($('#total_voucher').val());

                if(total_voucher > 1){
                    $('#total_voucher').val(total_voucher-1)
                }
                generate_voucher();
            });


        });

    </script>

{/block}