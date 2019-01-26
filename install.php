<?php


/*
 *  voucher category table
 */

$table = new Schema('voucher_category');
$table->add('category_name', 'varchar', 20, 'null');
$table->add('description', 'text');
$table->save();

/*
 *  voucher country
 */
$table = new Schema('voucher_country');
$table->add('country_name', 'varchar', 20);
$table->add('prefix', 'varchar',20);
$table->add('description', 'text');
$table->add('flag_img','text');
$table->save();

/*
 *  voucher custome fields
 */
$table = new Schema('voucher_customfields');
$table->add('ctype', 'text');
$table->add('relid','int', 10,0);
$table->add('voucher_id', 'int', 20,'null');
$table->add('page_id', 'int', 20, 'null');
$table->add('fieldname', 'text');
$table->add('fieldtype', 'text');
$table->add('description','text');
$table->add('fieldoptions','text');
$table->add('regexpr', 'text');
$table->add('adminonly', 'text');
$table->add('required', 'text');
$table->add('showorder', 'text');
$table->add('showinvoice', 'text');
$table->add('sorder', 'int', 10, 0);
$table->save();

/*
 *  custom field values
 */

$table = new Schema('voucher_customfieldsvalues');
$table->add('fieldid', 'int', 10);
$table->add('relid', 'int', 10);
$table->add('fvalue','text');
$table->save();

/*
 *  voucher format
 */
$table = new Schema('voucher_format');
$table->add('country_id', 'int',20);
$table->add('category_id', 'int', 20, 'null');
$table->add('template_id', 'int', 20, 'null');
$table->add('created_date', 'date','', 'null');
$table->add('expiry_date', 'date', '', 'null');
$table->add('cost_price', 'decimal', '10,2', 'null');
$table->add('sales_price', 'decimal', '10,2', 'null');
$table->add('expiry_duration', 'int', 2, 'null');
$table->add('billing_cycle', 'varchar',10, 'null');
$table->add('expiry_day', 'int', 2, 'null');
$table->add('description', 'text');
$table->add('voucher_img', 'text');
$table->save();

/*
 *  voucher generated
 */

$table = new Schema('voucher_generated');
$table->add('voucher_format_id','int', 20);
$table->add('prefix', 'varchar', 5 , 'null');
$table->add('contact_id', 'int', 20 , 'null');
$table->add('agent_id', 'int', 20 , 'null');
$table->add('serial_number', 'varchar', 11);
$table->add('create_invoice', 'int', 1, 1);
$table->add('add_payment', 'int', 1, 1);
$table->add('date', 'date', '', 'null');
$table->add('expiry_date', 'date', '','null');
$table->add('invoice_id' , 'int', 20, 0);
$table->add('status', 'varchar', 20 , 'null');
$table->add('redeem_status', 'varchar', 20, 'null');
$table->add('voucher_pdf', 'text');
$table->add('description', 'text');
$table->save();


/*
 *  voucher pages
 */
$table = new Schema('voucher_pages');
$table->add('voucher_format_id' , 'int', 20);
$table->add('title' , 'varchar', 50);
$table->add('status' , 'int', 1, 1);
$table->add('product_id' , 'int', 20, 'null');
$table->add('product_quantity' , 'int', 10, 1);
$table->add('sub_product_id' , 'int', 20, 'null');
$table->add('sub_product_quantity' , 'int', 10, 1);
$table->add('payment_req' , 'int', 2,1);
$table->add('status_id' , 'varchar', 10, 'null');
$table->add('address' , 'int', 2, 1);
$table->add('date_range' , 'int', 2,  1);
$table->add('remark', 'text');
$table->add('void_days' , 'int', 2 , 'null');
$table->add('front_img', 'text');
$table->add('back_img', 'text');
$table->save();

/*
 *  voucher page transaction
 */
$table = new Schema('voucher_page_transaction');
$table->add('voucher_id' , 'int', 20 , 'null');
$table->add('page_id' , 'int', 20 , 'null');
$table->add('invoice_id' , 'int', 10 , 'null');
$table->add('page_title' , 'varchar', 50 , 'null');
$table->add('country_name' , 'varchar', 50 , 'null');
$table->add('category' , 'varchar', 50,'null');
$table->add('voucher_number' , 'varchar', 50 , 'null');
$table->add('product_name' , 'varchar', 50 , 'null');
$table->add('product_quantity' , 'int', 10 , 'null');
$table->add('product_price',  'decimal', '10,2' , 'null');
$table->add('sub_product_name' , 'varchar', 50 , 'null');
$table->add('sub_product_quantity' , 'int', 10 , 'null');
$table->add('sub_product_price', 'decimal', '10,2' , 'null');
$table->add('sub_product_req' , 'int', 2, 1);
$table->add('customer_name' , 'varchar', 50, 'null');
$table->add('customer_address', 'text');
$table->add('departure_date', 'date','', 'null');
$table->add('return_date', 'date','', 'null');
$table->add('total_days' , 'int', 11 , 'null');
$table->add('remark', 'text');
$table->add('status' , 'varchar', 50 , 'null');
$table->add('createdon', 'TIMESTAMP','', '');
$table->save();

/*
 *  voucher setting
 */
$table = new Schema('voucher_setting');
$table->add('setting', 'text');
$table->add('value', 'text');
$table->add_primary_data('(`setting`, `value`) VALUES 
(\'user_require_make_payment\', \'1\'),
(\'activation_fee\', \'15.00\'),
(\'agreement_text\', \' \'),
(\'require_agree\', \'1\'),
(\'able_redeem_voucher_code\', \'1\'),
(\'cant_edit_submit_voucher\', \'0\'),
(\'alert_message\', \' \'),
(\'show_alert_message\', \'1\'),
(\'set_status_manually\', \'1\'),
(\'voucher_status_processing\', \'0\'),
(\'voucher_status_active\', \'0\'),
(\'voucher_status_expired\', \'0\'),
(\'voucher_status_cancelled\', \'0\'),
(\'page_status_processing\', \'0\'),
(\'page_status_confirmed\', \'0\'),
(\'page_status_cancelled\', \'0\'),
(\'pos_x\', \'135\'),
(\'pos_y\', \'109\'),
(\'font_size\', \'22\'),
(\'font_color\', \'#f00505\'),
(\'admin_notification\', \'1\')
');
$table->save();


/*
 *  voucher template
 */
$table = new Schema('voucher_template');
$table->add('template_name' , 'varchar', 20, 'null');
$table->add('description', 'text');
$table->add('cover_img', 'text');
$table->add('voucher_template', 'text');
$table->add('voucher_pgnum', 'varchar', 50 , 'null');
$table->save();

/*
 *  voucher transaction
 */
$table = new Schema('voucher_transaction');
$table->add('voucher_id' , 'int', 20, 'null');
$table->add('status' , 'varchar', 50, 'null');
$table->add('redeem_date', 'date', '');
$table->save();


/*
 *  voucher transaction active
 */
$table = new Schema('voucher_trans_active');
$table->add('customer_id' , 'int', 20, 'null');
$table->add('activation_fee', 'decimal', '5,2' , 'null');
$table->add('invoice_id' , 'int', 20, 'null');
$table->add('vtoken' , 'varchar', 20, 'null');
$table->add('createdon', 'TIMESTAMP','', '');
$table->save();