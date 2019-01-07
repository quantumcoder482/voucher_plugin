{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}

    <div class="row">
        <div class="col-md-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>
                        Add Category Form
                    </h5>
                    <input type="hidden" id="sure_msg" value="{$_L['are_you_sure']}" />
                </div>
                <div class="ibox-content" id="ibox_form">
                    <div class="alert alert-danger" id="emsg">
                        <span id="emsgbody"></span>
                    </div>

                    <form class="form-horizontal" id="rform">

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="category_name">Title <small class="red">*</small></label>

                            <div class="col-md-9">
                                <input class="form-control" type="text" id="category_name" name="category_name" value="" placeholder="Enter Title">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="description">Description</label>

                            <div class="col-md-9">
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>

                            </div>
                        </div>

                   </form>

                    <div class="form-group">
                        <div class="col-md-offset-9 col-md-3" style="text-align:right;">
                            <button class="btn btn-primary" type="submit" id="submit"><i class="fa fa-check"></i>{$_L['Submit']}</button>
                        </div>
                    </div>
                    <br/>
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
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>manage</th>
                        </tr>
                        </thead>
                        <tbody>

                        {foreach $category_list as $key=>$category}
                            <tr>
                                <td data-value="{$category['id']}">{$key+1}</td>
                                <td data-value="{$category['category_name']}">{$category['category_name']}</td>
                                <td data-value="{$category['description']}">{$category['description']}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-info btn-xs edit_category" id="{$category['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Edit']}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-xs cdelete" id="{$category['id']}" data-toggle="tooltip" data-placement="top" title="{$_L['Delete']}">
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
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/category_list.js"></script>
{/block}