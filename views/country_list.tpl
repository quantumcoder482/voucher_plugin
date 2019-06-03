{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}ui/lib/footable/css/footable.core.min.css" />
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

    <div class="row">
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>
                        Add Country Form
                    </h5>
                    <input type="hidden" id="sure_msg" value="{$_L['are_you_sure']}" />
                </div>
                <div class="ibox-content" id="ibox_form">
                    <div class="alert alert-danger" id="emsg">
                        <span id="emsgbody"></span>
                    </div>

                    <form class="form-horizontal" id="rform">

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="country_name">{$_L['Country']} <small class="red">*</small></label>

                            <div class="col-md-9">

                                <select class="form-control" id="country_name" name="country_name" style="width:100%">
                                    <option value="" selected>{$_L['Select Country']}</option>
                                    {foreach $countries as $country}
                                      {$country}
                                    {/foreach}
                                </select>


                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-3 control-label" for="prefix">Prefix <small class="red">*</small></label>

                            <div class="col-md-9">
                                <input class="form-control" type="text" id="prefix" name="prefix" value="" placeholder="Enter Prefix">
                            </div>
                        </div>

                        {*<div class="form-group">*}
                            {*<label class="col-md-3 control-label" for="category">category <small class="red">*</small></label>*}

                            {*<div class="col-md-9">*}

                                {*<select class="form-control" id="category" name="category" style="width:100%" >*}
                                    {*<option value="" selected>Select Category</option>*}
                                    {*<option value="Silver">Silver</option>*}
                                    {*<option value="Gold">Gold</option>*}
                                {*</select>*}
                            {*</div>*}

                        {*</div>*}

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="description">Description</label>

                            <div class="col-md-9">
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>

                            </div>
                        </div>

                        <input type="hidden" name="cid" id="cid" value="">
                        <input type="hidden" name="flag_img" id="flag_img" value="">

                   </form>

                    {* Flag upload *}

                    <div class="ibox-title">
                        <h5>
                            Upload Flag Image
                        </h5>
                    </div>
                    <div class="ibox-content" id="ibox_form">

                        <form action="" class="dropzone" id="upload_container">

                            <div class="dz-message">
                                <h3>
                                    <i class="fa fa-cloud-upload"></i> {$_L['Drop File Here']}</h3>
                                <br />
                                <span class="note">{$_L['Click to Upload']}</span>
                            </div>

                        </form>

                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-9 col-md-3">
                            <button class="btn btn-primary" type="submit" id="submit"><i class="fa fa-check"></i>{$_L['Submit']}</button>
                        </div>
                    </div>
                    <br/>

                </div>

            </div>

        </div>
        <div class="col-lg-8">

            <div class="panel">
                <div class="panel-body">

                    <form class="form-horizontal" method="post" action="">
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <input type="text" name="name" id="foo_filter" class="form-control" placeholder="{$_L['Search']}..."/>

                                </div>
                            </div>

                        </div>
                    </form>

                    <table class="table table-bordered table-hover sys_table footable" data-filter="#foo_filter" data-page-size="20">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Country</th>
                            <th>Prefix</th>
                            {*<th>Category</th>*}
                            <th>Description</th>
                            <th>flag</th>
                            <th>manage</th>
                        </tr>
                        </thead>
                        <tbody>

                        {foreach $list_country as $key=>$country}
                            <tr>
                                <td data-value="{$country['id']}">{$key+1}</td>
                                <td data-value="{$country['country_name']}">{$country['country_name']}</td>
                                <td data-value="{$country['prefix']}">{$country['prefix']}</td>
                                {*<td data-value="{$country['category']}">{$country['category']}</td>*}
                                <td data-value="{$country['description']}">{$country['description']}</td>
                                <td data-value="{$country['flag_img']}">
                                    <img src="{$baseUrl}/storage/system/{$country['flag_img']}" style="border:1px solid darkgray"  width="40px" />
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-info btn-xs edit_country" id="{$country['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-xs cdelete" id="{$country['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Delete']}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}

                        </tbody>

                        <tfoot>
                            <tr>
                                <td style="text-align: left" colspan="7">
                                    <ul class="pagination">
                                    </ul>
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

        </div>
    </div>



{/block}

{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/country_list.js"></script>
{/block}