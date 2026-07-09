<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Report extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Report';

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides trial balance view
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


            // all debit (start)

            // shop balance(start)
            $shopDeTab = DB()->table('shops');
            $queryCash = $shopDeTab->where('sch_id', $shopId)->get();
            if (!empty($queryCash->getRow()->cash)) {
                $cash = $queryCash->getRow()->cash;
            } else {
                $cash = 0;
            }
            // $purchasePri = $queryCash->row()->purchase_balance;

            $stockAmount = $queryCash->getRow()->stockAmount;
            $profit = $queryCash->getRow()->profit;
            // shop balance(end)


            // expence
            $shopExTab = DB()->table('shops');
            $expensequ = $shopExTab->where('sch_id', $shopId)->get();
            $expense = $expensequ->getRow()->expense;
            // expence





            // employe balance calculet(start)
            $emplTab2 = DB()->table('employee');
            $employee = $emplTab2->where('sch_id', $shopId)->get()->getResult();
            // employe balance calculet(start)




            $accountsAssets = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()
                ->getResult();



            $accountsExpenses = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'expenses')
                ->get()
                ->getResult();







            // vat amount(start)
            $vat_registerTable = DB()->table('vat_register');
            $vatEarn = $vat_registerTable->where('sch_id', $shopId)->get()->getRow()->balance;
            // vat amount(end)

            // capital
            $shopsTable2 = DB()->table('shops');
            $capital = $shopsTable2->where('sch_id', $shopId)->get()->getRow()->capital;
            $capitalCredit = 0;
            if ($capital > 0) {
                $capitalCredit = $capital;
            }
            // capital

            //service charge
            $shopsTable = DB()->table('shops');
            $serviceCharge = $shopsTable->where('sch_id', $shopId)->get()->getRow()->service_charge;
            //service charge

            // bank balance(start)
            $bankTab = DB()->table('bank');
            $queryBank = $bankTab->where('sch_id', $shopId)->get()->getResult();

            $customersTable = DB()->table('customers');
            $customerData = $customersTable->where('sch_id', $shopId)->get()->getResult();
            $loan_providerTable = DB()->table('loan_provider');
            $loanProData = $loan_providerTable->where('sch_id', $shopId)->get()->getResult();
            $suppliersTable2 = DB()->table('suppliers');
            $supplierData = $suppliersTable2->where('sch_id', $shopId)->get()->getResult();


            $data = array(
                'cash' => $cash,
                'vatEarn' => $vatEarn,
                'bankData' => $queryBank,
                'customerData' => $customerData,
                'loanProData' => $loanProData,
                'supplierData' => $supplierData,
                'capitalcr' => $capital,
                'expensedata' => $expense,
                'profit' => $profit,
                'service_charge' => $serviceCharge,
                'stockAmount' => $stockAmount,
                'employee' => $employee,
                'accountsAssets' => $accountsAssets,
                'accountsExpenses' => $accountsExpenses,

            );


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Report/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}