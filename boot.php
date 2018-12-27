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
   'name' => 'Voucher Setting',
   'link' => U.'voucher/app/voucher_setting/'
  ]

];

add_menu_admin('Voucher',U.'voucher/app','voucher','fa fa-credit-card',2,$admin_voucher_sub_menus);

add_menu_client('My Voucher',U.'voucher/client/myvoucher','myvoucher','fa fa-credit-card',2);

add_menu_client('Voucher Shop',U.'voucher/client/vouchershop','vouchershop','fa fa-credit-card',2);