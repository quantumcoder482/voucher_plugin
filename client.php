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
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','dt/dt','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','dt/dt','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];
        $active_status = '';
        $active_invoice_url = '';

        $admin_data = ORM::for_table('sys_users')
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->where('email_notify', '1')
            ->find_array();



        // Setting
        $redeem = ORM::for_table('voucher_setting')->where_equal('setting','able_redeem_voucher_code')->select('value')->find_one();
        $show_alert = ORM::for_table('voucher_setting')->where_equal('setting','show_alert_message')->select('value')->find_one();
        $alert_msg = ORM::for_table('voucher_setting')->where_equal('setting','alert_message')->select('value')->find_one();
        $alert_msg = str_replace('[Click Here]', '', $alert_msg['value']);
        $alert_msg = str_replace('<p>', '', $alert_msg);
        $alert_msg = str_replace('</p>', '', $alert_msg);
        $active_fee = ORM::for_table('voucher_setting')->where_equal('setting', 'activation_fee')->select('value')->find_one();
        $auto_create_active_invoice = ORM::for_table('voucher_setting')->where_equal('setting', 'user_require_make_payment') ->select('value')->find_one();
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting', 'require_agree')->select('value')->find_one();
        $set_status_manually = ORM::for_table('voucher_setting')->where_equal('setting', 'set_status_manually')->select('value')->find_one();

        // mail setting

        $set_status_manually = ORM::for_table('voucher_setting')->where('setting', 'set_status_manually')->find_one();
        $voucher_status_processing = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_processing')->find_one();
        $voucher_status_active = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_active')->find_one();
        $voucher_status_expired = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_expired')->find_one();
        $voucher_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'voucher_status_cancelled')->find_one();
        $page_status_processing = ORM::for_table('voucher_setting')->where('setting', 'page_status_processing')->find_one();
        $page_status_confirmed = ORM::for_table('voucher_setting')->where('setting', 'page_status_confirmed')->find_one();
        $page_status_cancelled = ORM::for_table('voucher_setting')->where('setting', 'page_status_cancelled')->find_one();
        $admin_notification =  ORM::for_table('voucher_setting')->where('setting', 'admin_notification')->find_one();

        $setting = array(
            'active_fee' => $active_fee['value'],
            'auto_create_active_invoice' => $auto_create_active_invoice['value'],
            'redeem' => $redeem['value'],
            'show_alert' => $show_alert['value'],
            'alert_msg' => $alert_msg,
            'require_agree' => $require_agree['value'],
            'set_status_manually' => $set_status_manually['value'],
            'voucher_status_processing' => $voucher_status_processing['value'],
            'voucher_status_active' => $voucher_status_active['value'],
            'voucher_status_expired' => $voucher_status_expired['value'],
            'voucher_status_cancelled' => $voucher_status_cancelled['value'],
            'page_status_processing' => $page_status_processing['value'],
            'page_status_confirmed' => $page_status_confirmed['value'],
            'page_status_cancelled' => $page_status_cancelled['value'],
            'admin_notification' => $admin_notification['value']
        );


        // Activation Fee
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
            ->select('c.country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('f.id', 'format_id')
            ->select('f.billing_cycle')
            ->select('f.expiry_day')
            ->select('f.voucher_img')
            ->select('s.status', 'invoice_status')
            ->where_equal('voucher_generated.contact_id',$account_id)
            ->order_by_desc('id')
            ->find_array();


        $voucher_status = array();
        $voucher_pages = array();
        $redeem_pages = array();
        $total_vouchers = 0;
        foreach($voucher_data as $v){

            // Voucher status

            if($setting['set_status_manually'] == '1'){
                $voucher_status[$v['id']] = $v['status'];
            }else {
                $voucher_status[$v['id']] = $v['status'];

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
                    ->find_one($v['id']);

                $now_date = date('Y-m-d');
                $date1 = date_create($now_date);
                $date2 = date_create($v['expiry_date']);
                $rest = date_diff($date1, $date2);
                $rest = intval($rest->format("%a"));


                if($v['status'] != 'Expired' && $v['status'] != 'Cancelled'){

                    if ($date2 < $date1 && $v['expiry_date'] != '0000-00-00') {
                        $voucher_status[$v['id']] = 'Expired';
                        if($setting['voucher_status_expired']){
                            $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_expired']);
                        }else{
                            $e = null;
                        }
                    }else{
                        if ($v['invoice_status'] == 'Paid' && $v['redeem_status'] == 'Redeem' && $v['status'] == 'Processing') {
                            $voucher_status[$v['id']] = 'Active';
                            if ($setting['voucher_status_active']) {
                                $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_active']);
                            } else {
                                $e = null;
                            }
                        }
//                        }elseif ($v['invoice_status'] != 'Paid' && $v['redeem_status'] == 'Redeem') {
//                            $voucher_status[$v['id']] = 'Processing';
//                            if($setting['voucher_status_processing']){
//                                $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_processing']);
//                            }else{
//                                $e = null;
//                            }
//
//                        }
                    }
                }

                if( $voucher_status[$v['id']] && $d['status'] != $voucher_status[$v['id']]){
                    $d->status = $voucher_status[$v['id']];
                    $d->save();

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
                        if($d['date'] == '0000-00-00' || $d['date'] == ''){
                            $subject->set('date_activated','-');
                        }else{
                            $subject->set('date_activated',date($config['df'], strtotime($d['date'])));
                        }
                        if($d['expiry_date'] == '0000-00-00' || $d['expiry_date'] == ''){
                            $subject->set('date_expire', '-');
                        }else{
                            $subject->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                        }
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
                        if($d['date'] == '0000-00-00' || $d['date'] == ''){
                            $message->set('date_activated','-');
                        }else{
                            $message->set('date_activated',date($config['df'], strtotime($d['date'])));
                        }
                        if($d['expiry_date'] == '0000-00-00' || $d['expiry_date'] == ''){
                            $message->set('date_expire', '-');
                        }else{
                            $message->set('date_expire', date($config['df'], strtotime($d['expiry_date'])));
                        }
                        $message->set('invoice_url', U . 'client/iview/' . $d['invoice_id'] . '/token_' . $d['invoice_vtoken']);
                        $message->set('invoice_id', $d['invoice_id']);
                        $message->set('invoice_due_date', date($config['df'], strtotime($d['invoice_due_date'])));
                        $message->set('invoice_amount', number_format($d['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $message->set('status', $d['status']);
                        $message_o = $message->output();

                        Notify_Email::_send($d['account'], $d['email'], $subj, $message_o);
                        if($setting['admin_notification'] == '1'){
                            foreach ($admin_data as $admin){
                                Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                            }
                        }
                    }
                }

                if ($rest < intval($v['expiry_day']) && $date2>$date1) {
                    $voucher_status[$v['id']] = $rest . ' Days Left';
                }


            }

            // Voucher Balance

            $voucher_pages[$v['id']] = ORM::for_table('voucher_pages')->where_equal('voucher_format_id',$v['format_id'])->count();
            $redeem_pages[$v['id']] = ORM::for_table('voucher_page_transaction')->where_equal('voucher_page_transaction.voucher_id', $v['id'])->count();

            $total_vouchers++;

        }



        // Recent transactions

        $recent_transaction = ORM::for_table('voucher_generated')
            ->left_outer_join('sys_invoices', array('voucher_generated.invoice_id', '=', 's.id'), 's')
            ->left_outer_join('voucher_format',array('voucher_generated.voucher_format_id','=','f.id'),'f')
            ->left_outer_join('crm_accounts', array('crm_accounts.id', '=', 'voucher_generated.contact_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id','=','f.category_id'))
            ->inner_join('voucher_country',array('f.country_id','=','c.id'),'c')
            ->select('voucher_generated.*')
            ->select('s.id', 'invoice_id')
            ->select('s.status', 'invoice_status')
            ->select('s.vtoken', 'invoice_token')
            ->select('s.total', 'invoice_amount')
            ->select('c.country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('crm_accounts.account')
            ->where_equal('voucher_generated.contact_id',$account_id)
            ->where_equal('voucher_generated.redeem_status', 'Redeem')
            ->order_by_desc('id')
            ->find_array();

        $total_transactions = 0;
        $invoice_url = array();
        foreach($recent_transaction as $r){
            $invoice_url[$r['id']] = U.'client/iview/'.$r['invoice_id'].'/token_'.$r['invoice_token'].'/';
            $total_transactions++;
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
            'recent_transaction' => $recent_transaction,
            'total_transactions' => $total_transactions,
            'invoice_url' => $invoice_url,
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



        // mail setting

        $setting_data = ORM::for_table('voucher_setting')->find_array();
        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $admin_data = ORM::for_table('sys_users')
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->where('email_notify', '1')
            ->find_array();


        $msg = '';

        $voucher_data = ORM::for_table('voucher_generated')
            ->inner_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
            ->select('voucher_generated.*')
            ->select('voucher_format.sales_price', 'sales_price')
            ->select('voucher_format.billing_cycle', 'billing_cycle')
            ->select('sys_invoices.id', 'invoice_id')
            ->select('sys_invoices.status', 'invoice_status')
            ->select('sys_invoices.vtoken', 'invoice_vtoken')
            ->select('voucher_category.category_name')
            ->where('serial_number', $serial_number)
            ->find_one();

        if(!$voucher_data){
            $msg .= "Voucher Serial Number invalid <br>";
        }

        switch($voucher_data['status']){
            case 'Cancelled':
                $msg .= "This Voucher is Cancelled <br>";
                break;
            case 'Expired':
                $msg .= "This Voucher is Expired <br>";
                break;
            case 'Active':
                $msg .= "Voucher Serial Number already redeemed <br>";
            case 'Processing':
                if($voucher_data['redeem_status'] == 'Redeem' && $voucher_data['invoice_status'] != 'Paid'){
                _msglog('r','You have to pay for redeem voucher');
                $invoice_url = U.'client/iview/'.$voucher_data['invoice_id'].'/token_'.$voucher_data['invoice_vtoken'].'/';
                echo $invoice_url;
                exit;
            }
        }


        if($msg == ''){

            $invoice_id = null;
            $invoice_url = "";
            if($voucher_data['create_invoice'] != '1' || !$voucher_data['invoice_id']){
                $invoice = Invoice::forSingleItem($account_id, $voucher_data['category_name'].' '.$prefix.$serial_number, $voucher_data['sales_price']);
                $invoice_id = $invoice['id'];
                $invoice_vtoken = $invoice['vtoken'];
                $invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';

                $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);
                $invoice_data->title = $voucher_data['category_name'].' '.$prefix.$serial_number;
                $invoice_data->save();

            }else{
                $invoice_id = $voucher_data['invoice_id'];
                $invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$voucher_data['invoice_vtoken'].'/';
            }

            // Expiry date calculation

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

            $d->contact_id = $account_id;
            $d->agent_id = $voucher_data['agent_id'];
            $d->create_invoice = 1;
            $d->date = $today;
            $d->expiry_date = $expiry_date;
            $d->invoice_id = $invoice_id;
            $d->redeem_status = "Redeem";
            $d->save();

            $vid = $d->id();


            if($vid) {
                $redeemed_voucher = ORM::for_table('voucher_generated')
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
                    ->find_one($vid);


                if($setting['voucher_status_processing']){
                    $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_processing']);
                }

                if ($redeemed_voucher['invoice_status'] != 'Paid'){


                    $invoice_info = ORM::for_table('sys_invoices')->find_one($invoice_id);
                    $total_price = $invoice_info['total'];

                    if ($invoice_info) {

                        // Customer balance chanage

                        $customer_info = ORM::for_table('crm_accounts')->find_one($account_id);
                        if ($customer_info) {
                            if ($customer_info['balance'] < $total_price) {
                                if($e){
                                    $subject = new Template($e['subject']);
                                    $subject->set('contact_name', $redeemed_voucher['account']);
                                    $subject->set('business_name', $config['CompanyName']);
                                    $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                                    $subject->set('client_login_url', U.'client/login');
                                    $subject->set('client_email', $redeemed_voucher['email']);
                                    $subject->set('voucher_category', $redeemed_voucher['category']);
                                    $subject->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                                    $subject->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                                    $subject->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                                    $subject->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                                    $subject->set('invoice_id', $redeemed_voucher['invoice_id']);
                                    $subject->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                                    $subject->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                    $subject->set('status', $redeemed_voucher['status']);
                                    $subj = $subject->output();

                                    $message = new Template($e['message']);
                                    $message->set('contact_name', $redeemed_voucher['account']);
                                    $message->set('business_name', $config['CompanyName']);
                                    $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                                    $message->set('client_login_url', U.'client/login');
                                    $message->set('client_email', $redeemed_voucher['email']);
                                    $message->set('voucher_category', $redeemed_voucher['category_name']);
                                    $message->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                                    $message->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                                    $message->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                                    $message->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                                    $message->set('invoice_id', $redeemed_voucher['invoice_id']);
                                    $message->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                                    $message->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                                    $message->set('status', $redeemed_voucher['status']);
                                    $message_o = $message->output();

                                    Notify_Email::_send($redeemed_voucher['account'], $redeemed_voucher['email'], $subj, $message_o);
                                }
                                _msglog('r', "We are verifying your voucher, you with receive an email notification within 24-hours");
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

                    // voucher status confirm

                    if($setting['set_status_manually'] != '1'){
                        $redeemed_voucher->status = 'Active';
                        $redeemed_voucher->date = $today;
                        $redeemed_voucher->expiry_date = $expiry_date;
                        $redeemed_voucher->save();

                        if($setting['voucher_status_active']){
                            $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_active']);
                        }

                    }

                    if($e){
                        $subject = new Template($e['subject']);
                        $subject->set('contact_name', $redeemed_voucher['account']);
                        $subject->set('business_name', $config['CompanyName']);
                        $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                        $subject->set('client_login_url', U.'client/login');
                        $subject->set('client_email', $redeemed_voucher['email']);
                        $subject->set('voucher_category', $redeemed_voucher['category']);
                        $subject->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                        $subject->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                        $subject->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                        $subject->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                        $subject->set('invoice_id', $redeemed_voucher['invoice_id']);
                        $subject->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                        $subject->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $subject->set('status', $redeemed_voucher['status']);
                        $subj = $subject->output();

                        $message = new Template($e['message']);
                        $message->set('contact_name', $redeemed_voucher['account']);
                        $message->set('business_name', $config['CompanyName']);
                        $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                        $message->set('client_login_url', U.'client/login');
                        $message->set('client_email', $redeemed_voucher['email']);
                        $message->set('voucher_category', $redeemed_voucher['category_name']);
                        $message->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                        $message->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                        $message->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                        $message->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                        $message->set('invoice_id', $redeemed_voucher['invoice_id']);
                        $message->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                        $message->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $message->set('status', $redeemed_voucher['status']);
                        $message_o = $message->output();

                        Notify_Email::_send($redeemed_voucher['account'], $redeemed_voucher['email'], $subj, $message_o);
                        if($setting['admin_notification'] == '1'){
                            foreach ($admin_data as $admin){
                                Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                            }
                        }
                    }
                    echo "reload";
                }else{
                    if($setting['set_status_manually'] != '1'){
                        $redeemed_voucher->status = 'Active';
                        $redeemed_voucher->date = $today;
                        $redeemed_voucher->expiry_date = $expiry_date;
                        $redeemed_voucher->save();

                        if($setting['voucher_status_active']){
                            $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_active']);
                        }

                    }
                    if($e){
                        $subject = new Template($e['subject']);
                        $subject->set('contact_name', $redeemed_voucher['account']);
                        $subject->set('business_name', $config['CompanyName']);
                        $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                        $subject->set('client_login_url', U.'client/login');
                        $subject->set('client_email', $redeemed_voucher['email']);
                        $subject->set('voucher_category', $redeemed_voucher['category']);
                        $subject->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                        $subject->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                        $subject->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                        $subject->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                        $subject->set('invoice_id', $redeemed_voucher['invoice_id']);
                        $subject->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                        $subject->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $subject->set('status', $redeemed_voucher['status']);
                        $subj = $subject->output();

                        $message = new Template($e['message']);
                        $message->set('contact_name', $redeemed_voucher['account']);
                        $message->set('business_name', $config['CompanyName']);
                        $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                        $message->set('client_login_url', U.'client/login');
                        $message->set('client_email', $redeemed_voucher['email']);
                        $message->set('voucher_category', $redeemed_voucher['category_name']);
                        $message->set('voucher_number', $redeemed_voucher['prefix'].$redeemed_voucher['serial_number']);
                        $message->set('date_activated',date($config['df'], strtotime($redeemed_voucher['date'])));
                        $message->set('date_expire', date($config['df'], strtotime($redeemed_voucher['expiry_date'])));
                        $message->set('invoice_url', U . 'client/iview/' . $redeemed_voucher['invoice_id'] . '/token_' . $redeemed_voucher['invoice_vtoken']);
                        $message->set('invoice_id', $redeemed_voucher['invoice_id']);
                        $message->set('invoice_due_date', date($config['df'], strtotime($redeemed_voucher['invoice_due_date'])));
                        $message->set('invoice_amount', number_format($redeemed_voucher['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                        $message->set('status', $redeemed_voucher['status']);
                        $message_o = $message->output();

                        Notify_Email::_send($redeemed_voucher['account'], $redeemed_voucher['email'], $subj, $message_o);
                        if($setting['admin_notification'] == '1'){
                            foreach ($admin_data as $admin){
                                Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                            }
                        }
                    }

                    echo "reload";
                }
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
            ->left_outer_join('sys_invoices', array('sys_invoices.id', '=', 'voucher_generated.invoice_id'))
            ->left_outer_join('voucher_category', array('voucher_category.id', '=', 'voucher_format.category_id'))
            ->select('voucher_country.country_name','country_name')
            ->select('voucher_category.category_name', 'category')
            ->select('voucher_format.voucher_img', 'voucher_img')
            ->select('sys_invoices.status', 'invoice_status')
            ->select_many('voucher_generated.*')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();

        if($voucher_info['redeem_status'] != 'Redeem'){
            r2(U.'voucher/client/myvoucher','e','This voucher is not redeemed');
        }else if($voucher_info['status'] != 'Active'){
            r2(U.'voucher/client/myvoucher','e','This voucher is not Active');
        }else if($voucher_info['invoice_status'] != 'Paid'){
            r2(U.'voucher/client/myvoucher','e','This voucher is not Paid');
        }


        // Mail Setting

        $setting_data = ORM::for_table('voucher_setting')->find_array();
        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $admin_data = ORM::for_table('sys_users')
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->where('email_notify', '1')
            ->find_array();


        $voucher_pages = ORM::for_table('voucher_pages')
            ->inner_join('voucher_generated',array('voucher_pages.voucher_format_id','=','g.voucher_format_id'),'g')
            ->select('voucher_pages.*')
            ->where_equal('g.id',$voucher_id)
            ->order_by_asc('voucher_pages.id')
            ->find_many();


        $page_status = array();
        $today = date('Y-m-d');
        $transaction_id = array();

        foreach($voucher_pages as $vp) {
            $page_status[$vp['id']] = 'Redeem';
            if($vp['void_days']){
                $now_date = date('Y-m-d');
                $date1 = date_create($now_date);
                $date2 = date_create($voucher_info['date']);
                $rest = date_diff($date1, $date2);
                $rest = intval($rest->format("%a"));

                if($vp['void_days'] <= $rest){
                    $page_status[$vp['id']] = 'void';
                }else {
                    $page_status[$vp['id']] = ($vp['void_days'] - $rest).' Day';
                }

            }

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
                ->where('voucher_id', $voucher_id)
                ->where('page_id', $vp['id'])
                ->find_one();

            if($redeem_page){
                if($redeem_page['status'] == 'Confirmed'){
                    $page_status[$vp['id']] = 'Confirmed';
                }elseif($redeem_page['status'] == 'Processing') {
                    $page_status[$vp['id']] = 'Processing';

                    if($setting['set_status_manually'] != '1' && $redeem_page['invoice_status'] == 'Paid'){
                        $redeem_page->status = 'Confirmed';
                        $redeem_page->save();
                        $page_status[$vp['id']] = 'Confirmed';


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
                            if($redeem_page['departure_date'] == '0000-00-00' || $redeem_page['departure_date'] == ''){
                                $subject->set('departure_date','-');
                            }else{
                                $subject->set('departure_date',date($config['df'], strtotime($redeem_page['departure_date'])));
                            }
                            if($redeem_page['return_date'] == '0000-00-00' || $redeem_page['return_date'] == ''){
                                $subject->set('return_date', '-');
                            }else{
                                $subject->set('return_date', date($config['df'], strtotime($redeem_page['return_date'])));
                            }
                            $subject->set('remark', $redeem_page['remark']);
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
                            if($redeem_page['departure_date'] == '0000-00-00' || $redeem_page['departure_date'] == ''){
                                $message->set('departure_date','-');
                            }else{
                                $message->set('departure_date',date($config['df'], strtotime($redeem_page['departure_date'])));
                            }
                            if($redeem_page['return_date'] == '0000-00-00' || $redeem_page['return_date'] == ''){
                                $message->set('return_date', '-');
                            }else{
                                $message->set('return_date', date($config['df'], strtotime($redeem_page['return_date'])));
                            }
                            $message->set('remark', $redeem_page['remark']);
                            $message->set('product_title', $redeem_page['product_name']);
                            $message->set('product_quantity', $redeem_page['product_quantity']);
                            $message->set('product_price', $redeem_page['product_price']);
                            $message->set('sub_product_title', $redeem_page['sub_product_name']);
                            $message->set('sub_product_quantity', $redeem_page['sub_product_quantity']);
                            $message->set('sub_product_price', $redeem_page['sub_product_price']);
                            $message_o = $message->output();

                            Notify_Email::_send($redeem_page['account'], $redeem_page['email'], $subj, $message_o);
                            if($setting['admin_notification'] == '1'){
                                foreach ($admin_data as $admin){
                                    Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                                }
                            }
                        }

                    }


                }elseif($redeem_page['status'] == 'Cancelled') {
                    $page_status[$vp['id']] = 'Cancelled';
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
            'voucher_info' => $voucher_info,
            'voucher_img' => $voucher_info['voucher_img'],
            'serial_number' => $voucher_info['prefix'].$voucher_info['serial_number'],
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
            ->select('voucher_generated.voucher_format_id')
            ->where_equal('voucher_generated.id',$voucher_id)
            ->find_one();

        $fs = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $voucher_info['voucher_format_id'])
            ->where('voucher_customfields.page_id', $page_id)
            ->order_by_asc('id')->find_many();


        $voucher_edit_enable = ORM::for_table('voucher_setting')->where_equal('setting','cant_edit_submit_voucher')->select('value')->find_one();
        $require_agree = ORM::for_table('voucher_setting')->where_equal('setting', 'require_agree')->select('value')->find_one();

        $setting = array(
            'voucher_edit_enable' => $voucher_edit_enable['value'],
            'require_agree' => $require_agree['value']
        );


        $ui->assign('_application_menu', 'My Voucher');
        $ui->assign('_st', $voucher_info['category'].' '.$voucher_info['prefix'].$voucher_info['serial_number']);
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

        $setting_data = ORM::for_table('voucher_setting')->find_array();
        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $admin_data = ORM::for_table('sys_users')
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->where('email_notify', '1')
            ->find_array();


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

        if($tid == '') {
            $tmp = ORM::for_table('voucher_page_transaction')->where('voucher_id', $voucher_id)->where('page_id', $page_id)->find_one();
            if($tmp){
                $msg .= 'This page redeemed already';
            }
        }


        if($msg == ''){
            if($tid == ''){
                $d = ORM::for_table('voucher_page_transaction')->create();
                _msglog('s','Redeemed Successfully');
            }else{
                $d = ORM::for_table('voucher_page_transaction')->find_one($tid);
                _msglog('s','Page Redeem updated Successfully');
            }

//            if($page_setting['date_range'] == 1){
//                $status = 'Processing';
//            }else {
//                $status = 'Confirm';
//            }
            $status = 'Processing';

            $invoice_url = "";

            if($invoice_id){
                if($sub_product_req == 1){
                    $product_amount = round((float)$product_price*$product_quantity + (float)$sub_product_price*$sub_product_quantity,2);
                }else {
                    $product_amount = round((float)$product_price*$product_quantity,2);
                }
                $product = ($sub_product_name != '' && $sub_product_req == 1)?$page_title.' ('.$product_name.' + '.$sub_product_name.')':$page_title.' ('.$product_name.')';

                $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);

//                if($invoice_data['status'] != 'Paid'){
                    $invoice_data->total = $product_amount;
                    $invoice_data->subtotal = $product_amount;
                    $invoice_data->save();

                    $invoice_item = ORM::for_table('sys_invoiceitems')->where('sys_invoiceitems.invoiceid', $invoice_data['id'])->find_one();
                    $invoice_item->amount = $product_amount;
                    $invoice_item->total = $product_amount;
                    $invoice_item->description = $product;
                    $invoice_item->save();


//                }else{
//                    _msglog('r','This page paid already');
//                    echo "page_list";
//                    exit;
//                }

            }

            if($page_setting['payment_req'] == 1 && $tid == ''){
                if($sub_product_req == 1){
                    $product_amount = round((float)$product_price*$product_quantity + (float)$sub_product_price*$sub_product_quantity,2);
                }else {
                    $product_amount = round((float)$product_price*$product_quantity,2);
                }
                $product = ($sub_product_name != '' && $sub_product_req == 1)?$page_title.' ('.$product_name.' + '.$sub_product_name.')':$page_title.' ('.$product_name.')';
                $invoice = Invoice::forSingleItem($account_id, $product, $product_amount);
                $invoice_id = $invoice['id'];
                $invoice_vtoken = $invoice['vtoken'];
                $invoice_url = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';


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

            $voucher_info = ORM::for_table('voucher_generated')
                ->find_one($voucher_id);

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
                if($pd['departure_date'] == '0000-00-00' || $pd['departure_date'] == ''){
                    $message->set('departure_date','-');
                }else{
                    $message->set('departure_date',date($config['df'], strtotime($pd['departure_date'])));
                }
                if($pd['return_date'] == '0000-00-00' || $pd['return_date'] == ''){
                    $message->set('return_date', '-');
                }else{
                    $message->set('return_date', date($config['df'], strtotime($pd['return_date'])));
                }
                $message->set('remark', $pd['remark']);
                $message->set('product_title', $pd['product_name']);
                $message->set('product_quantity', $pd['product_quantity']);
                $message->set('product_price', $pd['product_price']);
                $message->set('sub_product_title', $pd['sub_product_name']);
                $message->set('sub_product_quantity', $pd['sub_product_quantity']);
                $message->set('sub_product_price', $pd['sub_product_price']);
                $message_o = $message->output();

                Notify_Email::_send($pd['account'], $pd['email'], $subj, $message_o);
                if($setting['admin_notification'] == '1'){
                    foreach ($admin_data as $admin){
                        Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                    }
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
        $view_type = route(4);

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        $transaction_data = ORM::for_table('voucher_page_transaction')
            ->left_outer_join('voucher_generated', array('voucher_generated.id', '=', 'voucher_page_transaction.voucher_id'))
            ->select_many('voucher_page_transaction.*', 'voucher_generated.voucher_format_id')
            ->find_one($transaction_id);

        // Custom fields


        $fs = ORM::for_table('voucher_customfields')
            ->where('voucher_customfields.voucher_id', $transaction_data['voucher_format_id'])
            ->where('voucher_customfields.page_id', $transaction_data['page_id'])
            ->order_by_asc('id')->find_many();

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

//        $fs = ORM::for_table('voucher_customfields')->order_by_asc('id')->find_many();



        view('wrapper_modal',[
            '_include' => 'client_modal_editredeem',
            'baseUrl' => $baseUrl,
            't_data' => $transaction_data,
            'page_setting' => $page_setting,
            't_id' => $transaction_id,
            'view_type' => $view_type

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


        view('wrapper_modal',[
            '_include' => 'client_modal_cert',
            'baseUrl' => $baseUrl,
            'voucher' => $voucher
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

        // Settings

        $setting_data = ORM::for_table('voucher_setting')->find_array();
        $setting = array();

        foreach($setting_data as $s){
            $setting[$s['setting']] = $s['value'];
        }

        $admin_data = ORM::for_table('sys_users')
            ->where('user_type', 'Admin')
            ->where('status', 'Active')
            ->where('email_notify', '1')
            ->find_array();


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


        // Generate vouchers

        $voucher_numbers = explode(',',$serial_numbers);

        for($i=1;$i<=$total_voucher;$i++){

            $d = ORM::for_table('voucher_generated')->create();
            _msglog('s','Voucher Generated Successfully');
            $serial_number = $voucher_numbers[$i-1];
            $voucher_pdf = $serial_number.'.pdf';


            // Create Invoice

            $invoice_id = null;
            $amount = $voucher_info['sales_price'];
            $item_name = $voucher_info['category_name'].' '.$voucher_info['prefix'].$serial_number;
            $invoice = Invoice::forSingleItem($contact_id, $item_name, $amount);
            $invoice_id = $invoice['id'];
            $invoice_vtoken = $invoice['vtoken'];

            $invoice_data = ORM::for_table('sys_invoices')->find_one($invoice_id);
            $invoice_data->title = $item_name;
            $invoice_data->save();


            // insert into database

            $d->voucher_format_id = $id;
            $d->contact_id = $contact_id;
            $d->serial_number = $serial_number;
            $d->create_invoice = 1;
            $d->date = $date;
            $d->expiry_date = $expiry_date;
            $d->prefix = $prefix;
            $d->description = $description;
            $d->invoice_id = $invoice_id;
            $d->voucher_pdf = $voucher_pdf;
            $d->status = 'Processing';
            $d->redeem_status = 'Redeem';

            $d->save();

            $gid = $d->id();

            $dp = ORM::for_table('voucher_generated')
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

            if($setting['voucher_status_processing']){
                $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_processing']);
            }else{
                $e = null;
            }

            // Send Mail

            if($e){
                $subject = new Template($e['subject']);
                $subject->set('contact_name', $dp['account']);
                $subject->set('business_name', $config['CompanyName']);
                $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
                $subject->set('client_login_url', U.'client/login');
                $subject->set('client_email', $dp['email']);
                $subject->set('voucher_category', $dp['category']);
                $subject->set('voucher_number', $dp['prefix'].$dp['serial_number']);
                $subject->set('date_activated',date($config['df'], strtotime($dp['date'])));
                $subject->set('date_expire', date($config['df'], strtotime($dp['expiry_date'])));
                $subject->set('invoice_url', U . 'client/iview/' . $dp['invoice_id'] . '/token_' . $dp['invoice_vtoken']);
                $subject->set('invoice_id', $dp['invoice_id']);
                $subject->set('invoice_due_date', date($config['df'], strtotime($dp['invoice_due_date'])));
                $subject->set('invoice_amount', number_format($dp['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                $subject->set('status', $dp['status']);
                $subj = $subject->output();

                $message = new Template($e['message']);
                $message->set('contact_name', $dp['account']);
                $message->set('business_name', $config['CompanyName']);
                $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
                $message->set('client_login_url', U.'client/login');
                $message->set('client_email', $dp['email']);
                $message->set('voucher_category', $dp['category_name']);
                $message->set('voucher_number', $dp['prefix'].$dp['serial_number']);
                $message->set('date_activated',date($config['df'], strtotime($dp['date'])));
                $message->set('date_expire', date($config['df'], strtotime($dp['expiry_date'])));
                $message->set('invoice_url', U . 'client/iview/' . $dp['invoice_id'] . '/token_' . $dp['invoice_vtoken']);
                $message->set('invoice_id', $dp['invoice_id']);
                $message->set('invoice_due_date', date($config['df'], strtotime($dp['invoice_due_date'])));
                $message->set('invoice_amount', number_format($dp['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
                $message->set('status', $dp['status']);
                $message_o = $message->output();

                Notify_Email::_send($dp['account'], $dp['email'], $subj, $message_o);
                if($setting['admin_notification'] == '1'){
                    foreach ($admin_data as $admin){
                        Notify_Email::_send($admin['fullname'], $admin['username'], $subj, $message_o);
                    }
                }
            }

        }

//        r2(U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/','s');

        $str = U.'client/iview/'.$invoice_id.'/token_'.$invoice_vtoken.'/';
        echo $str;

        break;

    case 'clientvoucher':

        Event::trigger('voucher/client/client_voucher/');


        $ui->assign('_application_menu', 'Client Voucher');
        $ui->assign('_st', 'Client Voucher');
        $ui->assign('_title', $config['CompanyName']);
        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','dt/dt','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','dt/dt','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];

        view('client_wrapper',[
            '_include' => 'client_clientvoucher',
            'baseUrl' => $baseUrl,
            'user' => $c
        ]);

        break;

    case 'json_voucher_list':


        $baseUrl = APP_URL;
        $c = Contacts::details();
        $account_id = $c['id'];


        $columns = array();

        $columns[] = 'id';
        $columns[] = 'voucher_img';
        $columns[] = 'date';
        $columns[] = 'serial_number';
        $columns[] = 'contact_name';
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

        $d->where_equal('agent_id', $account_id);



        $serial_number = _post('serial_number');

        if($serial_number != ''){

            $d->where_like('serial_number',"%$serial_number%");

        }

        $contact_name = _post('contact');

        if($contact_name != ''){

            $d->where_like('contact.account',"%$contact_name%");

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

            $img = '<img src="' . APP_URL . '/storage/system/' . $xs['img'] . '" width="42px" alt="">';

            if($xs['contact_name'] == ''){
                $xs['contact_name'] = '-';
            }


            $page_count = ORM::for_table('voucher_pages')->where('voucher_format_id', $xs['voucher_format_id'])->count();
            $redeem_count = ORM::for_table('voucher_page_transaction')->where('voucher_id', $xs['id'])->count();
            $voucher_invoice = ORM::for_table('sys_invoices')->where_equal('id', $xs['invoice_id'])->find_one();


            switch ($xs['status']){
                case 'Processing':
                    $voucher_status = "<a href='#' class='btn btn-xs square-deactive' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Processing'>Processing</a>";
                    if($xs['date'] == '0000-00-00'){
                        $xs['date'] = '-';
                        $xs['expiry_date'] = '-';
                    }
                    break;
                case 'Active':
                    $voucher_status = "<a href='#' class='btn btn-xs square-active' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Active'>Active</a>";
                    if($xs['date'] == '0000-00-00'){
                        $xs['date'] = '-';
                        $xs['expiry_date'] = '-';
                    }

                    break;

                case 'Expired':
                    $voucher_status = "<a href='#' class='btn btn-xs square-expire' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Expired'>Expired</a>";
                    if($xs['date'] == '0000-00-00'){
                        $xs['date'] = '-';
                        $xs['expiry_date'] = '-';
                    }
                    break;

                case 'Cancelled':
                    $voucher_status = "<a href='#' class='btn btn-xs square-deactive' id='".$xs['id']."' data-toggle='tooltip' data-placement='top' title='Cancelled'>Cancelled</a>";
                    $xs['date'] = '-';
                    $xs['expiry_date'] = '-';

                    break;
            }


            $records["data"][] = array(
                0 => $xs['id'],
                1 => $img,
                2 => $xs['date'],
                3 => $xs['prefix'].$xs['serial_number'],
                4 => $xs['contact_name'],
                5 => $xs['expiry_date'],
                6 => $page_count. '<span style="color:#CAA931">('.$redeem_count.')</span>',
                7 => htmlentities($xs['description']),
                8 => $voucher_status,
                9 => '
                <a href="' . U . 'voucher/client/download_generated_voucher/' . $xs['id'] . '" class="btn btn-primary btn-xs cview" style="background-color: #92278F; border-color:#92278F" id="vid' . $xs['id'] . '"><i class="fa fa-file-pdf-o"></i> </a>
                ',

                "DT_RowId" => 'dtr_' . $xs['id']


            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        api_response($records);

        break;

    case 'download_generated_voucher':

        $id = route(3);

        $voucher_data = ORM::for_table('voucher_generated')
            ->left_outer_join('voucher_format', array('voucher_format.id', '=', 'voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_template', array('voucher_template.id', '=', 'voucher_format.template_id'))
            ->select_many('voucher_generated.*', 'voucher_template.cover_img', 'voucher_template.voucher_template', 'voucher_template.voucher_pgnum')
            ->find_one($id);

        $posx = ORM::for_table('voucher_setting')->where('setting', 'pos_x')->find_one();
        $posy = ORM::for_table('voucher_setting')->where('setting', 'pos_y')->find_one();
        $font_color = ORM::for_table('voucher_setting')->where('setting', 'font_color')->find_one();
        $font_size = ORM::for_table('voucher_setting')->where('setting', 'font_size')->find_one();

        $color_str = str_split(str_replace('#','',$font_color['value']),2);
        $r = hexdec($color_str[0]);
        $g = hexdec($color_str[1]);
        $b = hexdec($color_str[2]);

        $voucher_numbers = explode(',', $voucher_data['voucher_pgnum']);
        $template_file = 'storage/system/'.$voucher_data['voucher_template'];
        $newfile = $voucher_data['serial_number'].'.pdf';

        $pdf = new \Mpdf\Mpdf(['format' => [250, 148]]);
        $pdf->SetImportUse();
        $pagecount = $pdf->SetSourceFile($template_file);

        for ($i=1; $i<=$pagecount; $i++) {
            $import_page = $pdf->ImportPage($i);
            $pdf->SetPageTemplate($import_page);

            foreach ($voucher_numbers as $v){
                if($i == (int)$v+1){
                    $pdf->SetTextColor($r,$g,$b);
                    $pdf->SetFont('Arial','B',$font_size['value']);
                    $pdf->SetXY((int)$posx['value'],(int)$posy['value']);
                    $pdf->cell(0,0,$voucher_data['prefix'].$voucher_data['serial_number']);
                }
            }

            /*
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
            */

            $pdf->AddPage();

        }

        $pdf->Output($newfile, 'D');

        r2(U . 'voucher/app/generated_voucher_list/'.$voucher_data['voucher_format_id'], 's', $_L['Voucher Downloaded Successfully']);


        break;

    default:
        echo 'action not defined';
        break;

 }