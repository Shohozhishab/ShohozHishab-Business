<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Trial_balance_ajax extends BaseController
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


            // bank balance(start)
            $bankTab = DB()->table('bank');
            $queryBank = $bankTab->where('sch_id', $shopId)->get()->getResult();
            $bankBlTab = DB()->table('bank');
            $bankCash = $bankBlTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
            // bank balance(end)


            //total customer balance calculet (start)
            $whereCluseDr = array('sch_id' => $shopId, 'balance > ' => 0);
            $cusTab = DB()->table('customers');
            $cusCash = $cusTab->selectSum('balance')->where($whereCluseDr)->get()->getRow()->balance;
            $customerCash = 0;
            if ($cusCash > 0) {
                $customerCash = $cusCash;
            }
            //total customer balance calculet (end)


            //total Lone provider balance calculet(start)
            $whereCluseDr = array('sch_id' => $shopId, 'balance > ' => 0);
            $lonPrTab = DB()->table('loan_provider');
            $loanProCash = $lonPrTab->selectSum('balance')->where($whereCluseDr)->get()->getRow()->balance;
            $loanCash = 0;
            if ($loanProCash > 0) {
                $loanCash = $loanProCash;
            }
            //total Lone provider balance calculet(end)


            // employe balance calculet(start)
            $emplTab = DB()->table('employee');
            $emplBal = $emplTab->selectSum('balance')->where('sch_id', $shopId)->get()->getRow()->balance;
            $emplTab2 = DB()->table('employee');
            $employee = $emplTab2->where('sch_id', $shopId)->get()->getResult();
            // employe balance calculet(start)


            //total supplier due balance calculet (start)
            //$supCash = $this->db->select_sum('balance')->from('suppliers')->where('sch_id',$shopId)->get()->row()->balance;
            $whereCluseDr = array('sch_id' => $shopId, 'balance > ' => 0);
            $supTab = DB()->table('suppliers');
            $supCash = $supTab->selectSum('balance')->where($whereCluseDr)->get()->getRow()->balance;
            $supplierCash = 0;
            if ($supCash > 0) {
                $supplierCash = $supCash;
            }
            //total supplier due balance calculet (end)

            $totalDue = $customerCash + $loanCash + $supplierCash;


            $totalDebit = $totalDue + $cash + $bankCash + $stockAmount + $emplBal + $expense;
            // all debit (end)


            // all Credit(start)
            //total customer balance calculet (start)
            $customersTable = DB()->table('customers');
            $whereCluseCr = array('sch_id' => $shopId, 'balance < ' => 0);
            $queryCus = $customersTable->selectSum('balance')->where($whereCluseCr)->get()->getRow()->balance;
            $custCredit = 0;
            if ($queryCus < 0) {
                $custCredit = $queryCus;
            }
            //total customer balance calculet (end)


            //total Lone provider balance calculet(start)
            $whereCluseCr = array('sch_id' => $shopId, 'balance < ' => 0);
            $loan_providerTable = DB()->table('loan_provider');
            $queryLon = $loan_providerTable->selectSum('balance')->where($whereCluseCr)->get()->getRow()->balance;
            $loanCredit = 0;
            if ($queryLon < 0) {
                $loanCredit = $queryLon;
            }
            //total Lone provider balance calculet(end)


            //total supplier due balance calculet (start)
            $whereCluseCr = array('sch_id' => $shopId, 'balance < ' => 0);
            $suppliersTable = DB()->table('suppliers');
            $querySupp = $suppliersTable->selectSum('balance')->where($whereCluseCr)->get()->getRow()->balance;
            $supplierCredit = 0;
            if ($querySupp < 0) {
                $supplierCredit = $querySupp;
            }
            //total supplier due balance calculet (end)

            $totalAmo = $custCredit + $loanCredit + $supplierCredit;

            // sale amount (start)
            //$saleBal = $queryCash->row()->sale_balance;
            // sale amount (end)

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

            $totalCredit = $totalAmo + $capital + $profit + $vatEarn + $serviceCharge;

            // all Credit(end)

            $customersTable = DB()->table('customers');
            $customerData = $customersTable->where('sch_id', $shopId)->get()->getResult();
            $loan_providerTable = DB()->table('loan_provider');
            $loanProData = $loan_providerTable->where('sch_id', $shopId)->get()->getResult();
            $suppliersTable2 = DB()->table('suppliers');
            $supplierData = $suppliersTable2->where('sch_id', $shopId)->get()->getResult();


            $data = array(
                'allDue' => $totalDue,
                'cash' => $cash,
                'bankCash' => $bankCash,
                'bankData' => $queryBank,
                'totalDebit' => $totalDebit,
                'totalAmo' => $totalAmo,
                'vatEarn' => $vatEarn,
                'totalCredit' => $totalCredit,
                'customerData' => $customerData,
                'loanProData' => $loanProData,
                'supplierData' => $supplierData,
                'capitalcr' => $capital,
                'expensedata' => $expense,
                'profit' => $profit,
                'service_charge' => $serviceCharge,
                'stockAmount' => $stockAmount,
                'employee' => $employee

            );


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Trial_balance/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}