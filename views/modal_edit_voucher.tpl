<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
       Edit Voucher
    </h3>
</div>

<div class="modal-body">

    <div class="row">
    
        <div class="col-md-7">
            <div class="ibox float-e-margins">

                <div class="ibox-content" id="ib_modal_form">
                    
                    <form class="form-horizontal" id="mrform">
    
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="country">{$_L['Country']} <small class="red">*</small></label>
    
                            <div class="col-md-8">
                                <select id="country" name="country" style="width:100%" class="form-control">
                                    <option value="{$voucher['country_id']}" selected>{$voucher['country_name']}</option>
                                    {*{foreach $vehicles as $vehicle}*}
                                    {*<option value="{$vehicle['country']}">{$vehicle['country']} - {$vehicle['vehicle_type']}</option>*}
                                    {*{/foreach}*}
                                </select>
                                
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="category">Category <small class="red">*</small></label>
    
                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="category" name="category" disabled>
                                    <option value="{$voucher['category']}" selected>{$voucher['category']}</option>
                                    {*<option value="Silver">Silver</option>*}
                                    {*<option value="Gold">Gold</option>*}
                                </select>
                                <span class="help-block"> </span>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="cost_price">Cost Price <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="text" id="cost_price" name="cost_price" class="form-control amount" value="{$voucher['cost_price']}" autocomplete="off" data-a-sign="{$config['currency_code']} " data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="sales_price">Sales Price <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="text" id="sales_price" name="sales_price" class="form-control amount" value="{$voucher['sales_price']}" autocomplete="off" data-a-sign="{$config['currency_code']} "  data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="created_date">Date <small class="red">*</small></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control datepicker" value="{$voucher['created_date']}" name="date" id="created_date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="billing_cycle">Billing Cycle <small class="red">*</small></label>

                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="billing_cycle" name="billing_cycle">
                                    <option value="{$voucher['billing_cycle']}" selected>{$voucher['billing_cycle']}</option>
                                    <option value="annual"> Annual </option>
                                    <option value="monthly"> Monthly </option>
                                </select>
                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="expiry_day">Days to Expiry <small class="red">*</small></label>

                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="expiry_day" name="expiry_day">
                                    <option value="{$voucher['expiry_day']}" selected>{$voucher['expiry_day']}</option>
                                    <option value="7"> 7 </option>
                                    <option value="14"> 14 </option>
                                    <option value="21"> 21 </option>
                                    <option value="28"> 28 </option>
                                </select>

                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="description">{$_L['Description']}</label>
    
                            <div class="col-md-8">
                                <textarea id="description" name="description" class="form-control" rows="3">{$voucher['description']}</textarea>
    
                            </div>
                        </div>

                        <input type="hidden" name="vid" id="vid" value="{$voucher['id']}">
                        <input type="hidden" name="voucher_img" id="voucher_img" value="{$voucher['voucher_img']}">

                    </form>
                </div>
            </div>
        </div>
    
    
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    Uplolad Flag Image
                </div>
                <div class="ibox-content" id="ibox_form" >
    
                    <form action="" class="dropzone" id="upload_container">
    
                        <div class="dz-message">
                            <h3>
                                <i class="fa fa-cloud-upload"></i> {$_L['Drop File Here']}</h3>
                            <br />
                            <span class="note">{$_L['Click to Upload']}</span>
                        </div>
    
                    </form>
    
                </div>

                <div class="ibox-content" id="ibox_form" style="text-align: center;">
                    <img id="voucher_image" src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$voucher['voucher_img']}" width="100%">
                </div>
            </div>
    
        </div>
    
    </div>
</div>


<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
    <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">
        <i class="fa fa-check"></i> {$_L['Update']}</button>
</div>

{block name="script"}
<script>

    $(document).ready(function () {

        $(".progress").hide();
        $("#emsg").hide();
        $('#description').redactor(
            {
                minHeight: 150 // pixels
            }
        );

        var $country = $('#country');
        var $category = $('#category');
        var $billing_cycle = $('#billing_cycle')
        var $expiry_day = $('#expiry_day');

        $country.select2({
            theme: "bootstrap"
        });

        $category.select2({
            theme:"bootstrap"
        });

        $billing_cycle.select2({
            theme:"bootstrap"
        });

        $expiry_day.select2({
            theme:"bootstrap"
        });

        $('.datepicker').datepicker();


        var _url = $("#_url").val();
        var ib_submit = $("#submit");
        var $voucher_img= $("#voucher_img");


        var upload_resp;

        // Flag Image upload

        var ib_file = new Dropzone("#upload_container",
            {
                url: _url + "voucher/app/voucher_image_upload/",
                maxFiles: 1
            }
        );

        ib_file.on("sending", function () {

            ib_submit.prop('disabled', true);

        });

        ib_file.on("success", function (file, response) {

            ib_submit.prop('disabled', false);

            upload_resp = response;

            if (upload_resp.success == 'Yes') {

                toastr.success(upload_resp.msg);
                $voucher_img.val(upload_resp.file);
                $("#voucher_image").attr("src",upload_resp.fullpath);
            }
            else {
                toastr.error(upload_resp.msg);
            }

        });


        var $amount = $("#amount");


        function ib_autonumeric() {
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

        }

        ib_autonumeric();


    });
</script>
{/block}