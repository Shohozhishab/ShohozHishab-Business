<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Daily_book extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Daily_book';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides  view
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

            //Show today all cash transaction list in ledger_nagodan table (start)
            $db = DB();
            // 1. Define the window function column
            $mBalanceSubquery = "SUM(
                CASE 
                    WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                    WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                    ELSE 0 
                END
            ) OVER (ORDER BY ledg_nagodan_id) AS r_balance";
            // 2. Build the inner subquery
            $subquery = $db->table('ledger_nagodan')
                ->select('ledger_nagodan.*')
                ->select($mBalanceSubquery, false)
                ->where('sch_id', $shopId);
            // 3. Query from the subquery derived table
            $data['cashLedger'] = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId)
                ->like('DATE(createdDtm)', date('Y-m-d'))
                ->orderBy('ledg_nagodan_id', 'DESC')
                ->get()
                ->getResult();

            $data['cashrest_balance'] = get_data_by_id('cash','shops','sch_id',$shopId);



            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();



            $data['sales'] = DB()->table('sales')
                ->where('sales.sch_id', $shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['purchase_data'] = DB()->table('purchase')
                ->where('sch_id', $shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['capital'] = DB()->table('capital')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['transaction'] = DB()->table('transaction')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Daily_book/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
    /**
     * @description This method bank ledger search
     * @return void
     */
    public function search_bankLedg()
    {

        $bankId = $this->request->getPost('id');
        $date = $this->request->getPost('date');
        $searchDate = (empty($date)) ? date('Y-m-d') : $date;


        $restBalance = get_data_by_id('balance','bank','bank_id',$bankId);

        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledgBank_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_bank')
            ->select('ledger_bank.*')
            ->select($mBalanceSubquery, false)
            ->where("bank_id", $bankId); // false prevents CI4 from escaping the raw SQL window function
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("bank_id", $bankId)
            ->like('createdDtm', $searchDate)
            ->limit(30);
        $table->orderBy('ledgBank_id', 'ASC');
        $data = $table->get()->getResult();


        $view = '<span class="pull-right">Last Balance ' . showWithCurrencySymbol($restBalance) . '</span>';

        $view .= '<table class="table table-bordered table-striped" id="TFtable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <th>Particulars</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                        <td>' . invoiceDateFormat($row->createdDtm) . '</td>
                                        <td>' . get_data_by_id('name', 'bank', 'bank_id', $row->bank_id) . '</td>
                                        <td>' . $particulars . '</td>
                                        <td>' . $amountDr . '</td>
                                        <td>' . $amountCr . '</td>
                                        <td>' . showWithCurrencySymbol($row->r_balance) . '</td>
                                    </tr>';
        }

        print $view;


    }

    /**
     * @description This method search ledger
     * @return RedirectResponse|void
     */
    public function search()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $date = $this->request->getPost('date');
            $data['date'] = $date;

            $db = DB();
            // 1. Define the window function column
            $mBalanceSubquery = "SUM(
                CASE 
                    WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                    WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                    ELSE 0 
                END
            ) OVER (ORDER BY ledg_nagodan_id) AS r_balance";
            // 2. Build the inner subquery
            $subquery = $db->table('ledger_nagodan')
                ->select('ledger_nagodan.*')
                ->select($mBalanceSubquery, false)
                ->where('sch_id', $shopId);
            // 3. Query from the subquery derived table
            $data['cashledger'] = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId)
                ->like('createdDtm', $date)
                ->orderBy('ledg_nagodan_id', 'ASC')
                ->get()
                ->getResult();



            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();

            $data['sales'] = DB()->table('sales')
                ->where('sales.sch_id', $shopId)
                ->where('DATE(createdDtm)', $date)
                ->get()->getResult();

            $data['purchase_data'] = DB()->table('purchase')
                ->where('sch_id', $shopId)
                ->where('DATE(createdDtm)', $date)
                ->get()->getResult();

            $data['capital'] = DB()->table('capital')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', $date)
                ->get()->getResult();

            $data['transaction'] = DB()->table('transaction')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', $date)
                ->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Daily_book/search', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method print preview
     * @return RedirectResponse|void
     */
    public function print_preview()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $date = $this->request->getGet('date');

            $dateKey = !empty($date) ? $date: date('Y-m-d');

            $db = DB();
            // 1. Define the window function column
            $mBalanceSubquery = "SUM(
                CASE 
                    WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                    WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                    ELSE 0 
                END
            ) OVER (ORDER BY ledg_nagodan_id) AS r_balance";
            // 2. Build the inner subquery
            $subquery = $db->table('ledger_nagodan')
                ->select('ledger_nagodan.*')
                ->select($mBalanceSubquery, false)
                ->where('sch_id', $shopId);
            // 3. Query from the subquery derived table
            $data['cashLedger'] = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId)
                ->like('createdDtm', $dateKey)
                ->orderBy('ledg_nagodan_id', 'DESC')
                ->get()
                ->getResult();

            $data['cashrest_balance'] = get_data_by_id('cash','shops','sch_id',$shopId);

            //
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $prevbalance = $ledger_nagodanTab->where('createdDtm <', $dateKey)->where("sch_id", $shopId)->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['prevAll_balance'] = empty($prevbalance) ? 0 : $prevbalance->rest_balance;
            //


            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();

            $data['sales'] = DB()->table('sales')
                ->where('sales.sch_id', $shopId)
                ->where('DATE(createdDtm)', $dateKey)
                ->get()->getResult();

            $data['purchase_data'] = DB()->table('purchase')
                ->where('sch_id', $shopId)
                ->where('DATE(createdDtm)', $dateKey)
                ->get()->getResult();

            $data['capital'] = DB()->table('capital')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', $dateKey)
                ->get()->getResult();

            $data['transaction'] = DB()->table('transaction')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', $dateKey)
                ->get()->getResult();

            $data['searchDate'] = $dateKey;
            $data['dateSelected'] = !empty($date) ? $date: '';

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Daily_book/print', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}