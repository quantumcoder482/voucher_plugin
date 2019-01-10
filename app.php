<?php

// require 'apps/voucher/models/Voucher.php';
// require 'apps/voucher/models/VoucherCountry.php';

$action = route(2,'dashboard');
 _auth();
$ui->assign('_application_menu', 'Voucher');
$ui->assign('_title', 'Voucher '.'- '. $config['CompanyName']);
$user = User::_info();
$ui->assign('user', $user);


switch ($action){

    case 'dashboard':

        $baseUrl = APP_URL;

        $redeem_vouchers = null;
        $redeem_voucher_pages = null;
        $recent_vouchers = null;
        $latestincomes = null;
        $latestexpenses = null;

        $today = date('Y-m-d');

        $total_expired = ORM::for_table('voucher_generated')
            ->where_lt('voucher_generated.expiry_date', $today)
            ->count();

        $total_page_redeem = ORM::for_table('voucher_page_transaction')->count();
        $total_voucher_redeem = ORM::for_table('voucher_generated')
            ->where('redeem_status', 'Redeem')
            ->count();

        $total_generated_voucher = ORM::for_table('voucher_generated')->count();


        $redeem_vouchers = ORM::for_table('voucher_generated')
            ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
            ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->inner_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
            ->select('voucher_generated.*')
            ->select('crm_accounts.account', 'customer')
            ->select('voucher_country.country_name', 'country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.expiry_day', 'expiry_day')
            ->where('redeem_status', 'Redeem')
            ->find_array();

        $voucher_status = array();
        foreach ($redeem_vouchers as $v){

            $date1 = date_create($today);
            $date2 = date_create($v['expiry_date']);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));

            if($date2 < $date1){
                $voucher_status[$v['id']] = 'Expired';
            } elseif( $rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
                if($v['status'] == 'Paid'){
                    $voucher_status[$v['id']] = 'Active';
                }else {
                    $voucher_status[$v['id']] = 'Inactive';
                }

            }
        }

        $redeem_voucher_pages = ORM::for_table('voucher_page_transaction')
            ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
            ->select_many('voucher_page_transaction.*')
            ->select('voucher_generated.contact_id')
            ->order_by_desc('id')
            ->find_array();


        $recent_vouchers = ORM::for_table('voucher_generated')
        ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
        ->left_outer_join('sys_invoices', array('sys_invoices.id' ,'=', 'voucher_generated.invoice_id'))
        ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
        ->select('voucher_generated.*')
        ->select('crm_accounts.account', 'account')
        ->select('voucher_format.sales_price', 'amount')
        ->select('sys_invoices.status', 'invoice_status')
        ->find_array();


        $paid_count = 0;
        $unpaid_count = 0;
        $partially_count = 0;
        $total_count = 0;

        foreach($recent_vouchers as $v){
            if($v['invoice_status'] == 'Paid'){
                $paid_count++;
            }elseif ($v['invoice_status'] == 'Unpaid' || $v['invoice_id'] == '-1' || $v['invoice_id'] == '0'){
                $unpaid_count++;
            }else{
                $partially_count++;
            }
            $total_count++;
        }

        $count = array(
            'paid' => $paid_count,
            'unpaid' => $unpaid_count,
            'partially' => $partially_count
        );

        $percent = array(
            'paid' => floor($paid_count/$total_count*100),
            'unpaid' => floor($unpaid_count/$total_count*100),
            'partially' => floor($partially_count/$total_count*100)
        );


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        view('app_wrapper',[
            '_include' => 'dashboard',
            'redeem_vouchers' => $redeem_vouchers,
            'redeem_voucher_pages' => $redeem_voucher_pages,
            'recent_vouchers' => $recent_vouchers,
            'total_expired' => $total_expired,
            'total_page_redeem' => $total_page_redeem,
            'total_voucher_redeem' => $total_voucher_redeem,
            'total_generated_voucher' => $total_generated_voucher,
            'voucher_status' => $voucher_status,
            'percent' => $percent,
            'count' => $count,
            'latestincomes' => $latestincomes,
            'latestexpenses' => $latestexpenses,
            'baseUrl' => $baseUrl
        ]);

        break;

/*
 *  Add/List Country   (country_list.tpl, modal_edit_country, country_list.js)
 */

    case 'add_list_country':

        $countries = Countries::all(); // may add this $config['country_code']
        $list_country = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();
        $baseUrl = APP_URL;


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('app_wrapper',[
            '_include' => 'country_list',
            'baseUrl' => $baseUrl,
            'countries' => $countries,
            'list_country' => $list_country
        ]);


        break;

    case 'get_prefix':

        $country_name = _post('country_name');
        $prefix = Countries::full2short($country_name);

        header('Content-Type: application/json');
        echo json_encode($prefix);

        break;

    case 'post_country':

        $id = _post('cid');
        $country_name =_post('country_name');
        $prefix = _post('prefix');
//        $category = _post('category');
        $description = _post('description');
        $flag_img = _post('flag_img');


        $msg = '';


        if(!$country_name){
            $msg .= 'Country Name is required <br>';
        }
        if(!$prefix){
            $msg .= 'Prefix is required <br>';
        }
//        if(!$category){
//            $msg .= 'Category is requried <br>';
//        }
        if(!$flag_img){
            $msg .= 'Flag Image is required <br>';
        }

        if($id == ''){
            $country_data = ORM::for_table('voucher_country')
                ->where('country_name', $country_name)
                ->find_one();
            if($country_data){
                $msg .= 'Already Created Country Data';
            }
        }

        if($msg == ''){
            if($id == ''){
                _msglog('s','Country Added Successfully');
                $d = ORM::for_table('voucher_country')->create();
            } else {
                _msglog('s','Country Updated Successfully');
                $d = ORM::for_table('voucher_country')->find_one($id);
            }

            $d->country_name = $country_name;
            $d->prefix = $prefix;
            $d->description = $description;
            $d->flag_img = $flag_img;

            $d->save();
            echo $d->id();
        } else {
            echo $msg;
        }

        break;

    case 'delete_country':

        Event::trigger('voucher/app/delete_country');
        $id = route(3);

        $d = ORM::for_table('voucher_country')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/add_list_country', 's', 'Country Delete Successfully');
        }

        break;

    case 'modal_edit_country':

        $id= route(3);
        $modal_type = "edit";
        if(!$id){
            $modal_type = "add";
        }
        $countries = Countries::all(); // may add this $config['country_code']
        $country = ORM::for_table('voucher_country')->find_one($id);
        $baseUrl = APP_URL;


        $val=array();

        if($country){
            $val['id']=$id;
            $val['country_name']=$country->country_name;
            $val['prefix']=$country->prefix;
            $val['description']=$country->description;
            $val['flag_img']=$country->flag_img;

        }else{
            $val['id']="";
            $val['country_name']="";
            $val['prefix']="";
            $val['description']="";
            $val['flag_img']="";
        }



        view('wrapper_modal',[
            '_include'=>'modal_edit_country',
            'countries' => $countries,
            'val' => $val,
            'baseUrl' => $baseUrl,
            'modal_type' => $modal_type
        ]);

        break;

    case 'get_country_info':

        $id = _post('id');
        $country_info = array();

        $d = ORM::for_table('voucher_country')->find_one($id);
        $country_info = [
            'country_name' => $d->country_name,
            'prefix' => $d->prefix,
            'description' => $d->description,
            'flag_img' => $d->flag_img
        ];

        header('Content-Type: application/json');
        echo json_encode($country_info);

        break;

/*
 *  Add Category
 */

    case 'add_category':

        $baseUrl = APP_URL;
        $category_list = ORM::for_table('voucher_category')->order_by_asc('category_name')->find_array();


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('app_wrapper',[
            '_include' => 'category_list',
            'baseUrl' => $baseUrl,
            'category_list' => $category_list
        ]);
        break;

    case 'post_category':

        $id = _post('cid');
        $category_name = _post('category_name');
        $category_name = trim($category_name);
        $description = _post('description');

        $msg = '';

        if($category_name == ''){
            $msg .= 'Title is required <br>';
        }

        if($msg == ''){
            if($id == ''){
                _msglog('s','Category Added Successfully');
                $d = ORM::for_table('voucher_category')->create();
            } else {
                _msglog('s','Category Updated Successfully');
                $d = ORM::for_table('voucher_category')->find_one($id);
            }

            $d->category_name = $category_name;
            $d->description = $description;

            $d->save();
            echo $d->id();

        }else {
            echo $msg;
        }

        break;

    case 'modal_edit_category':

        $id= route(3);
        $modal_type = "edit";
        if(!$id){
            $modal_type = "add";
        }

        $category = ORM::for_table('voucher_category')->find_one($id);
        $baseUrl = APP_URL;


        $val=array();

        if($category){
            $val['id']=$id;
            $val['category_name']=$category->category_name;
            $val['description']=$category->description;
        }else{
            $val['id']="";
            $val['category_name']="";
            $val['description']="";
        }



        view('wrapper_modal',[
            '_include'=>'modal_edit_category',
            'val' => $val,
            'baseUrl' => $baseUrl,
            'modal_type' => $modal_type
        ]);

        break;

    case 'delete_category':

        Event::trigger('voucher/app/delete_category');
        $id = route(3);

        $d = ORM::for_table('voucher_category')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/add_category', 's', 'Category Delete Successfully');
        }

        break;


/*
 *  Add Template
 */

    case 'pdf_template':


        $list_template = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();
        $baseUrl = APP_URL;


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('app_wrapper',[
            '_include' => 'pdf_template',
            'baseUrl' => $baseUrl,
            'list_template' => $list_template
        ]);


        break;

    case 'post_template':

        $id = _post('tid');
        $template_name = _post('template_name');
        $description = _post('description');
        $cover_img = _post('cover_img');
        $voucher_template = _post('voucher_template');
        $voucher_pgnum = _post('voucher_pgnum');


        $msg = '';

        if(!$template_name){
            $msg .= 'Title is required <br>';
        }
        if(!$cover_img){
            $msg .= 'Cover Image is required <br>';
        }
        if(!$voucher_template){
            $msg .= 'PDF Template is required <br>';
        }
        if(!$voucher_pgnum){
            $msg .= 'PDF Page No is required <br>';
        }elseif(!is_numeric($voucher_pgnum)){
            $msg = 'PDf Page No is not Integer <br>';
        }


        if($msg == ''){
            if($id == ''){
                _msglog('s','Template Added Successfully');
                $d = ORM::for_table('voucher_template')->create();
            } else {
                _msglog('s','Template Updated Successfully');
                $d = ORM::for_table('voucher_template')->find_one($id);
            }

            $d->template_name = $template_name;
            $d->voucher_pgnum = $voucher_pgnum;
            $d->description = $description;
            $d->cover_img = $cover_img;
            $d->voucher_template = $voucher_template;


            $d->save();
            echo $d->id();
        } else {
            echo $msg;
        }

        break;

    case 'modal_edit_template':

        $id= route(3);
        $modal_type = "edit";
        if(!$id){
            $modal_type = "add";
        }

        $voucher_template = ORM::for_table('voucher_template')->find_one($id);
        $baseUrl = APP_URL;


        $val=array();

        if($voucher_template){
            $val['id']=$id;
            $val['template_name'] = $voucher_template->template_name;
            $val['voucher_pgnum'] = $voucher_template->voucher_pgnum;
            $val['description'] = $voucher_template->description;
            $val['cover_img'] = $voucher_template->cover_img;
            $val['voucher_template'] = $voucher_template->voucher_template;

        }else{
            $val['id']="";
            $val['template_name']="";
            $val['voucher_pgnum']="";
            $val['description']="";
            $val['cover_img']="";
            $val['voucher_template'] = "";
        }



        view('wrapper_modal',[
            '_include'=>'modal_edit_template',
            'val' => $val,
            'baseUrl' => $baseUrl,
            'modal_type' => $modal_type
        ]);

        break;

    case 'delete_template':

        Event::trigger('voucher/app/delete_template');
        $id = route(3);

        $d = ORM::for_table('voucher_template')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/pdf_template', 's', 'Template Delete Successfully');
        }

        break;

    case 'get_template_info':

        $id = _post('id');
        $template_info = array();

        $d = ORM::for_table('voucher_template')->find_one($id);
        $template_info = [
            'id' => $d->id,
            'template_name' => $d->template_name,
            'cover_img' => $d->cover_img,
            'description' => $d->description,
            'voucher_template' => $d->voucher_template
        ];

        header('Content-Type: application/json');
        echo json_encode($template_info);

        break;

/*
 *   Add Voucher  (add_voucher.tpl, add_voucher.js)
 */

    case 'add_voucher':

        $country_list = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();
        $category_list = ORM::for_table('voucher_category')->order_by_asc('category_name')->find_array();
        $template_list = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();

        $baseUrl = APP_URL;

        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));
        $ui->assign('xjq', '
            $(\'.amount\').autoNumeric(\'init\', {
            dGroup: ' . $config['thousand_separator_placement'] . ',
            aPad: ' . $config['currency_decimal_digits'] . ',
            pSign: \'' . $config['currency_symbol_position'] . '\',
            aDec: \'' . $config['dec_point'] . '\',
            aSep: \'' . $config['thousands_sep'] . '\',
            vMax: \'9999999999999999.00\',
            vMin: \'-9999999999999999.00\'
            });
            $(\'[data-toggle="tooltip"]\').tooltip();

        ');

        view('app_wrapper',[
            '_include' => 'add_voucher',
            'baseUrl' => $baseUrl,
            'country_list' => $country_list,
            'category_list' => $category_list,
            'template_list' => $template_list

        ]);

        break;

    case 'post_voucher':

        $id = _post('vid');
        $country_id = _post('country');
        $category_id = _post('category');
        $template_id = _post('template');
        $created_date = _post('date');
        $billing_cycle = _post('billing_cycle');
        $expiry_day = _post('expiry_day');
        $description = _post('description');
        $voucher_img = _post('voucher_img');


        $cost_price=_post('cost_price','0.00');
        $cost_price = Finance::amount_fix($cost_price);
        $sales_price=_post('sales_price','0.00');
        $sales_price = Finance::amount_fix($sales_price);

        $msg = '';

        if(!$country_id){
            $msg .= 'Country is required <br>';
        }
        if(!$category_id){
            $msg .= 'Category is required <br>';
        }
        if(!$template_id){
            $msg .= 'Template is required <br>';
        }
        if(!$created_date){
            $msg .= 'Date is required <br>';
        }
        if(!$cost_price){
            $msg .= 'Cost Pirce is required <br>';
        }
        if(!$sales_price){
            $msg .= 'Sales Price is required <br>';
        }
        if(!$billing_cycle){
            $msg .= 'Billing Cycle is required <br>';
        }
        if(!$expiry_day){
            $msg .= 'Days to Expiry is required <br>';
        }
        if(!$voucher_img){
            $msg .= 'Voucher Image is required <br>';
        }

        $voucher = ORM::for_table('voucher_format')
            ->where('country_id', $country_id)
            ->where('category_id', $category_id)
            ->find_one();


        if($voucher && ($id == '' || $id != $voucher['id'])){
            $msg .= 'Already Exist Voucher Format <br>';
        }

        if($msg == ''){

            if($id == ''){
                _msglog('s','Voucher Added Successfully');
                $d = ORM::for_table('voucher_format')->create();
            } else {
                _msglog('s','Voucher Updated Successfully');
                $d = ORM::for_table('voucher_format')->find_one($id);
            }


            $d->country_id = $country_id;
            $d->category_id = $category_id;
            $d->template_id = $template_id;
            $d->created_date = $created_date;
            $d->expiry_day = $expiry_day;
            $d->cost_price = $cost_price;
            $d->sales_price = $sales_price;
            $d->billing_cycle = $billing_cycle;
            $d->description = $description;
            $d->voucher_img = $voucher_img;

            $d->save();
            echo $d->id();
        } else {
            echo $msg;
        }

        break;

/*
 *  List Voucher  (list_voucher.tpl, modal_edit_voucher.tpl, list_voucher.js, voucher_codes.js)
 */

    case 'list_voucher':

        $vouchers = ORM::for_table('voucher_format')
            ->left_outer_join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_category.category_name')
            ->order_by_desc('voucher_format.created_date')
            ->find_many();

        $generated_voucher = array();
        $active_voucher =  array();
        $pages = array();

        foreach($vouchers as $v){
            $generated_voucher[$v['id']] = ORM::for_table('voucher_generated')->where('voucher_format_id', $v['id'])->count();
            $active_voucher[$v['id']] = ORM::for_table('voucher_generated')
                ->left_outer_join('sys_invoices', array('voucher_generated.invoice_id', '=', 'sys_invoices.id'))
                ->where('voucher_format_id', $v['id'])
                ->where('sys_invoices.status', 'Paid')
                ->count();
            $pages[$v['id']] = ORM::for_table('voucher_pages')->where('voucher_format_id', $v['id'])->count();
        }


        $baseUrl = APP_URL;

        $redeem_vouchers = ORM::for_table('voucher_generated')
            ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
            ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
            ->inner_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
            ->select('voucher_generated.*')
            ->select('crm_accounts.account', 'customer')
            ->select('voucher_country.country_name', 'country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.expiry_day', 'expiry_day')
            ->select('sys_invoices.status','invoice_status' )
            ->where('redeem_status', 'Redeem')
            ->find_array();

        $voucher_status = array();
        $today = date('Y-m-d');
        foreach ($redeem_vouchers as $v){

            $date1 = date_create($today);
            $date2 = date_create($v['expiry_date']);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));

            if($date2 < $date1){
                $voucher_status[$v['id']] = 'Expired';
            } elseif( $rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
                if($v['status'] == 'Paid'){
                    $voucher_status[$v['id']] = 'Active';
                }else {
                    $voucher_status[$v['id']] = 'Inactive';
                }

            }
        }


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));
        $ui->assign('xjq', '
            $(\'.amount\').autoNumeric(\'init\', {
            dGroup: ' . $config['thousand_separator_placement'] . ',
            aPad: ' . $config['currency_decimal_digits'] . ',
            pSign: \'' . $config['currency_symbol_position'] . '\',
            aDec: \'' . $config['dec_point'] . '\',
            aSep: \'' . $config['thousands_sep'] . '\',
            vMax: \'9999999999999999.00\',
            vMin: \'-9999999999999999.00\'
            });
            $(\'[data-toggle="tooltip"]\').tooltip();

        ');


        view('app_wrapper',[
            '_include' => 'list_voucher',
            'vouchers' => $vouchers,
            'generated_voucher' => $generated_voucher,
            'active_voucher' => $active_voucher,
            'redeem_vouchers' => $redeem_vouchers,
            'voucher_status' => $voucher_status,
            'pages' => $pages,
            'baseUrl' => $baseUrl,
        ]);
        break;

    case 'modal_edit_voucher':

        $id = route(3);
        $baseUrl = APP_URL;
        $country_list = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();
        $category_list = ORM::for_table('voucher_category')->order_by_asc('category_name')->find_array();
        $template_list = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();

        $voucher = ORM::for_table('voucher_format')
            ->left_outer_join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_category.category_name')
            ->order_by_asc('voucher_format.id')
            ->find_one($id);


        view('wrapper_modal',[
            '_include' => 'modal_edit_voucher',
            'baseUrl' => $baseUrl,
            'voucher' => $voucher,
            'country_list' => $country_list,
            'category_list' => $category_list,
            'template_list' => $template_list
        ]);


        break;

    case 'delete_voucher_format':

        Event::trigger('voucher/app/delete_voucher_format');
        $id = route(3);

        $d = ORM::for_table('voucher_format')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/list_voucher', 's', 'Country Delete Successfully');
        }


        break;

    case 'modal_generate_voucher':

        $id = route(3);
        $g_id = route(4);
        $baseUrl = APP_URL;


        $voucher = ORM::for_table('voucher_format')
            ->left_outer_join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_category.category_name')
            ->order_by_asc('voucher_format.id')
            ->find_one($id);

        $val = array();

        if($g_id){
            $g_id = str_replace('eid', '', $g_id);
            $val = ORM::for_table('voucher_generated')
                ->left_outer_join('voucher_template', array('voucher_template.id', '=', 'voucher_generated.template_id'))
                ->select_many('voucher_generated.*', 'voucher_template.cover_img', 'voucher_template.voucher_template', 'voucher_template.voucher_pgnum')
                ->find_one($g_id);
        }else{
            $val['id'] = '';
            $val['contact_id'] = '';
            $val['agent_id'] = '';
            $val['serial_number'] = '';
            $val['serial_pgnum'] = '';
            $val['template_id'] = '';
            $val['cover_img'] = '';
            $val['description'] = '';
            $val['create_invoice'] = '';
            $val['add_payment'] = '';
            $val['invoice_id'] = '';
            $val['status'] = 'Active';
        }

        $customers = ORM::for_table('crm_accounts')->where_in('type',array('Customer','Customer,Supplier'))->order_by_asc('account')->find_many();
        $suppliers = ORM::for_table('crm_accounts')->where_in('type',array('Supplier','Customer,Supplier'))->order_by_asc('account')->find_many();
//        $voucher_templates = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();

        // default setting

        $create_invoice = 'create';
        $add_payment = 'add_payment';


        view('wrapper_modal',[
           '_include' => 'modal_generate_voucher',
            'customers' => $customers,
            'suppliers' => $suppliers,
            'val' => $val,
            'voucher' => $voucher,
            'baseUrl' => $baseUrl,
            'create_invoice' => $create_invoice,
            'add_payment' => $add_payment,
        ]);
        break;

    case 'post_generate_voucher':

        $id = _post('vid');
        $gid = _post('gid');

        $contact_id = _post('contact_id');
        $agent_id = _post('agent_id');
        $prefix = _post('prefix');
        $serial_numbers = _post('serial_number');
        $total_voucher = _post('total_voucher');
        $date = _post('date');
        $create_invoice = _post('create_invoice');
        $add_payment = _post('add_payment');
        $description = _post('description');
        $template_id = _post('template_id');
        $invoice_id = _post('invoice_id');
        $status = _post('status');

        $msg = '';

        if(!$serial_numbers){
            $msg .= 'Serial Number is required <br>';
        }else{
            $serial_arr = explode(',',$serial_numbers);
            foreach($serial_arr as $s){
                $v = ORM::for_table('voucher_generated')->where('serial_number', $s)->find_one();
                if($gid){
                    if($v && $gid != $v['id']){
                        $msg .= $s.' is already exist <br>';
                    }
                }elseif($v){
                    $msg .= $s.' is already exist <br>';
                }
            }

        }

        if(!$template_id){
            $msg .= 'Template is required <br>';
        }

        if($msg == ''){

            if($total_voucher){

                // expiry date
                if($id != ''){

                    $v = ORM::for_table('voucher_format')->find_one($id);

                    if($date){
                        switch ($v['billing_cycle']){
                            case 'annual':
                                $interval = new DateInterval('P1Y');
                                $expiry_date = date_create($date)->add($interval);
                                $expiry_date = $expiry_date->format('Y-m-d');

                                break;
                            case 'monthly':
                                $interval = new DateInterval('P1M');
                                $expiry_date = date_create($date)->add($interval);
                                $expiry_date = $expiry_date->format('Y-m-d');
                                break;
                        }
                    }else{
                        $expiry_date = '';
                    }

                }

                /*
                // invoice create
                if($invoice_id == ''){

                    // Create Invoice

                    $invoice_id = null;

                    if($create_invoice == 'create'){

                        $voucher_info = ORM::for_table('voucher_format')
                            ->left_outer_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
                            ->find_one($id);

                        $amount = $total_voucher * $voucher_info['sales_price'];
                        $item_name = $voucher_info['country_name'].' '.$voucher_info['category'].' Voucher';
                        $invoice = Invoice::forSingleItem($contact_id, $item_name, $amount);

                        $invoice_id = $invoice['id'];

                    }

                    if($add_payment == 'add_payment'){
                        $invoice_info = ORM::for_table('sys_invoices')->find_one($invoice_id);
                        $total_price = $invoice_info['total'];
                        if($invoice_info){

                            $invoice_info->status = 'Paid';
                            $invoice_info->credit = $total_price;
                            $invoice_info->save();

                            // Customer balance chanage

                            $customer_info = ORM::for_table('crm_accounts')->find_one($contact_id);
                            if($customer_info){
                                if($customer_info['balance'] < $total_price){
                                    echo "Customer's credit balance is not enough";
                                    break;
                                }
                                $customer_info->balance = $customer_info['balance']-$total_price;
                                $customer_info->save();
                            }

                            // Transaction change

                            $account = 'Credit';
                            $type = 'Income';
                            $amount = $total_price;
                            $payer_id = $contact_id;
                            $method = 'Credit';
                            $ref = 'Client Paid with Account Credit';
                            $des = 'Invoice: '.$invoice_info->id().' Payment from Credit';
                            $date = date('Y-m-d');
                            $cr = $total_price;
                            $iid = $invoice_info->id();
                            $updated_at = date('Y-m-d H:i:s');

                            $transaction = ORM::for_table('sys_transactions')->create();
                            $transaction->account = $account;
                            $transaction->type = $type;
                            $transaction->amount = $amount;
                            $transaction->payerid = $payer_id;
                            $transaction->method = $method;
                            $transaction->ref = $ref;
                            $transaction->description = $des;
                            $transaction->date = $date;
                            $transaction->cr = $cr;
                            $transaction->iid = $iid;
                            $transaction->updated_at = $updated_at;
                            $transaction->save();

                        }

                    }

                }
                */

                $voucher_numbers = explode(',',$serial_numbers);

                for($i=1;$i<=$total_voucher;$i++){

                    if($gid == ''){
                        $d = ORM::for_table('voucher_generated')->create();
                        _msglog('s','Voucher Generated Successfully');
                    }else {
                        $d = ORM::for_table('voucher_generated')->find_one($gid);
                        _msglog('s','Voucher Updated Successfully');
                    }


                    $serial_number = $voucher_numbers[$i-1];

                    // voucher pdf create

//                    $template_file = 'apps/voucher/public/template/'.$voucher_template;
//                    $newfile = 'apps/voucher/public/vouchers/'.$serial_number.'.pdf';
                    $voucher_pdf = $serial_number.'.pdf';
//                    if(!copy($template_file,$newfile))
//                    {
//                        echo "failed to copy $file";
//                        break;
//                    } else {
//                      $voucher_pdf = $serial_number.'.pdf';
//                    }


                    // insert into database

                    $d->voucher_format_id = $id;
                    $d->contact_id = $contact_id;
                    $d->agent_id = $agent_id;
                    $d->serial_number = $serial_number;
//                    $d->serial_pgnum = $serial_pgnum;

                    if($create_invoice == 'create'){
                        $d->create_invoice = 1;
                    }else {
                        $d->create_invoice = 0;
                    }
                    if($add_payment == 'add_payment'){
                        $d->add_payment = 1;
                    }else {
                        $d->add_payment = 0;
                    }

                    $d->date = $date;
                    $d->expiry_date = $expiry_date;
                    $d->prefix = $prefix;
                    $d->description = $description;
                    $d->invoice_id = $invoice_id;
                    $d->template_id = $template_id;
                    $d->voucher_pdf = $voucher_pdf;
                    $d->status = $status;
                    $d->save();

                }

                echo $d->id();
            }

        } else {
            echo $msg;
        }


        break;

/*
 *  Generated Voucher Logic (generated_voucher_list.tpl)
 */

    case 'generated_voucher_list':

        Event::trigger('voucher/app/generated_voucher_list/');

        $vid = route(3);


//        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
//        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
//            's2/js/i18n/'.lan(),)));



        $redeem_voucher_pages = ORM::for_table('voucher_page_transaction')
            ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
            ->select_many('voucher_page_transaction.*', 'voucher_generated.contact_id', 'voucher_generated.voucher_format_id')
            ->select('sys_invoices.status', 'invoice_status')
            ->order_by_desc('id')
            ->find_array();


        $ui->assign('xheader',Asset::css(array('popover/popover','redactor/redactor','footable/css/footable.core.min','select/select.min','s2/css/select2.min','dt/dt','modal','dropzone/dropzone', 'dp/dist/datepicker.min')));
        $ui->assign('xfooter',Asset::js(array('popover/popover','redactor/redactor.min','footable/js/footable.all.min','js/redirect','select/select.min','s2/js/select2.min','s2/js/i18n/'.lan(),'dt/dt','modal', 'dropzone/dropzone', 'dp/dist/datepicker.min')));
        $ui->assign('jsvar', '
        _L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';
        ');


        view('app_wrapper',[
            '_include' => 'generated_voucher_list',
            'redeem_voucher_pages' => $redeem_voucher_pages,
            'vid' => $vid
        ]);

        break;

    case 'json_voucher_list':


        $format_id = route(3);

        $columns = array();

        $columns[] = 'id';
        $columns[] = 'voucher_img';
        $columns[] = 'date';
        $columns[] = 'prefix';
        $columns[] = 'serial_number';
        $columns[] = 'contact_name';
        $columns[] = 'agent_name';
        $columns[] = 'date';
        $columns[] = 'redeem';
        $columns[] = 'description';
        $columns[] = 'status';
        $columns[] = '';


        $order_by = $_POST['order'];

        $o_c_id = $order_by[0]['column'];
        $o_type = $order_by[0]['dir'];

        $a_order_by = $columns[$o_c_id];



        $d = ORM::for_table('voucher_generated')
            ->left_outer_join('crm_accounts',array('voucher_generated.contact_id','=','contact.id'),'contact')
            ->left_outer_join('crm_accounts',array('voucher_generated.agent_id','=','agent.id'), 'agent')
            ->left_outer_join('voucher_format',array('voucher_format.id','=','voucher_generated.voucher_format_id'));

        $d->select('voucher_generated.id','id');
        $d->select('voucher_generated.date', 'date');
        $d->select('voucher_generated.expiry_date', 'expiry_date');
        $d->select('voucher_generated.prefix','prefix');
        $d->select('voucher_generated.contact_id', 'contact_id');
        $d->select('voucher_generated.agent_id', 'agent_id');
        $d->select('voucher_generated.serial_number', 'serial_number');
        $d->select('voucher_generated.description', 'description');
        $d->select('voucher_generated.voucher_format_id', 'voucher_format_id');
        $d->select('voucher_generated.invoice_id', 'invoice_id');
        $d->select('voucher_generated.status', 'status');
        $d->select('voucher_format.voucher_img', 'img');
        $d->select('voucher_format.billing_cycle', 'billing_cycle');
        $d->select('voucher_format.expiry_day', 'expiry_day');
        $d->select('contact.account', 'contact_name');
        $d->select('agent.account', 'agent_name');

        $d->where_equal('voucher_format_id', $format_id);

        $prefix = _post('prefix');

        if($prefix != ''){

            $d->where_like('prefix',"%$prefix%");

        }

        $serial_number = _post('serial_number');

        if($serial_number != ''){

            $d->where_like('serial_number',"%$serial_number%");

        }

        $contact_name = _post('contact');

        if($contact_name != ''){

            $d->where_like('contact.account',"%$contact_name%");

        }

        $agent_name = _post('agent');

        if($agent_name != ''){

            $d->where_like('agent.account',"%$agent_name%");

        }



        $iTotalRecords =  $d->count();

        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;


        if($o_type == 'desc'){
            $d->order_by_desc($a_order_by);
        }
        else{
            $d->order_by_asc($a_order_by);
        }

        $d->limit($iDisplayLength);
        $d->offset($iDisplayStart);
        $x = $d->find_array();

        $i = $iDisplayStart;
        $colors = Colors::colorNames();

        foreach ($x as $xs) {

            $img = '<img src="' . APP_URL . '/apps/voucher/public/voucher_imgs/' . $xs['img'] . '" width="42px" alt="">';

            if($xs['agent_name'] == ''){
                $xs['agent_name']  =  '-';
            }else{
                $xs['agent_name'] = "<a href='".U."contacts/view/".$xs['agent_id']."/summary/'>".$xs['agent_name']."</a>";
            }
            if($xs['contact_name'] == ''){
                $xs['contact_name'] = '-';
            }else{
                $xs['contact_name'] = "<a href='".U."contacts/view/".$xs['contact_id']."/summary/'>".$xs['contact_name']."</a>";
            }


            $page_count = ORM::for_table('voucher_pages')->where('voucher_format_id', $xs['voucher_format_id'])->count();
            $redeem_count = ORM::for_table('voucher_page_transaction')->where('voucher_id', $xs['id'])->count();
            $voucher_invoice = ORM::for_table('sys_invoices')->where_equal('id', $xs['invoice_id'])->find_one();


            if($xs['status'] == 'Inactive' || $xs['status'] == ''){
                $voucher_status = "<a href='#' class='btn btn-xs square-deactive' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Inactive'>Inactive</a>";

                if($xs['date'] != '0000-00-00' && $xs['expiry_date'] < date('Y-m-d')){
                    $voucher_status = "<a href='#' class='btn btn-xs square-expire' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Expired'>Expired</a>";
                }

                $xs['date'] = '-';
                $xs['expiry_date'] = '-';
            }

            if($xs['status'] == 'Active'){
                $voucher_status = "<a href='#' class='btn btn-xs square-active' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Active'>Active</a>";
                $img = "<a href='".U."voucher/app/list_voucher_page/".$format_id."/".$xs['id']."'>".$img."</a>";
                $xs['serial_number'] = "<a href='".U."voucher/app/list_voucher_page/".$format_id."/".$xs['id']."'>".$xs['serial_number']."</a>";

                if($xs['date'] != '0000-00-00' && $xs['expiry_date'] < date('Y-m-d')){
                    $voucher_status = "<a href='#' class='btn btn-xs square-expire' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Expired'>Expired</a>";
                }

                if($xs['date'] == '0000-00-00'){
                    $xs['date'] = '-';
                    $xs['expiry_date'] = '-';
                }

            }


            $records["data"][] = array(
                //  0 => $xs['id'],
                0 => $xs['id'],
                1 => $img,
                2 => $xs['date'],
                3 => htmlentities($xs['prefix']),
                4 => $xs['serial_number'],
                5 => $xs['contact_name'],
                6 => $xs['agent_name'],
                7 => $xs['expiry_date'],
                8 => $page_count. '<span style="color:#CAA931">('.$redeem_count.')</span>',
                9 => htmlentities($xs['description']),
                10 => $voucher_status,
                11 => '
                <a href="' . U . 'voucher/app/download_generated_voucher/' . $xs['id'] . '" class="btn btn-primary btn-xs cview" id="vid' . $xs['id'] . '"><i class="fa fa-download"></i> </a>
                <a href="#" class="btn btn-warning btn-xs cedit" id="eid' . $xs['id'] . '"><i class="glyphicon glyphicon-pencil"></i> </a>
                <a href="#" class="btn btn-danger btn-xs cdelete" id="uid' . $xs['id'] . '"><i class="fa fa-trash"></i> </a>
                ',

                12 => $format_id,

                "DT_RowId" => 'dtr_' . $xs['id']


            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        api_response($records);


        break;

    case 'delete_generated_voucher':

        Event::trigger('voucher/app/delete_generated_voucher');
        $id = route(3);
        $id = str_replace('uid','',$id);

        $d = ORM::for_table('voucher_generated')->find_one($id);
        $format_id = $d['format_id'];

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/generated_voucher_list/'.$format_id, 's', 'Voucher Delete Successfully');
        }

        break;

    case 'edit_generated_voucher':

        $id = route(3);


        break;

    case 'download_generated_voucher':

        $id = route(3);

        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('voucher_template', array('voucher_template.id', '=', 'voucher_generated.template_id'))
            ->select_many('voucher_generated.*', 'voucher_template.cover_img', 'voucher_template.voucher_template', 'voucher_template.voucher_pgnum')
            ->find_one($id);

        $template_file = 'apps/voucher/public/template/'.$voucher_data['voucher_template'];
        $newfile = $voucher_data['serial_number'].'.pdf';

        $pdf = new \Mpdf\Mpdf(['format' => [250, 148]]);
        $pdf->SetImportUse();
        $pagecount = $pdf->SetSourceFile($template_file);

        for ($i=1; $i<=$pagecount; $i++) {
            $import_page = $pdf->ImportPage($i);
            $pdf->SetPageTemplate($import_page);

            if($i == 8){
                $pdf->SetFont('Arial','B',16);
                $pdf->SetXY(140,109);
                $pdf->cell(0,0,$voucher_data['prefix'].' '.$voucher_data['serial_number']);
            }

            if($i == 52 ){
                $pdf->SetFont('Arial','B',16);
                $pdf->SetXY(109,74);
                $pdf->cell(0,0,$voucher_data['prefix'].' '.$voucher_data['serial_number']);
            }
            $pdf->AddPage();

        }

        $pdf->Output($newfile, 'D');

        r2(U . 'voucher/app/generated_voucher_list/'.$voucher_data['voucher_format_id'], 's', $_L['Voucher Downloaded Successfully']);


        break;

/*
 *  Voucher transaction
 */

    case 'voucher_transaction':

        Event::trigger('client/app/voucher_transaction/');

        $customers = ORM::for_table('crm_accounts')->where_in('type',array('Customer','Customer,Supplier'))->order_by_asc('account')->find_many();
        $agents = ORM::for_table('crm_accounts')->where_in('type',array('Supplier','Customer,Supplier'))->order_by_asc('account')->find_many();
        $countries = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();
        $categories = ORM::for_table('voucher_category')->order_by_asc('category_name')->find_array();


        $ui->assign('xheader', Asset::css(array('s2/css/select2.min', 'dt/dt', 'fc/fc', 'fc/fc_ibilling', 'daterangepicker/daterangepicker')));
        $ui->assign('xfooter', Asset::js(array('s2/js/select2.min', 's2/js/i18n/' . lan() , 'dt/dt', 'fc/fc', 'daterangepicker/daterangepicker','numeric')));
        $ui->assign('xjq', '
            $(\'.amount\').autoNumeric(\'init\', {
            dGroup: ' . $config['thousand_separator_placement'] . ',
            aPad: ' . $config['currency_decimal_digits'] . ',
            pSign: \'' . $config['currency_symbol_position'] . '\',
            aDec: \'' . $config['dec_point'] . '\',
            aSep: \'' . $config['thousands_sep'] . '\',
            vMax: \'9999999999999999.00\',
                        vMin: \'-9999999999999999.00\'

            });
            $(\'[data-toggle="tooltip"]\').tooltip();

        ');


        view('app_wrapper',[
            '_include' => 'voucher_transaction',
            'countries' => $countries,
            'categories' => $categories,
            'customers' => $customers,
            'agents' => $agents
        ]);
        break;

    case 'tr_list':

        //  sleep(5);

        $columns = array();
        $columns[] = 'id';
        $columns[] = 'date';
        $columns[] = 'customer';
        $columns[] = 'agent';
        $columns[] = 'country_name';
        $columns[] = 'category';
        $columns[] = 'date';
        $columns[] = 'serialnumber';
        $columns[] = 'status';
        $columes[] = '';


        $order_by = $_POST['order'];
        $o_c_id = $order_by[0]['column'];
        $o_type = $order_by[0]['dir'];
        $a_order_by = $columns[$o_c_id];



        $d = ORM::for_table('voucher_generated')
            ->left_outer_join('crm_accounts',array('contact.id','=','voucher_generated.contact_id'),'contact')
            ->left_outer_join('crm_accounts',array('agent.id','=','voucher_generated.agent_id'),'agent')
            ->left_outer_join('voucher_format',array('voucher_format.id','=','voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
            ->left_outer_join('sys_invoices',array('sys_invoices.id','=','voucher_generated.invoice_id'))
            ->left_outer_join('voucher_category',array('voucher_category.id','=','voucher_format.category_id'));

        $d->select('voucher_generated.id','id');
        $d->select('voucher_generated.voucher_format_id');
        $d->select('voucher_generated.date', 'date');
        $d->select('voucher_generated.prefix', 'prefix');
        $d->select('voucher_generated.expiry_date', 'expiry_date');
        $d->select('voucher_generated.serial_number', 'serialnumber');
        $d->select('voucher_generated.contact_id', 'customer_id');
        $d->select('voucher_generated.agent_id', 'agent_id');
        $d->select('voucher_generated.invoice_id', 'invoice_id');
        $d->select('voucher_generated.status', 'status');
        $d->select('voucher_format.billing_cycle', 'billing_cycle');
        $d->select('voucher_format.expiry_day', 'expiry_day');
        $d->select('voucher_format.country_id', 'country_id');
        $d->select('voucher_format.category_id', 'category_id');
        $d->select('voucher_country.country_name', 'country_name');
        $d->select('voucher_category.category_name', 'category_name');
        $d->select('contact.account', 'customer');
        $d->select('agent.account', 'agent');
        $d->select('sys_invoices.status','invoice_status');


        $customer_id = _post('customer');
        if($customer_id != ''){
            $d->where_equal('voucher_generated.contact_id', $customer_id);
        }

        $agent_id = _post('agent');
        if($agent_id != ''){
            $d->where_equal('voucher_generated.agent_id', $agent_id);
        }

        $category = _post('category');
        if ($category != '') {
            $d->where_equal('voucher_format.category_id', $category);
        }

        $country_id = _post('country');
        if($country_id != ''){
            $d->where_equal('voucher_format.country_id', $country_id);
        }


        $reportrange = _post('reportrange');
        if ($reportrange != '') {
            $reportrange = explode('-', $reportrange);
            $from_date = trim($reportrange[0]);
            $to_date = trim($reportrange[1]);
            $d->where_gte('date', $from_date);
            $d->where_lte('date', $to_date);
        }

        $status = _post('status');
        if($status != ''){
            $today = date('Y-m-d');
            switch ($status){
                case 'Expired':
                    $d->where_lt('expiry_date', $today);
                    break;
                case 'Active':
                    $d->where_equal('voucher_generated.status','Active');
                    $d->where_gte('expiry_date',$today);
                    break;
                case 'Inactive':
                    $d->where_equal('voucher_generated.status','Inactive');
//                    $d->where_any_is(array(
//                        array('sys_invoices.status'=>'Unpaid'),
//                        array('sys_invoices.status'=>'Partially Paid'),
//                        array('voucher_generated.invoice_id'=>'0'))
//                    );
                    $d->where_gte('expiry_date',$today);
                    break;

            }
        }


        $filter_customer = _post('filter_customer');
        if($filter_customer != ''){
            $d->where_like('contact.account',"%$filter_customer%");
        }

        $filter_agent = _post('filter_agent');
        if($filter_agent != ''){
            $d->where_like('agent.account',"%$filter_agent%");
        }

        $filter_category = _post('filter_category');
        if($filter_category != ''){
            $d->where_like('voucher_category.category_name',"%$filter_category%");
        }

        $filter_country = _post('filter_country');
        if($filter_country != ''){
            $d->where_like('voucher_country.country_name',"%$filter_country%");
        }

        $filter_serialnumber = _post('filter_serialnumber');
        if($filter_serialnumber != ''){
            $d->where_like('serial_number',"%$filter_serialnumber%");
        }


//        $x = $d->find_array();
        $iTotalRecords = $d->count();


        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        if ($o_type == 'desc') {
            $d->order_by_desc($a_order_by);
        }
        else {
            $d->order_by_asc($a_order_by);
        }

        $d->limit($iDisplayLength);
        $d->offset($iDisplayStart);
        $x = $d->find_array();

        $i = $iDisplayStart;

//        print_r($x);

        foreach($x as $key => $xs) {

            $today = date('Y-m-d');

            if($xs['status'] == 'Active' && $xs['expiry_date'] > $today){
                $status_str = "<div class='label-success' style='margin:0 auto; font-size:85%;width:65px'>Active</div>";
            }
            if(($xs['status'] == 'Inactive' || $xs['status'] == '') && $xs['expiry_date'] > $today){
                $status_str = "<div class='label-default' style='margin:0 auto; font-size:85%;width:65px'>Inactive</div>";
            }
            if($xs['expiry_date'] < $today){
                $status_str = "<div class='label-danger' style='margin:0 auto; font-size:85%;width:65px'>Expired</div>";
            }

            if($xs['customer_id']){
                $xs['customer'] = '<a href="'. U .'contacts/view/'.$xs['customer_id'].'/summary/">'.$xs['customer'].'</a>';
            }else{
                $xs['customer'] = '-';
            }

            if($xs['agent_id']){
                $xs['agent'] = '<a href="'. U .'contacts/view/'.$xs['agent_id'].'/summary/">'.$xs['agent'].'</a>';
            }else{
                $xs['agent'] = '-';
            }

            $xs['serialnumber'] = '<a href="'. U .'voucher/app/list_voucher_page/'.$xs['voucher_format_id'].'/'.$xs['id'].'">'.$xs['prefix'].$xs['serialnumber'].'</a>';

            $records["data"][] = array(
                0 => $xs['id'],
                1 => $xs['date'],
                2 => $xs['customer'],
                3 => $xs['agent'],
                4 => htmlentities($xs['country_name']),
                5 => htmlentities($xs['category_name']),
                6 => $xs['expiry_date'],
                7 => $xs['serialnumber'],
                8 => $status_str,
                9 => '<a href="' . U . 'voucher/app/list_voucher_page/' .$xs['voucher_format_id'].'/'.$xs['id']. '" class="btn btn-primary btn-xs"><i class="fa fa-file-text-o"></i></a>'.' '.'<a href="#" class="btn btn-danger btn-xs cdelete" id="'.$xs['id'].'"><i class="fa fa-trash"></i></a>',
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        api_response($records);

        break;

    case 'delete_voucher':

        Event::trigger('voucher/app/delete_voucher');
        $id = route(3);
//        $id = str_replace('uid','',$id);

        $d = ORM::for_table('voucher_generated')->find_one($id);
        $format_id = $d['format_id'];

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/voucher_transaction/', 's', 'Voucher Delete Successfully');
        }

        break;

/*
 *  Voucher Settings
 */

    case 'voucher_setting':

        $setting_data = ORM::for_table('voucher_setting')->find_array();

        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));
        $ui->assign('xjq', '
            $(\'.amount\').autoNumeric(\'init\', {
            dGroup: ' . $config['thousand_separator_placement'] . ',
            aPad: ' . $config['currency_decimal_digits'] . ',
            pSign: \'' . $config['currency_symbol_position'] . '\',
            aDec: \'' . $config['dec_point'] . '\',
            aSep: \'' . $config['thousands_sep'] . '\',
            vMax: \'9999999999999999.00\',
            vMin: \'-9999999999999999.00\'
            });
            $(\'[data-toggle="tooltip"]\').tooltip();

        ');


        view('app_wrapper',[
            '_include' => 'voucher_setting',
            'setting' => $setting,
        ]);

        break;

    case 'post_setting':

        $agreement_text = _post('agreement_text');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','agreement_text')->find_one();
        $d->value = $agreement_text;
        $d->save();

        $activation_fee = _post('activation_fee');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','activation_fee')->find_one();
        $d->value = $activation_fee;
        $d->save();

        _msglog('s','Settings Updated Successfully');
        echo $d->id();


        break;

    case 'post_alert':

        $alert_message = _post('alert_message');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','alert_message')->find_one();
        $d->value = $alert_message;
        $d->save();

        _msglog('s','Settings Updated Successfully');
        echo $d->id();
        break;

    case 'update_settings':

        $opt = _post('opt');
        $val = _post('val');

        $d = ORM::for_table('voucher_setting')->where_equal('setting',$opt)->find_one();
        $d->value = $val;
        $d->save();

        break;

/*
 *  Voucher Pages (list_voucher_pages.tpl, add_page.tpl, list_voucher_pages.js, add_page.js)
 */

    case 'list_voucher_page':

        $vid = route(3);
        $gid = route(4);
        $baseUrl =APP_URL;

        $voucher_pages = ORM::for_table('voucher_pages')->where_equal('voucher_format_id',$vid)->order_by_asc('id')->find_many();
        $voucher_format = ORM::for_table('voucher_format')
            ->left_outer_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select_many('voucher_format.*', 'voucher_country.country_name', 'voucher_category.category_name')
            ->find_one($vid);

        $page_status = array();
        $today = date('Y-m-d');
        $total_pages = 0;

        foreach($voucher_pages as $v){
            $page_status[$v['id']] = 'redeem';
            $redeem_page = ORM::for_table('voucher_page_transaction')
                ->where('voucher_id', $gid)
                ->where('page_id',$v['id'])
                ->find_one();

            if($redeem_page) {
                if ($redeem_page['status'] == 'Confirm') {
                    $page_status[$v['id']] = 'confirm';
                }elseif ($redeem_page['status'] == 'Processing' && $redeem_page['return_date'] < $today) {
                    $redeem_page->status = 'Confirm';
                    $page_status[$v['id']] = 'confirm';
                    $redeem_page->save();
                } else {
                    $page_status[$v['id']] = 'processing';
                }
            }
            $total_pages++;
        }

        $voucher_info = array();
        $recent_transaction = array();
        $invoice_url = array();
        $account_url = array();
        if($gid != ''){
            $voucher_info = ORM::for_table('voucher_generated')
                ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
                ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
                ->select_many('voucher_generated.*', 'voucher_category.category_name')
                ->find_one($gid);

            $recent_transaction = ORM::for_table('voucher_page_transaction')
                ->left_outer_join('sys_invoices', array('sys_invoices.id','=','voucher_page_transaction.invoice_id'))
                ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
                ->select_many('voucher_page_transaction.*')
                ->select('sys_invoices.id', 'invoice_id')
                ->select('sys_invoices.total', 'invoice_amount')
                ->select('sys_invoices.date', 'invoice_date')
                ->select('sys_invoices.vtoken', 'invoice_vtoken')
                ->select('sys_invoices.status', 'invoice_status')
                ->select('voucher_generated.contact_id')
                ->where('voucher_id', $gid)
                ->order_by_desc('id')
                ->find_array();

            foreach($recent_transaction as $r){
                $invoice_url[$r['id']] = U.'invoices/view/'.$r['invoice_id'].'/';
                $account_url[$r['id']] = U.'contacts/view/'.$r['contact_id'].'/summary/';
            }


        }


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        view('app_wrapper',[
            '_include' => 'list_voucher_pages',
            'voucher_pages' => $voucher_pages,
            'voucher_format' => $voucher_format,
            'voucher_img' => $voucher_format['voucher_img'],
            'voucher_id' => $voucher_format['id'],
            'page_status' => $page_status,
            'gid' => $gid,
            'voucher_info' => $voucher_info,
            'recent_transaction' => $recent_transaction,
            'invoice_url' => $invoice_url,
            'account_url' => $account_url,
            'total_pages' => $total_pages,
            'baseUrl' => $baseUrl

        ]);

        break;

    case 'add_page':

        $voucher_format_id = route(3);
        $page_id = route(4);

        $type = "add";
        $page_data = null;
        $baseUrl = APP_URL;

        if($page_id){
            $page_data = ORM::for_table('voucher_pages')->find_one($page_id);
            $type = "edit";
        }

        $product_list = ORM::for_table('sys_items')->order_by_asc('name')->find_many();
        $cf = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));
        view('app_wrapper',[
            '_include' => 'add_page',
            'val' => $page_data,
            'pid' => $page_id,
            'vid' => $voucher_format_id,
            'product_list' => $product_list,
            'type' => $type,
            'baseUrl' => $baseUrl,
            'content_inner' => inner_contents($config['c_cache']),
            'cf' => $cf

        ]);

        break;
        
    case 'view_page':

        $voucher_format_id = route(3);
        $page_id = route(4);

        $type = "add";
        $page_data = null;
        $baseUrl = APP_URL;

        if($page_id){
            $page_data = ORM::for_table('voucher_pages')->find_one($page_id);
            $type = "view";
        }

        $product_list = ORM::for_table('sys_items')->order_by_asc('name')->find_many();
        $cf = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        view('app_wrapper',[
            '_include' => 'add_page',
            'val' => $page_data,
            'pid' => $page_id,
            'vid' => $voucher_format_id,
            'product_list' => $product_list,
            'type' => $type,
            'baseUrl' => $baseUrl,
            'content_inner' => inner_contents($config['c_cache']),
            'cf' => $cf

        ]);
        break;

    case 'clone_page':

        $voucher_format_id = route(3);
        $page_id = route(4);

        $page_data = null;
        $baseUrl = APP_URL;

        if($page_id){
            $page_data = ORM::for_table('voucher_pages')->find_one($page_id);
        }

        if($page_data){
            $clone_page = ORM::for_table('voucher_pages')->create();

            $clone_page->voucher_format_id = $voucher_format_id;
            $clone_page->title = $page_data['title'];
            $clone_page->product_id = $page_data['product_id'];
            $clone_page->status_id = $page_data['status_id'];
            $clone_page->payment_req = $page_data['payment_req'];
            $clone_page->address = $page_data['address'];
            $clone_page->date_range = $page_data['date_range'];
            $clone_page->remark = $page_data['remark'];
            $clone_page->front_img = $page_data['front_img'];
            $clone_page->back_img = $page_data['back_img'];

            $clone_page->save();

            r2(U.'voucher/app/list_voucher_page/'.$voucher_format_id,'s','Cloned Voucher Page');
        }



        break;

    case 'delete_page':

        Event::trigger('voucher/app/delete_page');
        $vid = route(3);
        $pid = route(4);


        $d = ORM::for_table('voucher_pages')->find_one($pid);

        if ($d) {
            $d->delete();
            r2(U.'voucher/app/list_voucher_page/'.$vid.'/'.$pid.'/','s','Voucher page Deleted Successfully');
        }

        break;

    case 'post_page':

        $voucher_format_id = _post('vid');
        $page_id = _post('pid');
        $title = _post('title');
        $product_id = _post('product_id');
        $sub_product_id = _post('sub_product_id');
        $payment_req = _post('payment_req');
        $status_id = _post('status_id');
        $address = _post('address');
        $date_range = _post('date_range');
        $remark = _post('remark');
        $front_img = _post('front_img');
        $back_img = _post('back_img');


        // Validate

        $msg = '';

        if(!$title){
            $msg .= 'Title is required <br>';
        }
        if(!$status_id){
            $msg .= 'Status is requried <br>';
        }
        if(!$front_img){
            $msg .= 'Front Image is required <br>';
        }
        if(!$back_img){
            $msg .= 'Back Image is required <br>';
        }

        if($payment_req == '1' && !$product_id){
            $msg .= 'Product is required <br>';
        }

        if($msg ==''){
            if($page_id == ''){
                _msglog('s','Voucher Page Added Successfully');
                $d = ORM::for_table('voucher_pages')->create();
            } else {
                _msglog('s','Voucher Page Update Successfully');
                $d = ORM::for_table('voucher_pages')->find_one($page_id);
            }

            $d->voucher_format_id = $voucher_format_id;
            $d->title = $title;
            $d->product_id = $product_id;
            $d->sub_product_id = $sub_product_id;
            $d->status_id = $status_id;
            $d->payment_req = $payment_req;
            $d->address = $address;
            $d->date_range = $date_range;
            $d->remark = $remark;
            $d->front_img = $front_img;
            $d->back_img = $back_img;

            $d->save();

//            r2(U.'voucher/app/list_voucher_page/'.$voucher_format_id);
            echo $d->id();

        }  else {
            echo $msg;
        }

        break;

    case 'customfields-post':

        $fieldname = _post('fieldname');
        $fieldtype = _post('fieldtype');
        $description = _post('description');
        $validation = _post('validation');
        $options = _post('options');

        if($fieldname != ''){

            $d = ORM::for_table('voucher_customfields')->create();
            $d->fieldname = $fieldname;
            $d->fieldtype = $fieldtype;
            $d->description = $description;
            $d->regexpr = $validation;
            $d->fieldoptions = $options;
            $d->ctype = 'crm';
            $d->relid = 0;
            $d->adminonly = '';
            $d->required = '';
            $d->showorder = '';
            $d->sorder = '0';
            $d->save();

            echo $d->id();
        }
        else{
            echo 'Name is Required';
        }

        break;

    case 'customfield-edit-post':

        $id = _post('id');

        $fieldname = _post('fieldname');

        if($fieldname == ''){
            ib_die('Name is Required');
        }

        $d = ORM::for_table('voucher_customfields')->find_one($id);
        if($d){

            $fieldtype = _post('fieldtype');
            $description = _post('description');
            $validation = _post('validation');
            $options = _post('options');

            $d->fieldname = $fieldname;
            $d->fieldtype = $fieldtype;
            $d->description = $description;
            $d->regexpr = $validation;
            $d->fieldoptions = $options;
            $d->ctype = 'crm';
            $d->relid = '';
            $d->adminonly = '';
            $d->required = '';
            $d->showorder = '';
            $d->sorder = '0';
            $d->save();
            echo $id;
        }
        else{
            echo 'Not Found';
        }


        break;

    case 'customfields-ajax-add':

        $ui->assign('content_inner',inner_contents($config['c_cache']));

        view('wrapper_modal',[
            '_include' => 'ajax-add-custom-field'
        ]);
        break;

    case 'customfields-ajax-edit':

        $id = route(3);
        $id = str_replace('f','',$id);

        $d = ORM::for_table('voucher_customfields')->find_one($id);
        if($d){
            $ui->assign('d',$d);
            view('wrapper_modal',[
                '_include' => 'ajax-edit-custom-field'
            ]);
        }
        else{
            echo 'Not Found';
        }


        break;

    case 'delete_customfield':

        $id = route(3);
        $vid = route(4);
        $pid = route(5);

        $id = str_replace('d','',$id);

        $d = ORM::for_table('voucher_customfields')->find_one($id);
        if($d){

            $d->delete();
            if($pid){
                r2(U.'voucher/app/add_page/'.$vid.'/'.$pid.'/','s','Custom Field Deleted Successfully');
            }else{
                r2(U.'voucher/app/add_page/'.$vid,'s','Custom Field Deleted Successfully');
            }

        }
        else{
            echo 'Custom Field Not found';
        }

        break;

    case 'view_redeem_page':

        $voucher_id = route(3);
        $page_id = route(4);
        $type = route(5);
        $baseUrl = APP_URL;

        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=','voucher_generated.invoice_id'))
            ->select('voucher_generated.*')
            ->select('sys_invoices.status', 'invoice_status')
            ->find_one($voucher_id);

        $account_id = $voucher_data['contact_id'];

        if($voucher_data['redeem_status'] != 'Redeem'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is not Redeemed');
        }

        if($voucher_data['invoice_status'] != 'Paid'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is not Paid');
        }

        $transaction_data = array();
        if($type == 'edit' || $type == 'view'){
            $transaction_data = ORM::for_table('voucher_page_transaction')
                ->where('voucher_id', $voucher_id)
                ->where('page_id', $page_id)
                ->find_one();
        }else{
            $transaction_data['id'] = '';
            $transaction_data['departure_date'] = '';
            $transaction_data['return_date'] = '';
            $transaction_data['total_days'] = '';
            $transaction_data['remark'] = '';
            $transaction_data['sub_product_req'] = '';
            $transaction_data['invoice_id'] = '';
        }

        $page_setting =ORM::for_table('voucher_pages')->find_one($page_id);

        $customer_data = ORM::for_table('crm_accounts')->find_one($account_id);
        $customer_addr =$customer_data['address']."  ".$customer_data['city']."  ".$customer_data['state']."  ".$customer_data['country']."   ".$customer_data['zip'];
        $product_data = ORM::for_table('sys_items')->find_one($page_setting['product_id']);
        $sub_product_data = ORM::for_table('sys_items')->find_one($page_setting['sub_product_id']);

        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_country.*')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.voucher_img', 'voucher_img')
            ->select('voucher_generated.serial_number', 'serial_number')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();


        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();

        $cf_value=array();

        if($transaction_data){
            foreach ($fs as $f) {
                $cf=ORM::for_table('voucher_customfieldsvalues')->where('relid',$transaction_data['id'])->where('fieldid',$f->id)->find_one();
                $cf_value[$f->id]='';
                if($cf){
                    $cf_value[$f->id]=$cf->fvalue;
                }
            }
        }

        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));


        view('app_wrapper',[
            '_include' => 'voucher_redeem_page',
            'page_setting' => $page_setting,
            'customer_data' => $customer_data,
            'customer_addr' => $customer_addr,
            'product_data' => $product_data,
            'sub_product_data' => $sub_product_data,
            'voucher_info' => $voucher_info,
            'transaction_data' => $transaction_data,
            'fs' => $fs,
            'cf_value' => $cf_value,
            'baseUrl' => $baseUrl,
            'voucher_id' => $voucher_id,
            'page_id' => $page_id,
            'vid' => $voucher_data['voucher_format_id'],
            'account_id' => $account_id,
            'type' => $type

        ]);

        break;

    case 'post_redeem_page':

        $tid = _post('tid');
        $status = _post('status');

        $voucher_id = _post('voucher_id');
        $page_id = _post('page_id');
        $account_id = _post('account_id');

        $page_title = _post('page_title');
        $product_name = _post('product_name');
        $product_price = _post('product_price');
        $sub_product_name = _post('sub_product_name');
        $sub_product_price = _post('sub_product_price');
        $voucher_number = _post('voucher_number');
        $country_name = _post('country_name');
        $category = _post('category');
        $customer_name = _post('customer_name');
        $customer_address = _post('customer_address');
        $departure_date = _post('departure_date');
        $return_date = _post('return_date');
        $total_days = _post('total_days');
        $remark = _post('remark');
        $sub_product_req = _post('sub_product_req');

        $invoice_id = _post('invoice_id');
        $invoice_id = $invoice_id?$invoice_id:'';



        $page_setting = array();
        if($page_id){
            $page_setting = ORM::for_table('voucher_pages')->find_one($page_id);
        }

        $msg = '';

        if($page_setting['date_range'] == 1 && $departure_date ==''){
            $msg .= 'Departure Date is required <br>';
        }

        if($page_setting['date_range'] == 1 && $return_date ==''){
            $msg .= 'Return Date is required <br>';
        }

        if($page_setting['date_range'] == 1 && $total_days == ''){
            $msg .= 'Return Date or Departure date is wrong<br>';
        }


        if($msg == ''){
            if($tid == ''){
                $d = ORM::for_table('voucher_page_transaction')->create();
                _msglog('s','Redeemed Successfully');
                if($page_setting['date_range'] == 1){
                    $status = 'Processing';
                }else {
                    $status = 'Confirm';
                }
            }else{

                if($invoice_id){
                    if($sub_product_req == 1){
                        $product_amount = round((float)$product_price + (float)$sub_product_price,2);
                    }else {
                        $product_amount = round((float)$product_price,2);
                    }

                    $product = ($sub_product_name != '' && $sub_product_req == 1)?'Voucher page redeem ('.$product_name.' + '.$sub_product_name.')':'Voucher page redeem ('.$product_name.')';

                    $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);

                    if($invoice_data['status'] != 'Paid'){
                        $invoice_data->total = $product_amount;
                        $invoice_data->subtotal = $product_amount;
                        $invoice_data->save();

                        $invoice_item = ORM::for_table('sys_invoiceitems')->where('sys_invoiceitems.invoiceid', $invoice_id)->find_one();
                        $invoice_item->amount = $product_amount;
                        $invoice_item->total = $product_amount;
                        $invoice_item->description = $product;
                        $invoice_item->save();

                    }else{
                        _msglog('r','This page paid already');
                        echo "page_list";
                        exit;
                    }

                }

                $d = ORM::for_table('voucher_page_transaction')
                    ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
                    ->select_many('voucher_page_transaction.*')
                    ->select('sys_invoices.id', 'invoice_id')
                    ->select('sys_invoices.vtoken', 'invoice_vtoken')
                    ->select('sys_invoices.status', 'invoice_status')
                    ->find_one($tid);

                switch ($status) {
                    case 'Redeem':
                        if ($d['invoice_status'] == 'Paid') {
                            $msg = 'This page is paid already <br>';
                            _msglog('r', $msg);
                            echo "reload";
                            exit;
                        }
                        break;
                    case 'Processing':

                        break;
                    case 'Confirm':
                        if ($d['invoice_status'] != 'Paid') {
                            $msg = 'This page is not paid <br>';
                            _msglog('r', $msg);
                            $invoice_url = U . 'invoices/view/' . $invoice_id . '/';
                            echo $invoice_url;
                            exit;
                        }

                }
                _msglog('s','Page Redeem updated Successfully');
            }




            // Invoice

            $invoice_url = "";

            if($page_setting['payment_req'] == 1 && $tid == ''){
                if($sub_product_req == 1){
                    $product_amount = round((float)$product_price + (float)$sub_product_price,2);
                }else {
                    $product_amount = round((float)$product_price,2);
                }
                $product = ($sub_product_name != '' && $sub_product_req == 1)?'Voucher page redeem ('.$product_name.' + '.$sub_product_name.')':'Voucher page redeem ('.$product_name.')';
                $invoice = Invoice::forSingleItem($account_id, $product, $product_amount);
                $invoice_id = $invoice['id'];
                $invoice_vtoken = $invoice['vtoken'];
                $invoice_url = U.'invoices/view/'.$invoice_id.'/';
            }

            $d->voucher_id = $voucher_id;
            $d->page_id =$page_id;
            $d->invoice_id = $invoice_id;
            $d->page_title = $page_title;
            $d->product_name = $product_name;
            $d->product_price = $product_price;
            $d->sub_product_name = $sub_product_name;
            $d->sub_product_price = $sub_product_price;
            $d->voucher_number = $voucher_number;
            $d->country_name = $country_name;
            $d->category = $category;
            $d->customer_name = $customer_name;
            $d->customer_address = $customer_address;
            $d->departure_date = $departure_date;
            $d->return_date = $return_date;
            $d->total_days = $total_days;
            $d->sub_product_req = $sub_product_req;
            $d->status = $status;
            $d->remark = $remark;

            $d->save();

            $cid = $d->id();

            // Custom Fields

            $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();

            foreach($fs as $f){
                $fvalue = _post('cf'.$f['id']);
                if($tid){
                    $fc=ORM::for_table('voucher_customfieldsvalues')->where('relid',$tid)->where('fieldid',$f['id'])->find_one();
                    if($fc){
                        $fc->fvalue = $fvalue;
                        $fc->save();
                    }else{
                        $fc = ORM::for_table('voucher_customfieldsvalues')->create();
                        $fc->fieldid = $f['id'];
                        $fc->relid = $cid;
                        $fc->fvalue = $fvalue;
                        $fc->save();
                    }

                }else{
                    $fc = ORM::for_table('voucher_customfieldsvalues')->create();
                    $fc->fieldid = $f['id'];
                    $fc->relid = $cid;
                    $fc->fvalue = $fvalue;
                    $fc->save();
                }

            }

            if($page_setting['payment_req'] == 1 && $tid == '') {
                echo $invoice_url;
            }else {
                echo "page_list";
            }

        }else{
            _msglog('r',$msg);
            echo "reload";
        }

        break;

/*
 *  Files upload Logic
 */
    case 'flagupload':

        if(APP_STAGE == 'Demo'){
            exit;
        }


        $uploader   =   new Uploader();
        $uploader->setDir('apps/voucher/public/flags/');
        $uploader->sameName(false);
        $uploader->setExtensions(array('jpg','jpeg','png','gif'));  //allowed extensions list//
        //$uploader->allowAllFormats();  //allowed extensions list//

        if($uploader->uploadFile('file')){   //txtFile is the filebrowse element name //
            $uploaded  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//

            $file = $uploaded;
            $msg = $_L['Uploaded Successfully'];
            $success = 'Yes';

            // create thumb

            $image = new Img();

            // indicate a source image (a GIF, PNG or JPEG file)
            $image->source_path = 'apps/voucher/public/flags/'.$file;

            // indicate a target image
            // note that there's no extra property to set in order to specify the target
            // image's type -simply by writing '.jpg' as extension will instruct the script
            // to create a 'jpg' file
            $image->target_path = 'apps/voucher/public/flags/thumb'.$file;

            // since in this example we're going to have a jpeg file, let's set the output
            // image's quality
            $image->jpeg_quality = 100;

            // some additional properties that can be set
            // read about them in the documentation
            $image->preserve_aspect_ratio = true;
            $image->enlarge_smaller_images = true;
            $image->preserve_time = true;

            // resize the image to exactly 200x100 pixels by using the "crop from center" method
            // (read more in the overview section or in the documentation)
            //  and if there is an error, check what the error is about
            if (!$image->resize(200, 100, ZEBRA_IMAGE_CROP_CENTER)) {
                // if no errors
            } else {
                // echo 'Success!';
            }

        }else{//upload failed
            $file = '';
            $msg = $uploader->getMessage();
            $success = 'No';
        }


        $a = array(
            'success' => $success,
            'msg' =>$msg,
            'file' =>$file,
            'fullpath' => APP_URL.'/apps/voucher/public/flags/'.$file
        );

        header('Content-Type: application/json');
        echo json_encode($a);

        break;

    case 'voucher_image_upload':

        if(APP_STAGE == 'Demo'){
            exit;
        }

        $uploader   =   new Uploader();
        $uploader->setDir('apps/voucher/public/voucher_imgs/');
        $uploader->sameName(false);
        $uploader->setExtensions(array('jpg','jpeg','png','gif'));  //allowed extensions list//
        //$uploader->allowAllFormats();  //allowed extensions list//
        if($uploader->uploadFile('file')){   //txtFile is the filebrowse element name //
            $uploaded  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//

            $file = $uploaded;
            $msg = $_L['Uploaded Successfully'];
            $success = 'Yes';

            // create thumb

            $image = new Img();

            // indicate a source image (a GIF, PNG or JPEG file)
            $image->source_path = 'apps/voucher/public/voucher_imgs/'.$file;

            // indicate a target image
            // note that there's no extra property to set in order to specify the target
            // image's type -simply by writing '.jpg' as extension will instruct the script
            // to create a 'jpg' file
            $image->target_path = 'apps/voucher/public/voucher_imgs/thumb'.$file;

            // since in this example we're going to have a jpeg file, let's set the output
            // image's quality
            $image->jpeg_quality = 100;

            // some additional properties that can be set
            // read about them in the documentation
            $image->preserve_aspect_ratio = true;
            $image->enlarge_smaller_images = true;
            $image->preserve_time = true;

            // resize the image to exactly 200x100 pixels by using the "crop from center" method
            // (read more in the overview section or in the documentation)
            //  and if there is an error, check what the error is about
            if (!$image->resize(276, 64, ZEBRA_IMAGE_CROP_CENTER)) {
                // if no errors
            } else {
                // echo 'Success!';
            }

        }else{//upload failed
            $file = '';
            $msg = $uploader->getMessage();
            $success = 'No';
        }

        $a = array(
            'success' => $success,
            'msg' =>$msg,
            'file' =>$file,
            'fullpath' => APP_URL.'/apps/voucher/public/voucher_imgs/'.$file
        );

        header('Content-Type: application/json');
        echo json_encode($a);

        break;

    case 'voucher_upload':

        if(APP_STAGE == 'Demo'){
            exit;
        }


        $uploader   =   new Uploader();
        $uploader->setDir('apps/voucher/public/template/');
        $uploader->sameName(false);
        $uploader->setExtensions(array('pdf','doc'));  //allowed extensions list//
        //$uploader->allowAllFormats();  //allowed extensions list//

        if($uploader->uploadFile('file')){   //txtFile is the filebrowse element name //
            $uploaded  =   $uploader->getUploadName(); //get uploaded file name, renames on upload//

            $file = $uploaded;
            $msg = $_L['Uploaded Successfully'];
            $success = 'Yes';

        }else{//upload failed
            $file = '';
            $msg = $uploader->getMessage();
            $success = 'No';
        }


        $a = array(
            'success' => $success,
            'msg' =>$msg,
            'file' =>$file,
            'fullpath' => APP_URL.'/apps/voucher/public/template/'.$file
        );

        header('Content-Type: application/json');
        echo json_encode($a);

        break;

    default:
        echo 'action not defined';
        break;

 }