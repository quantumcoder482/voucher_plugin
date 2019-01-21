<?php
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

        if($v['status'] == 'Processing' && $v['invoice_status'] == 'Paid' && $v['redeem_status'] == 'Redeem'){
            $d = ORM::for_table('voucher_generated')->find_one($v['id']);
            $d->status = 'Active';
            $d->save();
            if($setting['voucher_status_active']){
                $e = ORM::for_table('sys_email_templates')->find_one($setting['voucher_status_active']);
            }else{
                $e = null;
            }
        }
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
        $e = null;
        if($p['status'] == 'Processing' && $p['invoice_status'] == 'Paid'){
            $d = ORM::for_table('voucher_page_transaction')->find_one($p['id']);
            $d->status = 'Confirmed';
            $d->save();
            if($setting['page_status_confirmed']){
                $e = ORM::for_table('sys_email_templates')->find_one($setting['page_status_confirmed']);
            }else{
                $e = null;
            }
        }

        if($e){
            $subject = new Template($e['subject']);
            $subject->set('contact_name', $p['customer_name']);
            $subject->set('business_name', $config['CompanyName']);
            $subject->set('login_url', U.'login/');
//                                $subject->set('password_reset_link', U.'login/');
            $subject->set('client_login_url', U.'client/login');
            $subject->set('client_email', $p['email']);
            $subject->set('voucher_category', $p['category']);
            $subject->set('voucher_number', $p['voucher_number']);
            $subject->set('status', $p['voucher_status']);
            $subject->set('date_activated',date($config['df'], strtotime($p['date'])));
            $subject->set('date_expire', date($config['df'], strtotime($p['expiry_date'])));
            $subject->set('invoice_url', U . 'client/iview/' . $p['invoice_id'] . '/token_' . $p['invoice_vtoken']);
            $subject->set('invoice_id', $p['invoice_id']);
            $subject->set('invoice_due_date', date($config['df'], strtotime($p['invoice_due_date'])));
            $subject->set('invoice_amount', number_format($p['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
            $subject->set('page_title', $p['page_title']);
            $subject->set('product_title', $p['product_name']);
            $subject->set('product_quantity', $p['product_quantity']);
            $subject->set('product_price', $p['product_price']);
            $subject->set('sub_product_title', $p['sub_product_name']);
            $subject->set('sub_product_quantity', $p['sub_product_quantity']);
            $subject->set('sub_product_price', $p['sub_product_price']);
            $subj = $subject->output();

            $message = new Template($e['message']);
            $message->set('contact_name', $p['customer_name']);
            $message->set('business_name', $config['CompanyName']);
            $message->set('login_url', U.'login/');
//                                $message->set('password_reset_link', U.'login/');
            $message->set('client_login_url', U.'client/login');
            $message->set('client_email', $p['email']);
            $message->set('voucher_category', $p['category']);
            $message->set('voucher_number', $p['voucher_number']);
            $message->set('status', $p['voucher_status']);
            $message->set('date_activated',date($config['df'], strtotime($p['date'])));
            $message->set('date_expire', date($config['df'], strtotime($p['expiry_date'])));
            $message->set('invoice_url', U . 'client/iview/' . $p['invoice_id'] . '/token_' . $p['invoice_vtoken']);
            $message->set('invoice_id', $p['invoice_id']);
            $message->set('invoice_due_date', date($config['df'], strtotime($p['invoice_due_date'])));
            $message->set('invoice_amount', number_format($p['invoice_amount'],2, $config['dec_point'], $config['thousands_sep']));
            $message->set('page_title', $p['page_title']);
            $message->set('product_title', $p['product_name']);
            $message->set('product_quantity', $p['product_quantity']);
            $message->set('product_price', $p['product_price']);
            $message->set('sub_product_title', $p['sub_product_name']);
            $message->set('sub_product_quantity', $p['sub_product_quantity']);
            $message->set('sub_product_price', $p['sub_product_price']);
            $message_o = $message->output();

            Notify_Email::_send($p['account'], $p['email'], $subj, $message_o);
        }



    }

}

echo 'success';


?>