<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
        {if $modal_type eq 'edit'}
            Edit Country
        {else}
            Add Country
        {/if}
    </h3>
</div>

<div class="modal-body">

    <div class="row">
    
        <div class="col-md-7">
            <div class="ibox float-e-margins">

                <div class="ibox-content" id="ib_modal_form">
                    
                    <form class="form-horizontal" id="mrform">
    
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="md_country_name">{$_L['Country']} <small class="red">*</small></label>
    
                            <div class="col-md-8">
    
                                <select id="md_country_name" name="country_name" style="width:100%" class="form-control">
                                    {if $modal_type eq 'edit'}
                                        <option value="{$val['country_name']}" selected>{$val['country_name']}</option>
                                    {else}
                                        <option value="" selected>Select Country</option>
                                        {foreach $countries as $country}
                                            {$country}
                                        {/foreach}
                                    {/if}

                                </select>
                                
                                                                   
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="md_prefix">Prefix <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input class="form-control" type="text" id="md_prefix" name="prefix" value="{$val['prefix']}" placeholder="Enter Prefix">
                            </div>
                        </div>

                        {*<div class="form-group">*}
                            {*<label class="col-md-4 control-label" for="md_category">Category <small class="red">*</small></label>*}

                            {*<div class="col-md-8">*}

                                {*<select class="form-control" style="width:100%" id="md_category" name="category">*}
                                    {*<option value="{$val['category']}" selected>{$val['category']}</option>*}
                                    {*<option value="Silver">Silver</option>*}
                                    {*<option value="Gold">Gold</option>*}
                                {*</select>*}
                                {*<span class="help-block"> </span>*}
                            {*</div>*}

                            {**}
                        {*</div>*}

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="md_description">{$_L['Description']}</label>
    
                            <div class="col-md-8">
                                <textarea id="md_description" name="description" class="form-control" rows="3">{$val['description']}</textarea>
    
                            </div>
                        </div>

                        <input type="hidden" name="cid" id="cid" value="{$val['id']}">
                        <input type="hidden" name="flag_img" id="md_flag_img" value="{$val['flag_img']}">

                    </form>
                </div>
            </div>
        </div>
    
    
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    Upload Flag Image
                </div>
                <div class="ibox-content" id="ibox_form" style="height: 200px">
    
                    <form action="" class="dropzone" id="upload_container1">
    
                        <div class="dz-message">
                            <h3>
                                <i class="fa fa-cloud-upload"></i> {$_L['Drop File Here']}</h3>
                            <br />
                            <span class="note">{$_L['Click to Upload']}</span>
                        </div>
    
                    </form>
    
                </div>

                <div class="ibox-content" id="ibox_form" style="text-align: center;">
                    {if $val['flag_img'] eq NULL || $val['flag_img'] eq " "}
                        <img id="view_flag_img" src="" style="border:1px solid darkgray" width="100%">
                    {else}
                        <img id="view_flag_img" src="{$baseUrl}/apps/voucher/public/flags/{$val['flag_img']}" style="border:1px solid darkgray" width="100%">
                    {/if}
                </div>
            </div>
    
        </div>
    
    </div>
</div>


<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-danger">{$_L['Close']}</button>
    {if $modal_type eq 'edit'}
    <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">
        <i class="fa fa-check"></i> {$_L['Update']}</button>
    {else}
        <button type="submit" class="btn btn-primary modal_submit" id="modal_submit">
            <i class="fa fa-check"></i> {$_L['Submit']}</button>
    {/if}
</div>

{block name="script"}
<script>

    $(document).ready(function () {

        $(".progress").hide();
        $("#emsg").hide();
        $('#md_description').redactor(
            {
                toolbar:false,
                minHeight: 150 // pixels
            }
        );

        $('#md_country_name').select2({
            theme: "bootstrap"
        });


        $('#md_country_name').on('change', function(e){
            e.preventDefault();
            var c_name = {
                'country_name':$('#md_country_name').val()
            };
            $.post(_url + 'voucher/app/get_prefix', c_name)
                .done(function(data){
                    if(data){
                        $('#md_prefix').val(data);
                    }
                });

        });


        var _url = $("#_url").val();
        var ib_submit = $("#submit");
        var $md_flag_img= $("#md_flag_img");

        var upload_resp;

        // Flag Image upload

        var ib_file1 = new Dropzone("#upload_container1",
            {
                url: _url + "voucher/app/flagupload/",
                maxFiles: 1
            }
        );

        ib_file1.on("sending", function () {

            ib_submit.prop('disabled', true);

        });

        ib_file1.on("success", function (file, response) {

            ib_submit.prop('disabled', false);

            upload_resp = response;

            if (upload_resp.success == 'Yes') {

                toastr.success(upload_resp.msg);
                $md_flag_img.val(upload_resp.file);
                console.log(upload_resp);
                $('#view_flag_img').attr("src",upload_resp.fullpath);
            }
            else {
                toastr.error(upload_resp.msg);
            }

        });


    });
</script>
{/block}