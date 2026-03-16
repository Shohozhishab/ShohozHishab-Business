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
            $data['transaction_data'] = $transactionTable->where('sch_id', $shopId)->get()->getResult();


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


}