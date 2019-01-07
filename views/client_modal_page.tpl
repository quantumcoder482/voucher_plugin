{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
       {$page_data['title']}
    </h3>
</div>

<div class="modal-body">

    <div class="row">
        <div class="col-md-12">
            <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$page_data['front_img']}" style="border:1px solid darkgray" width="650px" >
        </div>
        <div class="col-md-12">
            <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$page_data['back_img']}" style="border:1px solid darkgray" width="650px" >
        </div>
    </div> <!-- Row end-->

</div>

<div class="modal-footer">
    <span>{$page_data['country_name']} ({$page_data['category']}) Price: </span>
    <span class="amount" data-a-sign="{$config['currency_code']}">{$page_data['sales_price']} </span>&nbsp;&nbsp;
    <button type="submit" class="btn btn-primary buy_now" id="modal_submit"> <i class="fa fa-shopping-cart"></i> Buy now </button>
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
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


        });

    </script>

{/block}