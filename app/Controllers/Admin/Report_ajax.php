<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Report_ajax extends BaseController
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

            // Shop balance
            $shop = DB()->table('shops')->where('sch_id', $shopId)->get()->getRow();
            $cash          = $shop->cash ?? 0;
            $stockAmount   = $shop->stockAmount ?? 0;
            $profit        = $shop->profit ?? 0;
            $expense       = $shop->expense ?? 0;
            $capital       = $shop->capital ?? 0;
            $serviceCharge = $shop->service_charge ?? 0;

            // Employees
            $employee = DB()->table('employee')->where('sch_id', $shopId)->get()->getResult();

            // Accounts (assets & expenses)
            $accountsAssets = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()->getResult();

            $accountsExpenses = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'expenses')
                ->get()->getResult();

            // VAT
            $vatEarn = DB()->table('vat_register')->where('sch_id', $shopId)->get()->getRow()->balance ?? 0;

            // Other data
            $queryBank     = DB()->table('bank')->where('sch_id', $shopId)->get()->getResult();
            $customerData  = DB()->table('customers')->where('sch_id', $shopId)->get()->getResult();
            $loanProData   = DB()->table('loan_provider')->where('sch_id', $shopId)->get()->getResult();
            $supplierData  = DB()->table('suppliers')->where('sch_id', $shopId)->get()->getResult();

            $data = [
                'cash'             => $cash,
                'vatEarn'          => $vatEarn,
                'bankData'         => $queryBank,
                'customerData'     => $customerData,
                'loanProData'      => $loanProData,
                'supplierData'     => $supplierData,
                'capitalcr'        => $capital,
                'expensedata'      => $expense,
                'profit'           => $profit,
                'service_charge'   => $serviceCharge,
                'stockAmount'      => $stockAmount,
                'employee'         => $employee,
                'accountsAssets'   => $accountsAssets,
                'accountsExpenses' => $accountsExpenses,
            ];


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Report/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}