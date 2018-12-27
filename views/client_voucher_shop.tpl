
{extends file="$layouts_client"}

{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3>
                        Country
                    </h3>
                </div>
                <div class="ibox-content" id="ibox_form">
                    <div class="row">
                    {foreach $country_list as $c}
                        <div class="col-md-2">
                            <img src="{$baseUrl}/apps/voucher/public/flags/{$c['flag_img']}" id="cid{$c['id']}" class="country" style="border:1px solid darkgray" width="100%" >
                        </div>
                    {/foreach}
                    </div>
                </div>

                {if $voucher_formats neq null}
                <div class="ibox-title">
                    <h5>
                       Category
                    </h5>
                </div>
                <div class="ibox-content" id="ajaxrender_category">
                    <div class="row">
                    {foreach $voucher_formats as $v}
                        <div class="col-md-2">
                            <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$v['voucher_img']}" id="vid{$v['id']}" class="voucher_format" style="border:1px solid darkgray" width="100%" >
                            <div style="text-align: center; margin-top: 10px">
                                <span class="amount" style="color:dimgray; text-decoration: line-through">{$v['cost_price']}</span>
                                &nbsp;&nbsp;
                                <span class="amount" style="color:black">{$v['sales_price']}</span>
                            </div>
                            <div style="text-align: center; margin-top: 10px">
                                <button class="btn btn-primary" type="submit" id="submit"><i class="fa fa-shopping-cart"></i> Buy Now </button>
                            </div>
                        </div>

                    {/foreach}
                    </div>
                </div>
                {/if}


                {if $voucher_pages neq null}
                <div class="ibox-title">
                    <h5>
                       Page
                    </h5>
                </div>
                <div class="ibox-content" id="ajaxrender_page">
                    <div class="row">
                    {foreach $voucher_pages as $p}
                        <div class="col-md-2">
                            <img src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$p['front_img']}" id="pid{$p['id']}" class="voucher_page" style="border:1px solid darkgray" width="100%" >
                        </div>
                    {/foreach}
                    </div>
                </div>
                {/if}

                <input type="hidden" id="country_id" name="country_id" value="{$country_id}">
                <input type="hidden" id="voucher_id" name="voucher_id" value="{$voucher_id}">
            </div>
        </div>
    </div>


{/block}

{block name=script}
    <script>
        $(function() {

            var _url = $("#_url").val();
            $.fn.modal.defaults.width = '700px';
            var $modal = $('#ajax-modal');


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

            $('.country').on('click', function(e){
                e.preventDefault();
                var id = this.id;
                window.location.href = _url+"voucher/client/vouchershop/"+id;
            });

            $('.voucher_format').on('click', function(e){
                e.preventDefault();
                var id = this.id;
                var country_id = $('#country_id').val();
                window.location.href = _url+"voucher/client/vouchershop/"+country_id+"/"+id;
            });

            $('.voucher_page').on('click', function(e) {
                e.preventDefault();
                var id = this.id;
                $('body').modalmanager('loading');

                $modal.load(_url + 'voucher/client/modal_page/'+id, '', function () {

                    $modal.modal();

                });

            });

        });

    </script>

{/block}