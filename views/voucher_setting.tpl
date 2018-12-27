{extends file="$layouts_admin"}
{block name="style"}
<link rel="stylesheet" type="text/css" href="{$app_url}ui/lib/footable/css/footable.core.min.css" />
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
    <div class="row">

        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <form class="form-horizontal" id="sform">
                    <div class="ibox-title">
                        <h5>
                            Setting
                        </h5>
                    </div>
                    <div class="ibox-content" id="ibox_form">
                        <div class="alert alert-danger" id="emsg">
                            <span id="emsgbody"></span>
                        </div>

                                <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <span style="font-weight: 600;">User Require Make Payment to activate account before redeem voucher code</span>
                                <br>
                                <span>An invoice will automatic generate once user register an account</span>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" data-toggle="toggle" data-size="small" {if $setting['user_require_make_payment'] eq 1} checked {/if}
                                       data-on="{$_L['Yes']}" data-off="{$_L['No']}" id="user_require_make_payment">

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-md-2 control-label" for="product_fee">Product </label>

                            <div class="col-md-10">
                                <select class="form-control" style="width:100%" id="product_fee" name="product_fee">
                                    <option value="15" {if $setting['product_fee'] eq 15} selected {/if}> Activation Fees($15) </option>
                                    <option value="20" {if $setting['product_fee'] eq 20} selected {/if}> Activation Fees($20) </option>
                                </select>
                            </div>
                        </div>
                        <br>
                    </div>

                    <div class="ibox-title">
                        <h5>
                            Default Activation Terms
                        </h5>
                    </div>
                    <div class="ibox-content" id="ibox_form">

                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="agreement_text" name="agreement_text" class="form-control" rows="3">
                                    {$setting['agreement_text']}
                                </textarea>

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-md-10 ">
                                <span style="font-weight: 600">User Require to Agree</span>
                                <br>
                                <span >User Require to Agree this terms & conditions activate the voucher</span>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" data-toggle="toggle" data-size="small" {if $setting['require_agree'] eq 1} checked {/if}
                                       data-on="{$_L['Yes']}" data-off="{$_L['No']}" id="require_agree">

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <span style="font-weight: 600">User able to redeem voucher code</span>
                                <br>
                                <span>User can redeem voucher code that already generated in the system </span>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" data-toggle="toggle" data-size="small" {if $setting['able_redeem_voucher_code'] eq 1} checked {/if}
                                       data-on="{$_L['Yes']}" data-off="{$_L['No']}" id="able_redeem_voucher_code">

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <span style="font-weight: 600">User can not edit submitted voucher</span>
                                <br>
                                <span>Once activated user can not edit or redeem voucher</span>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" data-toggle="toggle" data-size="small" {if $setting['cant_edit_submit_voucher'] eq 1} checked {/if}
                                       data-on="{$_L['Yes']}" data-off="{$_L['No']}" id="cant_edit_submit_voucher">

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>
                            <br>

                        <div class="form-group">
                            <div class="col-md-offset-10 col-md-2" style="text-align:right">
                                <button class="btn btn-primary" type="submit" id="submit1"><i class="fa fa-check"></i>{$_L['Submit']}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>


        <div class="col-md-6">
            <form class="form-horizontal" id="alert_form">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        Alert Message
                    </div>
                    <div class="ibox-content" id="ibox_form">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="alert_message" name="alert_message" class="form-control" rows="3">
                                    {$setting['alert_message']}
                                </textarea>

                            </div>
                        </div>

                            <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <span style="text-align:left">Show Alert Message</span>
                                <br>
                                <span style="text-align:left">Only Unactivated or Unpaid Account will see this message and appear on top of user dashboard</span>
                            </div>

                            <div class="col-md-2">
                                <input type="checkbox" data-toggle="toggle" data-size="small" {if $setting['show_alert_message'] eq 1} checked {/if}
                                       data-on="{$_L['Yes']}" data-off="{$_L['No']}" id="show_alert_message">

                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <br>

                        <div class="form-group">
                            <div class="col-md-offset-10 col-md-2" style="text-align:right">
                                <button class="btn btn-primary" type="submit" id="submit2"><i class="fa fa-check"></i>{$_L['Submit']}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>
{/block}
{block name=script}
<script>
    $(document).ready(function(){

        $(".progress").hide();
        $("#emsg").hide();
        $('#agreement_text').redactor(
            {
                minHeight: 200 // pixels
            }
        );
        $('#alert_message').redactor(
            {
                minHeight: 200 // pixels
            }
        );

        $('#product_fee').select2({
            theme:"bootstrap"
        });


        var _url = $("#_url").val();
        var ib_submit1 = $("#submit1");
        var ib_submit2 = $("#submit2");



        $('#user_require_make_payment').change(function() {

            $('#ibox_form').block({ message: null });


            if($(this).prop('checked')){

                $.post( _url+'voucher/app/update_settings/', { opt: "user_require_make_payment", val: "1" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });

            }
            else{
                $.post( _url+'voucher/app/update_settings', { opt: "user_require_make_payment", val: "0" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });
            }
        });



        $('#require_agree').change(function() {

            $('#ibox_form').block({ message: null });


            if($(this).prop('checked')){

                $.post( _url+'voucher/app/update_settings/', { opt: "require_agree", val: "1" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });

            }
            else{
                $.post( _url+'voucher/app/update_settings', { opt: "require_agree", val: "0" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });
            }
        });

        $('#able_redeem_voucher_code').change(function() {

            $('#ibox_form').block({ message: null });


            if($(this).prop('checked')){

                $.post( _url+'voucher/app/update_settings/', { opt: "able_redeem_voucher_code", val: "1" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });

            }
            else{
                $.post( _url+'voucher/app/update_settings', { opt: "able_redeem_voucher_code", val: "0" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });
            }
        });

        $('#cant_edit_submit_voucher').change(function() {

            $('#ibox_form').block({ message: null });


            if($(this).prop('checked')){

                $.post( _url+'voucher/app/update_settings/', { opt: "cant_edit_submit_voucher", val: "1" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });

            }
            else{
                $.post( _url+'voucher/app/update_settings', { opt: "cant_edit_submit_voucher", val: "0" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });
            }
        });

        $('#show_alert_message').change(function() {

            $('#ibox_form').block({ message: null });


            if($(this).prop('checked')){

                $.post( _url+'voucher/app/update_settings/', { opt: "show_alert_message", val: "1" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });

            }
            else{
                $.post( _url+'voucher/app/update_settings', { opt: "show_alert_message", val: "0" })
                    .done(function( data ) {
                        $('#ibox_form').unblock();
                        location.reload();
                    });
            }
        });



        ib_submit1.click(function (e) {

            e.preventDefault();

            $('#ibox_form').block({ message: null });

            $.post(_url + 'voucher/app/post_setting', $("#sform").serialize())
                .done(function (data) {


                    if ($.isNumeric(data)) {

                        location.reload();
                    }
                    else {
                        $('#ibox_form').unblock();

                        $("#emsgbody").html(data);
                        $("#emsg").show("slow");
                    }
                });
        });

        ib_submit2.click(function (e) {

            e.preventDefault();

            $('#ibox_form').block({ message: null });

            $.post(_url + 'voucher/app/post_alert', $("#alert_form").serialize())
                .done(function (data) {

                    if ($.isNumeric(data)) {

                        location.reload();
                    }
                    else {
                        $('#ibox_form').unblock();

                        $("#emsgbody").html(data);
                        $("#emsg").show("slow");
                    }
                });
        });




    });
</script>
{/block}
