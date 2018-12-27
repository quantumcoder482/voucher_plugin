
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">{$_L['Add Custom Field']}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <form role="form" name="add_form" id="add_form" method="post" action="{$_url}settings/customfields-post">
                    <div class="form-group">
                        <label for="fieldname">{$_L['Field Name']}</label>
                        <input type="text" class="form-control" id="fieldname" name="fieldname">
                    </div>

                    <div class="form-group">
                        <label for="fieldtype">{$_L['Field Type']}</label>
                        <select class="form-control" name="fieldtype" id="fieldtype">

                            <option value="text" selected="">{$_L['Text Box']}</option>
                            <option value="password">{$_L['Password']}</option>
                            <option value="dropdown">{$_L['Drop Down']}</option>
                            <option value="textarea">{$_L['Text Area']}</option>
                            <option value="date">{$_L['Date']}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">{$_L['Description']}</label>
                        <input type="text" class="form-control" id="description" name="description">
                        <span class="help-block">{$_L['Optional Description help']}</span>
                    </div>
                    <div class="form-group">
                        <label for="validation">{$_L['Validation']}</label>
                        <input type="text" class="form-control" id="validation" name="validation">
                        <span class="help-block">{$_L['Regular Expression Validation']}</span>
                    </div>
                    <div class="form-group">
                        <label for="options">{$_L['Select Options']}</label>
                        <input type="text" class="form-control" id="options" name="options">
                        <span class="help-block">{$_L['Comma Separated List']}</span>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="add_submit"><i class="fa fa-check"></i> {$_L['Submit']}</button>
    </div>




