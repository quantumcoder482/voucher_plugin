<?php

$admin_voucher_sub_menus = [
  [
   'name' => 'Voucher Dashboard',
   'link' => U.'voucher/app/dashboard/'
  ],
  [
   'name' => 'Add/List Country',
   'link' => U.'voucher/app/add_list_country/'
  ],
  [
   'name' => 'Add Category',
   'link' => U.'voucher/app/add_category/'
  ],
  [
   'name' => 'Add PDF Template',
   'link' => U.'voucher/app/pdf_template/'
  ],
  [
   'name' => 'Add Voucher',
   'link' => U.'voucher/app/add_voucher/'
  ],
  [
   'name' => 'List Voucher',
   'link' => U.'voucher/app/list_voucher/'
  ],
  [
   'name' => 'Voucher Transaction',
   'link' => U.'voucher/app/voucher_transaction/'
  ],
  [
   'name' => 'Page Transaction',
   'link' => U.'voucher/app/voucher_page_transaction/'
  ],
  [
   'name' => 'Voucher Setting',
   'link' => U.'voucher/app/voucher_setting/'
  ]

];

$client_voucher_sub_menus = [
 [
  'name' => 'My Voucher',
  'link' => U.'voucher/client/myvoucher'
 ],
 [
  'name' => 'Voucher Shop',
  'link' => U.'voucher/client/vouchershop'
 ],
 [
  'name' => 'Client Voucher',
  'link' => U.'voucher/client/clientvoucher'
 ]

];


add_menu_admin('Voucher',U.'voucher/app','voucher','fa fa-credit-card',2,$admin_voucher_sub_menus);

add_menu_client('Voucher',U.'voucher/client','voucher','fa fa-credit-card',2, $client_voucher_sub_menus);
