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
        Event::trigger('voucher/app/dashboard/');
        $baseUrl = APP_URL;

        $redeem_vouchers = null;
        $redeem_voucher_pages = null;
        $recent_vouchers = null;
        $latestincomes = null;
        $latestexpenses = null;

        $today = date('Y-m-d');

        $total_expired = ORM::for_table('voucher_generated')
            ->where('status', 'Expired')
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

            if($date2 <= $date1){
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


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dt/dt','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dt/dt','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
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
        Event::trigger('voucher/app/add_list_country/');
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
        Event::trigger('voucher/app/get_prefix/');
        $country_name = _post('country_name');
        $prefix = Countries::full2short($country_name);

        header('Content-Type: application/json');
        echo json_encode($prefix);

        break;

    case 'post_country':
        Event::trigger('voucher/app/post_country/');
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

        Event::trigger('voucher/app/delete_country/');
        $id = route(3);

        $d = ORM::for_table('voucher_country')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/add_list_country', 's', 'Country Delete Successfully');
        }

        break;

    case 'modal_edit_country':
        Event::trigger('voucher/app/modal_edit_country/');
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
        Event::trigger('voucher/app/get_country_info/');

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
        Event::trigger('voucher/app/add_category/');

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
        Event::trigger('voucher/app/post_category/');

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
        Event::trigger('voucher/app/modal_edit_category/');

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

        Event::trigger('voucher/app/delete_category/');
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
        Event::trigger('voucher/app/post_template/');
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
        Event::trigger('voucher/app/modal_edit_template/');
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

        Event::trigger('voucher/app/delete_template/');
        $id = route(3);

        $d = ORM::for_table('voucher_template')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/pdf_template', 's', 'Template Delete Successfully');
        }

        break;

    case 'get_template_info':
        Event::trigger('voucher/app/get_template_info/');
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
        Event::trigger('voucher/app/add_voucher/');
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
        Event::trigger('voucher/app/post_voucher/');
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
//        if(!$cost_price){
//            $msg .= 'Cost Pirce is required <br>';
//        }
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
        Event::trigger('voucher/app/list_voucher/');
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
            $active_voucher[$v['id']] = ORM::for_table('voucher_generated')->where('voucher_format_id', $v['id'])->where('status', 'Active')->count();
//                ->left_outer_join('sys_invoices', array('voucher_generated.invoice_id', '=', 'sys_invoices.id'))
//                ->where('voucher_format_id', $v['id'])
//                ->where('sys_invoices.status', 'Paid')
//                ->count();
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
            ->order_by_desc('id')
            ->find_array();

        $voucher_status = array();
        $today = date('Y-m-d');
        foreach ($redeem_vouchers as $v){

            $date1 = date_create($today);
            $date2 = date_create($v['expiry_date']);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));

            if($rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
//                if($v['status'] == 'Paid'){
//                    $voucher_status[$v['id']] = 'Active';
//                }else {
//                    $voucher_status[$v['id']] = 'Inactive';
//                }
                $voucher_status[$v['id']] = $v['status'];
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
        Event::trigger('voucher/app/modal_edit_voucher/');
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

        Event::trigger('voucher/app/delete_voucher_format/');
        $id = route(3);

        $d = ORM::for_table('voucher_format')->find_one($id);

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/list_voucher', 's', 'Country Delete Successfully');
        }


        break;

    case 'modal_generate_voucher':
        Event::trigger('voucher/app/modal_generate_voucher/');
        $id = route(3);
        $g_id = route(4);
        $baseUrl = APP_URL;
        $type = 'add';

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
                ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
                ->left_outer_join('voucher_template', array('voucher_template.id', '=', 'voucher_format.template_id'))
                ->select_many('voucher_generated.*', 'voucher_template.cover_img', 'voucher_template.voucher_template', 'voucher_template.voucher_pgnum')
                ->find_one($g_id);

            $type = 'edit';
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
            $val['date'] = '';
            $val['status'] = 'Processing';
        }

        $customers = ORM::for_table('crm_accounts')->where_in('type',array('Customer','Customer,Supplier'))->order_by_asc('account')->find_many();
        $suppliers = ORM::for_table('crm_accounts')->where_in('type',array('Supplier','Customer,Supplier'))->order_by_asc('account')->find_many();
//        $voucher_templates = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();



        view('wrapper_modal',[
           '_include' => 'modal_generate_voucher',
            'customers' => $customers,
            'suppliers' => $suppliers,
            'val' => $val,
            'voucher' => $voucher,
            'baseUrl' => $baseUrl,
            'type' => $type
        ]);
        break;

    case 'post_generate_voucher':

        Event::trigger('voucher/app/post_generate_voucher/');

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

        // Default status setting
        $status = _post('status');
        $status = $status?$status:'Processing';


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


        // Settings

        $set_status_manually = ORM::for_table('voucher_setting')->where('setting', 'set_status_manually')->find_one();
        $voucher_status_processing = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_processing')->find_one();
        $voucher_status_active = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_active')->find_one();
        $voucher_status_expired = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_expired')->find_one();
        $voucher_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_cancelled')->find_one();
        $page_status_processing = ORM::for_table('voucher_setting')->where('setting', 'page_status_processing')->find_one();
        $page_status_confirmed = ORM::for_table('voucher_setting')->where('setting', 'page_status_confirmed')->find_one();
        $page_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'page_status_cancelled')->find_one();

        $setting = array(
            'set_status_manually' => $set_status_manually['value'],
            'voucher_status_processing' => $voucher_status_processing['value'],
            'voucher_status_active' => $voucher_status_active['value'],
            'voucher_status_expired' => $voucher_status_expired['value'],
            'voucher_status_cancelled' => $voucher_status_cancelled['value'],
            'page_status_processing' => $page_status_processing['value'],
            'page_status_confirmed' => $page_status_confirmed['value'],
            'page_status_cancelled' => $page_status_cancelled['value']
        );


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


                $voucher_numbers = explode(',',$serial_numbers);

                for($i=1;$i<=$total_voucher;$i++){

                    if($gid == ''){
                        $d = ORM::for_table('voucher_generated')->create();
                        _msglog('s','Voucher Generated Successfully');
                    }else {
                        $d = ORM::for_table('voucher_generated')
                            ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                            ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
                            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
                            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
                            ->select_many('voucher_generated.*')
                            ->select('voucher_category.category_name')
                            ->select_many('crm_accounts.account', 'crm_accounts.email')
                            ->select('sys_invoices.id', 'invoice_id')
                            ->select('sys_invoices.total', 'invoice_amount')
                            ->select('sys_invoices.duedate', 'invoice_due_date')
                            ->select('sys_invoices.vtoken', 'invoice_vtoken')
                            ->select('sys_invoices.status', 'invoice_status')
                            ->find_one($gid);


                        if($setting['set_status_manually'] != '1' && $d['status'] != $status){
                            echo "Can not set status manually";
                            exit;
                        }elseif($setting['set_status_manually'] == '1' && $d['status'] != $status){
                            switch ($status){
                                case 'Processing':
                                    if($d['invoice_status'] == 'Paid'){

                                    }
                                    if($setting['voucher_status_processing']){
                                        $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_processing']);
                                    }else{
                                        $e = null;
                                    }

                                    $date = '';
                                    $expiry_date = '';
                                    break;

                                case 'Active':
                                    if($d['redeem_status'] != 'Redeem') {
                                        echo "This voucher is not redeemed yet";
                                        exit;
                                    }
                                    if($d['invoice_status'] != 'Paid'){
                                        echo 'this voucher does not paid yet';
                                        exit;
                                    }
                                    if($setting['voucher_status_active']){
                                        $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_active']);
                                    }else{
                                        $e = null;
                                    }

                                    $date = date('Y-m-d');
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

                                    break;

                                case 'Expired':
                                    if($d['redeem_status'] != 'Redeem') {
                                        echo "This voucher is not redeemed yet";
                                        exit;
                                    }
                                    if($setting['voucher_status_expired']){
                                        $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_expired']);
                                    }else{
                                        $e = null;
                                    }

                                    $date = $d['date'];
                                    $expiry_date = date('Y-m-d');

                                    break;

                                case 'Cancelled':
                                    if($d['redeem_status'] != 'Redeem') {
                                        echo "This voucher is not redeemed yet";
                                        exit;
                                    }
                                    if($d['invoice_status'] == 'Paid'){

                                    }
                                    if($setting['voucher_status_cancelled']){
                                        $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_cancelled']);
                                    }else{
                                        $e = null;
                                    }
//                                    $date = $d['date']!='0000-00-00'?$d['date']:'';
//                                    $expiry_date = $d['expiry_date']!='0000-00-00'?$d['expiry_date']:'';
                                    $date = $d['date'];
                                    $expiry_date = $d['expiry_date'];
                                    break;

                                default :

                                    break;
                            }

                            // Send Mail

                            if($e){
                                $subject = new Template($e['subject']);
                                $subject->set('contact_name', $d['account']);
                                $subject->set('business_name', $config['CompanyName']);
                                $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                                $subject->set('client_login_url', U.'client/login');
                                $subject->set('client_email', $d['email']);
                                $subject->set('voucher_category', $d['category']);
                                $subject->set('voucher_number', $d['prefix'].$d['serial_number']);
                                $subject->set('date_activated',date($config['df'], strtotime($date)));
                                $subject->set('date_expire', date($config['df'], strtotime($expiry_date)));
                                $subject->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                $subject->set('invoice_id', $d['invoice_id']);
                                $subject->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                $subject->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                $subject->set('status', $d['status']);
                                $subj = $subject->output();

                                $message = new Template($e['message']);
                                $message->set('contact_name', $d['account']);
                                $message->set('business_name', $config['CompanyName']);
                                $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                                $message->set('client_login_url', U.'client/login');
                                $message->set('client_email', $d['email']);
                                $message->set('voucher_category', $d['category_name']);
                                $message->set('voucher_number', $d['prefix'].$d['serial_number']);
                                $message->set('date_activated',date($config['df'], strtotime($date)));
                                $message->set('date_expire', date($config['df'], strtotime($expiry_date)));
                                $message->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                $message->set('invoice_id', $d['invoice_id']);
                                $message->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                $message->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                $message->set('status', $d['status']);
                                $message_o = $message->output();

                                Notify_Email::_send($d['account'], $d['email'], $subj, $message_o);
                            }


                        }

                        _msglog('s','Voucher Updated Successfully');
                    }


                    $serial_number = $voucher_numbers[$i-1];
                    $voucher_pdf = $serial_number.'.pdf';

                    // insert into database

                    $d->voucher_format_id = $id;
                    $d->contact_id = $contact_id;
                    $d->agent_id = $agent_id;
                    $d->serial_number = $serial_number;

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
        Event::trigger('voucher/app/json_voucher_list/');

        $format_id = route(3);

        $columns = array();

        $columns[] = '';
        $columns[] = 'id';
        $columns[] = 'voucher_img';
        $columns[] = 'date';
        $columns[] = 'prefix';
        $columns[] = 'serial_number';
        $columns[] = 'contact_name';
        $columns[] = 'agent_name';
        $columns[] = 'expiry_date';
        $columns[] = '';
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


            if($xs['status'] == 'Processing' || $xs['status'] == ''){
                $voucher_status = "<a href='#' class='btn btn-xs square-deactive' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Inactive'>Processing</a>";

                if($xs['date'] != '0000-00-00' && $xs['expiry_date'] < date('Y-m-d')){
                    $voucher_status = "<a href='#' class='btn btn-xs square-expire' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Expired'>Expired</a>";
                }

                if($xs['date'] == '0000-00-00'){
                    $xs['date'] = '-';
                    $xs['expiry_date'] = '-';
                }
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

            if($xs['status'] == 'Expired'){
                $voucher_status = "<a href='#' class='btn btn-xs square-expire' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Expired'>Expired</a>";

                if($xs['date'] == '0000-00-00'){
                    $xs['date'] = '-';
                    $xs['expiry_date'] = '-';
                }
            }

            if($xs['status'] == 'Cancelled'){
                $voucher_status = "<a href='#' class='btn btn-xs square-deactive' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Inactive'>Cancelled</a>";

                if($xs['date'] == '0000-00-00'){
                    $xs['date'] = '-';
                    $xs['expiry_date'] = '-';
                }
            }


            $records["data"][] = array(
                0 => '<input id="row_'.$xs['id'].'" type="checkbox" value="" name="" class="i-checks"/>',
                1 => $xs['id'],
                2 => $img,
                3 => $xs['date'],
                4 => htmlentities($xs['prefix']),
                5 => $xs['serial_number'],
                6 => $xs['contact_name'],
                7 => $xs['agent_name'],
                8 => $xs['expiry_date'],
                9 => $page_count. '<span style="color:#CAA931">('.$redeem_count.')</span>',
                10 => htmlentities($xs['description']),
                11 => $voucher_status,
                12 => '
                <a href="' . U . 'voucher/app/download_generated_voucher/' . $xs['id'] . '" class="btn btn-primary btn-xs cview" style="background-color: #92278F; border-color:#92278F" id="vid' . $xs['id'] . '"><i class="fa fa-file-pdf-o"></i> </a>
                <a href="#" class="btn btn-warning btn-xs cedit" id="eid' . $xs['id'] . '"><i class="glyphicon glyphicon-pencil"></i> </a>
                <a href="#" class="btn btn-danger btn-xs cdelete" id="uid' . $xs['id'] . '"><i class="fa fa-trash"></i> </a>
                ',

                13 => $format_id,

                "DT_RowId" => 'dtr_' . $xs['id']


            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        api_response($records);


        break;

    case 'delete_generated_voucher':

        Event::trigger('voucher/app/delete_generated_voucher/');
        $id = route(3);
        $id = str_replace('uid','',$id);

        $d = ORM::for_table('voucher_generated')->find_one($id);
        $format_id = $d['format_id'];

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/generated_voucher_list/'.$format_id, 's', 'Voucher Delete Successfully');
        }

        break;

    case 'delete_many_voucher':
        Event::trigger('voucher/app/delete_many_voucher/');
        $vid = _post('format_id');

        if(!isset($_POST['ids'])){
            exit;
        }

        $ids_raw = $_POST['ids'];

        $ids = array();

        foreach ($ids_raw as $id_single){
            $id = str_replace('row_','',$id_single);
            array_push($ids,$id);
        }

        $contacts = ORM::for_table('voucher_generated')->where_id_in($ids)->delete_many();

        r2(U.'voucher/app/generated_voucher_list/'.$vid,'s',$_L['Deleted Successfully']);

        break;

    case 'edit_generated_voucher':

        $id = route(3);


        break;

    case 'download_generated_voucher':

        $id = route(3);

        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_template', array('voucher_template.id', '=', 'voucher_format.template_id'))
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
                $pdf->cell(0,0,$voucher_data['prefix'].$voucher_data['serial_number']);
            }

            if($i == $voucher_data['voucher_pgnum']+1 ){
                $pdf->SetFont('Arial','B',16);
                $pdf->SetXY(109,74);
                $pdf->cell(0,0,$voucher_data['prefix'].$voucher_data['serial_number']);
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

        Event::trigger('voucher/app/voucher_transaction/');

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
        Event::trigger('voucher/app/tr_list/');
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
            $d->where_equal('status', $status);
            $today = date('Y-m-d');
//            switch ($status){
//                case 'Processing':
//                    $d->where_lt('expiry_date', $today);
//                    $d->where('status', 'Expired')
//                    break;
//                case 'Active':
//                    $d->where_equal('voucher_generated.status','Active');
//                    $d->where_gte('expiry_date',$today);
//                    break;
//                case 'Expired':
//                    $d->where_equal('voucher_generated.status','Inactive');
//                    $d->where_any_is(array(
//                        array('sys_invoices.status'=>'Unpaid'),
//                        array('sys_invoices.status'=>'Partially Paid'),
//                        array('voucher_generated.invoice_id'=>'0'))
//                    );
//                    $d->where_gte('expiry_date',$today);
//                    break;
//                case 'Cancelled':
//
//                    break;
//
//            }
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

            if($xs['status'] == 'Active'){
                $status_str = "<div class='label-success' style='margin:0 auto; font-size:85%;width:65px'>Active</div>";
                $xs['serialnumber'] = '<a href="'. U .'voucher/app/list_voucher_page/'.$xs['voucher_format_id'].'/'.$xs['id'].'">'.$xs['prefix'].$xs['serialnumber'].'</a>';
            }
            if($xs['status'] == 'Processing'){
                $status_str = "<div class='label-default' style='margin:0 auto; font-size:85%;width:65px'>Processing</div>";
            }
            if($xs['status'] == 'Cancelled'){
                $status_str = "<div class='label-default' style='margin:0 auto; font-size:85%;width:65px'>Cancelled</div>";
            }
            if($xs['status'] == 'Expired'){
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

//            $xs['serialnumber'] = '<a href="'. U .'voucher/app/list_voucher_page/'.$xs['voucher_format_id'].'/'.$xs['id'].'">'.$xs['prefix'].$xs['serialnumber'].'</a>';

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

        Event::trigger('voucher/app/delete_voucher/');
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
 *  Page Transaction
*/

    case 'voucher_page_transaction':

        Event::trigger('voucher/app/voucher_page_transaction/');

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
            '_include' => 'voucher_page_transaction',
            'countries' => $countries,
            'categories' => $categories,
            'customers' => $customers,
            'agents' => $agents
        ]);
        break;



        break;

    case 'page_tr_list':
        Event::trigger('voucher/app/page_tr_list/');
        //  sleep(5);

        $columns = array();
        $columns[] = 'id';
        $columns[] = 'date';
        $columns[] = 'serialnumber';
        $columns[] = 'title';
        $columns[] = 'customer';
        $columns[] = 'country_name';
        $columns[] = 'category';
        $columns[] = 'status';
        $columes[] = '';


        $order_by = $_POST['order'];
        $o_c_id = $order_by[0]['column'];
        $o_type = $order_by[0]['dir'];
        $a_order_by = $columns[$o_c_id];



        $d = ORM::for_table('voucher_page_transaction')
            ->left_outer_join('voucher_generated', array('voucher_generated.id','=','voucher_page_transaction.voucher_id'))
            ->left_outer_join('voucher_format',array('voucher_format.id','=','voucher_generated.voucher_format_id'));

        $d->select('voucher_page_transaction.id', 'id');
        $d->select('voucher_page_transaction.*');
        $d->select('voucher_generated.id', 'voucher_id');
        $d->select('voucher_generated.voucher_format_id', 'format_id');
        $d->select('voucher_generated.contact_id', 'customer_id');
        $d->select('voucher_format.country_id', 'country_id');
        $d->select('voucher_format.category_id', 'category_id');


        $customer_id = _post('customer');
        if($customer_id != ''){
            $d->where_equal('voucher_generated.contact_id', $customer_id);
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
            $d->where_gte('createdon', $from_date);
            $d->where_lte('createdon', $to_date);
        }

        $status = _post('status');
        if($status != ''){
            $d->where_equal('voucher_page_transaction.status',$status);
        }


        $filter_serialnumber = _post('filter_serialnumber');
        if($filter_serialnumber != ''){
            $d->where_like('voucher_page_transaction.voucher_number',"%$filter_serialnumber%");
        }

        $filter_title = _post('filter_title');
        if($filter_title != ''){
            $d->where_like('voucher_page_transaction.page_title',"%$filter_title%");
        }

        $filter_customer = _post('filter_customer');
        if($filter_customer != ''){
            $d->where_like('voucher_page_transaction.customer_name',"%$filter_customer%");
        }


        $filter_category = _post('filter_category');
        if($filter_category != ''){
            $d->where_like('voucher_page_transaction.category',"%$filter_category%");
        }

        $filter_country = _post('filter_country');
        if($filter_country != ''){
            $d->where_like('voucher_page_transaction.country_name',"%$filter_country%");
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

            if($xs['customer_id']){
                $xs['customer_name'] = '<a href="'. U .'contacts/view/'.$xs['customer_id'].'/summary/">'.$xs['customer_name'].'</a>';
            }else{
                $xs['customer_name'] = '-';
            }

            $xs['voucher_number'] = '<a href="'. U .'voucher/app/list_voucher_page/'.$xs['format_id'].'/'.$xs['voucher_id'].'">'.$xs['voucher_number'].'</a>';
            $xs['page_title'] = '<a href="'.U.'voucher/app/view_redeem_page/'.$xs['voucher_id'].'/'.$xs['page_id'].'/view/">'.$xs['page_title'].'</a>';

            if($xs['status'] == 'Confirmed'){
                $status_str = "<div class='label-success' style='margin:0 auto; font-size:85%;width:85px'>".$xs['status']."</div>";
            }else{
                $status_str = "<div class='label-default' style='margin:0 auto; font-size:85%;width:85px'>".$xs['status']."</div>";
            }

            $records["data"][] = array(
                0 => $xs['id'],
                1 => date($config['df'], strtotime($xs['createdon'])),
                2 => $xs['voucher_number'],
                3 => $xs['page_title'],
                4 => $xs['customer_name'],
                5 => htmlentities($xs['country_name']),
                6 => htmlentities($xs['category']),
                7 => $status_str,
                8 => '<a href="'.U.'voucher/app/view_redeem_page/'.$xs['voucher_id'].'/'.$xs['page_id'].'/view/" class="btn btn-primary btn-xs"><i class="fa fa-file-text-o"></i></a>'.' '.'<a href="#" class="btn btn-danger btn-xs cdelete" id="'.$xs['id'].'"><i class="fa fa-trash"></i></a>',
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        api_response($records);

        break;

    case 'delete_page_transaction':

        Event::trigger('voucher/app/delete_page_transaction/');
        $id = route(3);
//        $id = str_replace('uid','',$id);

        $d = ORM::for_table('voucher_page_transaction')->find_one($id);
        $format_id = $d['format_id'];

        if ($d) {
            $d->delete();
            r2(U . 'voucher/app/voucher_page_transaction/', 's', 'Page Transaction Delete Successfully');
        }

        break;


/*
 *  Voucher Settings
 */

    case 'voucher_setting':
        Event::trigger('voucher/app/voucher_setting/');
        $setting_data = ORM::for_table('voucher_setting')->find_array();

        $mail_templates = ORM::for_table('sys_email_templates')->find_array();

        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $products = ORM::for_table('sys_items')->order_by_asc('name')->find_array();

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
            'mail_templates' => $mail_templates,
            'setting' => $setting,
            'products' => $products
        ]);

        break;

    case 'post_setting':
        Event::trigger('voucher/app/post_setting/');
        $agreement_text = _post('agreement_text');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','agreement_text')->find_one();
        $d->value = $agreement_text;
        $d->save();

        $activation_fee = _post('activation_fee');
        if($activation_fee == ''){
            echo 'Activation Fee is required <br>';
            break;
        }else {
            $d = ORM::for_table('voucher_setting')->where_equal('setting', 'activation_fee')->find_one();
            $d->value = $activation_fee;
            $d->save();
        }



        _msglog('s','Settings Updated Successfully');
        echo $d->id();


        break;

    case 'post_alert':
        Event::trigger('voucher/app/post_alert/');
        $alert_message = _post('alert_message');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','alert_message')->find_one();
        $d->value = $alert_message;
        $d->save();



        $voucher_status_processing = _post('voucher_status_processing');
        $voucher_status_active = _post('voucher_status_active');
        $voucher_status_expired = _post('voucher_status_expired');
        $voucher_status_cancelled = _post('voucher_status_cancelled');
        $page_status_processing = _post('page_status_processing');
        $page_status_confirmed = _post('page_status_confirmed');
        $page_status_cancelled = _post('page_status_cancelled');



        $msg = '';

        // Validation

        if($voucher_status_processing == ''){
            $msg .= 'Voucher Processing Status is required';
        }
        if($voucher_status_active == ''){
            $msg .= 'Voucher Active Status is required';
        }
        if($voucher_status_expired == ''){
            $msg .= 'Voucher Expired Status is required';
        }
        if($voucher_status_cancelled == ''){
            $msg .= 'Voucher Cancelled Status is required';
        }
        if($page_status_processing == ''){
            $msg .= 'Voucher Page Processing Status is required';
        }
        if($page_status_confirmed == ''){
            $msg .= 'Voucher Page Confirmed Status is required';
        }
        if($page_status_cancelled == ''){
            $msg .= 'Voucher Page Cancelled Status is required';
        }


        $d = ORM::for_table('voucher_setting')->where_equal('setting','voucher_status_processing')->find_one();
        $d->value = $voucher_status_processing;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','voucher_status_active')->find_one();
        $d->value = $voucher_status_active;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','voucher_status_expired')->find_one();
        $d->value = $voucher_status_expired;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','voucher_status_cancelled')->find_one();
        $d->value = $voucher_status_cancelled;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','page_status_processing')->find_one();
        $d->value = $page_status_processing;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','page_status_confirmed')->find_one();
        $d->value = $page_status_confirmed;
        $d->save();

        $d = ORM::for_table('voucher_setting')->where_equal('setting','page_status_cancelled')->find_one();
        $d->value = $page_status_cancelled;
        $d->save();

        _msglog('s','Settings Updated Successfully');
        echo $d->id();
        break;

    case 'update_settings':
        Event::trigger('voucher/app/update_setting/');
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
        Event::trigger('voucher/app/list_voucher_page/');
        $vid = route(3);
        $gid = route(4);
        $baseUrl =APP_URL;



        // Mail Setting

        $set_status_manually = ORM::for_table('voucher_setting')->where('setting', 'set_status_manually')->find_one();
        $voucher_status_processing = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_processing')->find_one();
        $voucher_status_active = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_active')->find_one();
        $voucher_status_expired = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_expired')->find_one();
        $voucher_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_cancelled')->find_one();
        $page_status_processing = ORM::for_table('voucher_setting')->where('setting', 'page_status_processing')->find_one();
        $page_status_confirmed = ORM::for_table('voucher_setting')->where('setting', 'page_status_confirmed')->find_one();
        $page_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'page_status_cancelled')->find_one();

        $setting = array(
            'set_status_manually' => $set_status_manually['value'],
            'voucher_status_processing' => $voucher_status_processing['value'],
            'voucher_status_active' => $voucher_status_active['value'],
            'voucher_status_expired' => $voucher_status_expired['value'],
            'voucher_status_cancelled' => $voucher_status_cancelled['value'],
            'page_status_processing' => $page_status_processing['value'],
            'page_status_confirmed' => $page_status_confirmed['value'],
            'page_status_cancelled' => $page_status_cancelled['value']
        );



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
            $page_status[$v['id']] = 'Redeem';
            $redeem_page = ORM::for_table('voucher_page_transaction')
                ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
                ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
                ->select_many('voucher_page_transaction.*')
                ->select_many('voucher_generated.date', 'voucher_generated.expiry_date')
                ->select('voucher_generated.status', 'voucher_status')
                ->select_many('crm_accounts.account', 'crm_accounts.email')
                ->select('sys_invoices.id', 'invoice_id')
                ->select('sys_invoices.total', 'invoice_amount')
                ->select('sys_invoices.duedate', 'invoice_due_date')
                ->select('sys_invoices.vtoken', 'invoice_vtoken')
                ->select('sys_invoices.status', 'invoice_status')
                ->where('voucher_id', $gid)
                ->where('page_id',$v['id'])
                ->find_one();

            if($redeem_page) {
                $page_status[$v['id']] = $redeem_page['status'];

                if($setting['set_status_manually'] != '1' && $redeem_page['status'] == 'Processing' && $redeem_page['invoice_status'] == 'Paid'){
                    $redeem_page->status = 'Confirmed';
                    $redeem_page->save();

                    // mail

                    if($setting['page_status_confirmed']){
                        $e = ORM::for_table('sys_email_templates')->find_one($setting['page_status_confirmed']);
                    }else{
                        $e = null;
                    }
                    if($e){
                        $subject = new Template($e['subject']);
                        $subject->set('contact_name', $redeem_page['customer_name']);
                        $subject->set('business_name', $config['CompanyName']);
                        $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                        $subject->set('client_login_url', U.'client/login');
                        $subject->set('client_email', $redeem_page['email']);
                        $subject->set('voucher_category', $redeem_page['category']);
                        $subject->set('voucher_number', $redeem_page['voucher_number']);
                        $subject->set('status', $redeem_page['voucher_status']);
                        $subject->set('date_activated',date($config['df'], strtotime($redeem_page['date'])));
                        $subject->set('date_expire', date($config['df'], strtotime($redeem_page['expiry_date'])));
                        $subject->set('invoice_url', U . 'client/iview/' . $redeem_page['invoice_id'] . '/token_' . $redeem_page['invoice_vtoken']);
                        $subject->set('invoice_id', $redeem_page['invoice_id']);
                        $subject->set('invoice_due_date', date($config['df'], strtotime($redeem_page['invoice_due_date'])));
                        $subject->set('invoice_amount', number_format($redeem_page['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $subject->set('page_title', $redeem_page['page_title']);
                        $subject->set('product_title', $redeem_page['product_name']);
                        $subject->set('product_quantity', $redeem_page['product_quantity']);
                        $subject->set('product_price', $redeem_page['product_price']);
                        $subject->set('sub_product_title', $redeem_page['sub_product_name']);
                        $subject->set('sub_product_quantity', $redeem_page['sub_product_quantity']);
                        $subject->set('sub_product_price', $redeem_page['sub_product_price']);
                        $subj = $subject->output();

                        $message = new Template($e['message']);
                        $message->set('contact_name', $redeem_page['customer_name']);
                        $message->set('business_name', $config['CompanyName']);
                        $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                        $message->set('client_login_url', U.'client/login');
                        $message->set('client_email', $redeem_page['email']);
                        $message->set('voucher_category', $redeem_page['category']);
                        $message->set('voucher_number', $redeem_page['voucher_number']);
                        $message->set('status', $redeem_page['voucher_status']);
                        $message->set('date_activated',date($config['df'], strtotime($redeem_page['date'])));
                        $message->set('date_expire', date($config['df'], strtotime($redeem_page['expiry_date'])));
                        $message->set('invoice_url', U . 'client/iview/' . $redeem_page['invoice_id'] . '/token_' . $redeem_page['invoice_vtoken']);
                        $message->set('invoice_id', $redeem_page['invoice_id']);
                        $message->set('invoice_due_date', date($config['df'], strtotime($redeem_page['invoice_due_date'])));
                        $message->set('invoice_amount', number_format($redeem_page['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $message->set('page_title', $redeem_page['page_title']);
                        $message->set('product_title', $redeem_page['product_name']);
                        $message->set('product_quantity', $redeem_page['product_quantity']);
                        $message->set('product_price', $redeem_page['product_price']);
                        $message->set('sub_product_title', $redeem_page['sub_product_name']);
                        $message->set('sub_product_quantity', $redeem_page['sub_product_quantity']);
                        $message->set('sub_product_price', $redeem_page['sub_product_price']);
                        $message_o = $message->output();

                        Notify_Email::_send($redeem_page['account'], $redeem_page['email'], $subj, $message_o);
                    }

                    $page_status[$v['id']] = 'Confirmed';
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
                ->left_outer_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'))
                ->select_many('voucher_generated.*', 'voucher_category.category_name', 'voucher_country.country_name')
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
        Event::trigger('voucher/app/add_page/');
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

        $cf = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $voucher_format_id)
            ->where('voucher_customfields.page_id', $page_id)
            ->order_by_asc('id')->find_many();


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
        Event::trigger('voucher/app/view_page/');
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
        $cf = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $voucher_format_id)
            ->where('voucher_customfields.page_id', $page_id)
            ->order_by_asc('id')->find_many();



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
        Event::trigger('voucher/app/clone_page/');
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

        Event::trigger('voucher/app/delete_page/');
        $vid = route(3);
        $pid = route(4);


        $d = ORM::for_table('voucher_pages')->find_one($pid);

        if ($d) {
            $d->delete();
            r2(U.'voucher/app/list_voucher_page/'.$vid.'/','s','Voucher page Deleted Successfully');
        }

        break;

    case 'post_page':
        Event::trigger('voucher/app/post_page/');

        $voucher_format_id = _post('vid');
        $page_id = _post('pid');
        $title = _post('title');
        $product_id = _post('product_id');
        $sub_product_id = _post('sub_product_id');
        $product_quantity = _post('product_quantity');
        $sub_product_quantity = _post('sub_product_quantity');
        $payment_req = _post('payment_req');
        $status_id = _post('status_id');
        $address = _post('address');
        $date_range = _post('date_range');
        $remark = _post('remark');
        $void_days_req = _post('void_days_req');
        $void_days = _post('void_days');
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

        if($void_days_req == '1' && $void_days == ''){
            $msg .= 'Days to void is required <br>';
        }

        if($product_id != '' && $product_quantity == ''){
            $msg .= 'Product Quantity is required';
        }

        if($sub_product_id != '' && $sub_product_quantity == ''){
            $msg .= 'Sub Product Quantity is required';
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
            $d->product_quantity = $product_quantity;
            $d->sub_product_quantity = $sub_product_quantity;
            $d->status_id = $status_id;
            $d->payment_req = $payment_req;
            $d->address = $address;
            $d->date_range = $date_range;
            $d->remark = $remark;
            $d->void_days = $void_days;
            $d->front_img = $front_img;
            $d->back_img = $back_img;

            $d->save();

            $pid = $d->id();
//            r2(U.'voucher/app/list_voucher_page/'.$voucher_format_id);

            // customfields

            $custom_fields = ORM::for_table('voucher_customfields')
                ->where('voucher_customfields.voucher_id', $voucher_format_id)
                ->where('voucher_customfields.page_id', 0)
                ->find_array();

            foreach($custom_fields as $c){
                $cf = ORM::for_table('voucher_customfields')->find_one($c['id']);
                $cf->page_id = $pid;
                $cf->save();
            }

            echo $pid;

        }  else {
            echo $msg;
        }

        break;

    case 'customfields-post':
        Event::trigger('voucher/app/customefileds-post/');
        $fieldname = _post('fieldname');
        $fieldtype = _post('fieldtype');
        $description = _post('description');
        $validation = _post('validation');
        $options = _post('options');
        $vid = _post('vid');
        $pid = _post('pid');

        if($fieldname != ''){

            $d = ORM::for_table('voucher_customfields')->create();
            $d->fieldname = $fieldname;
            $d->fieldtype = $fieldtype;
            $d->description = $description;
            $d->regexpr = $validation;
            $d->fieldoptions = $options;
            $d->ctype = 'crm';
            $d->relid = 0;
            $d->voucher_id = $vid;
            $d->page_id = $pid;
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
        Event::trigger('voucher/app/customefield-edit-post/');
        $id = _post('id');
        $vid = _post('vid');
        $pid = _post('pid');

        $fieldname = _post('fieldname');

        if($fieldname == ''){
            ib_die('Name is Required');
        }

        $d = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $vid)
            ->where('voucher_customfields.page_id', $pid)
            ->find_one($id);
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
            $d->voucher_id = $vid;
            $d->page_id = $pid;
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
        Event::trigger('voucher/app/customefields-ajax-add/');
        $vid = route(3);
        $pid = route(4);
        $ui->assign('content_inner',inner_contents($config['c_cache']));

        view('wrapper_modal',[
            '_include' => 'ajax-add-custom-field',
            'vid' => $vid,
            'pid' => $pid
        ]);
        break;

    case 'customfields-ajax-edit':
        Event::trigger('voucher/app/customefields-ajax-edit/');
        $id = route(3);
        $id = str_replace('f','',$id);
        $vid = route(4);
        $pid = route(5);

        $d = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $vid)
            ->where('voucher_customfields.page_id', $pid)
            ->find_one($id);

        if($d){
            $ui->assign('d',$d);
            view('wrapper_modal',[
                '_include' => 'ajax-edit-custom-field',
                'vid' => $vid,
                'pid' => $pid
            ]);
        }
        else{
            echo 'Not Found';
        }


        break;

    case 'delete_customfield':
        Event::trigger('voucher/app/delete_customfield/');
        $id = route(3);
        $vid = route(4);
        $pid = route(5);

        $id = str_replace('d','',$id);

        $d = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $vid)
            ->where('voucher_customfields.page_id', $pid)
            ->find_one($id);

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
        Event::trigger('voucher/app/view_redeem_page/');
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


        // Redirect

        if($voucher_data['redeem_status'] != 'Redeem'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is not Redeemed');
        }

        if($voucher_data['invoice_status'] != 'Paid'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is not Paid');
        }

        if($voucher_data['status'] == 'Processing'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is Processing');
        }

        if($voucher_data['status'] == 'Expired'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is Expired');
        }

        if($voucher_data['status'] == 'Cancelled'){
            r2(U . 'voucher/app/list_voucher_page/'.$voucher_data['voucher_format_id'].'/'.$voucher_id.'/', 'e', 'This Voucher is Cancelled');
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
            $transaction_data['product_quantity'] = '';
            $transaction_data['sub_product_quantity'] = '';
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
            ->select('voucher_generated.voucher_format_id')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();


        $fs = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $voucher_info['voucher_format_id'])
            ->where('voucher_customfields.page_id', $page_id)
            ->order_by_asc('id')->find_many();

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

        Event::trigger('voucher/app/post_redeem_page/');

        $tid = _post('tid');
        $status = _post('status');

        $voucher_id = _post('voucher_id');
        $page_id = _post('page_id');
        $account_id = _post('account_id');

        $page_title = _post('page_title');
        $product_name = _post('product_name');
        $product_price = _post('product_price');
        $product_quantity = _post('product_quantity');
        $sub_product_name = _post('sub_product_name');
        $sub_product_price = _post('sub_product_price');
        $sub_product_quantity = _post('sub_product_quantity');
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


        // Mail Setting

        $set_status_manually = ORM::for_table('voucher_setting')->where('setting', 'set_status_manually')->find_one();
        $voucher_status_processing = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_processing')->find_one();
        $voucher_status_active = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_active')->find_one();
        $voucher_status_expired = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_expired')->find_one();
        $voucher_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_cancelled')->find_one();
        $page_status_processing = ORM::for_table('voucher_setting')->where('setting', 'page_status_processing')->find_one();
        $page_status_confirmed = ORM::for_table('voucher_setting')->where('setting', 'page_status_confirmed')->find_one();
        $page_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'page_status_cancelled')->find_one();

        $setting = array(
            'set_status_manually' => $set_status_manually['value'],
            'voucher_status_processing' => $voucher_status_processing['value'],
            'voucher_status_active' => $voucher_status_active['value'],
            'voucher_status_expired' => $voucher_status_expired['value'],
            'voucher_status_cancelled' => $voucher_status_cancelled['value'],
            'page_status_processing' => $page_status_processing['value'],
            'page_status_confirmed' => $page_status_confirmed['value'],
            'page_status_cancelled' => $page_status_cancelled['value']
        );


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
                $status = 'Processing';

//                if($page_setting['date_range'] == 1){
//                    $status = 'Processing';
//                }else {
//                    $status = 'Confirm';
//                }
            }else{

                $d = ORM::for_table('voucher_page_transaction')
                    ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
                    ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                    ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
                    ->select_many('voucher_page_transaction.*')
                    ->select_many('voucher_generated.date', 'voucher_generated.expiry_date')
                    ->select('voucher_generated.status', 'voucher_status')
                    ->select_many('crm_accounts.account', 'crm_accounts.email')
                    ->select('sys_invoices.id', 'invoice_id')
                    ->select('sys_invoices.total', 'invoice_amount')
                    ->select('sys_invoices.duedate', 'invoice_due_date')
                    ->select('sys_invoices.vtoken', 'invoice_vtoken')
                    ->select('sys_invoices.status', 'invoice_status')
                    ->find_one($tid);


                if($setting['set_status_manually'] != '1' && $d['status'] != $status){
                    $msg = "Can not set status manually";
                    _msglog('r', $msg);
                    echo "reload";
                    exit;
                }else{
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

                            if($invoice_id){
                                if($sub_product_req == 1){
                                    $product_amount = round((float)$product_price*$product_quantity + (float)$sub_product_price*$sub_product_quantity,2);
                                }else {
                                    $product_amount = round((float)$product_price*$product_quantity,2);
                                }

                                $product = ($sub_product_name != '' && $sub_product_req == 1)?$page_title.' ('.$product_name.' + '.$sub_product_name.')':$page_title.'('.$product_name.')';

                                $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);

//                            if($invoice_data['status'] != 'Paid'){

                                $invoice_data->total = $product_amount;
                                $invoice_data->subtotal = $product_amount;
                                $invoice_data->save();

                                $invoice_item = ORM::for_table('sys_invoiceitems')->where('sys_invoiceitems.invoiceid', $invoice_id)->find_one();
                                $invoice_item->amount = $product_amount;
                                $invoice_item->total = $product_amount;
                                $invoice_item->description = $product;
                                $invoice_item->save();

//                            }else{
//                                _msglog('r','This page paid already');
//                                echo "page_list";
//                                exit;
//                            }


                            }

                            break;

                        case 'Confirmed':
                            if ($d['invoice_id'] != 0 && $d['invoice_status'] != 'Paid') {
                                $msg = 'This page is not paid <br>';
                                _msglog('r', $msg);
                                $invoice_url = U . 'invoices/view/' . $invoice_id . '/';
                                echo $invoice_url;
                                exit;

                            }else{
                                if($d['status'] != 'Confirmed'){
                                    // mail
                                    if($setting['page_status_confirmed']){
                                        $e = ORM::for_table('sys_email_templates')->find_one($setting['page_status_confirmed']);
                                    }else{
                                        $e = null;
                                    }
                                    if($e){
                                        $subject = new Template($e['subject']);
                                        $subject->set('contact_name', $d['customer_name']);
                                        $subject->set('business_name', $config['CompanyName']);
                                        $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                                        $subject->set('client_login_url', U.'client/login');
                                        $subject->set('client_email', $d['email']);
                                        $subject->set('voucher_category', $d['category']);
                                        $subject->set('voucher_number', $d['voucher_number']);
                                        $subject->set('status', $d['voucher_status']);
                                        $subject->set('date_activated',date($config['df'], strtotime($d['date'])));
                                        $subject->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                                        $subject->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                        $subject->set('invoice_id', $d['invoice_id']);
                                        $subject->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                        $subject->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                        $subject->set('page_title', $d['page_title']);
                                        $subject->set('product_title', $d['product_name']);
                                        $subject->set('product_quantity', $d['product_quantity']);
                                        $subject->set('product_price', $d['product_price']);
                                        $subject->set('sub_product_title', $d['sub_product_name']);
                                        $subject->set('sub_product_quantity', $d['sub_product_quantity']);
                                        $subject->set('sub_product_price', $d['sub_product_price']);
                                        $subj = $subject->output();

                                        $message = new Template($e['message']);
                                        $message->set('contact_name', $d['customer_name']);
                                        $message->set('business_name', $config['CompanyName']);
                                        $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                                        $message->set('client_login_url', U.'client/login');
                                        $message->set('client_email', $d['email']);
                                        $message->set('voucher_category', $d['category']);
                                        $message->set('voucher_number', $d['voucher_number']);
                                        $message->set('status', $d['voucher_status']);
                                        $message->set('date_activated',date($config['df'], strtotime($d['date'])));
                                        $message->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                                        $message->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                        $message->set('invoice_id', $d['invoice_id']);
                                        $message->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                        $message->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                        $message->set('page_title', $d['page_title']);
                                        $message->set('product_title', $d['product_name']);
                                        $message->set('product_quantity', $d['product_quantity']);
                                        $message->set('product_price', $d['product_price']);
                                        $message->set('sub_product_title', $d['sub_product_name']);
                                        $message->set('sub_product_quantity', $d['sub_product_quantity']);
                                        $message->set('sub_product_price', $d['sub_product_price']);
                                        $message_o = $message->output();

                                        Notify_Email::_send($d['account'], $d['email'], $subj, $message_o);
                                    }

                                }

                                _msglog('s','Page Redeem updated Successfully');
                                $d->status = 'Confirmed';
                                $d->save();

                                echo 'page_list';
                                exit;
                            }
                            break;

                        case 'Cancelled':

                            if($d['status'] != 'Cancelled'){
                                // mail
                                if($setting['page_status_cancelled']){
                                    $e = ORM::for_table('sys_email_templates')->find_one($setting['page_status_cancelled']);
                                }else{
                                    $e = null;
                                }
                                if($e){
                                    $subject = new Template($e['subject']);
                                    $subject->set('contact_name', $d['customer_name']);
                                    $subject->set('business_name', $config['CompanyName']);
                                    $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                                    $subject->set('client_login_url', U.'client/login');
                                    $subject->set('client_email', $d['email']);
                                    $subject->set('voucher_category', $d['category']);
                                    $subject->set('voucher_number', $d['voucher_number']);
                                    $subject->set('status', $d['voucher_status']);
                                    $subject->set('date_activated',date($config['df'], strtotime($d['date'])));
                                    $subject->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                                    $subject->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                    $subject->set('invoice_id', $d['invoice_id']);
                                    $subject->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                    $subject->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                    $subject->set('page_title', $d['page_title']);
                                    $subject->set('product_title', $d['product_name']);
                                    $subject->set('product_quantity', $d['product_quantity']);
                                    $subject->set('product_price', $d['product_price']);
                                    $subject->set('sub_product_title', $d['sub_product_name']);
                                    $subject->set('sub_product_quantity', $d['sub_product_quantity']);
                                    $subject->set('sub_product_price', $d['sub_product_price']);
                                    $subj = $subject->output();

                                    $message = new Template($e['message']);
                                    $message->set('contact_name', $d['customer_name']);
                                    $message->set('business_name', $config['CompanyName']);
                                    $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                                    $message->set('client_login_url', U.'client/login');
                                    $message->set('client_email', $d['email']);
                                    $message->set('voucher_category', $d['category']);
                                    $message->set('voucher_number', $d['voucher_number']);
                                    $message->set('status', $d['voucher_status']);
                                    $message->set('date_activated',date($config['df'], strtotime($d['date'])));
                                    $message->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                                    $message->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                                    $message->set('invoice_id', $d['invoice_id']);
                                    $message->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                                    $message->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                    $message->set('page_title', $d['page_title']);
                                    $message->set('product_title', $d['product_name']);
                                    $message->set('product_quantity', $d['product_quantity']);
                                    $message->set('product_price', $d['product_price']);
                                    $message->set('sub_product_title', $d['sub_product_name']);
                                    $message->set('sub_product_quantity', $d['sub_product_quantity']);
                                    $message->set('sub_product_price', $d['sub_product_price']);
                                    $message_o = $message->output();

                                    Notify_Email::_send($d['account'], $d['email'], $subj, $message_o);
                                }

                            }


                            _msglog('s','Page Redeem updated Successfully');
                            $d->status = 'Cancelled';
                            $d->save();

                            echo 'page_list';
                            exit;

                            break;

                    }
                }



                _msglog('s','Page Redeem updated Successfully');
            }




            // Invoice

            $invoice_url = "";

            if($page_setting['payment_req'] == 1 && $tid == ''){
                if($sub_product_req == 1){
                    $product_amount = round((float)$product_price*$product_quantity + (float)$sub_product_price*$sub_product_quantity,2);
                }else {
                    $product_amount = round((float)$product_price*$product_quantity,2);
                }
                $product = ($sub_product_name != '' && $sub_product_req == 1)?$page_title.' ('.$product_name.' + '.$sub_product_name.')':$page_title.'('.$product_name.')';
                $invoice = Invoice::forSingleItem($account_id, $product, $product_amount);
                $invoice_id = $invoice['id'];
                $invoice_vtoken = $invoice['vtoken'];
                $invoice_url = U.'invoices/view/'.$invoice_id.'/';

                $voucher_info = ORM::for_table('voucher_generated')
                    ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
                    ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
                    ->select_many('voucher_generated.*', 'voucher_category.category_name')
                    ->find_one($voucher_id);

                $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);
                $invoice_data->title = $voucher_info['category_name'].' '.$voucher_info['prefix'].$voucher_info['serial_number'];
                $invoice_data->save();

            }

            $d->voucher_id = $voucher_id;
            $d->page_id =$page_id;
            $d->invoice_id = $invoice_id;
            $d->page_title = $page_title;
            $d->product_name = $product_name;
            $d->product_price = $product_price;
            $d->product_quantity = $product_quantity;
            $d->sub_product_name = $sub_product_name;
            $d->sub_product_price = $sub_product_price;
            $d->sub_product_quantity = $sub_product_quantity;
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

            if($d){
                $voucher_info = ORM::for_table('voucher_generated')
                    ->find_one($voucher_id);
            }

            $fs = ORM::for_table('voucher_customfields')
                ->where('voucher_customfields.voucher_id', $voucher_info['voucher_format_id'])
                ->where('voucher_customfields.page_id', $page_id)
                ->order_by_asc('id')->find_many();

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


            // Processing Email

            $pd = ORM::for_table('voucher_page_transaction')
                ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
                ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
                ->select_many('voucher_page_transaction.*')
                ->select_many('voucher_generated.date', 'voucher_generated.expiry_date')
                ->select('voucher_generated.status', 'voucher_status')
                ->select_many('crm_accounts.account', 'crm_accounts.email')
                ->select('sys_invoices.id', 'invoice_id')
                ->select('sys_invoices.total', 'invoice_amount')
                ->select('sys_invoices.duedate', 'invoice_due_date')
                ->select('sys_invoices.vtoken', 'invoice_vtoken')
                ->select('sys_invoices.status', 'invoice_status')
                ->find_one($cid);

            if($setting['page_status_processing']){
                $e = ORM::for_table('sys_email_templates')->find_one($setting['page_status_processing']);
            }else{
                $e = null;
            }
            if($e){
                $subject = new Template($e['subject']);
                $subject->set('contact_name', $pd['customer_name']);
                $subject->set('business_name', $config['CompanyName']);
                $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                $subject->set('client_login_url', U.'client/login');
                $subject->set('client_email', $pd['email']);
                $subject->set('voucher_category', $pd['category']);
                $subject->set('voucher_number', $pd['voucher_number']);
                $subject->set('status', $pd['voucher_status']);
                $subject->set('date_activated',date($config['df'], strtotime($pd['date'])));
                $subject->set('date_expire', date($config['df'], strtotime($pd['expiry_date'])));
                $subject->set('invoice_url', U . 'client/iview/' . $pd['invoice_id'] . '/token_' . $pd['invoice_vtoken']);
                $subject->set('invoice_id', $pd['invoice_id']);
                $subject->set('invoice_due_date', date($config['df'], strtotime($pd['invoice_due_date'])));
                $subject->set('invoice_amount', number_format($pd['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                $subject->set('page_title', $pd['page_title']);
                $subject->set('product_title', $pd['product_name']);
                $subject->set('product_quantity', $pd['product_quantity']);
                $subject->set('product_price', $pd['product_price']);
                $subject->set('sub_product_title', $pd['sub_product_name']);
                $subject->set('sub_product_quantity', $pd['sub_product_quantity']);
                $subject->set('sub_product_price', $pd['sub_product_price']);
                $subj = $subject->output();

                $message = new Template($e['message']);
                $message->set('contact_name', $pd['customer_name']);
                $message->set('business_name', $config['CompanyName']);
                $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                $message->set('client_login_url', U.'client/login');
                $message->set('client_email', $pd['email']);
                $message->set('voucher_category', $pd['category']);
                $message->set('voucher_number', $pd['voucher_number']);
                $message->set('status', $pd['voucher_status']);
                $message->set('date_activated',date($config['df'], strtotime($pd['date'])));
                $message->set('date_expire', date($config['df'], strtotime($pd['expiry_date'])));
                $message->set('invoice_url', U . 'client/iview/' . $pd['invoice_id'] . '/token_' . $pd['invoice_vtoken']);
                $message->set('invoice_id', $pd['invoice_id']);
                $message->set('invoice_due_date', date($config['df'], strtotime($pd['invoice_due_date'])));
                $message->set('invoice_amount', number_format($pd['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                $message->set('page_title', $pd['page_title']);
                $message->set('product_title', $pd['product_name']);
                $message->set('product_quantity', $pd['product_quantity']);
                $message->set('product_price', $pd['product_price']);
                $message->set('sub_product_title', $pd['sub_product_name']);
                $message->set('sub_product_quantity', $pd['sub_product_quantity']);
                $message->set('sub_product_price', $pd['sub_product_price']);
                $message_o = $message->output();

                Notify_Email::_send($pd['account'], $pd['email'], $subj, $message_o);
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

    case 'cronjob':

        // Settings

        $set_status_manually = ORM::for_table('voucher_setting')->where('setting', 'set_status_manually')->find_one();
        $voucher_status_processing = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_processing')->find_one();
        $voucher_status_active = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_active')->find_one();
        $voucher_status_expired = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_expired')->find_one();
        $voucher_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_cancelled')->find_one();
        $page_status_processing = ORM::for_table('voucher_setting')->where('setting', 'page_status_processing')->find_one();
        $page_status_confirmed = ORM::for_table('voucher_setting')->where('setting', 'page_status_confirmed')->find_one();
        $page_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'page_status_cancelled')->find_one();

        $setting = array(
            'set_status_manually' => $set_status_manually['value'],
            'voucher_status_processing' => $voucher_status_processing['value'],
            'voucher_status_active' => $voucher_status_active['value'],
            'voucher_status_expired' => $voucher_status_expired['value'],
            'voucher_status_cancelled' => $voucher_status_cancelled['value'],
            'page_status_processing' => $page_status_processing['value'],
            'page_status_confirmed' => $page_status_confirmed['value'],
            'page_status_cancelled' => $page_status_cancelled['value']
        );

        $today = date('Y-m-d');

        if($setting['set_status_manually'] != '1'){

            // Generated Voucher

            $vouchers = ORM::for_table('voucher_generated')
                ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
                ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
                ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
                ->select_many('voucher_generated.*')
                ->select('voucher_category.category_name')
                ->select_many('crm_accounts.account', 'crm_accounts.email')
                ->select('sys_invoices.id', 'invoice_id')
                ->select('sys_invoices.total', 'invoice_amount')
                ->select('sys_invoices.duedate', 'invoice_due_date')
                ->select('sys_invoices.vtoken', 'invoice_vtoken')
                ->select('sys_invoices.status', 'invoice_status')
                ->find_many();

            foreach ($vouchers as $v){

                $e = null;
                if($v['expiry_date']<$today && $v['expiry_date'] != '0000-00-00' && $v['status'] != 'Expired' && $v['redeem_status'] == 'Redeem'){
                    $d = ORM::for_table('voucher_generated')->find_one($v['id']);
                    $d->status = 'Expired';
                    $d->save();
                    if($setting['voucher_status_expired']){
                        $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_expired']);
                    }else{
                        $e = null;
                    }

                }


                if($e){
                    $subject = new Template($e['subject']);
                    $subject->set('contact_name', $v['account']);
                    $subject->set('business_name', $config['CompanyName']);
                    $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                    $subject->set('client_login_url', U.'client/login');
                    $subject->set('client_email', $v['email']);
                    $subject->set('voucher_category', $v['category']);
                    $subject->set('voucher_number', $v['prefix'].$v['serial_number']);
                    $subject->set('date_activated',date($config['df'], strtotime($v['date'])));
                    $subject->set('date_expire', date($config['df'], strtotime($v['expiry_date'])));
                    $subject->set('invoice_url', U . 'client/iview/' . $v['invoice_id'] . '/token_' . $v['invoice_vtoken']);
                    $subject->set('invoice_id', $v['invoice_id']);
                    $subject->set('invoice_due_date', date($config['df'], strtotime($v['invoice_due_date'])));
                    $subject->set('invoice_amount', number_format($v['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                    $subject->set('status', $v['status']);
                    $subj = $subject->output();

                    $message = new Template($e['message']);
                    $message->set('contact_name', $v['account']);
                    $message->set('business_name', $config['CompanyName']);
                    $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                    $message->set('client_login_url', U.'client/login');
                    $message->set('client_email', $v['email']);
                    $message->set('voucher_category', $v['category_name']);
                    $message->set('voucher_number', $v['prefix'].$v['serial_number']);
                    $message->set('date_activated',date($config['df'], strtotime($v['date'])));
                    $message->set('date_expire', date($config['df'], strtotime($v['expiry_date'])));
                    $message->set('invoice_url', U . 'client/iview/' . $v['invoice_id'] . '/token_' . $v['invoice_vtoken']);
                    $message->set('invoice_id', $v['invoice_id']);
                    $message->set('invoice_due_date', date($config['df'], strtotime($v['invoice_due_date'])));
                    $message->set('invoice_amount', number_format($v['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                    $message->set('status', $v['status']);
                    $message_o = $message->output();

                    Notify_Email::_send($v['account'], $v['email'], $subj, $message_o);
                }

            }

            $pages = ORM::for_table('voucher_page_transaction')
                ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
                ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
                ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_page_transaction.invoice_id'))
                ->select_many('voucher_page_transaction.*')
                ->select_many('voucher_generated.date', 'voucher_generated.expiry_date')
                ->select('voucher_generated.status', 'voucher_status')
                ->select_many('crm_accounts.account', 'crm_accounts.email')
                ->select('sys_invoices.id', 'invoice_id')
                ->select('sys_invoices.total', 'invoice_amount')
                ->select('sys_invoices.duedate', 'invoice_due_date')
                ->select('sys_invoices.vtoken', 'invoice_vtoken')
                ->select('sys_invoices.status', 'invoice_status')
                ->find_many();

            foreach($pages as $p){

            }

        }

        break;

    default:
        echo 'action not defined';
        break;

 }