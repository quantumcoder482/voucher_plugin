<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>
        {if $modal_type eq 'edit'}
            Edit Category
        {else}
            Add Category
        {/if}
    </h3>
</div>

<div class="modal-body">

    <div class="row">
    
        <div class="col-md-12">
            <div class="ibox float-e-margins">

                <div class="ibox-content" id="ib_modal_form">
                    
                    <form class="form-horizontal" id="mrform">
    

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="md_category">Title <small class="red">*</small></label>

                            <div class="col-md-9">
                                <input class="form-control" type="text" id="md_category" name="category_name" value="{$val['category_name']}" placeholder="Enter Title">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="md_description">{$_L['Description']}</label>
    
                            <div class="col-md-9">
                                <textarea id="md_description" name="description" class="form-control" rows="3">{$val['description']}</textarea>
    
                            </div>
                        </div>

                        <input type="hidden" name="cid" id="cid" value="{$val['id']}">

                    </form>
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

    });
</script>
{/block}