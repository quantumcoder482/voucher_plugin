{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}ui/lib/footable/css/footable.core.min.css" />
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-3 col-sm-6">

                            <form id="frm_search">
                                <div class="form-group">
                                    <label for="reportrange">Date Range</label>
                                    <input type="text" name="reportrange" class="form-control" id="reportrange">
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="customer_id">Customer</label>
                                    <select id="customer_id" name="customer_id" class="form-control">
                                        <option value="" selected>{$_L['All']}</option>
                                        {foreach $customers as $customer}
                                            <option value="{$customer['id']}">{$customer['account']}</option>
                                        {/foreach}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="agent_id">Agent</label>
                                    <select id="agent_id" name="agent_id" class="form-control">
                                        <option value="" selected>{$_L['All']}</option>
                                        {foreach $agents as $agent}
                                            <option value="{$agent['id']}">{$agent['account']}</option>
                                        {/foreach}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="category">Voucher Category</label>
                                    <select id="category" name="category" class="form-control">
                                        <option value="">{$_L['All']}</option>
                                        {foreach $categories as $category}
                                            <option value="{$category['id']}">{$category['category_name']}</option>
                                        {/foreach}
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="country_id">Voucher Country</label>
                                    <select id="country_id" name="country_id" class="form-control">
                                        <option value="">{$_L['All']}</option>
                                        {foreach $countries as $country}
                                            <option value="{$country['id']}">{$country['country_name']}</option>
                                        {/foreach}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="">{$_L['All']}</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Expired">Expired</option>
                                    </select>
                                </div>


                                <button type="submit" id="ib_filter" class="btn btn-primary">Filter</button>

                                <br>
                            </form>

                        </div>
                        <div class="col-md-9 col-sm-6 ib_right_panel">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive" id="ib_data_panel">
                                        <table class="table table-bordered table-hover display" id="ib_dt">
                                            <thead>
                                            <tr class="heading">
                                                <th> # </th>
                                                <th width="80px">Date</th>
                                                <th>Customer</th>
                                                <th>Agent</th>
                                                <th>Country</th>
                                                <th>Category</th>
                                                <th width="80px">Expiry</th>
                                                <th>Serial No.</th>
                                                <th>Status</th>
                                                <th class="text-right" style="width: 80px;">Manage</th>
                                            </tr>
                                            <tr class="heading">
                                                <td></td>
                                                <td></td>
                                                <td><input type="text" id="filter_customer" name="filter_customer" class="form-control"></td>
                                                <td><input type="text" id="filter_agent" name="filter_agent" class="form-control"></td>
                                                <td><input type="text" id="filter_country" name="filter_country" class="form-control"></td>
                                                <td><input type="text" id="filter_category" name="filter_category" class="form-control"></td>
                                                <td></td>
                                                <td><input type="text" id="filter_serialnumber" name="filter_serialnumber" class="form-control"></td>
                                                <td></td>
                                                <td class="text-center" style="width: 80px;"><button type="submit" id="inner_filter" class="btn btn-primary">{$_L['Filter']}</button></td>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div> <!-- Row end-->


{/block}
{block name="script"}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/voucher_transaction.js"></script>
    <script>
        $(function () {

        })
    </script>
{/block}
