<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Transaction_ajax extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Transaction';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides transaction view
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
            $transactionTable = DB()->table('transaction');

            // Date Filter
            $start_date = $this->request->getGet('st_date');
            $end_date = $this->request->getGet('en_date');
            $category = $this->request->getGet('category');

            if ($start_date) {
                $transactionTable->where('createdDtm >=', $start_date . ' 00:00:00');
            }
            if ($end_date) {
                $transactionTable->where('createdDtm <=', $end_date . ' 23:59:59');
            }

            // Exclusive Entity Filters based on Category
            $customer_id = $this->request->getGet('customer_id');
            $supplier_id = $this->request->getGet('supplier_id');
            $loan_pro_id = $this->request->getGet('loan_pro_id');
            $bank_id = $this->request->getGet('bank_id');
            $employee_id = $this->request->getGet('employee_id');

            if ($category == 'customer') {
                $transactionTable->where('customer_id !=', NULL);
                if ($customer_id) $transactionTable->where('customer_id', $customer_id);
            } elseif ($category == 'supplier') {
                $transactionTable->where('supplier_id !=', NULL);
                if ($supplier_id) $transactionTable->where('supplier_id', $supplier_id);
            } elseif ($category == 'loan_provider') {
                $transactionTable->where('loan_pro_id !=', NULL);
                if ($loan_pro_id) $transactionTable->where('loan_pro_id', $loan_pro_id);
            } elseif ($category == 'fund_transfer') {
                $transactionTable->where('bank_to_id !=', NULL);
                if ($bank_id) {
                    $transactionTable->groupStart()
                        ->where('bank_id', $bank_id)
                        ->orWhere('bank_to_id', $bank_id)
                        ->groupEnd();
                }
            } elseif ($category == 'employee') {
                $transactionTable->where('employee_id !=', NULL);
                if ($employee_id) $transactionTable->where('employee_id', $employee_id);
            } elseif ($category == 'vat') {
                $transactionTable->where('vat_id !=', NULL);
            } elseif ($category == 'expense') {
                $transactionTable->where('loan_pro_id', NULL)
                    ->where('customer_id', NULL)
                    ->where('supplier_id', NULL)
                    ->where('bank_id', NULL)
                    ->where('lc_id', NULL)
                    ->where('employee_id', NULL)
                    ->where('trangaction_type', 'Cr.');
            } elseif ($category == 'othersales') {
                $transactionTable->where('loan_pro_id', NULL)
                    ->where('customer_id', NULL)
                    ->where('supplier_id', NULL)
                    ->where('bank_id', NULL)
                    ->where('lc_id', NULL)
                    ->where('trangaction_type', 'Dr.');
            }

            $data['transaction_data'] = $transactionTable->where('sch_id', $shopId)->get()->getResult();
            $data['st_date'] = isset($start_date)?$start_date:'';
            $data['en_date'] = isset($end_date)?$end_date:'';
            $data['active_category'] = $category;

            $data['customer_id_filter'] = $customer_id;
            $data['supplier_id_filter'] = $supplier_id;
            $data['loan_pro_id_filter'] = $loan_pro_id;
            $data['bank_id_filter'] = $bank_id;
            $data['employee_id_filter'] = $employee_id;

            $data['customers'] = DB()->table('customers')->where('sch_id', $shopId)->get()->getResult();
            $data['suppliers'] = DB()->table('suppliers')->where('sch_id', $shopId)->get()->getResult();
            $data['loan_providers'] = DB()->table('loan_provider')->where('sch_id', $shopId)->get()->getResult();
            $data['banks'] = DB()->table('bank')->where('sch_id', $shopId)->get()->getResult();
            $data['employees'] = DB()->table('employee')->where('sch_id', $shopId)->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Transaction/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides transaction create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['button'] = 'Process';
            $data['action'] = base_url('Admin/Transaction/customer_transaction_action');
            $data['actionsuppl'] = base_url('Admin/Transaction/supplier_transaction_action');
            $data['actionLoanPro'] = base_url('Admin/Transaction/loan_pro_transaction_action');
            $data['actionLc'] = base_url('Admin/Transaction/lc_transaction_action');
            $data['actionBank'] = base_url('Admin/Transaction/bank_transaction_action');
            $data['actionExpense'] = base_url('Admin/Transaction/expense_transaction_action');
            $data['actionOtherSales'] = base_url('Admin/Transaction/otherSales_transaction_action');
            $data['actionSalaryEmployee'] = base_url('Admin/Transaction/salaryEmployee_transaction_action');
            $data['actionVatPay'] = base_url('Admin/Transaction/vat_pay_action');
            $data['actionAssetsPay'] = base_url('Admin/Transaction/assets_pay_action');

            $data['assets'] = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()
                ->getResult();

            $data['expenses'] = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'expenses')
                ->get()
                ->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Transaction/create', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method store money receipt transaction
     * @param int $id
     * @return RedirectResponse|void
     */
    public function moneyReceipt($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $transactionTab = DB()->table('transaction');
            $data['money'] = $transactionTab->where('trans_id', $id)->get()->getResult();

            $shopsTab = DB()->table('shops');
            $data['shops'] = $shopsTab->where('sch_id', $shopId)->get()->getResult();
            $data['transactionId'] = $id;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['mod_access'] == 1) {
                echo view('Admin/Transaction/moneyreceipt', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method transaction view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function read($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $transactionTab = DB()->table('transaction');
            $data['trans'] = $transactionTab->where('trans_id', $id)->get()->getRow();
            $data['transactionId'] = $id;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Transaction/read', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method store salary receipt transaction
     * @param int $id
     * @return RedirectResponse|void
     */
    public function salaryreceipt($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $shopTab = DB()->table('shops');
            $data['shops'] = $shopTab->where('sch_id', $shopId)->get()->getResult();
            $data['transactionId'] = $id;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['mod_access'] == 1) {
                echo view('Admin/Transaction/salaryreceipt', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    public function transaction_flow($trans_id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['flow'] = DB()->table('transaction_entries')
                ->where('trans_id',$trans_id)
                ->get()
                ->getResult();

            $data['transaction'] = DB()->table('transaction')
                ->where('trans_id',$trans_id)
                ->get()
                ->getRow();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Transaction/transaction_flow', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}