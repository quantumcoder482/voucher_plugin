<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
        {if $modal_type eq 'edit'}
            Edit PDF Template
        {else}
            Add PDF Template
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
                            <label class="col-md-4 control-label" for="md_template_name">Title <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input class="form-control" type="text" id="md_template_name" name="template_name" value="{$val['template_name']}" placeholder="Enter Title">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="md_voucher_pgnum">PDF Page No. <small class="red">*</small></label>

                            <div class="col-md-8">
                                <input class="form-control" type="text" id="md_voucher_pgnum" name="voucher_pgnum" value="{$val['voucher_pgnum']}" placeholder="Enter Page Number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="md_description">{$_L['Description']}</label>
    
                            <div class="col-md-8">
                                <textarea id="md_description" name="description" class="form-control" rows="3">{$val['description']}</textarea>
    
                            </div>
                        </div>

                        <input type="hidden" name="tid" id="tid" value="{$val['id']}">
                        <input type="hidden" name="cover_img" id="md_cover_img" value="{$val['cover_img']}">
                        <input type="hidden" name="voucher_template" id="md_voucher_template" value="{$val['voucher_template']}">

                    </form>

                    <div class="ibox-title">
                        Upload PDF Template
                    </div>
                    <div class="ibox-content" id="ibox_form" style="height: 200px">

                        <form action="" class="dropzone" id="md_upload_container2">

                            <div class="dz-message">
                                <h3>
                                    <i class="fa fa-cloud-upload"></i> {$_L['Drop File Here']}</h3>
                                <br />
                                <span class="note">{$_L['Click to Upload']}</span>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    
    
        <div class="col-md-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    Upload Cover Image
                </div>
                <div class="ibox-content" id="ibox_form" style="height: 200px">
    
                    <form action="" class="dropzone" id="md_upload_container1">
    
                        <div class="dz-message">
                            <h3>
                                <i class="fa fa-cloud-upload"></i> {$_L['Drop File Here']}</h3>
                            <br />
                            <span class="note">{$_L['Click to Upload']}</span>
                        </div>
    
                    </form>
    
                </div>

                <div class="ibox-content" id="ibox_form" style="text-align: center;">
                    {if $val['cover_img'] eq NULL || $val['cover_img'] eq " "}
                        <img id="view_cover_img" src="" style="border:1px solid darkgray" width="100%">
                    {else}
                        <img id="view_cover_img" src="{$baseUrl}/storage/system/{$val['cover_img']}" style="border:1px solid darkgray" width="100%">
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
                toolbar: false,
                minHeight: 150 // pixels
            }
        );


        var _url = $("#_url").val();
        var ib_submit = $("#submit");
        var $md_cover_img= $("#md_cover_img");
        var $md_voucher_template= $("#md_voucher_template");

        var upload_resp;

        // voucher Image upload

        var ib_file1 = new Dropzone("#md_upload_container1",
            {
                url: _url + "voucher/app/voucher_image_upload/",
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
                $md_cover_img.val(upload_resp.file);
                $('#view_cover_img').attr("src",upload_resp.fullpath);
            }
            else {
                toastr.error(upload_resp.msg);
            }

        });

        // Voucher Template Upload

        var ib_file2 = new Dropzone("#md_upload_container2",
            {
                url: _url + "voucher/app/voucher_upload/",
                maxFiles: 1
            }
        );

        ib_file2.on("sending", function () {

            ib_submit.prop('disabled', true);

        });

        ib_file2.on("success", function (file, response) {

            ib_submit.prop('disabled', false);

            upload_resp = response;

            if (upload_resp.success == 'Yes') {

                toastr.success(upload_resp.msg);
                $md_voucher_template.val(upload_resp.file);
            }
            else {
                toastr.error(upload_resp.msg);
            }

        });


    });
</script>
{/block}