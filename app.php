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


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        view('app_wrapper',[
            '_include' => 'dashboard',
            'redeem_vouchers' => $redeem_vouchers,
            'redeem_voucher_pages' => $redeem_voucher_pages,
            'recent_vouchers' =>$recent_vouchers,
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
        $category = _post('category');
        $description = _post('description');
        $flag_img = _post('flag_img');


        $msg = '';


        if(!$country_name){
            $msg .= 'Country Name is required <br>';
        }
        if(!$prefix){
            $msg .= 'Prefix is required <br>';
        }
        if(!$category){
            $msg .= 'Category is requried <br>';
        }
        if(!$flag_img){
            $msg .= 'Flag Image is requeried <br>';
        }

        if($id == ''){
            $country_data = ORM::for_table('voucher_country')
                ->where('country_name', $country_name)
                ->where('prefix', $prefix)
                ->where('category', $category)
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
            $d->category = $category;
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
            $val['category']=$country->category;
            $val['description']=$country->description;
            $val['flag_img']=$country->flag_img;

        }else{
            $val['id']="";
            $val['country_name']="";
            $val['prefix']="";
            $val['category']="";
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
            'category' => $d->category,
            'description' => $d->description,
            'flag_img' => $d->flag_img
        ];

        header('Content-Type: application/json');
        echo json_encode($country_info);

        break;

/*
 *   Add Voucher  (add_voucher.tpl, add_voucher.js)
 */


    case 'add_voucher':

        $countries = Countries::all($config['country']); // may add this $config['country_code']
        $country_list = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();

        $baseUrl = APP_URL;


        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
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
            'countries' => $countries,
            'country_list' => $country_list

        ]);

        break;

    case 'post_voucher':

        $id = _post('vid');
        $country_id = _post('country');
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


        if($msg == ''){

            if($id == ''){
                _msglog('s','Voucher Added Successfully');
                $d = ORM::for_table('voucher_format')->create();
            } else {
                _msglog('s','Voucher Updated Successfully');
                $d = ORM::for_table('voucher_format')->find_one($id);
            }


            $d->country_id = $country_id;
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

        $countries = Countries::all($config['country']); // may add this $config['country_code']

        $vouchers = ORM::for_table('voucher_format')->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_country.category')
            ->join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
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
                ->where('status', 'Active')
                ->where('sys_invoices.status', 'Paid')
                ->count();
            $pages[$v['id']] = ORM::for_table('voucher_pages')->where('voucher_format_id', $v['id'])->count();
        }

        $view_type = 'default';
        $view_type = 'filter';
        $paginator = array();

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

        $paginator['contents'] = "";


        view('app_wrapper',[
            '_include' => 'list_voucher',
            'vouchers' => $vouchers,
            'generated_voucher' => $generated_voucher,
            'active_voucher' => $active_voucher,
            'pages' => $pages,
            'view_type' => $view_type,
            'baseUrl' => $baseUrl,
            'paginator' => $paginator
        ]);
        break;

    case 'modal_edit_voucher':

        $id = route(3);
        $baseUrl = APP_URL;


        $voucher = ORM::for_table('voucher_format')->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_country.category')
            ->join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->order_by_asc('voucher_format.id')
            ->find_one($id);


        view('wrapper_modal',[
           '_include' => 'modal_edit_voucher',
           'baseUrl' => $baseUrl,
           'voucher' => $voucher

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
        $voucher = ORM::for_table('voucher_format')->select_many('voucher_format.*','voucher_country.country_name', 'voucher_country.prefix', 'voucher_country.category')
            ->join('voucher_country',array('voucher_country.id','=','voucher_format.country_id'))
            ->order_by_asc('voucher_format.id')
            ->find_one($id);
        $baseUrl = APP_URL;


        $customers = ORM::for_table('crm_accounts')->where_in('type',array('Customer','Customer,Supplier'))->order_by_asc('account')->find_many();
        $suppliers = ORM::for_table('crm_accounts')->where_in('type',array('Supplier','Customer,Supplier'))->order_by_asc('account')->find_many();

        // default setting

        $create_invoice = 'create';
        $add_payment = 'add_payment';


        view('wrapper_modal',[
           '_include' => 'modal_generate_voucher',
            'customers' => $customers,
            'suppliers' => $suppliers,
            'voucher' => $voucher,
            'baseUrl' => $baseUrl,
            'create_invoice' => $create_invoice,
            'add_payment' => $add_payment
        ]);
        break;

    case 'post_generate_voucher':

        $id = _post('vid');
        $contact_id = _post('contact_id');
        $partner_id = _post('partner_id');
        $prefix = _post('prefix');
        $serial_numbers = _post('serial_number');
        $serial_pgnum = _post('page_number');
        $total_voucher = _post('total_voucher');
        $date = _post('date');
        $create_invoice = _post('create_invoice');
        $add_payment = _post('add_payment');
        $description = _post('description');
        $voucher_template = _post('voucher_template');

        $msg = '';

        if(!$contact_id){
            $msg .= 'Contact is required. <br>';
        }
        if(!$serial_numbers){
            $msg .= 'Serial Number is required. <br>';
        }
        if(!$serial_pgnum){
            $msg .= 'Page Number is required. <br>';
        }
        if(!$date){
            $msg .= 'Date is required. <br>';
        }
        if(!$voucher_template){
            $msg .= 'Pdf Template is required. <br>';
        }

        if($msg == ''){

            if($total_voucher){

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


                }

                $voucher_numbers = explode(',',$serial_numbers);

                for($i=1;$i<=$total_voucher;$i++){

                    $d = ORM::for_table('voucher_generated')->create();

                    $serial_number = $voucher_numbers[$i-1];

                    // voucher pdf create

                    $template_file = 'apps/voucher/public/template/'.$voucher_template;
                    $newfile = 'apps/voucher/public/vouchers/'.$serial_number.'.pdf';
                    $voucher_pdf = $serial_number.'.pdf';
                    if(!copy($template_file,$newfile))
                    {
                        echo "failed to copy $file";
                        break;
                    } else {
                      $voucher_pdf = $serial_number.'.pdf';
                    }


                    // insert into database

                    $d->voucher_format_id = $id;
                    $d->contact_id = $contact_id;
                    $d->partner_id = $partner_id;
                    $d->serial_number = $serial_number;
                    $d->serial_pgnum = $serial_pgnum;
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
                    $d->prefix = $prefix;
                    $d->description = $description;
                    $d->invoice_id = $invoice_id;
                    $d->voucher_template = $voucher_template;
                    $d->voucher_pdf = $voucher_pdf;

                    $d->save();

                }
                _msglog('s','Voucher Generated Successfully');
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


//        $ui->assign('companies',db_find_array('sys_companies',array('id','company_name')));

        $ui->assign('xheader',Asset::css(array('popover/popover','select/select.min','s2/css/select2.min','dt/dt','modal', 'dp/dist/datepicker.min')));
        $ui->assign('xfooter',Asset::js(array('popover/popover','js/redirect','select/select.min','s2/js/select2.min','s2/js/i18n/'.lan(),'dt/dt','modal', 'dp/dist/datepicker.min')));
        $ui->assign('jsvar', '
        _L[\'are_you_sure\'] = \''.$_L['are_you_sure'].'\';
        ');


        view('app_wrapper',[
            '_include' => 'generated_voucher_list',
            'vid' => $vid
//            'vouchers' => $vouchers,
//            'view_type' => $view_type,
//            'baseUrl' => $baseUrl,
//            'paginator' => $paginator
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
        $columns[] = 'partner_name';
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
            ->left_outer_join('crm_accounts',array('voucher_generated.partner_id','=','partner.id'), 'partner')
            ->left_outer_join('voucher_format',array('voucher_format.id','=','voucher_generated.voucher_format_id'));

        $d->select('voucher_generated.id','id');
        $d->select('voucher_generated.date', 'date');
        $d->select('voucher_generated.prefix','prefix');
        $d->select('voucher_generated.serial_number', 'serial_number');
        $d->select('voucher_generated.description', 'description');
        $d->select('voucher_generated.voucher_format_id', 'voucher_format_id');
//        $d->select('voucher_generated.status', 'status');
        $d->select('voucher_format.voucher_img', 'img');
        $d->select('voucher_format.billing_cycle', 'billing_cycle');
        $d->select('voucher_format.expiry_day', 'expiry_day');
        $d->select('contact.account', 'contact_name');
        $d->select('partner.account', 'partner_name');

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

        $partner_name = _post('partner');

        if($partner_name != ''){

            $d->where_like('partner.account',"%$partner_name%");

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

            switch ($xs['billing_cycle']){
                case 'annual':
                    $interval = new DateInterval('P1Y');
                    $expiry_date = date_create($xs['date'])->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');

                    break;
                case 'monthly':
                    $interval = new DateInterval('P1M');
                    $expiry_date = date_create($xs['date'])->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');
                    break;
            }


            if($xs['partner_name'] == ''){
                $xs['partner_name']  =  '-';
            }
            if($xs['contact_name'] == ''){
                $xs['contact_name'] = '-';
            }



            $records["data"][] = array(
                //  0 => $xs['id'],
                0 => $xs['id'],
                1 => $img,
                2 => $xs['date'],
                3 => htmlentities($xs['prefix']),
                4 => htmlentities($xs['serial_number']),
                5 => htmlentities($xs['contact_name']),
                6 => htmlentities($xs['partner_name']),
                7 => $expiry_date,
                8 => 'redeem',
                9 => htmlentities($xs['description']),
                10 => htmlentities('status'),
                11 => '
                <a href="' . U . 'voucher/app/download_generated_voucher/' . $xs['id'] . '" class="btn btn-primary btn-xs cview" id="vid' . $xs['id'] . '"><i class="fa fa-download"></i> </a>
                <a href="' . U . 'voucher/app/edit_generated_voucher/' . $xs['id'] . '" class="btn btn-warning btn-xs cedit" id="eid' . $xs['id'] . '"><i class="glyphicon glyphicon-pencil"></i> </a>
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

        $voucher_data = ORM::for_table('voucher_generated')->find_one($id);

        $template_file = 'apps/voucher/public/template/'.$voucher_data['voucher_template'];
        $newfile = $voucher_data['serial_number'].'.pdf';

        $pdf = new \Mpdf\Mpdf(['format' => [250, 148]]);
        $pdf->SetImportUse();
        $pagecount = $pdf->SetSourceFile($template_file);

        for ($i=1; $i<=$pagecount; $i++) {
            $import_page = $pdf->ImportPage($i);
            $pdf->SetPageTemplate($import_page);

            if($i == $voucher_data['serial_pgnum']+1 ){
                $pdf->SetFont('Arial','B',18);
                $pdf->SetXY(112,74);
                $pdf->cell(0,0,$voucher_data['serial_number']);
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

        $customers = ORM::for_table('crm_accounts')->order_by_asc('id')->find_array();
        $countries = ORM::for_table('voucher_country')->order_by_asc('country_name')->find_array();


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
            'customers' => $customers
        ]);
        break;

    case 'tr_list':

        //  sleep(5);

        $columns = array();
        $columns[] = 'id';
        $columns[] = 'date';
        $columns[] = 'customer';
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
            ->left_outer_join('crm_accounts',array('voucher_generated.contact_id','=','contact.id'),'contact')
            ->left_outer_join('voucher_format',array('voucher_format.id','=','voucher_generated.voucher_format_id'))
            ->left_outer_join('voucher_country', array('voucher_country.id', '=', 'voucher_format.country_id'));

        $d->select('voucher_generated.id','id');
        $d->select('voucher_generated.date', 'date');
        $d->select('voucher_generated.serial_number', 'serialnumber');
        $d->select('voucher_generated.contact_id', 'customer_id');
        $d->select('voucher_format.billing_cycle', 'billing_cycle');
        $d->select('voucher_format.expiry_day', 'expiry_day');
        $d->select('voucher_format.country_id', 'country_id');
        $d->select('voucher_country.country_name', 'country_name');
        $d->select('voucher_country.category', 'category');
        $d->select('contact.account', 'customer');


        $customer_id = _post('customer');
        if($customer_id != ''){
            $d->where_equal('voucher_generated.contact_id', $customer_id);
        }

        $category = _post('category');
        if ($category != '') {
            $d->where_equal('voucher_country.category', $category);
        }

        $country_id = _post('country');
        if($country_id != ''){
            $d->where_equal('voucher_format.country_id', $country_id);
        }

        $status = _post('status');
        if($status != ''){
            switch ($status){

                case 'Active':

                    break;
                case 'Inactive':

                    break;
                case 'Expired':

                    break;
            }
        }

        $reportrange = _post('reportrange');
        if ($reportrange != '') {
            $reportrange = explode('-', $reportrange);
            $from_date = trim($reportrange[0]);
            $to_date = trim($reportrange[1]);
            $d->where_gte('date', $from_date);
            $d->where_lte('date', $to_date);
        }


        $filter_customer = _post('filter_customer');
        if($filter_customer != ''){
            $d->where_like('contact.account',"%$filter_customer%");
        }

        $filter_category = _post('filter_category');
        if($filter_category != ''){
            $d->where_like('voucher_country.category',"%$filter_category%");
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

        foreach($x as $xs) {

            switch ($xs['billing_cycle']){
                case 'annual':
                    $interval = new DateInterval('P1Y');
                    $expiry_date = date_create($xs['date'])->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');

                    break;
                case 'monthly':
                    $interval = new DateInterval('P1M');
                    $expiry_date = date_create($xs['date'])->add($interval);
                    $expiry_date = $expiry_date->format('Y-m-d');
                    break;
            }


            $records["data"][] = array(
                0 => $xs['id'],
                1 => $xs['date'],
                2 => htmlentities($xs['customer']),
                3 => htmlentities($xs['country_name']),
                4 => htmlentities($xs['category']),
                5 => $expiry_date,
                6 => htmlentities($xs['serialnumber']),
                7 => '',
                8 => '<a href="' . U . 'voucher/app/edit_generated_voucher/' . $xs['id'] . '" class="btn btn-primary btn-xs"><i class="fa fa-file-text-o"></i></a>',
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        api_response($records);

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



        view('app_wrapper',[
            '_include' => 'voucher_setting',
            'setting' => $setting
        ]);

        break;

    case 'post_setting':

        $agreement_text = _post('agreement_text');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','agreement_text')->find_one();
        $d->value = $agreement_text;
        $d->save();

        $product_fee = _post('product_fee');
        $d = ORM::for_table('voucher_setting')->where_equal('setting','product_fee')->find_one();
        $d->value = $product_fee;
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

        $id = route(3);
        $voucher_pages = ORM::for_table('voucher_pages')->where_equal('voucher_format_id',$id)->order_by_desc('id')->find_many();
        $voucher_format = ORM::for_table('voucher_format')->find_one($id);

        $baseUrl =APP_URL;

        $ui->assign('xheader', Asset::css(array('modal','dp/dist/datepicker.min','footable/css/footable.core.min','dropzone/dropzone','redactor/redactor','s2/css/select2.min')));
        $ui->assign('xfooter', Asset::js(array('modal','dp/dist/datepicker.min','footable/js/footable.all.min','dropzone/dropzone','redactor/redactor.min','numeric','s2/js/select2.min',
            's2/js/i18n/'.lan(),)));

        view('app_wrapper',[
            '_include' => 'list_voucher_pages',
            'voucher_pages' => $voucher_pages,
            'voucher_img' => $voucher_format['voucher_img'],
            'voucher_id' => $voucher_format['id'],
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
            $page_data = ORM::for_table('voucher_pages')->find_one($id);
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

    case 'post_page':

        $voucher_format_id = _post('vid');
        $page_id = _post('pid');
        $title = _post('title');
        $product_id = _post('product_id');
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
        if(!$product_id){
            $msg .= 'Product is required <br>';
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


        if($msg ==''){
            if($page_id == ''){
                _msglog('s','Voucher Added Successfully');
                $d = ORM::for_table('voucher_pages')->create();
            } else {
                _msglog('s','Voucher Update Successfully');
                $d = ORM::for_table('voucher_pages')->find_one($page_id);
            }

            $d->voucher_format_id = $voucher_format_id;
            $d->title = $title;
            $d->product_id = $product_id;
            $d->status_id = $status_id;
            $d->payment_req = $payment_req;
            $d->address = $address;
            $d->date_range = $date_range;
            $d->remark = $remark;
            $d->front_img = $front_img;
            $d->back_img = $back_img;

            $d->save();

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