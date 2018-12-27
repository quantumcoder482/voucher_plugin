<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
       Terms & Conditions
    </h3>
</div>

<div class="modal-body">

    <div class="row">
        <form class="form-horizontal" id="mrform">
            <div class="form-group">
                <div class="col-md-12">
                    <textarea id="terms" name="terms_conditions" class="form-control" rows="8">{$setting['agreement_text']}</textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="i-checks" name="payment_req" value="1">
                                I have read, understand, and fully agree to all term and conditions of this benefit offer and <br>
                                I agree to the declaration and cancelling policy which will be forwarded to me at time of booking.
                        </label>
                    </div>
                </div>
            </div>

            {*<input type="hidden" name="cid" id="cid" value="{$val['id']}">*}
            {*<input type="hidden" name="flag_img" id="md_flag_img" value="{$val['flag_img']}">*}

        </form>

    </div>
</div>


<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
    <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">
        <i class="fa fa-check"></i>Processed</button>

</div>

{block name="script"}
<script>

    $(document).ready(function () {

        $(".progress").hide();
        $("#emsg").hide();
        $('#md_description').redactor(
            {
                minHeight: 150 // pixels
            }
        );



        var _url = $("#_url").val();
        var ib_submit = $("#submit");
        var $md_flag_img= $("#md_flag_img");


    });
</script>
{/block}