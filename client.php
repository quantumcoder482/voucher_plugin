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

        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('voucher_format',array('voucher_generated.voucher_format_id','=','f.id'),'f')
            ->inner_join('voucher_country',array('f.country_id','=','c.id'),'c')
            ->select('voucher_generated.*')
            ->select('c.country_name')
            ->select('c.category')
            ->select('f.billing_cycle')
            ->select('f.expiry_day')
            ->select('f.voucher_img')
            ->where_equal('voucher_generated.contact_id',$account_id)
            ->find_array();

      
        $expire_date = array();
        $voucher_status = array();
        foreach($voucher_data as $v){

            $interval = null;
            $now_date = date('Y-m-d');

            switch($v['billing_cycle']){
                case 'annual':
                    
                    $interval = new DateInterval('P1Y');

                    break;

                case 'monthly':
                    
                    $interval = new DateInterval('P1M');
                    
                    break;
            }
            $expire_date[$v['id']] = date_create($v['date'])->add($interval);
            $expire_date[$v['id']] =  $expire_date[$v['id']]->format('Y-m-d');
            
            $date1 = date_create($now_date);
            $date2 = date_create($expire_date[$v['id']]);
            $rest = date_diff($date1, $date2);
            $rest = intval($rest->format("%a"));
           
            if($date2 < $date1){
                $voucher_status[$v['id']] = 'Expired';
            } elseif( $rest < intval($v['expiry_day'])) {
                $voucher_status[$v['id']] = 'Limit';
            } else {
                $voucher_status[$v['id']] = 'Active';
            }
            
        }

        $redeem = ORM::for_table('voucher_setting')->where_equal('setting','able_redeem_voucher_code')->select('value')->find_one();
        $show_alert = ORM::for_table('voucher_setting')->where_equal('setting','show_alert_message')->select('value')->find_one();
        $alert_msg = ORM::for_table('voucher_setting')->where_equal('setting','alert_message')->select('value')->find_one();
        $alert_msg = str_replace('[Click Here]', '', $alert_msg['value']);
        $alert_msg = str_replace('<p>', '', $alert_msg);
        $alert_msg = str_replace('</p>', '', $alert_msg);
        $setting = array(
            'redeem' => $redeem['value'],
            'show_alert' => $show_alert['value'],
            'alert_msg' => $alert_msg
        );
        
       

        view('client_wrapper',[
            '_include' => 'client_myvoucher',
            'voucher_data' => $voucher_data,
            'voucher_status' => $voucher_status,
            'expire_date' => $expire_date,
            'setting' => $setting,
            'baseUrl' => $baseUrl,
            'user' => $c
        ]);


        break;
    

    case 'voucher_page':

        $voucher_id = route(3);
        
        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->select('voucher_country.country_name','country_name')
            ->select('voucher_country.category', 'category')
            ->select('voucher_format.voucher_img', 'voucher_img')
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
        foreach($voucher_pages as $vp) {
//           echo $vp['id'] . ' ' . $vp['transaction_id'] . ' ' . $vp['transaction_status'] . ' ' . $vp['title'] . '<br>';
            $page_status[$vp['id']] = 'redeem';
        }

//       exit;


        $voucher_edit_enable = ORM::for_table('voucher_setting')->where_equal('setting','cant_edit_submit_voucher')->select('value')->find_one();

        $setting = array(
            'voucher_edit_enable' => $voucher_edit_enable['value'],
        );



        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', 'Voucher'.' | '.$voucher_info['country_name'].' | '.$voucher_info['category']);
        $ui->assign('_title', $config['CompanyName']);
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('client_wrapper',[
            '_include' => 'client_voucher_page',
            'voucher_pages' => $voucher_pages,
            'voucher_img' => $voucher_info['voucher_img'],
            'page_status' => $page_status,
            'setting' => $setting,
            'baseUrl' => $baseUrl,
            'voucher_id' => $voucher_id,
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


        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->select('voucher_country.*')
            ->select('voucher_format.voucher_img', 'voucher_img')
            ->select('voucher_generated.serial_number', 'serial_number')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();

        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', 'Voucher'.' | '.$voucher_info['country_name'].' | '.$voucher_info['category']);
        $ui->assign('_title', $config['CompanyName']);
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));



        view('client_wrapper',[
            '_include' => 'client_voucherpage_redeem',
            'page_setting' => $page_setting,
            'customer_data' => $customer_data,
            'customer_addr' => $customer_addr,
            'product_data' => $product_data,
            'voucher_info' => $voucher_info,
            'fs' => $fs,
            'baseUrl' => $baseUrl,
            'voucher_id' => $voucher_id,
            'page_id' => $page_id,
            'user' => $c,

        ]);

        break;


    case 'modal_edit_redeem':

        $transaction_id = route(3);

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $transaction_data = ORM::for_table('voucher_page_transaction')->find_one($transaction_id);

        $page_setting =ORM::for_table('voucher_pages')->find_one($transaction_data['page_id']);
//        $customer_data = ORM::for_table('crm_accounts')->find_one($account_id);
//        $customer_addr =$customer_data['address']."  ".$customer_data['city']."  ".$customer_data['state']."  ".$customer_data['country']."   ".$customer_data['zip'];
//        $product_data = ORM::for_table('sys_items')->find_one($page_setting['product_id']);


        $voucher_info =ORM::for_table('voucher_country')
            ->inner_join('voucher_format',array('voucher_country.id','=','voucher_format.country_id'))
            ->inner_join('voucher_generated',array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->select('voucher_country.*')
            ->select('voucher_generated.serial_number', 'serial_number')
            ->where_equal('voucher_generated.id',$transaction_data['voucher_id'])
            ->find_one();

        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        view('wrapper_modal',[
            '_include' => 'client_modal_editredeem',
            'baseUrl' => $baseUrl,
            't_data' => $transaction_data,
            'page_setting' => $page_setting,

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
            ->inner_join('voucher_format', array('voucher_country.id','=','voucher_format.country_id'))->find_many();

        if($country_id){
            $voucher_formats = ORM::for_table('voucher_format')->where_equal('country_id', $country_id)->find_many();
        }else {
            $voucher_formats = null;
        }

        if($voucher_id){
            $voucher_pages = ORM::for_table('voucher_pages')->where_equal('voucher_format_id', $voucher_id)->order_by_asc('id')->find_many();
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
            ->select('voucher_pages.*')
            ->select('voucher_format.sales_price')
            ->select('voucher_country.country_name')
            ->select('voucher_country.category')
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



        break;

    default:
        echo 'action not defined';
        break;

 }