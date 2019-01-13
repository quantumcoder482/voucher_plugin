{extends file="$layouts_admin"}
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$app_url}apps/voucher/views/css/global.css" />
{/block}
{block name="content"}
    <div class="row">
        <form class="form-horizontal" id="rform">
            <div class="col-md-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>
                            Add Voucher Form
                        </h5>
                    </div>
                    <div class="ibox-content" id="ibox_form">
                        <div class="alert alert-danger" id="emsg">
                            <span id="emsgbody"></span>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label" for="country">{$_L['Country']} <small class="red">*</small></label>

                            <div class="col-md-10">

                                <select id="country" name="country" class="form-control" style="width:100%">
                                    <option value="">Select Country</option>
                                    {foreach $country_list as $c}
                                     <option value="{$c['id']}" >{$c['country_name']}</option>
                                    {/foreach}
                                </select>
                                <span class="help-block"><a href="#" class="add_country"><i class="fa fa-plus"></i>New Country</a>
                                </span>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label" for="category">Category <small class="red">*</small></label>

                            <div class="col-md-10">

                                <select id="category" name="category" class="form-control" style="width:100%">
                                    <option value="">Select Category</option>
                                    {foreach $category_list as $c}
                                        <option value="{$c['id']}" >{$c['category_name']}</option>
                                    {/foreach}
                                </select>
                                {*<span class="help-block"><a href="#" class="add_country"><i class="fa fa-plus"></i>New Country</a>*}
                                {*</span>*}

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date" class="col-md-2 control-label">Date <small class="red">*</small></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value=" " name="date" id="date" datepicker data-date-format="yyyy-mm-dd" data-auto-close="true">
                            </div>
                        </div>


                        <div class="form-group"><label class="col-md-2 control-label" for="cost_price">Cost Price</label>

                            <div class="col-md-10"><input type="text" id="cost_price" name="cost_price" class="form-control amount" autocomplete="off" data-a-sign="{$config['currency_code']} "  data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">

                            </div>
                        </div>

                        <div class="form-group"><label class="col-md-2 control-label" for="sales_price">Sales Price <small class="red">*</small></label>

                            <div class="col-md-10">

                                <input type="text" id="sales_price" name="sales_price" class="form-control amount" autocomplete="off" data-a-sign="{$config['currency_code']} "  data-a-dec="{$config['dec_point']}" data-a-sep="{$config['thousands_sep']}" data-d-group="2">

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label" for="billing_cycle">Billing Cycle <small class="red">*</small></label>

                            <div class="col-md-10">
                                <select class="form-control" style="width:100%" id="billing_cycle" name="billing_cycle">
                                    <option value=""></option>
                                    <option value="annual"> Annual </option>
                                    <option value="monthly"> Monthly </option>
                                </select>

                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label" for="expiry_day">Days to Expiry <small class="red">*</small></label>

                            <div class="col-md-10">
                                <select class="form-control" style="width:100%" id="expiry_day" name="expiry_day">
                                    <option value=""></option>
                                    <option value="7"> 7 </option>
                                    <option value="14"> 14 </option>
                                    <option value="21"> 21 </option>
                                    <option value="28"> 28 </option>
                                </select>

                                {*<span class="help-block"> {$_L['vehicle comment']}</span>*}
                            </div>
                        </div>

                        <div class="form-group"><label class="col-md-2 control-label" for="description">{$_L['Description']}</label>

                            <div class="col-md-10"><textarea id="description" name="description" class="form-control" rows="3"></textarea>

                            </div>
                        </div>


                        <input type="hidden" name="voucher_img" id="voucher_img" value="">

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">

                                <button class="btn btn-primary" type="submit" id="submit"><i class="fa fa-check"></i>{$_L['Submit']}</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        Select Voucher Template <small class="red">*</small>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-10">
                                <select class="form-control" style="width:100%" id="template" name="template">
                                    <option value="" >Select Template</option>
                                    {foreach $template_list as $t}
                                        <option value="{$t['id']}">{$t['template_name']}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        Voucher Image
                    </div>
                    {*<div class="ibox-content" id="ibox_form">*}

                        {*<form action="" class="dropzone" id="upload_container">*}

                            {*<div class="dz-message">*}
                                {*<h3> <i class="fa fa-cloud-upload"></i>  {$_L['Drop File Here']}</h3>*}
                                {*<br />*}
                                {*<span class="note">{$_L['Click to Upload']}</span>*}
                            {*</div>*}

                        {*</form>*}

                    {*</div>*}

                    <div class="ibox-content" id="ibox_form" style="text-align: center;">
                        <img id="voucher_image" src=""  width="100%" >
                    </div>
                </div>

            </div>

        </form>

    </div>
{/block}
{block name=script}
    <script type="text/javascript" src="{$app_url}apps/voucher/views/js/add_voucher.js"></script>
{/block}
