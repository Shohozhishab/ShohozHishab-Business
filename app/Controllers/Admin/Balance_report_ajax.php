<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Balance_report_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Balance_report';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides Balance report view
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


            //shops cash search(start)
            $shopsTab = DB()->table('shops');
            $cash = $shopsTab->where('sch_id', $shopId)->get()->getRow()->cash;
            //shops cash search(start)

            //total bank amount calculet(start)
            $bankCash = 0;
            $bankTab = DB()->table('bank');
            $bankCashsearc = $bankTab->where('sch_id', $shopId)->get()->getResult();
            if (!empty($bankCashsearc)) {
                foreach ($bankCashsearc as $resultBank) {
                    if ($resultBank->balance > 0) {
                        $bankCash += $resultBank->balance;
                    }
                }
            }

            //total bank amount calculet(end)

            //total customer balance calculet (start)
            $customerCash = 0;
            $customersTab = DB()->table('customers');
            $customerCashsearch = $customersTab->where('sch_id', $shopId)->get()->getResult();
            if (!empty($customerCashsearch)) {
                foreach ($customerCashsearch as $resultCus) {
                    if ($resultCus->balance > 0) {
                        $customerCash += $resultCus->balance;
                    }
                }
            }
            //total customer balance calculet (end)

            //total Lone provider balance calculet(start)
            $loan_providerTab = DB()->table('loan_provider');
            $loanPro = $loan_providerTab->where('sch_id', $shopId)->get()->getResult();

            $loanProGetCash = 0;
            foreach ($loanPro as $result) {
                if ($result->balance > 0) {
                    $loanProGetCash += $result->balance;
                }
            }
            //total Lone provider balance calculet(end)


            //total invoice profite calculet (start)
            $invoiceCash = 0;
            $invoiceTab = DB()->table('invoice');
            $invoiceCashsearch = $invoiceTab->where('sch_id', $shopId)->get()->getResult();
            foreach ($invoiceCashsearch as $row) {
                $invoiceCash += $row->profit;
            }

            $productsTab = DB()->table('products');
            $product = $productsTab->where('sch_id', $shopId)->get()->getResult();
            $totalProdPrice = 0;
            foreach ($product as $row) {
                $totalProdPrice += $row->quantity * $row->purchase_price;
            }
            //total invoice profite calculet (end)


            //total other sale amount calculet (start)
            $otherSaleCash = 0;
            $ledger_other_salesTab = DB()->table('ledger_other_sales');
            $otherSaleCashSear = $ledger_other_salesTab->where('sch_id', $shopId)->get()->getResult();
            foreach ($otherSaleCashSear as $row) {
                $otherSaleCash += $row->amount;
            }
            //total other sale amount calculet (end)


            //Total balance calculet (start)
            $totalGetCash = $customerCash + $loanProGetCash;
            $totalCash = $cash + $bankCash + $totalGetCash + $totalProdPrice;
            //Total balance calculet (end)


            //total employee Balance (start)
            $employeeBalan = 0;
            $employeeTab = DB()->table('employee');
            $employeeBalanSear = $employeeTab->where('sch_id', $shopId)->get()->getResult();
            foreach ($employeeBalanSear as $result) {
                $employeeBalan += $result->balance;
            }
            //total employee Balance (end)


            //total supplier due balance calculet (start)
            $suppliersTab = DB()->table('suppliers');
            $suppl = $suppliersTab->where('sch_id', $shopId)->get()->getResult();
            $suppCash = 0;
            foreach ($suppl as $result) {
                if ($result->balance < 0) {
                    $suppCash -= $result->balance;
                }
            }
            //total supplier due balance calculet (end)


            // capital
            $shopsCapTab = DB()->table('shops');
            $capital = $shopsCapTab->where('sch_id', $shopId)->get()->getRow()->capital;
            $capitalCredit = 0;
            if ($capital < 0) {
                $capitalCredit -= $capital;
            }
            // capital

            //total Lone due Cash calculet (start)
            $loanProCash = 0;
            foreach ($loanPro as $result) {
                if ($result->balance < 0) {
                    $loanProCash -= $result->balance;
                }
            }

            //total customer due Cash calculate (start)
            $cusCash = 0;
            $customersTab = DB()->table('customers');
            $cusSearch = $customersTab->where('sch_id', $shopId)->get()->getResult();
            if (!empty($cusSearch)) {
                foreach ($cusSearch as $rowCus) {
                    if ($rowCus->balance < 0) {
                        $cusCash -= $rowCus->balance;
                    }
                }
            }
            //total customer due Cash calculate (start)

            $totalDueCash = $suppCash + $loanProCash + $cusCash;
            //total Lone due Cash calculet (end)


            //total expens amount calculet (start)
            $ledger_expenseTab = DB()->table('ledger_expense');
            $expenseCash = $ledger_expenseTab->selectSum('amount')->where('sch_id', $shopId)->get()->getRow()->amount;
            //total expens amount calculet (end)


            //total return sale return profit(start)
            $return_saleTab = DB()->table('return_sale');
            $returnProfit = $return_saleTab->selectSum('rtn_profit')->where('sch_id', $shopId)->get()->getRow()->rtn_profit;
            //total return sale return profit(start)

            // Total vat earning (start)
            $vat_registerTab = DB()->table('vat_register');
            $vatEarning = $vat_registerTab->where('sch_id', $shopId)->get()->getRow()->balance;
            // Total vat earning (end)


            //Total due balance calculet (start)
            $totalDue = $totalDueCash + $capitalCredit;
            $totalBalance = $totalCash - $totalDue;
            //Total due balance calculet (end)

            $invoice_itemTab = DB()->table('invoice_item');
            $saleprofit = $invoice_itemTab->selectSum('profit')->where('sch_id', $shopId)->get()->getRow()->profit;

            $data = array(
                'cash' => $cash,
                'bankCash' => $bankCash,
                'totalCash' => $totalCash,
                'invoiceCash' => $invoiceCash,
                'otherSaleCash' => $otherSaleCash,
                'totalProdPrice' => $totalProdPrice,
                'totalDue' => $totalDue,
                'totalGetCash' => $totalGetCash,
                'expenseCash' => $expenseCash,
                'totalDueCash' => $totalDueCash,
                'loanProGetCash' => $loanProGetCash,
                'totalBalance' => $totalBalance,
                'employeeBalan' => $employeeBalan,
                'returnProfit' => $returnProfit,
                'vatEarning' => $vatEarning,
                'capital' => $capitalCredit,
                'profit' => $saleprofit,
            );


            $data['menu'] = view('Admin/menu_report');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Balance_report/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}