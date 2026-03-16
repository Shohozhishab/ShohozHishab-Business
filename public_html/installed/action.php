<?php
    session_start();


    $dbName = $_POST['dbName'];
    $dbUserName = $_POST['dbUserName'];
    $dbPassword = $_POST['dbPassword'];
    $dbHost = $_POST['dbHost'];

    $data = array(
        'dbHost' => $dbHost,
        'dbName' => $dbName,
        'dbUserName' => $dbUserName,
        'dbPassword' => $dbPassword,
    );
    $_SESSION['DB'] = $data;


    // Name of the file
    $filename = 'shohozhishab.sql';
    $mysql_host = $_SESSION['DB']['dbHost'];
    $mysql_username = $_SESSION['DB']['dbUserName'];
    $mysql_password = $_SESSION['DB']['dbPassword'];
    $mysql_database = $_SESSION['DB']['dbName'];

    // Connect to MySQL server
    $conn = mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . $conn->error());
    // Select database
    $conn->select_db($mysql_database) or die('Error selecting MySQL database: ' . $conn->error());

    // Temporary variable, used to store current query
    $templine = '';
    // Read in entire file
    $lines = file($filename);
    // Loop through each line
    foreach ($lines as $line)
    {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        // Add this line to the current segment
        $templine .= $line;
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';')
        {
            // Perform the query
            $conn->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $conn->error() . '<br /><br />');
            // Reset temp variable to empty
            $templine = '';
        }
    }
    $conn -> close();



    $conn = mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . $conn->error());
    $conn->select_db($mysql_database) or die('Error selecting MySQL database: ' . $conn->error());

    //super admin create(start)
    $super_admin_sql = "INSERT INTO admin (email, password, name, mobile, address, role_id, status, createdBy) VALUES ('imranertaza12@gmail.com', SHA1('12345678'), 'Syed Imran Ertaza','1924329315', 'Noapara, Abhaynagar, Jessore', '1', '1', '1')";
    $conn->query($super_admin_sql);
    //super admin create(end)


    // shop create (start)
    $shop_sql = "INSERT INTO shops (name, status) VALUES ('default','1')";
    $conn->query($shop_sql);
    $shopId = $conn->insert_id;
    // shop create (end)

    //create Vat in vat_register table (start)
    $vat_sql = "INSERT INTO vat_register (sch_id, is_default, name, vat_register_no) VALUES ($shopId,'1','Default Vat Name', 'BIN-0000-01')";
    $conn->query($vat_sql);
    //create Vat in vat_register table (end)

    // create default store in stores table(start)
    $store_sql = "INSERT INTO stores (sch_id, name, description, is_default) VALUES ($shopId, 'Default', 'Default Store', '1')";
    $conn->query($store_sql);
    // create default store in stores table(end)

    //roles insert in roles table(start)
    $permission = '{"Dashboard":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Pages":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Bank":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Customers":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Purchase":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Purchase_item":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Product_category":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Bank_deposit":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Settings":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Bank_withdraw":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Warranty_manage":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Suppliers":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Sales":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1","discount":"1","warranty":"1"},"Role":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Money_receipt":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Loan_provider":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_suppliers":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_nagodan":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_loan":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"User":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Stores":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_lc":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_bank":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Invoice":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1","discount":"1","warranty":"1"},"Expense_category":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Balance_report":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Lc_installment":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Lc":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Customer_type":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Products":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Transaction":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Chaque":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Stock_report":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Sales_report":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Purchase_report":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Daily_book":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Brand":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Employee":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_employee":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Return_sale":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Return_purchase":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Vat_register":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_vat":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Acquisition_due":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Owe_amount":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_sales":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_purchase":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Trial_balance":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Capital":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_capital":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_profit":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_stock":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_expense":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"HeadToheadTransfer":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_other_sales":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"},"Ledger_discount":{"mod_access":"1","create":"1","read":"1","update":"1","delete":"1"}}';
    $roles_sql = "INSERT INTO roles (sch_id, is_default, permission) VALUES ($shopId, '1','".$permission."')";
    $conn->query($roles_sql);
    $roleId = $conn->insert_id;
    //roles insert in roles table(end)

    //create users in users table (start)
    $users_sql = "INSERT INTO users (sch_id, role_id, is_default, name, email, password, status) VALUES ($shopId, $roleId, '1', 'admin', 'admin@gmail.com', SHA1('12345678'), '1')";
    $conn->query($users_sql);
    //create users in users table (end)

    //general settings insert in gen_settings table (start)
    $gen_settingsData = array(
        array('sch_id' => $shopId,'label' => 'barcode_img_size','value' => '100'
        ),array('sch_id' => $shopId,'label' => 'barcode_type','value' => 'C128A'
        ),array('sch_id' => $shopId,'label' => 'business_type','value' => 'Ownership business'
        ),array('sch_id' => $shopId,'label' => 'currency','value' => 'BDT'
        ),array('sch_id' => $shopId,'label' => 'currency_before_symbol','value' => '৳'
        ),array('sch_id' => $shopId,'label' => 'currency_after_symbol','value' => '/-'
        ),array('sch_id' => $shopId,'label' => 'running_year','value' => '2018-2019'
        ),array('sch_id' => $shopId,'label' => 'disable_frontend','value' => '0'
        ),array('sch_id' => $shopId,'label' => 'phone_code','value' => '880'
        ),array('sch_id' => $shopId,'label' => 'country','value' => 'Bangladesh'
        ),

    );

    foreach ($gen_settingsData as $val){
        $label = $val["label"];
        $value = $val["value"];
        $gen_settings_sql = "INSERT INTO gen_settings (sch_id, label, value) VALUES ('$shopId', '$label', '$value')";
        $conn->query($gen_settings_sql);
    }
    //general settings insert in gen_settings table (end)

    //general settings super insert in gen_settings table (start)
    $gen_settings_superData = array(
        array('label' => 'site_title','value' => 'Shohoz Hishab | Accounting management system'
        ),array('label' => 'loading_message','value' => 'Please wait until it is processing'
        ),

    );
    foreach ($gen_settings_superData as $gen){
        $label = $gen["label"];
        $value = $gen["value"];
        $gen_settings_super_sql = "INSERT INTO gen_settings_super (label, value) VALUES ('$label', '$value')";
        $conn->query($gen_settings_super_sql);
    }
    //general settings super insert in gen_settings table (end)



    //license create (start)
    $licKey = uniqid();
    $start_date = date("Y-m-d");
    $end_date = date('Y-m-d', strtotime('+1 year', strtotime($start_date)) );;
    $license_sql = "INSERT INTO license (sch_id, lic_key, start_date, end_date) VALUES ('$shopId', '$licKey', '$start_date', '$end_date')";
    $conn->query($license_sql);
    //license create (end)


    $conn -> close();


    $_SESSION['install'] = '1';

    print '<div style="margin-top: 12px" class="alert alert-success" id="message">Create Record Success</div>';
?>