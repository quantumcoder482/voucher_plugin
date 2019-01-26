<?php
$table = new Schema('app_notes'); # app_notes is the table name which will be created automatically when installing
$table->add('title','varchar', 200);
$table->add('contents','text');
$table->add('created_at','TIMESTAMP','','null');
$table->add('updated_at','TIMESTAMP','','null');
$table->save();


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
$table->add('createdon', 'TIMESTAMP','', 'CURRENT_TIMESTAMP');
$table->save();

/*
 *  voucher setting
 */
$table = new Schema('voucher_setting');
$table->add('setting', 'text');
$table->add('value', 'text');
$data = "(1, 'user_require_make_payment', '1'),
(2, 'activation_fee', '15.00'),
(3, 'agreement_text', '<p><strong>DECLARATION FOR VOUCHER HOLDERS <br></strong></p><ol><li>I declare that I have read ALL the terms and conditions for every benefit in this voucher and I fully understand each term and condition and fully agree to them. </li><li>I declare that I if I want to book a vacation I must book the vacation 12 weeks in advance. </li><li>I declare that I must activate the voucher on receipt or the voucher will be completely invalid. </li><li>I declare that I must give at least three alternative dates for my desired travel. My chosen dates must be 28 days apart otherwise I will not be able to make any booking enquiry or get any vacation confirmation. </li><li>I declare that I must book my vacation within 12 weeks of activating the voucher, and that I can travel anytime in the 12 months the voucher is valid, subject to availability, including but not limited to seasonal limitations and optional upgrade surcharge fees. </li><li>I declare to pay all booking, administration and Vacation Security Deposit fees before I can receive any vacation confirmation. </li><li>I declare that I must send my original booking form with booking fees (if required), a certified true copy of my personal identification. This can be your Passport/Identification Card or Driving License. I must submit my credit card details (where applicable) to secure the vacation booking and send this original signed declaration from before I can make any booking. </li><li>I declare that I agree to receive my vacation confirmation sent to my given email address and do not require a telephone confirmation. </li><li>For any group bookings for 3 (three) travelling parties and over there will be a surcharge of $39 per night per room in the confirmed vacation accommodation booking above and beyond any peak season and room upgrade surcharges. </li><li>I declare that if I want to make a vacation booking within 12 weeks and additional surcharge will be levied. The additional surcharge will be $29 per person (min two persons) for the local vacation and $75 per person (min two persons) for the , 'int', ernational vacation. The minimum time a vacation can be booked in advance is 28 days before departure subject to availability. </li><li>I have read, understand and fully agree with the vacation cancellation policy. </li><li>A $5 Rich List Club activation fee will apply to secure new and additional benefits. </li></ol><p><strong>DECLARATION FOR VOUCHER HOLDERS </strong></p><p>When booking accommodation on any NAKED Rewards brand offers, you (\"the customer\"), AGREE to the following terms and conditions:</p><ol><li>The confirmation of bookings made requires payment of booking fees (where applicable) to be made at the time of booking together with submission of your credit card details for the Vacation Security Deposit required as per the promotional offers (where applicable). </li><li>All room tariffs/fees are calculated on the selected room rate plus charges for extra persons (as indicated by the customer). In the event, the number of persons checking in differs from the booking form submitted by you then the resort/hotel reserves the right to either <br>(a) refuse total admission for the extra people or <br>(b) charge you the full rack rate for the extra person(s). </li><li>In the event of any cancellation of your vacation accommodation; <br>a) The booking fees are totally non-refundable once confirmation has been advised; <br>b) For cancellations within 180 - 61 days prior to check-in, 50% of the deposit shall be refunded; <br>c) For cancellations within 60 - 31 days prior to check-in, 25% of the deposit shall be refunded; <br>d) For cancellations within 30 days of check-in, the full deposit shall be forfeited. </li><li>The Customer shall be required to provide a valid credit card at the resort/hotel upon check-in to cover any additional charges and will be required to provide their passport and photographic identification.</li></ol><p><strong>DISCLAIMER \r\n</strong></p><p>The contents of any workshop, seminar, or masterclass, and all related documents (collectively, the \"Content\") are provided for informational purposes only and do not constitute legal, financial, technical, medical, or personal advice, or professional advice of any other kind whatsoever. The New Rich List Inc. (\"TNRL\") makes no representations, warranties, or guarantees, whether express or implied, as to the accuracy, completeness, timeliness or reliability of the Content, and TNRL accepts neither any responsibility nor liability of any kind, under any circumstances, for any loss or damage whatsoever suffered as a result of or arising from any omission, inadequacy, or inaccuracy in the Content or otherwise arising in connection with the use of, or inability to use, all or any part of the Content. TNRL has no obligation to update or correct the Content. You acknowledge and agree both: (A) that you are responsible for making your own independent judgments and decisions with respect to your use of the suggested or recommended strategies contained in the Content; and (B) not to attempt to hold TNRL liable for any such decisions, judgments, actions, or results, at any time, under any circumstances. </p><p>We have taken every effort to ensure we accurately represent these strategies and their potential to help you grow your business. However, we do not , 'int', end this as an \"easy money making scheme\" and there is no guarantee that you will earn any money using the techniques in this program or these materials. Your level of success in attaining results is dependent upon a number of factors including your skill, knowledge, ability, dedication, personality, market, audience, business savvy, business focus, business goals, partners, luck, and financial situation. Because these factors differ according to individuals, we cannot guarantee your success, income level, or ability to earn revenue, nor can we state that any results suggested in these materials are typical. Any forward-looking or financial statements outlined here are simply illustrative and not promises for actual performance. These statements and the strategies offered in these materials are simply our opinion or experience. So again, as stipulated by law, no future guarantees can be made that you will achieve any results or income from our information and we offer no professional legal or financial advice. </p>'),
(4, 'require_agree', '1'),
(5, 'able_redeem_voucher_code', '1'),
(6, 'cant_edit_submit_voucher', '0'),
(7, 'alert_message', '<p>You haven\'t activate your account yet, kindly make (1)one time payment to activate your account. </p>'),
(8, 'show_alert_message', '1'),
(9, 'set_status_manually', '1'),
(10, 'voucher_status_processing', '0'),
(11, 'voucher_status_active', '0'),
(12, 'voucher_status_expired', '0'),
(13, 'voucher_status_cancelled', '0'),
(14, 'page_status_processing', '0'),
(15, 'page_status_confirmed', '0'),
(16, 'page_status_cancelled', '0'),
(17, 'pos_x', '135'),
(18, 'pos_y', '109'),
(19, 'font_size', '22'),
(20, 'font_color', '#f00505'),
(21, 'admin_notification', '1');";
$table->add_primary_data($data);
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
$table->add('redeem_date', 'datetime', 'null');
$table->save();


/*
 *  voucher transaction active
 */
$table = new Schema('voucher_trans_active');
$table->add('customer_id' , 'int', 20, 'null');
$table->add('activation_fee', 'decimal', '5,2' , 'null');
$table->add('invoice_id' , 'int', 20, 'null');
$table->add('vtoken' , 'varchar', 20, 'null');
$table->add('createdon', 'TIMESTAMP','', 'CURRENT_TIMESTAMP');
$table->save();