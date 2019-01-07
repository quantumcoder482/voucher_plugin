<?php

// require 'apps/voucher/models/Voucher.php';
// require 'apps/voucher/models/VoucherCountry.php';

 if(isset($routes['2']) && $routes['2'] != ''){
     $action = $routes['2'];
 } else {
     $action = 'myvoucher';
 }

switch ($action){
  
/*
 *  My Voucher  (client_myvoucher.tpl)
 */

    case 'myvoucher':

        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', 'My Voucher');
        $ui->assign('_title', $config['CompanyName']);
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];
        $active_status = '';
        $active_invoice_url = '';


        $redeem = ORM::for_table('voucher_setting')->where_equal('setting','able_redeem_voucher_code')->select('value')->find_one();
        $show_alert = ORM::for_table('voucher_setting')->where_equal('setting','show_alert_message')->select('value')->find_one();
        $alert_msg = ORM::for_table('voucher_setting')->where_equal('setting','alert_message')->select('value')->find_one();
        $alert_msg = str_replace('[Click Here]', '', $alert_msg['value']);
        $alert_msg = str_replace('<p>', '', $alert_msg);
        $alert_msg = str_replace('</p>', '', $alert_msg);
        $active_fee = ORM::for_table('voucher_setting')->where_equal('setting', 'activation_fee')->select('value')->find_one();
        $auto_create_active_invoice = ORM::for_table('voucher_setting')->where_equal('setting', 'user_require_make_payment') ->select('value')->find_one();
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting', 'require_agree')->select('value')->find_one();

        $setting = array(
            'active_fee' => $active_fee['value'],
            'auto_create_active_invoice' => $auto_create_active_invoice['value'],
            'redeem' => $redeem['value'],
            'show_alert' => $show_alert['value'],
            'alert_msg' => $alert_msg,
            'require_agree' => $require_agree['value']
        );


        $activation = ORM::for_table('voucher_trans_active')
            ->inner_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_trans_active.invoice_id'))
            ->select('voucher_trans_active.*')
            ->select('sys_invoices.status', 'invoice_status')
            ->where_equal('customer_id', $account_id)
            ->find_one();


        if($activation && $activation['invoice_status'] == 'Paid'){
            $active_status = 'Yes';
        }else {
            $active_status = 'No';
            if(!$activation){
                if($auto_create_active_invoice['value'] == 1){

                    $invoice = Invoice::forSingleItem($account_id, 'Active account fee', $active_fee['value']);
                    $invoice_id = $invoice['id'];
                    $invoice_vtoken = $invoice['vtoken'];
                    $active_invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';

                    $d =ORM::for_table('voucher_trans_active')->create();
                    $d->customer_id = $account_id;
                    $d->activation_fee = $active_fee['value'];
                    $d->invoice_id = $invoice_id;
                    $d->vtoken = $invoice_vtoken;
                    $d->save();

                }

            }elseif($activation['invoice_status'] != 'Paid'){
                $active_invoice_url = U.'client/iview/'.$activation['invoice_id'].'/token_'.$activation['vtoken'].'/';
            }

        }


        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('sys_invoices', array('voucher_generated.invoice_id', '=', 's.id'), 's')
            ->left_outer_join('voucher_format',array('voucher_generated.voucher_format_id','=','f.id'),'f')
            ->left_outer_join('voucher_category', array('voucher_category.id','=','f.category_id'))
            ->inner_join('voucher_country',array('f.country_id','=','c.id'),'c')
            ->select('voucher_generated.*')
            ->select('s.status', 'invoice_status')
            ->select('c.country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('f.id', 'format_id')
            ->select('f.billing_cycle')
            ->select('f.expiry_day')
            ->select('f.voucher_img')
            ->where_equal('voucher_generated.contact_id',$account_id)
//            ->where_equal('voucher_generated.status', 'Redeem')
            ->order_by_desc('id')
            ->find_array();


        $voucher_status = array();
        $voucher_pages = array();
        $redeem_pages = array();
        $total_vouchers = 0;
        foreach($voucher_data as $v){

            // Voucher status

            $now_date = date('Y-m-d');
            $date1 = date_create($now_date);
            $date2 = date_create($v['expiry_date']);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));

            if($date2 < $date1){
                $voucher_status[$v['id']] = 'Expired';
            } elseif( $rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
                if($v['invoice_status'] == 'Paid' && $v['redeem_status'] == 'Redeem'){
                    $voucher_status[$v['id']] = 'Active';
                }else {
                    $voucher_status[$v['id']] = 'Inactive';
                }

            }

            // Voucher Balance

            $voucher_pages[$v['id']] = ORM::for_table('voucher_pages')->where_equal('voucher_format_id',$v['format_id'])->count();
            $redeem_pages[$v['id']] = ORM::for_table('voucher_page_transaction')->where_equal('voucher_page_transaction.voucher_id', $v['id'])->count();

            $total_vouchers++;

        }


        view('client_wrapper',[
            '_include' => 'client_myvoucher',
            'voucher_data' => $voucher_data,
            'voucher_status' => $voucher_status,
            'voucher_pages' => $voucher_pages,
            'redeem_pages' => $redeem_pages,
            'total_vouchers' => $total_vouchers,
            'active_status' => $active_status,
            'active_invoice_url' => $active_invoice_url,
            'setting' => $setting,
            'baseUrl' => $baseUrl,
            'user' => $c
        ]);


        break;

    case 'redeem_voucher':

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $serial_number_str= _post('serial_number');
        $serial_number_str = trim($serial_number_str);

        $prefix = '';
        $serial_number = '';
        if($serial_number_str != '' && strlen($serial_number_str)>2){
            $prefix = substr($serial_number_str, 0, 2);
            $serial_number = trim(substr($serial_number_str, 2, strlen($serial_number_str)-1));
        }

//        $serial_arr = explode(' ', $serial_number_str);
//        $prefix = $serial_arr[0];
//        $serial_number = @$serial_arr[1];

        $msg = '';

        $voucher_data = ORM::for_table('voucher_generated')
            ->inner_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
            ->select('voucher_generated.*')
            ->select('voucher_format.sales_price', 'sales_price')
            ->select('voucher_format.billing_cycle', 'billing_cycle')
            ->select('sys_invoices.id', 'invoice_id')
            ->select('sys_invoices.status', 'invoice_status')
            ->select('sys_invoices.vtoken', 'invoice_vtoken')
            ->where('serial_number', $serial_number)
            ->find_one();

        if(!$voucher_data){
            $msg .= "Voucher Serial Number invalid <br>";
        }elseif($voucher_data['redeem_status'] == 'Redeem'){
            if($voucher_data['invoice_status'] == 'Paid'){
                $msg .= "Voucher Serial Number already redeemed <br>";
            }else{
                _msglog('r','You have to pay for voucher redeem');
                $invoice_url = U.'client/iview/'.$voucher_data['invoice_id'].'/token_'.$voucher_data['invoice_vtoken'].'/';
                echo $invoice_url;
                exit;
            }

        }elseif($voucher_data['status'] != 'Active'){
            $msg .= "Voucher Status is not Active <br>";
        }


        if($msg == ''){

            $invoice_id = null;
            $invoice_url = "";
            if($voucher_data['create_invoice'] == '1'){
                $invoice = Invoice::forSingleItem($account_id, 'Redeem voucher '.$serial_number_str, $voucher_data['sales_price']);
                $invoice_id = $invoice['id'];
                $invoice_vtoken = $invoice['vtoken'];
                $invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';
            }

            if($voucher_data['add_payment'] == '1') {
                $invoice_info = ORM::for_table('sys_invoices')->find_one($invoice_id);
                $total_price = $invoice_info['total'];
                if ($invoice_info) {

                    // Customer balance chanage

                    $customer_info = ORM::for_table('crm_accounts')->find_one($account_id);
                    if ($customer_info) {
                        if ($customer_info['balance'] < $total_price) {
                            _msglog('r',"Customer's credit balance is not enough");
                            echo "reload";
                            break;
                        }
                        $customer_info->balance = $customer_info['balance'] - $total_price;
                        $customer_info->save();
                    }

                    $invoice_info->status = 'Paid';
                    $invoice_info->credit = $total_price;
                    $invoice_info->save();

                    // Transaction change

                    $account = 'Credit';
                    $type = 'Income';
                    $amount = $total_price;
                    $payer_id = $account_id;
                    $method = 'Credit';
                    $ref = 'Client Paid with Account Credit';
                    $des = 'Invoice: ' . $invoice_info->id() . ' Payment from Credit';
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

            $today = date('Y-m-d');

            $d = ORM::for_table('voucher_generated')->find_one($voucher_data['id']);
            _msglog('s','Redeem Voucher Successfully');


            switch ($voucher_data['billing_cycle']){
                case 'annual':
                    $interval = new DateInterval('P1Y');
                    $expiry_date = date_create($today)->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');

                    break;
                case 'monthly':
                    $interval = new DateInterval('P1M');
                    $expiry_date = date_create($today)->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');
                    break;
            }


            // insert into database

//            $d->voucher_format_id = $voucher_data['voucher_format_id'];
//            $d->contact_id = $voucher_data['contact_id'];
            $d->contact_id = $account_id;
            $d->agent_id = $voucher_data['agent_id'];
//            $d->serial_number = $voucher_data['serial_number'];
//            $d->serial_pgnum = $voucher_data['serial_pgnum'];
            $d->create_invoice = 1;
            $d->date = $today;
            $d->expiry_date = $expiry_date;
//            $d->prefix = $voucher_data['prefix'];
//            $d->description = $voucher_data['description'];
            $d->invoice_id = $invoice_id;
//            $d->voucher_template = $voucher_data['voucher_template'];
//            $d->voucher_pdf = $voucher_data['voucher_pdf'];
            $d->redeem_status = "Redeem";
            $d->save();

//            echo $d->id();

            if($voucher_data['add_payment'] != '1' && $invoice_id != null){
                echo $invoice_url;
            }else{
                echo "reload";
            }

        }else{
            _msglog('r',$msg);
            echo "reload";
        }

        break;

    case 'voucher_page':

        $voucher_id = route(3);
        $view_type = route(4);
        
        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_country.country_name','country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.voucher_img', 'voucher_img')
            ->select_many('voucher_generated.prefix', 'voucher_generated.serial_number')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();


        $voucher_pages = ORM::for_table('voucher_pages')
            ->inner_join('voucher_generated',array('voucher_pages.voucher_format_id','=','g.voucher_format_id'),'g')
//            ->left_outer_join('voucher_page_transaction',array('voucher_pages.id', '=', 't.page_id' ), 't')
//            ->left_outer_join('voucher_page_transaction',array('g.id', '=', 't1.voucher_id'),'t1')
            ->select('voucher_pages.*')
//            ->select('t.id', 'transaction_id')
//            ->select('t.status', 'transaction_status')
            ->where_equal('g.id',$voucher_id)
            ->order_by_asc('voucher_pages.id')
            ->find_many();


        $page_status = array();
        $today = date('Y-m-d');
        $transaction_id = array();

        foreach($voucher_pages as $vp) {
            $page_status[$vp['id']] = 'redeem';
            $redeem_page = ORM::for_table('voucher_page_transaction')
                ->where('voucher_id', $voucher_id)
                ->where('page_id', $vp['id'])
                ->find_one();
            if($redeem_page){
                if($redeem_page['status'] == 'Confirm'){
                    $page_status[$vp['id']] = 'confirm';
                }elseif($redeem_page['status'] == 'Processing' && $redeem_page['return_date']<$today){
                    $redeem_page->status = 'Confirm';
                    $page_status[$vp['id']] = 'confirm';
                    $redeem_page->save();
                }else{
                    $page_status[$vp['id']] = 'processing';
                }

                $transaction_id[$vp['id']] = $redeem_page['id'];
            }
        }


        $voucher_edit_enable = ORM::for_table('voucher_setting')->where_equal('setting','cant_edit_submit_voucher')->select('value')->find_one();
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting', 'require_agree')->select('value')->find_one();

        $setting = array(
            'voucher_edit_enable' => $voucher_edit_enable['value'],
            'require_agree' => $require_agree['value']
        );


        $recent_transaction = ORM::for_table('voucher_page_transaction')
            ->left_outer_join('sys_invoices', array('sys_invoices.id','=','voucher_page_transaction.invoice_id'))
            ->select_many('voucher_page_transaction.*')
            ->select('sys_invoices.id', 'invoice_id')
            ->select('sys_invoices.total', 'invoice_amount')
            ->select('sys_invoices.date', 'invoice_date')
            ->select('sys_invoices.vtoken', 'invoice_vtoken')
            ->select('sys_invoices.status', 'invoice_status')
            ->where('voucher_id', $voucher_id)
            ->order_by_desc('id')
            ->find_array();

        $invoice_url = array();
        foreach($recent_transaction as $r){
            $invoice_url[$r['id']] = U.'client/iview/'.$r['invoice_id'].'/token_'.$r['invoice_vtoken'].'/';
        }


        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', 'Voucher'.' | '.$voucher_info['country_name'].' | '.$voucher_info['category']);
        $ui->assign('_title', $config['CompanyName']);
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



        view('client_wrapper',[
            '_include' => 'client_voucher_page',
            'voucher_pages' => $voucher_pages,
            'voucher_img' => $voucher_info['voucher_img'],
            'serial_number' => $voucher_info['prefix']." ".$voucher_info['serial_number'],
            'page_status' => $page_status,
            't_id' => $transaction_id,
            'recent_transaction' => $recent_transaction,
            'setting' => $setting,
            'baseUrl' => $baseUrl,
            'voucher_id' => $voucher_id,
            'invoice_url' => $invoice_url,
            'view_type' => $view_type,
            'user' => $c
        ]);

        break;

    case 'redeem_voucher_page':

        $voucher_id = route(3);
        $page_id = route(4);

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

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


        $voucher_edit_enable = ORM::for_table('voucher_setting')->where_equal('setting','cant_edit_submit_voucher')->select('value')->find_one();
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting', 'require_agree')->select('value')->find_one();

        $setting = array(
            'voucher_edit_enable' => $voucher_edit_enable['value'],
            'require_agree' => $require_agree['value']
        );



        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('client_wrapper',[
            '_include' => 'client_voucherpage_redeem',
            'page_setting' => $page_setting,
            'customer_data' => $customer_data,
            'customer_addr' => $customer_addr,
            'product_data' => $product_data,
            'sub_product_data' => $sub_product_data,
            'voucher_info' => $voucher_info,
            'setting' => $setting,
            'fs' => $fs,
            'baseUrl' => $baseUrl,
            'voucher_id' => $voucher_id,
            'page_id' => $page_id,
            'user' => $c,

        ]);

        break;

    case 'post_redeem_page':

        $tid = route(3);

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $voucher_id = _post('voucher_id');
        $page_id = _post('page_id');

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
            }else{
                $d = ORM::for_table('voucher_page_transaction')->find_one($tid);
                _msglog('s','Page Redeem updated Successfully');
            }

            if($page_setting['date_range'] == 1){
                $status = 'Processing';
            }else {
                $status = 'Confirm';
            }

            $invoice_url = "";

            if($invoice_id){
                if($sub_product_req == 1){
                    $product_amount = round((float)$product_price + (float)$sub_product_price,2);
                }else {
                    $product_amount = round((float)$product_price,2);
                }

                $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);
                if($invoice_data['status'] != 'Paid'){
                    $invoice_data->total = $product_amount;
                    $invoice_data->subtotal = $product_amount;
                    $invoice_data->save();
                }else{
                    _msglog('r','This page paid already');
                    echo "page_list";
                    exit;
                }

            }

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
                $invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';
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

    case 'modal_edit_redeem':

        $transaction_id = route(3);

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $transaction_data = ORM::for_table('voucher_page_transaction')->find_one($transaction_id);

        // Custom fields

        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();
        $ui->assign('fs',$fs);
        $cf_value=array();
        foreach ($fs as $f) {
            $cf=ORM::for_table('voucher_customfieldsvalues')->where('relid',$transaction_id)->where('fieldid',$f->id)->find_one();
            $cf_value[$f->id]='';
            if($cf){
                $cf_value[$f->id]=$cf->fvalue;
            }
        }
        $ui->assign('cf_value',$cf_value);
        
        
        $page_setting =ORM::for_table('voucher_pages')->find_one($transaction_data['page_id']);
//        $customer_data = ORM::for_table('crm_accounts')->find_one($account_id);
//        $customer_addr =$customer_data['address']."  ".$customer_data['city']."  ".$customer_data['state']."  ".$customer_data['country']."   ".$customer_data['zip'];
//        $product_data = ORM::for_table('sys_items')->find_one($page_setting['product_id']);


        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_country.*')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_generated.serial_number', 'serial_number')
            ->where_equal('voucher_generated.id',$transaction_data['voucher_id'])
            ->find_one();

        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        view('wrapper_modal',[
            '_include' => 'client_modal_editredeem',
            'baseUrl' => $baseUrl,
            't_data' => $transaction_data,
            'page_setting' => $page_setting,
            't_id' => $transaction_id

        ]);

        break;

    case 'confirm_redeem':

        $baseUrl = APP_URL;
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting','require_agree')->select('value')->find_one();
        $require_terms_condition = ORM::for_table('voucher_setting')->where_equal('setting','agreement_text')->select('value')->find_one();

        $setting = array(
            'require_agree' => $require_agree['value'],
            'agreement_text' => $require_terms_condition['value']
        );

        view('wrapper_modal',[
            '_include' => 'client_terms_condition',
            'baseUrl' => $baseUrl,
            'setting' => $setting

        ]);

        break;

    case 'vouchershop':

        $country_id = route(3);
        $country_id = str_replace('cid','',$country_id);

        $voucher_id = route(4);
        $voucher_id = str_replace('vid','',$voucher_id);


        $baseUrl = APP_URL;
        $c = Contacts::details();

        $country_list = ORM::for_table('voucher_country')
            ->inner_join('voucher_format', array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_country.*')
            ->select('voucher_category.category_name', 'category')
            ->group_by('country_name')
            ->find_array();


        if($country_id){
            $voucher_formats = ORM::for_table('voucher_format')->where_equal('country_id', $country_id)->find_array();
        }else {
            $voucher_formats = null;
        }

        if($voucher_id){
            $voucher_pages = ORM::for_table('voucher_pages')->where_equal('voucher_format_id', $voucher_id)->order_by_asc('id')->find_array();
        }else {
            $voucher_pages = null;
        }


        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', 'My Voucher');
        $ui->assign('_title', $config['CompanyName']);

        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));


        view('client_wrapper',[
            '_include' => 'client_voucher_shop',
            'country_list' => $country_list,
            'voucher_formats' => $voucher_formats,
            'voucher_pages' => $voucher_pages,
            'country_id' => $country_id,
            'voucher_id' => $voucher_id,
            'baseUrl' => $baseUrl,
            'user' => $c,

        ]);

        break;

    case 'modal_page':

        $page_id = route(3);
        $page_id = str_replace('pid','', $page_id);

        $baseUrl = APP_URL;
        $c = Contacts::details();


        $page_data = ORM::for_table('voucher_pages')
            ->inner_join('voucher_format', array('voucher_pages.voucher_format_id', '=', 'voucher_format.id'))
            ->inner_join('voucher_country', array('voucher_format.country_id', '=', 'voucher_country.id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_pages.*')
            ->select('voucher_format.sales_price')
            ->select('voucher_country.country_name')
            ->select('voucher_category.category_name', 'category')
            ->where_equal('voucher_pages.id', $page_id)
            ->find_one();

//
//        print_r($page_data);
//        exit;

        view('wrapper_modal',[
            '_include' => 'client_modal_page',
            'page_data' => $page_data,
            'baseUrl' => $baseUrl,
            'user' => $c
        ]);


        break;

    case 'buy_voucher':

        $id = route(3);
        $baseUrl = APP_URL;


        $voucher = ORM::for_table('voucher_format')
            ->join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix')
            ->select('voucher_category.category_name', 'category')
            ->order_by_asc('voucher_format.id')
            ->find_one($id);

        $templates = ORM::for_table('voucher_template')->order_by_asc('template_name')->find_array();

        view('wrapper_modal',[
            '_include' => 'client_modal_cert',
            'baseUrl' => $baseUrl,
            'voucher' => $voucher,
            'templates' => $templates
        ]);

        break;

    case 'generate_voucher':


        $c = Contacts::details();
        $contact_id = $c['id'];

        // Posted data
        $id = _post('vid');
        $description = _post('description');
        $serial_numbers = _post('serial_number');
        $total_voucher = _post('total_voucher');
        $template_id = _post('template_id');


        $voucher_info = ORM::for_table('voucher_format')
            ->left_outer_join('voucher_country', array('voucher_country.id','=','voucher_format.country_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_format.*')
            ->select_many('voucher_country.country_name', 'voucher_country.prefix')
            ->select('voucher_category.category_name')
            ->find_one($id);

        $prefix = $voucher_info['prefix'];

        $date = date('Y-m-d');

        switch ($voucher_info['billing_cycle']){
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



        // Create Invoice

        $invoice_id = null;

        $amount = $total_voucher * $voucher_info['sales_price'];
        $item_name = $voucher_info['country_name'].' '.$voucher_info['category_name'].' Voucher';
        $invoice = Invoice::forSingleItem($contact_id, $item_name, $amount);
        $invoice_id = $invoice['id'];
        $invoice_vtoken = $invoice['vtoken'];


        // Generate vouchers

        $voucher_numbers = explode(',',$serial_numbers);

        for($i=1;$i<=$total_voucher;$i++){


            $d = ORM::for_table('voucher_generated')->create();
            _msglog('s','Voucher Generated Successfully');

            $serial_number = $voucher_numbers[$i-1];

            // voucher pdf create

//            $template_file = 'apps/voucher/public/template/'.$voucher_template;
//            $newfile = 'apps/voucher/public/vouchers/'.$serial_number.'.pdf';
            $voucher_pdf = $serial_number.'.pdf';
//            if(!copy($template_file,$newfile))
//            {
//                echo "failed to copy $file";
//                break;
//            } else {
//                $voucher_pdf = $serial_number.'.pdf';
//            }


            // insert into database

            $d->voucher_format_id = $id;
            $d->template_id = $template_id;
            $d->contact_id = $contact_id;
            $d->serial_number = $serial_number;
            $d->create_invoice = 1;
            $d->date = $date;
            $d->expiry_date = $expiry_date;
            $d->prefix = $prefix;
            $d->description = $description;
            $d->invoice_id = $invoice_id;
            $d->voucher_pdf = $voucher_pdf;
            $d->status = 'Active';
            $d->redeem_status = 'Redeem';

            $d->save();

        }

//        r2(U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/','s');

        $str = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';
        echo $str;

        break;

    case 'clientvoucher':

        $ui->assign('_application_menu', 'Client Voucher');
        $ui->assign('_st', 'Client Voucher');
        $ui->assign('_title', $config['CompanyName']);
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];
        $active_status = '';
        $active_invoice_url = '';


        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
            ->left_outer_join('voucher_format',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id','=','voucher_format.category_id'))
            ->inner_join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->select('voucher_generated.*')
            ->select('voucher_country.country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.id', 'format_id')
            ->select_many('voucher_format.billing_cycle','voucher_format.expiry_day','voucher_format.voucher_img')
            ->select('crm_accounts.account', 'customer')
            ->where_equal('voucher_generated.agent_id',$account_id)
            ->order_by_desc('id')
            ->find_array();


        $voucher_status = array();
        $voucher_pages = array();
        $redeem_pages = array();
        $total_vouchers = 0;

        foreach($voucher_data as $v){

            // Voucher status

            $now_date = date('Y-m-d');
            $date1 = date_create($now_date);
            $date2 = date_create($v['expiry_date']);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));

            if($date2 < $date1){
                $voucher_status[$v['id']] = 'Expired';
            } elseif( $rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
                if($v['status'] == 'Redeem'){
                    $voucher_status[$v['id']] = 'Redeem';
                }else {
                    $voucher_status[$v['id']] = 'UnRedeem';
                }

            }

            // Voucher Balance

            $voucher_pages[$v['id']] = ORM::for_table('voucher_pages')->where_equal('voucher_format_id',$v['format_id'])->count();
            $redeem_pages[$v['id']] = ORM::for_table('voucher_page_transaction')->where_equal('voucher_page_transaction.voucher_id', $v['id'])->count();

            $total_vouchers++;

        }


        view('client_wrapper',[
            '_include' => 'client_clientvoucher',
            'voucher_data' => $voucher_data,
            'voucher_status' => $voucher_status,
            'voucher_pages' => $voucher_pages,
            'redeem_pages' => $redeem_pages,
            'total_vouchers' => $total_vouchers,
            'baseUrl' => $baseUrl,
            'user' => $c
        ]);
        break;

    default:
        echo 'action not defined';
        break;

 }