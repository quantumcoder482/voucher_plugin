
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">{$_L['Edit Custom Field']}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <form role="form" name="edit_form" id="edit_form" method="post" action="{$_url}settings/customfields-post">
                <div class="form-group">
                    <label for="fieldname">{$_L['Field Name']}</label>
                    <input type="text" class="form-control" id="fieldname" name="fieldname" value="{$d['fieldname']}">
                </div>

                <div class="form-group">
                    <label for="fieldtype">{$_L['Field Type']}</label>
                    <select class="form-control" name="fieldtype" id="fieldtype">

                        <option value="text" {if ($d['fieldtype']) eq 'text'}selected=""{/if}>{$_L['Text Box']}</option>
                        <option value="password" {if ($d['fieldtype']) eq 'password'}selected=""{/if}>{$_L['Password']}</option>
                        <option value="dropdown" {if ($d['fieldtype']) eq 'dropdown'}selected=""{/if}>{$_L['Drop Down']}</option>
                        <option value="textarea" {if ($d['fieldtype']) eq 'textarea'}selected=""{/if}>{$_L['Text Area']}</option>
                        <option value="date" {if ($d['fieldtype']) eq 'date'}selected=""{/if}>{$_L['Date']}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">{$_L['Description']}</label>
                    <input type="text" class="form-control" id="description" name="description" value="{$d['description']}">
                    <span class="help-block">{$_L['Optional Description help']}</span>
                </div>
                <div class="form-group">
                    <label for="validation">{$_L['Validation']}</label>
                    <input type="text" class="form-control" id="validation" name="validation" value="{$d['regexpr']}">
                    <span class="help-block">{$_L['Regular Expression Validation']}</span>
                </div>
                <div class="form-group">
                    <label for="options">{$_L['Select Options']}</label>
                    <input type="text" class="form-control" id="options" name="options" value="{$d['fieldoptions']}">
                    <span class="help-block">{$_L['Comma Separated List']}</span>
                </div>


<input type="hidden" name="id" value="{$d['id']}">
            </form>
        </div>
    </div>
</div>
<div class="modal-footer">

    <button type="button" class="btn btn-primary" id="edit_submit"><i class="fa fa-check"></i> {$_L['Submit']}</button>
</div>