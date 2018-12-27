<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
        Generate Voucher
    </h3>
</div>

<div class="modal-body">

    <div class="row">
    
        <div class="col-md-7">
            <div class="ibox float-e-margins">

                <div class="ibox-content" id="ib_modal_form">
                    
                    <form class="form-horizontal" id="mrform">
    
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="contact">Contact <small class="red">*</small></label>
    
                            <div class="col-md-8">
                                <select id="contact" name="contact_id" style="width:100%" class="form-control">
                                    <option value="">Select Contact</option>
                                    {foreach $customers as $customer}
                                    <option value="{$customer['id']}">{$customer['account']}</option>
                                    {/foreach}
                                </select>
                                
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="prefix">Prefix <small class="red">*</small></label>
    
                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="prefix" name="prefix">
                                    <option value="{$voucher['prefix']}" selected>{$voucher['prefix']}</option>
                                    {*<option value="SG">SG</option>*}
                                    {*<option value="CH">CH</option>*}
                                </select>

                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="serial_number">Serial Number <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="text" id="serial_number" name="serial_number" class="form-control" value="" autocomplete="off">
                            </div>
                            <span class="col-md-offset-4 col-md-8 help-block" style="font-size: x-small">Auto-generate Multiple Serial No. or Single Serial Number you may enter custom serial number. </span>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="total_voucher">Total Voucher <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="number" id="total_voucher" min="1" name="total_voucher" class="form-control" value="1" autocomplete="off">
                            </div>
                            <span class="col-md-offset-4 col-md-8 help-block" style="font-size: x-small">Enter Total Serial Number to be generate</span>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="page_number">PDF Page No. <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="text" id="page_number" name="page_number" class="form-control" value="" autocomplete="off">
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for=date">Date</label>

                            <div class="col-md-8">
                                <input type="text" class="form-control datepicker" value="{$voucher['created_date']}" name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="create_invoice">Create Invoice</label>

                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="i-checks" name="create_invoice" value="create" {if $create_invoice eq 'create'}checked{/if}>
                                        Invoice will be auto-generated
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="add_payment">Add Payment</label>

                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="i-checks" name="add_payment" value="add_payment" {if $add_payment eq 'add_payment'}checked{/if}>
                                        Invoice will make as PAID credit to account
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="partner">Partner</label>

                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="partner" name="partner_id">
                                    <option value="">Select Partner</option>
                                    {foreach $suppliers as $supplier}
                                        <option value="{$supplier['id']}">{$supplier['account']}</option>
                                    {/foreach}
                                </select>
                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="description">{$_L['Description']}</label>
    
                            <div class="col-md-8">
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
    
                            </div>
                        </div>

                        <input type="hidden" name="vid" id="vid" value="{$voucher['id']}">
                        <input type="hidden" name="voucher_template" id="voucher_template" value="{$voucher['voucher_template']}">

                    </form>
                </div>
            </div>
        </div>
    
    
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    Uplolad PDF Template
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
    <button type="submit" class="btn btn-primary generate_modal_submit" id="modal_submit">
        <i class="fa fa-check"></i> {$_L['Submit']}</button>
</div>

{block name="script"}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/voucher_codes.js"></script>
<script>

    $(document).ready(function () {

        $(".progress").hide();
        $("#emsg").hide();
        // $('#description').redactor(
        //     {
        //         minHeight: 200 // pixels
        //     }
        // );

        var $contact = $('#contact');
        var $prefix = $('#prefix');
        var $partner = $('#partner')

        $contact.select2({
            theme: "bootstrap"
        });

        $prefix.select2({
            theme:"bootstrap"
        });

        $partner.select2({
            theme:"bootstrap"
        });


        var _url = $("#_url").val();
        var ib_submit = $("#submit");
        var $voucher_template= $("#voucher_template");

        $('.datepicker').datepicker();
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });

        var upload_resp;

        // Flag Image upload

        var ib_file = new Dropzone("#upload_container",
            {
                url: _url + "voucher/app/voucher_upload/",
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
                $voucher_template.val(upload_resp.file);
            }
            else {
                toastr.error(upload_resp.msg);
            }

        });


        // voucher code generate
        $('total_voucher').val(1);

        var voucher_code_list = voucher_codes.generate({
            length: 11,
            count: 200,
            charset: "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        });
        $('#serial_number').val(voucher_code_list[0]);


        $('#total_voucher').on('change', function() {
            var voucher_num = $('#total_voucher').val();

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



        });


    });


</script>
{/block}