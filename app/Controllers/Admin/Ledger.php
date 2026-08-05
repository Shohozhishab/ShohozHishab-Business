<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $customer_id = $this->request->getGet('customer_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');
            $query = [];
            if (!empty($customer_id)) {
                $db = DB();
                // 1. Define the window function column
                $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledg_id) AS r_balance";
                // 2. Build the inner subquery
                $subquery = $db->table('ledger')
                    ->select('ledger.*')
                    ->select($mBalanceSubquery, false)
                    ->where("customer_id", $customer_id);
                // 3. Query from the subquery derived table
                $table = $db->newQuery()
                    ->fromSubquery($subquery, 't')
                    ->where("customer_id", $customer_id);
                if (!empty($st_date) && !empty($en_date)) {
                    // Assuming your database column name is 'date'
                    $table->where('createdDtm >=', $st_date . ' 00:00:00');
                    $table->where('createdDtm <=', $en_date . ' 23:59:59');
                }
                $table->orderBy('ledg_id', 'ASC');
                $query = $table->get()->getResult();
            }

            $data['result'] = $query;

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';
            $data['customer_id'] = $customer_id;

            $result = [];
            if (!empty($customer_id)) {
                $table = DB()->table('ledger');
                $table->where("customer_id", $customer_id);
                $result = $table->get()->getResult();
            }
            $data['mainResult'] = $result;



            $data['id'] = $shopId;
            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method search customer ledger
     * @return void
     */
    public function search_customerLedg()
    {
        $customerId = $this->request->getPost('id');

        $ledgerTab = DB()->table('ledger');
        $data = $ledgerTab->where('customer_id', $customerId)->get()->getResult();

        $name = get_data_by_id('customer_name', 'customers', 'customer_id', $customerId);
        $balance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Customer: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td> Total Buy:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customerId)) . '</td></tr><tr><td>Total Pay:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customerId)) .
            '</td></tr><tr><td>Last Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Invoice Id/Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            // foreach ($data as $row) {

            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);

            if (($data[$i]->invoice_id == NULL) && ($data[$i]->trans_id == NULL)) {
                $invoice_id = '---';
            } else {
                $invoice_id = ($data[$i]->invoice_id == NULL) ? '<a href="' . site_url('Admin/Transaction/moneyReceipt/' . $data[$i]->trans_id) . '">TRNS_' . $data[$i]->trans_id . '</a>' : '<a href="' . site_url('Admin/Invoice/view/' . $data[$i]->invoice_id) . '">INV_' . $data[$i]->invoice_id . '</a>';
            }
            $bankName = led_id_by_bank_name($data[$i]->invoice_id);
            $checkNumber = led_id_by_chaque_number($data[$i]->invoice_id);
            $view .= '<tr>
                                    <td>' . $data[$i]->ledg_id . '</td>
                                    <td>' . $data[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '<br><small>'.$bankName. $checkNumber.'</small></td>
                                    <td>' . $invoice_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Invoice Id/Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customerId)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customerId)) . '</th>
                                    <th>= ' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        print $view;
    }

    /**
     * @description This method search customer ledger print
     * @return void
     */
    public function search_customerLedgPrint()
    {
        $customerId = $this->request->getPost('id');

        $ledgerTab = DB()->table('ledger');
        $data = $ledgerTab->where('customer_id', $customerId)->get()->getResult();

        $logo = (logo_image() == NULL) ? 'no_image.jpg' : logo_image();

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <img src="' . base_url() . '/uploads/schools/' . $logo . '" alt="User Image" >
                                </div>
                                <div class="col-xs-6">
                                    ' . address() . '
                                </div>
                            </div>
                    <div class="col-xs-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            // foreach ($data as $row) {

            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $view .= '<tr>
                                    <td>' . bdDateFormat($data[$i]->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                        </table>
                        </div>
                    </div>';

        print $view;
    }

    /**
     * @description This method search customer ledger date to date
     * @return void
     */
    public function search_dateTodate()
    {
        $customerId = $this->request->getPost('customerId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $ledgerTab = DB()->table('ledger');
        $data = $ledgerTab->where('customer_id', $customerId)->where('createdDtm >=', $st_date . ' 00:00:00')->where('createdDtm <=', $en_date . ' 23:59:59')->orderBy('ledg_id', "DESC")->get()->getResult();

        $name = get_data_by_id('customer_name', 'customers', 'customer_id', $customerId);
        $balance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);

        $view = ' <div class="box" >
                        <div class="box-header">
                            <h3 class="box-title">Customer: ' . $name . '</h3>
                            <span class="pull-right"><table class="table table-bordered table-striped" id="TFtable"><tr><td> Total Buy:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customerId, $st_date, $en_date)) . '</td></tr><tr><td>Total Pay:</td><td>' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customerId, $st_date, $en_date)) .
            '</td></tr><tr><td>Due Balance:</td><td>' . showWithCurrencySymbol($balance) . '</td></tr></table></span>
                        </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="example1">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Invoice Id/Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $invoice_id = ($data[$i]->invoice_id == NULL) ? '<a href="' . site_url('transaction/moneyReceipt/' . $data[$i]->trans_id) . '">TRNS_' . $data[$i]->trans_id . '</a>' : '<a href="' . site_url('invoice/read/' . $data[$i]->invoice_id) . '">INV_' . $data[$i]->invoice_id . '</a>';
            $bankName = led_id_by_bank_name($data[$i]->invoice_id);
            $checkNumber = led_id_by_chaque_number($data[$i]->invoice_id);
            $view .= '<tr>
                                    <td>' . $data[$i]->ledg_id . '</td>
                                    <td>' . $data[$i]->createdDtm . '</td>
                                    <td>' . $particulars . '<br><small>'.$bankName. $checkNumber.'</small></td>
                                    <td>' . $invoice_id . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Invoice Id/Memo</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Dr.', 'customer_id', $customerId, $st_date, $en_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol(get_total('ledger', 'amount', 'Cr.', 'customer_id', $customerId, $st_date, $en_date)) . '</th>
                                    <th>= ' . showWithCurrencySymbol($balance) . '</th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>';

        print $view;
    }

    /**
     * @description This method search customer ledger print
     * @return void
     */
    public function search_dateTodatePrint()
    {
        $customerId = $this->request->getPost('customerId');
        $st_date = $this->request->getPost('st_date');
        $en_date = $this->request->getPost('en_date');

        $ledgerTab = DB()->table('ledger');
        $data = $ledgerTab->where('customer_id', $customerId)->where('createdDtm >=', $st_date . ' 00:00:00')->where('createdDtm <=', $en_date . ' 23:59:59')->orderBy('ledg_id', "DESC")->get()->getResult();

        $logo = (logo_image() == NULL) ? 'no_image.jpg' : logo_image();

        $view = '<div class="col-xs-12" style="text-transform: capitalize;" >
                            <div class="col-xs-12" style="margin-bottom: 20px;   ">
                                <div class="col-xs-6">
                                    <img src="' . base_url() . '/uploads/schools/' . $logo . '" alt="User Image" >
                                </div>
                                <div class="col-xs-6">
                                    ' . address() . '
                                </div>
                            </div>
                    <div class="col-xs-12">
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        $totalRows = count($data) - 1;
        for ($i = $totalRows; $i >= 0; $i--) {
            $particulars = ($data[$i]->particulars == NULL) ? "Pay due" : $data[$i]->particulars;
            $amountCr = ($data[$i]->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $amountDr = ($data[$i]->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($data[$i]->amount);
            $view .= '<tr>
                                    <td>' . bdDateFormat($data[$i]->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($data[$i]->rest_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                        </table>
                        </div>
                    </div>';

        print $view;
    }


}