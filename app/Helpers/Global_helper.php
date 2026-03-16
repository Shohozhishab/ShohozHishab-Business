<?php

use App\Libraries\Permission;

/**
 * @description This function provides database connection
 * @return \CodeIgniter\Database\BaseConnection
 */
function DB()
{
    $db = \Config\Database::connect();
    return $db;
}

/**
 * @description This function provides cart
 * @return mixed
 */
function Cart(){
    $ca = \Config\Services::cart();
    return $ca;
}

/**
 * @description This function provides session
 * @return \CodeIgniter\Session\Session
 */
function newSession()
{
    $session = \Config\Services::session();
    return $session;
}


/**
 * @description This function provides get short content
 * @param string $long_text
 * @param int $show
 * @return string
 */
function getShortContent($long_text = '', $show = 100) {

    $filtered_text = strip_tags($long_text);
    if ($show < strlen($filtered_text)) {
        return substr($filtered_text, 0, $show) . '...';
    } else {
        return $filtered_text;
    }
}

/**
 * @description This function provides that likely displays or processes a status based on the selected value
 * @param $selected
 * @return string
 */
function statusView($selected = '1') {
    $status = [
        '0' => 'Inactive',
        '1' => 'Active',
    ];

    $row = '';
    foreach ($status as $key => $option) {
        $row .= ($selected == $key) ? $option : '';
    }
    return $row;
}

/**
 * @description This function provides that likely displays or processes a status based on the selected value
 * @param $selected
 * @return string
 */
function globalStatus($selected = 'sel') {
    $status = [
        '1' => 'Active',
        '0' => 'Inactive',
    ];

    $row = '';
    foreach ($status as $key => $option) {
        $row .= '<option value="' . $key . '"';
        $row .= ($selected == $key) ? ' selected' : '';
        $row .= '>' . $option . '</option>';
    }
    return $row;
}

/**
 * @description This function provides get login user data
 * @param string $key
 * @return null
 */
function getLoginUserData($key = '') {
    //key: user_id, user_mail, role_id, name, photo
    $data = DB();
    $global = json_decode(base64_decode($data->input->cookie('fm_login_data', false)));
    return isset($global->$key) ? $global->$key : null;
}

/**
 * @description This function provides numeric drop down
 * @param $i
 * @param $end
 * @param $incr
 * @param $selected
 * @return string
 */
function numericDropDown($i = 0, $end = 12, $incr = 1, $selected = 0) {
    $option = '';
    for ($i; $i <= $end; $i+=$incr) {
        $option .= '<option value="' . $i . '"';
        $option .= ( $selected == $i) ? ' selected' : '';
        $option .= '>' . sprintf('%02d', $i) . '</option>';
    }
    return $option;
}

/**
 * @description This function provides html radio
 * @param string $name
 * @param string $selected
 * @param array $array
 * @return string
 */
function htmlRadio($name = 'input_radio', $selected = '', $array = ['Male' => 'Male', 'Female' => 'Female']) {
    $radio = '';
    $id = 0;

    if (count($array)) {
        foreach ($array as $key => $value) {
            $id++;
            $radio .= '<label>';
            $radio .= '<input type="radio" name="' . $name . '" id="' . $name . '_' . $id . '"';
            $radio .= ( trim($selected) === $key) ? ' checked ' : '';
            $radio .= 'value="' . $key . '" /> ' . $value;
            $radio .= '&nbsp;&nbsp;&nbsp;</label>';
        }
    }
    return $radio;
}

/**
 * @description This function provides fetch the required column from the specified table.
 * @param int|float|string|double $selected
 * @param string $tblId
 * @param int|float|string|double $needCol
 * @param string $table
 * @return string
 */
function getListInOption($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $query = $table->where('sch_id',$_SESSION['shopId'])->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->$needCol. '</option>';
    }
    return $options;
}

/**
 * @description This function provides license check.
 * @param int|float|string $selected
 * @param string $tblId
 * @param int|float|string|double $needCol
 * @param string $table
 * @return string
 */
function getListInOptionCheckLicens($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $query = $table->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        if ($value->status != 1) {
            $options .= '<option value="' . $value->$tblId . '" ';
            $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
            $options .= '>' . $value->$needCol. '</option>';
        }
    }
    return $options;
}

/**
 * @description This function provides license check.
 * @param int|float|string $selected
 * @param string $tblId
 * @param int|float|string|double $needCol
 * @param string $table
 * @return string
 */
function getListInOptionCheckLicens2($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $query = $table->get();
    $options = '';
    foreach ($query->getResult() as $value) {
            $options .= '<option value="' . $value->$tblId . '" ';
            $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
            $options .= '>' . $value->$needCol. '</option>';
    }
    return $options;
}

/**
 * @description This function provides fetch the required column from the specified table.
 * @param $selected
 * @param $tblId
 * @param $table
 * @param $class
 * @return string
 */
function getSectionListByClass($selected, $tblId, $table, $class)
{
    $CI =DB();
    $query = $CI->db->query("SELECT * FROM `".$table."`");
    $options = '';
    foreach ($query->result() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->name.'/'.$value->nick_name. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all list option
 * @param $selected
 * @param $tblId
 * @param $needCol
 * @param $table
 * @return string
 */
function getAllListInOption($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $query = $table->where('sch_id',$_SESSION['shopId'])->where('deleted IS NULL')->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->$needCol. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all list option with status
 * @param string $selected
 * @param string $tblId
 * @param int $needCol
 * @param string $table
 * @param int $orderBy
 * @return string
 */
function getAllListInOptionWithStatus($selected, $tblId, $needCol, $table,$orderBy)
{
    $table = DB()->table($table);

    $query = $table->where('sch_id',$_SESSION['shopId'])->where('status','1')->where('deleted IS NULL')->orderBy($orderBy,'ASC')->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->$needCol. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all list two option with status
 * @param $selected
 * @param $tblId
 * @param $needCol
 * @param $needCol2
 * @param $table
 * @return string
 */
function getTwoValueInOptionWithStatus($selected, $tblId,$needCol,$needCol2, $table)
{
    $table = DB()->table($table);
    $query = $table->where('sch_id',$_SESSION['shopId'])->where('status','1')->where('deleted IS NULL')->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' .$value->$needCol.'--'.$value->$needCol2. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all list two option
 * @param int $selected
 * @param string|int $tblId
 * @param string $needCol
 * @param string $needCol2
 * @param string $table
 * @return string
 */
function getTwoValueInOption($selected, $tblId,$needCol,$needCol2, $table)
{
    $table = DB()->table($table);
    $query = $table->where('sch_id',$_SESSION['shopId'])->where('deleted IS NULL')->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' .$value->$needCol.'--'.$value->$needCol2. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all list option with selected
 * @param int $selected
 * @param string|int $tblId
 * @param string $needCol
 * @param string $table
 * @return string
 */
function getCatListInOption($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $query = $table->where('parent_pro_cat',0)->where('sch_id',$_SESSION['shopId'])->where("status !=", 1)->where("deleted IS NULL")->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        $options .= '<option value="' . $value->$tblId . '" ';
        $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->$needCol. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all sub category list option with selected
 * @param int $selected
 * @return string
 */
function subCategoryListOption($selected)
{
    $table = DB()->table('product_category');
    $query = $table->where('parent_pro_cat',0)->where('sch_id',$_SESSION['shopId'])->where('status !=',1)->where('deleted IS NULL')->get()->getResult();
    $options = '';
    foreach ($query as $value) {
        $options .= '<option value="' . $value->prod_cat_id . '" ';
        $options .= ($value->prod_cat_id == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->product_category. '</option>';
    }
    return $options;
}

/**
 * @description This function provides all category list option with selected
 * @param int $categoryId
 * @return string
 */
function categoryListInOption($categoryId)
{
    $table  = DB()->table('product_category');
    $query = $table->where('parent_pro_cat',0)->where('sch_id',$_SESSION['shopId'])->where('status!=',1)->where('deleted IS NULL')->get();

    $catId = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$categoryId);

    $options = '';
    if (!empty($catId)) {
        foreach ($query->getResult() as $value) {
            $options .= '<option value="' . $value->prod_cat_id . '" ';
            $options .= ($value->prod_cat_id == $catId ) ? ' selected="selected"' : '';
            $options .= '>' .$value->product_category. '</option>';
        }
    }else{
        foreach ($query->getResult() as $value) {
            $options .= '<option value="' . $value->prod_cat_id . '" ';
            $options .= ($value->prod_cat_id == $categoryId ) ? ' selected="selected"' : '';
            $options .= '>' .$value->product_category. '</option>';
        }
    }

    return $options;

}

/**
 * @description This function provides all sub category list option with selected
 * @param int $categoryId
 * @return string
 */
function subCatListInOption($categoryId)
{
    $table  = DB()->table('product_category');
    $catId = get_data_by_id('parent_pro_cat','product_category','prod_cat_id',$categoryId);
    $query = $table->where('parent_pro_cat',$catId)->where('sch_id',$_SESSION['shopId'])->where('deleted IS NULL')->get();

    $options = '';


        foreach ($query->getResult() as $value) {
            $options .= '<option value="' . $value->prod_cat_id . '" ';
            $options .= ($value->prod_cat_id == $categoryId ) ? ' selected="selected"' : '';
            $options .= '>' .$value->product_category. '</option>';
        }


    return $options;

}

/**
 * @description This function provides all sub category list option by class section  with selected
 * @param int $selected
 * @param int $classId
 * @param int $sectionId
 * @return string
 */
function getSubjectListByClassSection($selected, $classId, $sectionId)
{
    $CI =DB();
    $query = $CI->db->query("SELECT * FROM `subject` WHERE `sch_id` = ".$_SESSION['schoolId']." AND `class_id` = ".$classId." AND `section_id` = ".$sectionId);
    $options = '';
    foreach ($query->result() as $value) {
        $options .= '<option value="' . $value->subject_id . '" ';
        $options .= ($value->subject_id == $selected ) ? ' selected="selected"' : '';
        $options .= '>' . $value->name. '</option>';
    }
    return $options;
}

/**
 * @description This function provides show data from array with selected
 * @param int $selected
 * @param $array
 * @return string
 */
function showDataFromArray($selected = '', $array = null) {

    $result = '';
    if (count($array)) {
        foreach ($array as $key => $value) {
            $result .= ($key == $selected ) ? $value : '';
        }
    }
    return $result;
}

/*
 * We will use it into header.php or footer.php or any view page
 * to load module wise css or js file
 */
/**
 * @description This function provides load module asset
 * @param string $module
 * @param string $type
 * @param string $script
 * @return void
 */
function load_module_asset($module = null, $type = 'css', $script = null) {

    $file = ($type == 'css') ? 'style.css.php' : 'script.js.php';
    if ($script) {
        $file = $script;
    }

    $path = APPPATH . '/modules/' . $module . '/assets/' . $file;
    if ($module && file_exists($path)) {
        include ($path);
    }
}

/**
 * @description This function provides age calculator.
 * @param string $date
 * @return string
 * @throws Exception
 */
function ageCalculator($date = null) {
    if ($date) {
        $tz = new DateTimeZone('Europe/London');
        $age = DateTime::createFromFormat('Y-m-d', $date, $tz)
            ->diff(new DateTime('now', $tz))
            ->y;
        return $age . ' years';
    } else {
        return 'Unknown';
    }
}

/**
 * @description This function provides since calculator.
 * @param $date
 * @return string
 * @throws Exception
 */
function sinceCalculator($date = null) {

    if ($date) {

        $date = date('Y-m-d', strtotime($date));
        $tz = new DateTimeZone('Europe/London');
        $age = DateTime::createFromFormat('Y-m-d', $date, $tz)
            ->diff(new DateTime('now', $tz));

        $result = '';
        $result .= ($age->y) ? $age->y . 'y ' : '';
        $result .= ($age->m) ? $age->m . 'm ' : '';
        $result .= ($age->d) ? $age->d . 'd ' : '';
        $result .= ($age->h) ? $age->h . 'h ' : '';
        return $result;
    } else {
        return 'Unknown';
    }
}

/**
 * @description This function provides password encryption.
 * @param string $string
 * @return false|string|null
 */
function password_encription($string = '') {
    return password_hash($string, PASSWORD_BCRYPT);
}

/**
 * @description This function provides get admin email.
 * @return false
 */
function get_admin_email() {
    return getSettingItem('IncomingEmail');
}

/**
 * @description This function provides get setting item by key.
 * @param string $setting_key
 * @return false
 */
function getSettingItem($setting_key = null) {
    $ci = DB();
    $setting = $ci->db->get_where('settings', ['label' => $setting_key])->row();
    return isset($setting->value) ? $setting->value : false;
}

/**
 * @description This function provides user status.
 * @param string $selected
 * @return string
 */
function userStatus($selected = null) {
    $status = ['Pending', 'Active', 'Inactive'];
    $options = '';
    foreach ($status as $row) {
        $options .= '<option value="' . $row . '" ';
        $options .= ($row == $selected ) ? 'selected="selected"' : '';
        $options .= '>' . $row . '</option>';
    }
    return $options;
}

/**
 * @description This function provides bd date format .
 * @param string $data
 * @return false|string
 */
function bdDateFormat($data = '0000-00-00') {
    return ($data == '0000-00-00') ? 'Unknown' : date('d/m/y', strtotime($data));
}

/**
 * @description This function provides is checked
 * @param string|int|float $checked
 * @param string|int|float $match
 * @return string
 */
function isCheck($checked = 0, $match = 1) {
    $checked = ($checked);
    return ($checked == $match) ? 'checked="checked"' : '';
}

/**
 * @description This function provides currency show option with selected
 * @param string $selected
 * @return string
 */
function getCurrency($selected = '&pound') {
    $codes = [
        '&pound' => "&pound; GBP",
        '&dollar' => "&dollar; USD",
        '&nira' => "&#x20A6; NGN"
    ];

    $row = '<select name="data[Setting][Currency]" class="form-control">';
    foreach ($codes as $key => $option) {
        $row .= '<option value="' . htmlentities($key) . '"';
        $row .= ($selected == $key) ? ' selected' : '';
        $row .= '>' . $option . '</option>';
    }
    $row .= '</select>';
    return $row;
}

/**
 * @description This function provides show currency symbol
 * @param int|float $money
 * @return string
 */
function showWithCurrencySymbol($money) {
    $table = DB()->table('gen_settings');
    $currency_before_symbol = $table->where('sch_id',$_SESSION['shopId'])->where('label','currency_before_symbol')->get()->getRow();
    $currency_after_symbol = $table->where('sch_id',$_SESSION['shopId'])->where('label','currency_after_symbol')->get()->getRow();
    $result = $currency_before_symbol->value." ".number_format($money, 2, '.', ',')." ".$currency_after_symbol->value;
    return $result;
    //number_format($money, 2, '.', ',')
}

/**
 * @description This function provides show currency symbol super.
 * @param int $shopId
 * @param int|float $money
 * @return string
 */
function showWithCurrencySymbolSuper($shopId,$money) {
    $table = DB()->table('gen_settings');
    $currency_before_symbol = $table->where('sch_id',$shopId)->where('label','currency_before_symbol')->get()->getRow();
    $currency_after_symbol = $table->where('sch_id',$shopId)->where('label','currency_after_symbol')->get()->getRow();
    $result = $currency_before_symbol->value." ".number_format($money, 2, '.', ',')." ".$currency_after_symbol->value;
    return $result;
    //number_format($money, 2, '.', ',')
}

/**
 * @description This function provides show phone number with country code.
 * @param int|float $number
 * @return string
 */
function showWithPhoneNummberCountryCode($number) {
    $table = DB()->table('gen_settings');
    $phoneCode = $table->where('label' , "phone_code")->get()->getRow();
    $result = $phoneCode->value." ".$number;
    return $result;
}

/**
 * @description This function get gen_settings by lavel.
 * @param string $lavel
 * @return mixed
 */
function get_gen_settings_by_lavel($lavel) {
    $table = DB()->table('gen_settings');
    $query = $table->where('label' , $lavel)->where('sch_id',$_SESSION['shopId'])->get()->getRow();
    $result = $query->value;
    return $result;
}

/**
 * @description This function provides date global date time format.
 * @param string $datetime
 * @return false|string
 */
function globalDateTimeFormat($datetime = '0000-00-00 00:00:00') {

    if ($datetime == '0000-00-00 00:00:00' or $datetime == '0000-00-00' or $datetime == '') {
        return 'Unknown';
    }
    return date('h:i A d/m/y', strtotime($datetime));
}

/**
 * @description This function provides date invoice date time format.
 * @param string $datetime
 * @return false|string
 */
function invoiceDateFormat($datetime = '0000-00-00 00:00:00') {

    if ($datetime == '0000-00-00 00:00:00' or $datetime == '0000-00-00' or $datetime == '') {
        return 'Unknown';
    }
    return date('d M Y h:i A ', strtotime($datetime));
}

/**
 * @description This function provides date sale date time format.
 * @param string $datetime
 * @return string
 */
function saleDate($datetime = '0000-00-00 00:00:00') {

    if ($datetime == '0000-00-00 00:00:00' or $datetime == '0000-00-00' or $datetime == '') {
        return 'Unknown';
    }

    $date = date('d/m/y', strtotime($datetime));
    $time = date('h:i a', strtotime($datetime));

    return $date . '<br/>' . $time;
}

/**
 * @description This function provides date global date time format.
 * @param string $datetime
 * @return false|string
 */
function globalTimeStamp($datetime = '0000-00-00 00:00:00') {
    return date('d-M-y - h:i A ', strtotime($datetime));
}

/**
 * @description This function provides date global date format.
 * @param string $datetime
 * @return false|string
 */
function globalDateFormat($datetime = '0000-00-00 00:00:00') {

    if ($datetime == '0000-00-00 00:00:00' or $datetime == '0000-00-00' or $datetime == null) {
        return 'Unknown';
    }
    return date('d M y', strtotime($datetime));
}

/**
 * @description This function provides date global time format.
 * @param string $datetime
 * @return false|string
 */
function globalTimeOnly($datetime = '0000-00-00 00:00:00') {

    if ($datetime == '0000-00-00 00:00:00' or $datetime == '0000-00-00' or $datetime == null) {
        return 'Unknown';
    }
    return date('h:i A', strtotime($datetime));
}

/**
 * @description This function provides data make json.
 * @param array $array
 * @return false|string
 */
function returnJSON($array = []) {
    return json_encode($array);
}

/**
 * @description This function provides ajax respond.
 * @param int|string $status
 * @param string $msg
 * @return false|string
 */
function ajaxRespond($status = 'FAIL', $msg = 'Fail! Something went wrong') {
    return returnJSON([ 'Status' => strtoupper($status), 'Msg' => $msg]);
}

/**
 * @description This function provides ajax authorized.
 * @return true|void
 */
function ajaxAuthorized() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    } else {
        //die( ajaxRespond('Fail', 'Access Forbidden') );

        $html = '';
        $html .= '<center>';
        $html .= '<h1 style="color:red;">Access Denied !</h1>';
        $html .= '<hr>';
        $html .= '<p>It seems that you might come here via an unauthorised way</p>';
        $html .= '</center>';

        die($html);
    }
}

/**
 * @description This function provides global currency format.
 * @param string $string
 * @param string $prefix
 * @param string $sufix
 * @return string
 */
function globalCurrencyFormat($string = 0, $prefix = '৳ ', $sufix = '') {

    if (is_null($string) or empty($string)) {
        return 0 . $sufix;
    } else {
        //return $prefix . number_format($string, 0 ) . $sufix;
        return number_format($string, 2) . $sufix;
    }
}

/**
 * @description This function provides bd contact number.
 * @param int $contact
 * @return mixed|string|null
 */
function bdContactNumber($contact = null) {

    if ($contact && strlen($contact) == 11) {
        return substr($contact, 0, 5) . '-' . substr($contact, 5, 3) . '-' . substr($contact, 8, 3);
    } else {
        return $contact;
    }
}

/**
 * @description This function provides get paginator limiter.
 * @param string $selected
 * @return string
 */
function getPaginatorLimiter($selected = 100) {
    $range = [100, 500, 1000, 2000, 5000];
    $option = '';
    foreach ($range as $limit) {
        $option .= '<option';
        $option .= ( $selected == $limit) ? ' selected' : '';
        $option .= '>' . $limit . '</option>';
    }
    return $option;
}

/**
 * @description This function provides format number to text.
 * @param int|float $tk
 * @param string $extension
 * @return string
 */
function formatNumberToText($tk = 0, $extension = 'BDT') {
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return $f->format($tk) . $extension;
}

/**
 * @description This function provides convert number to word.
 * @param int|float $num
 * @return mixed
 */
function convertNumberToWord($num = false) {
    //$price = new NumbersToWords();
    //return  $price->convert( $num );
    return convert_number($num);
}

/**
 * @description This function provides Retrieve specific data from a table based on a condition.
 * @param int|float|string|double $needCol
 * @param string $table
 * @param string $whereCol
 * @param int|float|string|double $whereInfo
 * @return false|null
 */
function get_data_by_id($needCol, $table, $whereCol, $whereInfo)
{
    $table = DB()->table($table);

    $query = $table->where($whereCol,$whereInfo)->get();
    $findResult = $query->getRow();
    if (!empty($findResult)) {
        $col = ($findResult->$needCol == NULL) ? NULL: $findResult->$needCol;
    }else {
        $col = false;
    }
    return $col;
}

/**
 * @description This function provides Retrieve specific data from a table based on a condition.
 * @param int|float|string|double $needCol
 * @param string $table
 * @param string $whereArray
 * @return false|null
 */
function get_data_by_columns($needCol, $table, $whereArray)
{
    $CI =DB();

    $query = $CI->db->select($needCol)->from($table)->where($whereArray)->get();

    $findResult = $query->num_rows();
    $result = $query->row();
    if ($findResult > 0) {
        $col = ($result->$needCol == NULL) ? NULL: $result->$needCol;
    }else {
        $col = false;
    }
    return $col;
}

/**
 * @description This function provides  total student count
 * @param int $parentID
 * @return mixed
 */
function get_total_student_count_by_parentID($parentID)
{
    $CI =DB();
    $result = $CI->db->query("SELECT * FROM `student` WHERE `parent_id` = ".$parentID)->num_rows();
    return $result;

}

/**
 * @description This function provides  profile image show
 * @return string
 */
function profile_image(){
    $table = DB()->table('shops');

    $shopId = isset($_SESSION['shopId']) ? $_SESSION['shopId'] : "0";

    if ($shopId != 0) {
        $query = $table->where('sch_id',$shopId)->get()->getRow();
        $result = $query->image;
    }else{
        $result = "#";
    }
    return $result;
}

/**
 * @description This function provides  profile name show
 * @return string
 */
function profile_name(){
    $table = DB()->table('shops');

    $shopId = isset($_SESSION['shopId']) ? $_SESSION['shopId'] : "0";

    if ($shopId != 0) {
        $query = $table->where('sch_id',$shopId)->get()->getRow();

        $result = $query->name;

    }else{
        $result = "#";
    }
    return $result;
}

/**
 * @description This function provides  super admin profile name show
 * @return string
 */
function super_profile_name(){
    $table = DB()->table('admin');

    $supeId = $_SESSION['userIdSuper'];
    $query = $table->where('user_id',$supeId)->get()->getRow();
    $result = $query->name;
    return $result;
}

/**
 * @description This function provides  logo image show
 * @return string
 */
function logo_image(){
    $table = DB()->table('shops');

    $shopId = isset($_SESSION['shopId']) ? $_SESSION['shopId'] : "0";

    if ($shopId != 0) {
        $query = $table->where('sch_id',$shopId)->get()->getRow();
        $result = $query->logo;
    }else{
        $result = "no_image_logo.jpg";
    }
    return $result;
}
/**
 * @description This function provides  address show
 * @return string
 */
function address(){
    $table = DB()->table('shops');

    $shopId = isset($_SESSION['shopId']) ? $_SESSION['shopId'] : "0";

    if ($shopId != 0) {
        $query = $table->where('sch_id',$shopId)->get()->getRow();
        $result = '<div style="float: right;  text-align: right; border-right: 3px solid #decf77;padding: 5px;" >
                    '.$query->name.'</br>
                    '.$query->mobile.'</br>
                    '.$query->address.'</br>
                </div>';
    }else{
        $result = "No address found";
    }
    return $result;
}

/**
 * @description This function provides  parents name by userID
 * @param string $name
 * @return mixed
 */
function parents_name_by_userID($name)
{
    $CI =DB();

    $result = $CI->db->query("SELECT `parent_id` FROM `parents` WHERE `name` = '".$name."'")->row();
    $col = $result->parent_id;
    return $col;

}

/**
 * @description This function provides  parents name by userID
 * @param $parent_id
 * @return string
 */
function parents_userID_by_name($parent_id)
{
    $CI =DB();

    $query = $CI->db->query("SELECT `name` FROM `parents` WHERE `parent_id` = '".$parent_id."'");

    $result = $query->num_rows();

    if ($result == 0) {
        $data = "No parent selected";
    }else{
        $data = $query->row()->name;
    }

    return $data;

}

/**
 * @description This function provides  admin cash
 * @return string
 */
function admin_cash()
{
    $shopTable = DB()->table('shops');
    $result = '';
    if (!empty($_SESSION['shopId'])){
        $query = $shopTable->where("sch_id", $_SESSION['shopId'])->countAllResults();
        $result = $query;
    }

    if ($result == 0) {
        $data = "No Cash Available";
    }else{
        $shopTable2 = DB()->table('shops');
        $query2 = $shopTable2->where("sch_id", $_SESSION['shopId'])->get()->getRow();
        $data = showWithCurrencySymbol($query2->cash);
    }
    return $data;
}

/**
 * @description This function provides  bank balance
 * @param int $bankID
 * @return string
 */
function getBankBalance($bankID)
{
    $CI =DB();
    $CI->db->select("balance");
    $query = $CI->db->get_where("bank", array("bank_id" => $bankID, "sch_id" => $_SESSION['shopId']));
    $result = $query->num_rows();
    if ($result == 0) {
        $data = "No available balance";
    }else{
        $data = $query->row()->balance;
    }
    return $data;
}

/**
 * @description This function provides  check data exist
 * @param string $data
 * @return mixed|string
 */
function data_exist($data)
{
    $view = $data ? $data: '<span style="color: #999; "><i>Not Set</i></span>';

    return $view;
}

/**
 * @description This function provides  check bank balance
 * @param int $bankID
 * @param int $requiredBalance
 * @return bool
 */
function checkBankBalance($bankID, $requiredBalance){
    $table  = DB()->table('bank');
    $result = $table->where("bank_id", $bankID)->where("sch_id" , $_SESSION['shopId'])->countAllResults();

    if ($result == 0) {
        $data = false;
    }else{
        $bank  = DB()->table('bank');
        $qu = $bank->where("bank_id", $bankID)->where("sch_id" , $_SESSION['shopId'])->get();
        $balance = $qu->getRow()->balance;
        if ($balance >= $requiredBalance) {
            $data = true;
        }else{
            $data = false;
        }
    }
    return $data;
}

/**
 * @description This function provides  check nagad balance
 * @param int $requiredBalance
 * @return bool
 */
function checkNagadBalance($requiredBalance){
    $table = DB()->table('shops');
    $result = $table->where('sch_id',$_SESSION['shopId'])->countAllResults();
    if ($result == 0) {
        $data = false;
    }else{
        $shops = DB()->table('shops');
        $query = $shops->where('sch_id',$_SESSION['shopId'])->get()->getRow()->cash;
        $balance = $query;
        if ($balance >= $requiredBalance) {
            $data = true;
        }else{
            $data = false;
        }
    }
    return $data;
}

/**
 * @description This function provides  number to words convert
 * @param float $number
 * @return string
 */
function numberTowords(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "(point)." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) : '';
    return ($Rupees ? $Rupees : '') . $paise;
}


/**
 * @description This function provides  suppliers Total Purchase Amount
 * @param int $supplierId
 * @return string
 */
function suppliersTotalPurchaseAmount($supplierId){
    $table = DB()->table('ledger_suppliers');
    $result = $table->selectSum("amount")->where("supplier_id", $supplierId)->where("sch_id", $_SESSION['shopId'])->where("trangaction_type", 'Cr.')->get()->getRow();

    if (!empty($result)) {
        $balance = showWithCurrencySymbol($result->amount);
    }else{
        $balance = "No Pursess Available Amount";
    }
    return $balance;
}

/**
 * @description This function provides  Customer Total Sale Amount
 * @param int $customerId
 * @return string
 */
function CustomerTotalSaleAmount($customerId){
    $table = DB()->table('ledger');
    $result = $table->selectSum('amount')->where("customer_id", $customerId)->where("sch_id", $_SESSION['shopId'])->where("trangaction_type", 'Cr.')->get()->getRow();

    if (!empty($result)) {
        $balance = showWithCurrencySymbol($result->amount);
    }else{
        $balance = "No Pursess Available Amount";
    }
    return $balance;
}

/**
 * @description This function provides  transaction type message
 * @param string $type
 * @return string
 */
function transaction_type_message($type) {
    if ($type == 'Cr.') {
        $row = 'খরচ (Cr.)';
    }else{
        $row = 'জমা (Dr.)';
    }
    return $row;
}

/**
 * @description This function provides  check unique field
 * @param string $value
 * @param string $colam
 * @param string $table
 * @return bool
 */
function checkUniqueField($value, $colam, $table)
{
    $CI =DB();
    $shopId = $_SESSION['shopId'];

    $query = $CI->db->query("SELECT `".$colam."` FROM `".$table."` WHERE `".$colam."` =  '".$value."' AND `deleted` IS NULL AND `sch_id` = ".$shopId);

    $result = $query->num_rows();

    if ($result == 0) {
        $data = true;
    }else{
        $data = false;
    }

    return $data;
}

/**
 * @description This function provides getRoleIdListInOption
 * @param string|int $selected
 * @param int|string $tblId
 * @param string $needCol
 * @param string $table
 * @return string
 */
function getRoleIdListInOption($selected, $tblId, $needCol, $table)
{
    $table = DB()->table($table);
    $shopId = $_SESSION['shopId'];

    $query = $table->where('sch_id',$shopId)->get();
    $options = '';
    foreach ($query->getResult() as $value) {
        if ($value->$tblId != 1 ) {
            $options .= '<option value="' . $value->$tblId . '" ';
            $options .= ($value->$tblId == $selected ) ? ' selected="selected"' : '';
            $options .= '>' . $value->$needCol. '</option>';
        }

    }
    return $options;
}

/**
 * @description This function provides default check
 * @param int $Id
 * @param string $col
 * @param string $table
 * @return mixed
 */
function is_default($Id, $col, $table)
{
    $table = DB()->table($table);
    $query = $table->where($col,$Id)->get();
    return $query->getRow()->is_default;
}

/**
 * @description This function provides total nogod balance
 * @param string $tbl
 * @param int $sumRow
 * @param string $trnsType
 * @param string $start_date
 * @param string $end_date
 * @return mixed
 */
function get_total_nogodBalance($tbl,$sumRow,$trnsType,$start_date=0, $end_date=0)
{
    $table = DB()->table($tbl);
    $shopId = $_SESSION['shopId'];
    $query = $table->selectSum($sumRow)->where('trangaction_type' , $trnsType)->where('sch_id',$shopId )->get()->getRow()->$sumRow;
    if (($start_date == 0) && ($end_date == 0)) {
        $query = $table->selectSum($sumRow)->where('trangaction_type' , $trnsType)->where('sch_id',$shopId )->get()->getRow()->$sumRow;
    }else {
        $query = $table->selectSum($sumRow)->where(array('trangaction_type' => $trnsType,'sch_id'=>$shopId,'createdDtm >=' => $start_date.' 00:00:00', 'createdDtm <=' => $end_date.' 23:59:59' ))->get()->getRow()->$sumRow;
    }

    return $query;

}

/**
 * @description This function provides get total
 * @param int $tbl
 * @param int|float $sumRow
 * @param string $trnsType
 * @param string|int $wherId
 * @param string $byId
 * @param string $start_date
 * @param string $end_date
 * @return mixed
 */
function get_total($tbl,$sumRow,$trnsType,$wherId,$byId, $start_date=0, $end_date=0)
{
    $shopId = $_SESSION['shopId'];
    if (($start_date == 0) && ($end_date == 0)) {
        $table  = DB()->table($tbl);
        $query = $table->selectSum($sumRow)->where('trangaction_type', $trnsType)->where($wherId, $byId)->where('sch_id',$shopId)->get()->getRow()->$sumRow;
    }else {
        $table  = DB()->table($tbl);
        $query = $table->selectSum($sumRow)->where('trangaction_type', $trnsType)->where($wherId, $byId)->where('sch_id',$shopId)->where('createdDtm >=', $start_date.' 00:00:00')->where('createdDtm <=', $end_date.' 23:59:59')->get()->getRow()->$sumRow;
    }

    return $query;
}

/**
 * @description This function provides unit array
 * @return string[]
 */
function unitArray() {
    $status = [
        '1' => 'Piece',
        '2' => 'KG',
        '3' => 'LETTER',
        '4' => 'TON'
    ];
    return $status;
}

/**
 * @description This function provides unit name show
 * @param string $selected
 * @return string
 */
function showUnitName($selected = '1') {
    $status = unitArray();
    $row =  $status[$selected];
    return $row;
}

/**
 * @description This function provides select options
 * @param $selected
 * @param $array
 * @return string
 */
function selectOptions($selected = '', $array = null) {

    $options = '';
    if (count($array)) {
        foreach ($array as $key => $value) {
            $options .= '<option value="' . $key . '" ';
            $options .= ($key == $selected ) ? ' selected="selected"' : '';
            $options .= '>' . $value . '</option>';
        }
    }
    return $options;
}

/**
 * @description This function provides profile image super
 * @param int $Id
 * @return string
 */
function profile_image_super($Id){
    $table = DB()->table('admin');

    if ($Id != 0) {
        $query = $table->where('user_id',$Id)->get()->getRow();

        $result = $query->pic;

    }else{
        $result = "#";
    }
    return $result;
}

/**
 * @description This function provides check user two condition
 * @param string $table
 * @param array $whereArray
 * @return bool
 */
function check_user_two_condition($table, $whereArray)
{
    $CI =DB();

    $query = $CI->db->from($table)->where($whereArray)->get();
    $findResult = $query->num_rows();

    if ($findResult > 0) {
        $col = true;
    }else {
        $col = false;
    }
    return $col;
}

/**
 * @description This function provides shop opening status
 * @return mixed
 */
function shop_opening_status(){
    $table = DB()->table('shops');
    $shopId = $_SESSION['shopId'];
    $query = $table->where('sch_id',$shopId)->get();
    $col = $query->getRow()->opening_status;

    return $col;
}

/**
 * @description This function provides check ledger exists
 * @param string $table
 * @param string $whereCol
 * @param string $whereInfo
 * @return bool
 */
function ledger_exists($table, $whereCol, $whereInfo){
    $table  = DB()->table($table);
    $shopId = $_SESSION['shopId'];
    $findResult = $table->where('sch_id',$shopId)->where($whereCol,$whereInfo)->countAllResults();

    if ($findResult == 0) {
        $col = true;
    }else {
        $col = false;
    }
    return $col;
}

/**
 * @description This function provides image view with no image
 * @param string $image_path
 * @param string $no_image_path
 * @param string $imageName
 * @return string
 */
function no_image_view($image_path,$no_image_path,$imageName = '1'){
    $imgPathcheck = FCPATH.$image_path;
    if ((empty($imageName))||(!file_exists($imgPathcheck))){
        return base_url().$no_image_path;
    }else{
        return base_url().$image_path;
    }
}

/**
 * @description This function provides check shop exists
 * @param string $table
 * @param string $whereID
 * @param string|int $id
 * @return int
 */
function check_shop($table,$whereID,$id){
    $table = DB()->table($table);
    $shopId = $_SESSION['shopId'];
    $query = $table->where($whereID, $id)->where( 'sch_id', $shopId)->countAllResults();
    return !empty($query)?1:0;
}

/**
 * @description This function provides check unique
 * @param string $table
 * @param string $whereField
 * @param string|int $field
 * @return bool
 */
function is_unique($table,$whereField,$field){
    $table = DB()->table($table);
    $shopId = $_SESSION['shopId'];
    $query = $table->where($whereField , $field)->where('sch_id' , $shopId)->countAllResults();
    return !empty($query)?false:true;
}

/**
 * @description This function provides check unique when update
 * @param string $table
 * @param string $whereField
 * @param string|int $field
 * @param string $whereClause
 * @param int $id
 * @return bool
 */
function is_unique_update($table,$whereField,$field,$whereClause,$id){
    $table = DB()->table($table);
    $shopId = $_SESSION['shopId'];
    $query = $table->where($whereField , $field)->where($whereClause.'!=' , $id)->where('sch_id' , $shopId)->countAllResults();
    return !empty($query)?false:true;
}

/**
 * @description This function provides settings value by label
 * @param string $lavel
 * @return string
 */
function get_sup_settings_by_lavel($lavel){
    $table = DB()->table('gen_settings_super');
    $query = $table->where('label',$lavel)->get()->getRow();
    return !empty($query)?$query->value:'no data available';
}

/**
 * @description This function provides bank ledger data
 * @param int $bankId
 * @param string $date
 * @return array
 */
function bank_ledger($bankId,$date){
    $searchDate = (empty($date))? date('Y-m-d') :$date;

    $ledger_bankTab = DB()->table('ledger_bank');
    $data = $ledger_bankTab->where("bank_id",$bankId)->like('createdDtm',$searchDate)->limit(30)->orderBy("createdDtm","DESC")->get()->getResult();

    return $data;
}

/**
 * @description This function provides bank ledger previse rest balance
 * @param int $bankId
 * @param string $date
 * @return int
 */
function bank_ledger_prev_restBalance($bankId,$date){
    $searchDate = (empty($date))? date('Y-m-d') :$date;

    $ledger_bankTab = DB()->table('ledger_bank');
    $balance = $ledger_bankTab->where("bank_id",$bankId)->where('createdDtm <',$searchDate)->limit(1)->orderBy("createdDtm","DESC")->get()->getRow();

    return empty($balance)? 0 : $balance->rest_balance;
}

/**
 * @description This function provides bank ledger rest balance
 * @param int $bankId
 * @param string $date
 * @return int
 */
function bank_ledger_restBalance($bankId,$date){
    $searchDate = (empty($date))? date('Y-m-d') :$date;

    $ledger_bankTab = DB()->table('ledger_bank');
    $balance = $ledger_bankTab->where("bank_id",$bankId)->like('createdDtm',$searchDate)->limit(1)->orderBy("createdDtm","DESC")->get()->getRow();
    return empty($balance)? 0 : $balance->rest_balance;
}

/**
 * @description This function provides menu with permission
 * @param string $title
 * @param string $url
 * @param int $roleId
 * @param string $icon
 * @param string $ajaxUrl
 * @param string $module_name
 * @return string|void
 */
function add_main_ajax_based_menu_with_permission($title, $url, $roleId, $icon, $ajaxUrl, $module_name){

    $active_url  = current_url(true);
    $permission = new Permission();
    $menu       = '';

    $access = $permission->have_access($roleId, $module_name, 'mod_access');
    if ($access == 1) {
        $class_active   = ($active_url === $url ) ? ' class="active"' : '';
        $menu .= '<li '.$class_active.'><a href="#" onclick="showData(\''.site_url($ajaxUrl).'\', \''.$url.'\'),activeTab(this)">';
        $menu .= '<i class="fa '. $icon .'"></i>';
        $menu .= '<span>'.$title .'</span>';
        $menu .= '</a><li>';

        return $menu;
    }
}

/**
 * @description This function provides menu with all permission
 * @param array $module_name_array
 * @param int $role_id
 * @return bool
 */
function all_menu_permission_check($module_name_array,$role_id){
    $permission = new Permission();
    foreach ($module_name_array as $module_name){
        $access[] = $permission->have_access($role_id, $module_name, 'mod_access');
    }
    return empty(array_filter($access))?false:true;
}

/**
 * @description This function provides main menu
 * @param string $title
 * @param string $url
 * @param string $access
 * @param string $icon
 * @return string
 */
function add_main_menu($title, $url, $access, $icon){
    // $title, $url, $icon, $access.
    $active_url  = current_url();


    $menu       = '';
    $class_active   = ($active_url == base_url('index.php'.$url) ) ? 'class="active"' : '';

    $menu .= '<li '.$class_active.'><a href="'. base_url() .$url .'">';
    $menu .= '<i class="fa '. $icon .'"></i>';
    $menu .= '<span>'.$title .'</span>';
    $menu .= '</a><li>';
    return $menu;

}

/**
 * @description This function provides check unique super
 * @param string $table
 * @param string $whereField
 * @param int $field
 * @return bool
 */
function is_unique_super($table,$whereField,$field){
    $table = DB()->table($table);
    $query = $table->where($whereField , $field)->countAllResults();
    return !empty($query)?false:true;
}

/**
 * @description This function provides check unique super when update
 * @param $table
 * @param $whereField
 * @param $field
 * @param $whereClause
 * @param $id
 * @return bool
 */
function is_unique_super_update($table,$whereField,$field,$whereClause,$id){
    $table = DB()->table($table);
    $query = $table->where($whereField , $field)->where($whereClause.'!=' , $id)->countAllResults();
    return !empty($query)?false:true;
}

/**
 * @description This function provides check unique bank
 * @param $table
 * @param $whereField
 * @param $field
 * @param $orWhereField
 * @param $orField
 * @return bool
 */
function is_unique_bank($table,$whereField,$field,$orWhereField,$orField){
    $table = DB()->table($table);
    $shopId = $_SESSION['shopId'];
    $query = $table->where($whereField , $field)->where($orWhereField , $orField)->where('sch_id' , $shopId)->countAllResults();
    return !empty($query)?false:true;
}

function send_sms($phone,$message){
    $url = "https://bulksmsbd.net/api/smsapi";
    $api_key = "Hn7xRTUtKvj5nCU1KShg";
    $senderid = "8809617622673";

    $data = array(
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $phone,
        "message" => $message
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
}
function led_id_by_bank_name($invoice_id) {
    $name = '';
    if(!empty($invoice_id)){
        $bank_id = get_data_by_id('bank_id','invoice','invoice_id',$invoice_id);
        $name = !empty($bank_id)?get_data_by_id('name','bank','bank_id',$bank_id).'<br>('.get_data_by_id('account_no','bank','bank_id',$bank_id).')':'';
    }
    return $name;
}

function led_id_by_chaque_number($invoice_id) {
    $number = '';
    if(!empty($invoice_id)){
        $chaque_id = get_data_by_id('chaque_id','invoice','invoice_id',$invoice_id);
        $number = !empty($chaque_id)?'Chaque No: '.get_data_by_id('chaque_number','chaque','chaque_id',$chaque_id):'';
    }
    return $number;
}

function serviceArray() {
    $status = [
        'onetime' => 'Onetime',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly'
    ];
    return $status;
}

/**
 * @description This function provides unit name show
 * @param string $selected
 * @return string
 */
function showServiceName($selected = '1') {
    $status = serviceArray();
    $row =  $status[$selected];
    return $row;
}

/**
 * @description This function is deletable
 * @param string $table
 * @param string $whereCol
 * @param int $whereInfo
 * @return bool
 */
function is_deletable($table,$whereCol, $whereInfo){
    $tableData = DB()->table($table);
    $count = $tableData->where($whereCol,$whereInfo)->countAllResults();
    return ($count === 0);
}

/**
 * @description This function edit expire check
 * @param string $date
 * @return false|int
 * @throws Exception
 */
function edit_expire_check($date){
    $start = new DateTime($date);
    $today = new DateTime();
    $interval = $start->diff($today);

    $setDay = get_gen_settings_by_lavel('edit_permission_expire');
    $day = !empty($setDay)?$setDay:'10';

    return ($interval->days <= $day)?true:false;
}