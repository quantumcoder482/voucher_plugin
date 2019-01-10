<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
        {if $val['id'] eq ''}
        Generate Voucher
        {else}
        Edit Voucher
        {/if}
    </h3>
</div>

<div class="modal-body">

    <div class="row">
        <form class="form-horizontal" id="mrform">
            <div class="col-md-7">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" id="ib_modal_form">
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="contact">Contact</label>

                            <div class="col-md-8">
                                <select id="contact" name="contact_id" style="width:100%" class="form-control">
                                    <option value="">Select Contact</option>
                                    {foreach $customers as $customer}
                                    <option value="{$customer['id']}" {if $customer['id'] eq $val['contact_id']} selected {/if}>{$customer['account']}</option>
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
                                <input type="text" id="serial_number" name="serial_number" class="form-control" value="{$val['serial_number']}" autocomplete="off" {if $val['serial_number'] neq ''} disabled {/if}>
                            </div>
                            <span class="col-md-offset-4 col-md-8 help-block" style="font-size: x-small">Auto-generate Multiple Serial No. or Single Serial Number you may enter custom serial number. </span>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="total_voucher">Total Voucher <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input type="number" id="total_voucher" min="1" name="total_voucher" class="form-control" value="1" autocomplete="off" {if $val['serial_number'] neq ''} disabled {/if}>
                            </div>
                            <span class="col-md-offset-4 col-md-8 help-block" style="font-size: x-small">Enter Total Serial Number to be generate</span>
                        </div>

                        {*<div class="form-group">*}
                            {*<label class="col-md-4 control-label" for="page_number">PDF Page No. <small class="red">*</small></label>*}

                            {*<div class="col-md-8">*}
                                {*<input type="text" id="page_number" name="page_number" class="form-control" value="{$val['serial_pgnum']}" autocomplete="off">*}
                            {*</div>*}

                        {*</div>*}

                        <div class="form-group">
                            <label class="col-md-4 control-label" for=date">Date</label>

                            <div class="col-md-8">
                                <input type="text" class="form-control datepicker" value="" name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="create_invoice">Create Invoice</label>

                            <div class="col-md-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="i-checks" name="create_invoice" value="create" {if $create_invoice eq 'create' || $val['create_invoice'] eq '1' }checked{/if}>
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
                                        <input type="checkbox" class="i-checks" name="add_payment" value="add_payment" {if $add_payment eq 'add_payment' || $val['add_payment'] eq '1' }checked{/if}>
                                        Invoice will make as PAID credit to account
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="agent">Agent</label>

                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="agent" name="agent_id">
                                    <option value="">Select Agent</option>
                                    {foreach $suppliers as $supplier}
                                        <option value="{$supplier['id']}" {if $supplier['id'] eq $val['agent_id']} selected {/if}>{$supplier['account']}</option>
                                    {/foreach}
                                </select>
                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="description">{$_L['Description']}</label>

                            <div class="col-md-8">
                                <textarea id="description" name="description" class="form-control" rows="3">{$val['description']}</textarea>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="status">Status</label>

                            <div class="col-md-8">
                                <select class="form-control" style="width:100%" id="status" name="status">
                                    <option value="Active" {if $val['status'] eq 'Active'} selected {/if}> Active </option>
                                    <option value="Inactive" {if $val['status'] eq 'Inactive'} selected {/if}>Inactive</option>
                                </select>

                            </div>

                        </div>

                        <input type="hidden" name="vid" id="vid" value="{$voucher['id']}">
                        <input type="hidden" name="gid" id="gid" value="{$val['id']}">
                        <input type="hidden" name="invoice_id" id="invoice_id" value="{$val['invoice_id']}">
                        <input type="hidden" name="template_id" id="template_id" value="{$voucher['template_id']}">

                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        Voucher Image
                    </div>
                    <div class="ibox-content" id="ibox_form" style="text-align: center;">
                        <img id="voucher_img" src="{$baseUrl}/apps/voucher/public/voucher_imgs/{$voucher['voucher_img']}" width="100%">
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
    {if $val['id'] eq ''}
    <button type="submit" class="btn btn-primary generate_modal_submit" id="modal_submit">
        <i class="fa fa-check"></i> {$_L['Submit']}</button>
    {else}
        <button type="submit" class="btn btn-primary generate_modal_submit" id="modal_submit">
            <i class="fa fa-check"></i> Update </button>
    {/if}
</div>

{block name="script"}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/voucher_codes.js"></script>
<script>

    $(document).ready(function () {

        $(".progress").hide();
        $("#emsg").hide();
        $('#description').redactor(
            {
                toolbar:false,
                minHeight: 150 // pixels
            }
        );

        var $contact = $('#contact');
        var $prefix = $('#prefix');
        var $agent = $('#agent')

        var $status = $('#status');

        $contact.select2({
            theme: "bootstrap"
        });

        $prefix.select2({
            theme:"bootstrap"
        });

        $agent.select2({
            theme:"bootstrap"
        });

        $status.select2({
            theme:"bootstrap"
        });

        var _url = $("#_url").val();
        var ib_submit = $("#submit");


        $('.datepicker').datepicker();
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });


        // voucher code generate
        $('total_voucher').val(1);

        var voucher_code_list = voucher_codes.generate({
            length: 11,
            count: 200,
            charset: "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        });

        if($('#serial_number').val() == ''){
            $('#serial_number').val(voucher_code_list[0]);
        }


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