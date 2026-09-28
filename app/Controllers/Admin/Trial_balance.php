<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Trial_balance extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Trial_balance';

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
        $role_id    = $this->session->role;

        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        }

        $shopId = $this->session->shopId;

        // ------------------------------------------------------------------
        // Shop balances
        // ------------------------------------------------------------------
        $shop = DB()->table('shops')
            ->where('sch_id', $shopId)
            ->get()
            ->getRow();

        $cash            = $shop->cash            ?? 0;
        $purchaseBalance = $shop->purchase_balance ?? 0;
        $saleBalance     = $shop->sale_balance     ?? 0;
        $expense         = $shop->expense          ?? 0;
        $capital         = $shop->capital          ?? 0;
        $serviceCharge   = $shop->service_charge   ?? 0;

        $stockAmount = 0;
        $profit      = 0;

        // ------------------------------------------------------------------
        // Bank
        // ------------------------------------------------------------------
        $bankData = DB()->table('bank')
            ->where('sch_id', $shopId)
            ->get()
            ->getResult();

        $bankCash = DB()->table('bank')
            ->selectSum('balance')
            ->where('sch_id', $shopId)
            ->get()
            ->getRow()
            ->balance ?? 0;

        // ------------------------------------------------------------------
        // Customers (debit = positive balance, credit = negative balance)
        // ------------------------------------------------------------------
        $customerCash = DB()->table('customers')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance >' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $customerCash = max(0, $customerCash);

        $custCredit = DB()->table('customers')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance <' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $custCredit = min(0, $custCredit);

        // ------------------------------------------------------------------
        // Loan providers
        // ------------------------------------------------------------------
        $loanCash = DB()->table('loan_provider')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance >' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $loanCash = max(0, $loanCash);

        $loanCredit = DB()->table('loan_provider')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance <' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $loanCredit = min(0, $loanCredit);

        // ------------------------------------------------------------------
        // Suppliers
        // ------------------------------------------------------------------
        $supplierCash = DB()->table('suppliers')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance >' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $supplierCash = max(0, $supplierCash);

        $supplierCredit = DB()->table('suppliers')
            ->selectSum('balance')
            ->where(['sch_id' => $shopId, 'balance <' => 0])
            ->get()
            ->getRow()
            ->balance ?? 0;
        $supplierCredit = min(0, $supplierCredit);

        // ------------------------------------------------------------------
        // Employees
        // ------------------------------------------------------------------
        $emplBal = DB()->table('employee')
            ->selectSum('balance')
            ->where('sch_id', $shopId)
            ->get()
            ->getRow()
            ->balance ?? 0;

        $employee = DB()->table('employee')
            ->where('sch_id', $shopId)
            ->get()
            ->getResult();

        // ------------------------------------------------------------------
        // Accounts – Assets
        // ------------------------------------------------------------------
        $accountsAssets = DB()->table('accounts')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'assets')
            ->get()
            ->getResult();

        $assetsBal = DB()->table('accounts')
            ->selectSum('accounts.balance')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'assets')
            ->get()
            ->getRow()
            ->balance ?? 0;
        $assets = max(0, $assetsBal);

        // ------------------------------------------------------------------
        // Accounts – Expenses
        // ------------------------------------------------------------------
        $accountsExpenses = DB()->table('accounts')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'expenses')
            ->get()
            ->getResult();

        $expensesBal = DB()->table('accounts')
            ->selectSum('accounts.balance')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'expenses')
            ->get()
            ->getRow()
            ->balance ?? 0;
        $expenses = max(0, $expensesBal);

        // ------------------------------------------------------------------
        // Accounts – Other Income
        // ------------------------------------------------------------------
        $otherIncomeData = DB()->table('accounts')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'other_income')
            ->get()
            ->getResult();

        $otherIncome = DB()->table('accounts')
            ->selectSum('accounts.balance')
            ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
            ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
            ->where('accounts.sch_id', $shopId)
            ->where('account_type.type_key', 'other_income')
            ->get()
            ->getRow()
            ->balance ?? 0;

        // ------------------------------------------------------------------
        // VAT
        // ------------------------------------------------------------------
        $vatEarn = DB()->table('vat_register')
            ->where('sch_id', $shopId)
            ->get()
            ->getRow()
            ->balance ?? 0;

        // ------------------------------------------------------------------
        // Totals
        // ------------------------------------------------------------------
        $totalDue   = $customerCash + $loanCash + $supplierCash + $assets + $expenses;
        $totalDebit = $totalDue + $cash + $bankCash + $emplBal + $purchaseBalance;

        $totalAmo    = $custCredit + $loanCredit + $supplierCredit;
        $totalCredit = $totalAmo + $capital + $vatEarn + $serviceCharge + $otherIncome + $saleBalance;

        // ------------------------------------------------------------------
        // Detail lists (for the view)
        // ------------------------------------------------------------------
        $customerData  = DB()->table('customers')->where('sch_id', $shopId)->get()->getResult();
        $loanProData   = DB()->table('loan_provider')->where('sch_id', $shopId)->get()->getResult();
        $supplierData  = DB()->table('suppliers')->where('sch_id', $shopId)->get()->getResult();

        // ------------------------------------------------------------------
        // View data
        // ------------------------------------------------------------------
        $data = [
            'allDue'          => $totalDue,
            'cash'            => $cash,
            'bankCash'        => $bankCash,
            'bankData'        => $bankData,
            'totalDebit'      => $totalDebit,
            'totalAmo'        => $totalAmo,
            'vatEarn'         => $vatEarn,
            'totalCredit'     => $totalCredit,
            'customerData'    => $customerData,
            'loanProData'     => $loanProData,
            'supplierData'    => $supplierData,
            'capitalcr'       => $capital,
            'expensedata'     => $expense,
            'profit'          => $profit,
            'service_charge'  => $serviceCharge,
            'stockAmount'     => $stockAmount,
            'employee'        => $employee,
            'accountsAssets'  => $accountsAssets,
            'accountsExpenses'=> $accountsExpenses,
            'otherIncome'     => $otherIncomeData,
            'saleBalance'     => $saleBalance,
            'purchaseBalance' => $purchaseBalance,
        ];

        // Permissions
        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        echo view('Admin/header');
        echo view('Admin/sidebar');

        if (isset($data['mod_access']) && $data['mod_access'] == 1) {
            echo view('Admin/Trial_balance/list', $data);
        } else {
            echo view('no_permission');
        }

        echo view('Admin/footer');
    }


}