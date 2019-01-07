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
                <div class="col-md-1">
                    <div class="checkbox">
                        <label style="float:left">
                            <input type="checkbox" class="i-checks" id="modal_agree_check" name="modal_agree_check" value="1" checked>
                        </label>
                    </div>
                </div>
                <div class="col-md-10">
                    <span>I have read, understand, and fully agree to all term and conditions of this benefit offer and I agree to the declaration and cancelling policy which will be forwarded to me at time of booking.</span>
                </div>
            </div>

            {*<input type="hidden" name="cid" id="cid" value="{$val['id']}">*}
            {*<input type="hidden" name="flag_img" id="md_flag_img" value="{$val['flag_img']}">*}

        </form>

    </div>
</div>


<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
    <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">Proceed</button>

</div>

{block name="script"}
<script>

    $(document).ready(function () {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });

        // $('#modal_submit').prop('disabled', true);

        $('#modal_agree_check').on('ifChanged', function(e) {
            e.preventDefault();

            var isChecked = e.currentTarget.checked;

            if(isChecked == true){
                $('#modal_submit').prop('disabled', false);
                $('#modal_submit').removeClass('btn-secondary').addClass('btn-primary');
            }else{
                $('#modal_submit').prop('disabled', true);
                $('#modal_submit').removeClass('btn-primary').addClass('btn-secondary');

            }
        });

        $('#terms').redactor(
            {
                toolbar: false,
                minHeight: 500,
                maxHeight: 500
            }
        );

    });
</script>
{/block}