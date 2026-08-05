<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Libraries\TransactionLog;
use CodeIgniter\HTTP\RedirectResponse;


class Transaction extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $transactionLog;
    private $module_name = 'Transaction';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->transactionLog = new TransactionLog();
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
                $transactionTable->where('date >=', $start_date . ' 00:00:00');
            }
            if ($end_date) {
                $transactionTable->where('date <=', $end_date . ' 23:59:59');
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Transaction/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Transaction/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store transaction
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['date'] = $this->request->getPost('date');
        $data['name'] = $this->request->getPost('name');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {


            $TransactionTable = DB()->table('Transaction');
            if ($TransactionTable->insert($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Your transaction is successful  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method store customer transaction
     * @return void
     */
    public function customer_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $trangactionType = $this->request->getPost('trangaction_type');
        $paymentType = $this->request->getPost('payment_type');
        $amount = str_replace(',', '', $this->request->getPost('amount'));
        $custId = $this->request->getPost('customer_id');
        //customer rest balance
        $custBalance = get_data_by_id('balance', 'customers', 'customer_id', $custId);

        //Ledger Nagodan
        $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);


        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = $this->request->getPost('chequeAmount');
        $sms = $this->request->getPost('sms');


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }


        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);

            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        $shopCheck = check_shop('customers', 'customer_id', $custId);

        if ($shopCheck == 1) {
            if ($trangactionType == 1) {
                if ($availableBalance == true) {

                    DB()->transStart();

                    $custBalance2 = get_data_by_id('balance', 'customers', 'customer_id', $custId);
                    $custRestBalan2 = $custBalance2 + $amount;

                    //insert Transaction in transaction table (start)
                    $transdata = array(
                        'sch_id' => $shopId,
                        'customer_id' => $custId,
                        'title' => $this->request->getPost('particulars'),
                        'date' => $this->request->getPost('date'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $transactionTab = DB()->table('transaction');
                    $transactionTab->insert($transdata);
                    $transId2 = DB()->insertID();
                    //insert Transaction in transaction table (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'         => $transId2,
                        'createdDtm'       => date('Y-m-d H:i:s')
                    ]);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $transId2, $transId2, $amount);
                    //insert log (end)


                    // transaction amount calculet to customer balance and update customer balance (start)
                    $dataCustBlan2 = array(
                        'balance' => $custRestBalan2,
                        'updatedBy' => $userId,
                    );
                    $customersTab = DB()->table('customers');
                    $customersTab->where('customer_id', $custId)->update($dataCustBlan2);
                    // transaction amount calculet to customer balance and update customer balance (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('customers', $custId, $transId2, $amount);
                    //insert log (end)


                    //insert transaction in ledger Transaction table (start)
                    $cusLedgdata2 = array(
                        'sch_id' => $shopId,
                        'trans_id' => $transId2,
                        'customer_id' => $custId,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $custRestBalan2,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger');
                    $ledgerTab->insert($cusLedgdata2);
                    $ledg_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($transId2, $ledg_id, 'ledger', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger', $ledg_id, $transId2, $amount);
                    //insert log (end)

                    //transaction payment amount payment cash or bank(start)
                    //Ledger Nagodan
                    $shopsBalance2 = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                    $shopRestBalan2 = $shopsBalance2 - $amount;

                    if ($paymentType == 2) {
                        //transaction cash payment calculet cash amount and update cash or create ledger (start)
                        $shopedata2 = array(
                            'sch_id' => $shopId,
                            'trans_id' => $transId2,
                            'particulars' => $this->request->getPost('particulars'),
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $shopRestBalan2,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTab = DB()->table('ledger_nagodan');
                        $ledger_nagodanTab->insert($shopedata2);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId2, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $transId2, $amount);
                        //insert log (end)

                        //update shops balance
                        $shopeupdatedata2 = array(
                            'cash' => $shopRestBalan2,
                            'updatedBy' => $userId,
                        );
                        $shopsTab = DB()->table('shops');
                        $shopsTab->where('sch_id', $shopId)->update($shopeupdatedata2);
                        //transaction cash payment calculet cash amount and update cash or create ledger (end)

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $transId2, $amount);
                        //insert log (end)

                    } else {
                        //transaction bank payment calculet bank amount and update bank or create ledger bank (start)
                        $bankId = $this->request->getPost('bank_id');
                        $bankRestBalan2 = '';
                        if ($bankId) {
                            $bankCash2 = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                            $bankRestBalan2 = $bankCash2 - $amount;
                        }

                        $lgBankData2 = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $transId2,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankRestBalan2,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTab = DB()->table('ledger_bank');
                        $ledger_bankTab->insert($lgBankData2);
                        $ledgBank_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId2, $ledgBank_id, 'ledger_bank', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transId2, $amount);
                        //insert log (end)

                        //update bank balance
                        $bankData2 = array(
                            'balance' => $bankRestBalan2,
                            'updatedBy' => $userId,
                        );
                        $bankTab = DB()->table('bank');
                        $bankTab->where('bank_id', $bankId)->update($bankData2);
                        //transaction bank payment calculet bank amount and update bank or create ledger bank (end)

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $transId2, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $transId2)->update($tranDataBank);
                    }
                    //transaction payment amount payment cash or bank(end)

                    DB()->transComplete();

                    if (!empty($sms)) {
                        $message = 'Thank you for your transaction.Your transaction amount is-' . $amount;
                        $phone = get_data_by_id('mobile', 'customers', 'customer_id', $custId);
                        send_sms($phone, $message);
                    }

                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }


            } else {
                if ($chequeNo > 0) {

                    //cheque pay amount calculate and insert cheque tabile(start)
                    $chequeData = array(
                        'sch_id' => $shopId,
                        'chaque_number' => $chequeNo,
                        'to' => $userId,
                        'from' => $custId,
                        'amount' => $chequeAmount,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );

                    $chaqueTab = DB()->table('chaque');
                    $chaqueTab->insert($chequeData);

                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    //cheque pay amount calculate and insert cheque tabile(end)

                } else {

                    $bankId = $this->request->getPost('bank_id');
                    $bankRestBalan = '';
                    if ($bankId) {
                        $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                        $bankRestBalan = $bankCash + $amount;
                    }

//                    if ($custBalance >= $amount) {
                    $custRestBalan = $custBalance - $amount;
                    $shopRestBalan = $shopsBalance + $amount;

                    DB()->transStart();
                    //insert Transaction in transaction table (start)
                    $transdata = array(
                        'sch_id' => $shopId,
                        'customer_id' => $custId,
                        'date' => $this->request->getPost('date'),
                        'title' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );

                    $transactionTable = DB()->table('transaction');
                    $transactionTable->insert($transdata);
                    $transId = DB()->insertID();
                    //insert Transaction in transaction table (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $transId, $transId, $amount);
                    //insert log (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'         => $transId,
                        'createdDtm'       => date('Y-m-d H:i:s')
                    ]);


                    // transaction amount calculet to customer balance and update customer balance (start)
                    $dataCustBlan = array(
                        'balance' => $custRestBalan,
                        'updatedBy' => $userId,
                    );
                    $customersTable = DB()->table('customers');
                    $customersTable->where('customer_id', $custId)->update($dataCustBlan);
                    // transaction amount calculet to customer balance and update customer balance (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('customers', $custId, $transId, $amount);
                    //insert log (end)


                    //insert transaction in ledger Transaction table (start)
                    $cusLedgdata = array(
                        'sch_id' => $shopId,
                        'trans_id' => $transId,
                        'customer_id' => $custId,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => $custRestBalan,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTable = DB()->table('ledger');
                    $ledgerTable->insert($cusLedgdata);
                    $ledg_id = DB()->insertID();
                    //insert transaction in ledger Transaction table (end)

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($transId, $ledg_id, 'ledger', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger', $ledg_id, $transId, $amount);
                    //insert log (end)


                    //transaction payment amount payment cash or bank(start)
                    if ($paymentType == 2) {
                        //transaction cash payment calculet cash amount and update cash or create ledger (start)
                        $shopedata = array(
                            'sch_id' => $shopId,
                            'trans_id' => $transId,
                            'particulars' => $this->request->getPost('particulars'),
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $shopRestBalan,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTable = DB()->table('ledger_nagodan');
                        $ledger_nagodanTable->insert($shopedata);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledg_nagodan_id, 'ledger_nagodan', 'Dr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $transId, $amount);
                        //insert log (end)

                        //update shops balance
                        $shopeupdatedata = array(
                            'cash' => $shopRestBalan,
                            'updatedBy' => $userId,
                        );
                        $shopsTable = DB()->table('shops');
                        $shopsTable->where('sch_id', $shopId)->update($shopeupdatedata);
                        //transaction cash payment calculet cash amount and update cash or create ledger (end)

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $transId, $amount);
                        //insert log (end)
                    } else {
                        //transaction bank payment calculet bank amount and update bank or create ledger bank (start)
                        $lgBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $transId,
                            'trangaction_type' => 'Dr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankRestBalan,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTable = DB()->table('ledger_bank');
                        $ledger_bankTable->insert($lgBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transId, $amount);
                        //insert log (end)

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledgBank_id, 'ledger_bank', 'Dr.');

                        //update bank balance
                        $bankData = array(
                            'balance' => $bankRestBalan,
                            'updatedBy' => $userId,
                        );
                        $bankTable = DB()->table('bank');
                        $bankTable->where('bank_id', $bankId)->update($bankData);
                        //transaction bank payment calculet bank amount and update bank or create ledger bank (end)

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $transId, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $transId)->update($tranDataBank);
                    }
                    //transaction payment amount payment cash or bank(end)

                    DB()->transComplete();

                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                }
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid customer<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method customer ledger
     * @return void
     */
    public function custData()
    {

        $custData = $this->request->getPost('custId');

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
            ->where("customer_id", $custData);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("customer_id", $custData);
        $table->orderBy('ledg_id', 'DESC')->limit(10);
        $data = $table->get()->getResult();

        $customerBalance = get_data_by_id('balance', 'customers', 'customer_id', $custData);

        $view = '<span class="pull-right"> Balance: ' . showWithCurrencySymbol($customerBalance) . '</span>';
        $view .= '<table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Pay due" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);

            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($row->r_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody></table>';

        print $view;
    }

    /**
     * @description This method store supplier transaction
     * @return void
     */
    public function supplier_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = str_replace(',', '', $this->request->getPost('chequeAmount'));
        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //Supplier Balance
        $supplierId = $this->request->getPost('supplier_id');
        $supplierBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $this->request->getPost('supplier_id'));

        //Payment Type
        $paymentType = $this->request->getPost('payment_type');
        $trangactionType = $this->request->getPost('trangaction_type');
        $sms = $this->request->getPost('sms');
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);


        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');

            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);

            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }


        $shopCheck = check_shop('suppliers', 'supplier_id', $supplierId);
        if ($shopCheck == 1) {
            if ($availableBalance == true) {
                $restBalance = $supplierBalance + $amount;
                $shopUpdateBalance = $shopBalance - $amount;

                DB()->transStart();

                if ($trangactionType == 1) {
                    //insert Transaction table
                    $transdata = array(
                        'sch_id' => $shopId,
                        'date' => $this->request->getPost('date'),
                        'supplier_id' => $this->request->getPost('supplier_id'),
                        'title' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $transactionTab = DB()->table('transaction');
                    $transactionTab->insert($transdata);
                    $ledgSupId = DB()->insertID();

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $ledgSupId, $ledgSupId, $amount);
                    //insert log (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'         => $ledgSupId,
                        'createdDtm'       => date('Y-m-d H:i:s')
                    ]);


                    //insert data
                    $data = array(
                        'sch_id' => $shopId,
                        'trans_id' => $ledgSupId,
                        'supplier_id' => $this->request->getPost('supplier_id'),
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $restBalance,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($data);
                    $ledg_sup_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($ledgSupId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, $ledgSupId, $amount);
                    //insert log (end)

                    //Suppliers Balance Update
                    $dataSuppBlan = array(
                        'balance' => $restBalance,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($dataSuppBlan);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers', $supplierId, $ledgSupId, $amount);
                    //insert log (end)

                    //admin transaction
                    if ($paymentType == 2) {
                        //shop balance update
                        $shopData = array(
                            'cash' => $shopUpdateBalance,
                            'updatedBy' => $userId,
                        );
                        $shopsTab = DB()->table('shops');
                        $shopsTab->where('sch_id', $shopId)->update($shopData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $ledgSupId, $amount);
                        //insert log (end)

                        //insert ledger_nagodan
                        $lgNagData = array(
                            'sch_id' => $shopId,
                            'trans_id' => $ledgSupId,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $shopUpdateBalance,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTab = DB()->table('ledger_nagodan');
                        $ledger_nagodanTab->insert($lgNagData);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($ledgSupId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgSupId, $amount);
                        //insert log (end)

                    } else {
                        $bankData = array(
                            'balance' => $bankUpData,
                            'updatedBy' => $userId,
                        );
                        $bankTab = DB()->table('bank');
                        $bankTab->where('bank_id', $bankId)->update($bankData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $ledgSupId, $amount);
                        //insert log (end)

                        //insert ledger_bank
                        $lgBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $ledgSupId,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankUpData,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTab = DB()->table('ledger_bank');
                        $ledger_bankTab->insert($lgBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($ledgSupId, $ledgBank_id, 'ledger_bank', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $ledgSupId, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $ledgSupId)->update($tranDataBank);

                    }
                } else {
                    if ($chequeNo > 0) {

                        //cheque pay amount calculate and insert cheque tabile(start)
                        $chequeData = array(
                            'sch_id' => $shopId,
                            'chaque_number' => $chequeNo,
                            'to' => $userId,
                            'from' => $supplierId,
                            'amount' => $chequeAmount,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );

                        $chaqueTab = DB()->table('chaque');
                        $chaqueTab->insert($chequeData);

                        print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        //cheque pay amount calculate and insert cheque tabile(end)

                    } else {

                        //insert Transaction table
                        $transdata = array(
                            'sch_id' => $shopId,
                            'supplier_id' => $supplierId,
                            'date' => $this->request->getPost('date'),
                            'title' => $this->request->getPost('particulars'),
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $transactionTab = DB()->table('transaction');
                        $transactionTab->insert($transdata);
                        $ledgSupId2 = DB()->insertID();


                        //insert log (start)
                        $this->transactionLog->insert_log_data('transaction', $ledgSupId2, $ledgSupId2, $amount);
                        //insert log (end)

                        // transaction events insert;
                        DB()->table('transaction_events')->insert([
                            'sch_id' => $shopId,
                            'trans_id'         => $ledgSupId2,
                            'createdDtm'       => date('Y-m-d H:i:s')
                        ]);

                        //insert data
                        $supplierBalance2 = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                        $restBalance2 = $supplierBalance2 - $amount;
                        $data2 = array(
                            'sch_id' => $shopId,
                            'trans_id' => $ledgSupId2,
                            'supplier_id' => $supplierId,
                            'particulars' => $this->request->getPost('particulars'),
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $restBalance2,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_suppliersTab = DB()->table('ledger_suppliers');
                        $ledger_suppliersTab->insert($data2);
                        $ledg_sup_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($ledgSupId2, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_suppliers', $ledg_sup_id, $ledgSupId2, $amount);
                        //insert log (end)

                        //Suppliers Balance Update(start)
                        $dataSuppBlan2 = array(
                            'balance' => $restBalance2,
                            'updatedBy' => $userId,
                        );
                        $suppliersTab = DB()->table('suppliers');
                        $suppliersTab->where('supplier_id', $supplierId)->update($dataSuppBlan2);
                        //Suppliers Balance Update(start)

                        //insert log (start)
                        $this->transactionLog->insert_log_data('suppliers', $supplierId, $ledgSupId2, $amount);
                        //insert log (end)

                        //admin transaction
                        if ($paymentType == 2) {
                            //shop balance update
                            $shopBalance2 = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                            $shopUpdateBalance2 = $shopBalance2 + $amount;
                            $shopData2 = array(
                                'cash' => $shopUpdateBalance2,
                                'updatedBy' => $userId,
                            );
                            $shopsTab = DB()->table('shops');
                            $shopsTab->where('sch_id', $shopId)->update($shopData2);

                            //insert log (start)
                            $this->transactionLog->insert_log_data('shops', $shopId, $ledgSupId2, $amount);
                            //insert log (end)

                            //insert ledger_nagodan
                            $lgNagData2 = array(
                                'sch_id' => $shopId,
                                'trans_id' => $ledgSupId2,
                                'trangaction_type' => 'Dr.',
                                'particulars' => $this->request->getPost('particulars'),
                                'amount' => $amount,
                                'rest_balance' => $shopUpdateBalance2,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_nagodanTab = DB()->table('ledger_nagodan');
                            $ledger_nagodanTab->insert($lgNagData2);
                            $ledg_nagodan_id = DB()->insertID();


                            //insert transaction in ledger Transaction table (end)
                            $this->transaction_entries($ledgSupId2, $ledg_nagodan_id, 'ledger_nagodan', 'Dr.');

                            //insert log (start)
                            $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgSupId2, $amount);
                            //insert log (end)

                        } else {
                            if ($bankId) {
                                $bankCash2 = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                                $bankUpData2 = $bankCash2 + $amount;
                            }

                            $bankData2 = array(
                                'balance' => $bankUpData2,
                                'updatedBy' => $userId,
                            );
                            $bankTab = DB()->table('bank');
                            $bankTab->where('bank_id', $bankId)->update($bankData2);

                            //insert log (start)
                            $this->transactionLog->insert_log_data('bank', $bankId, $ledgSupId2, $amount);
                            //insert log (end)

                            //insert ledger_bank
                            $lgBankData2 = array(
                                'sch_id' => $shopId,
                                'bank_id' => $bankId,
                                'trans_id' => $ledgSupId2,
                                'trangaction_type' => 'Dr.',
                                'particulars' => $this->request->getPost('particulars'),
                                'amount' => $amount,
                                'rest_balance' => $bankUpData2,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_bankTab = DB()->table('ledger_bank');
                            $ledger_bankTab->insert($lgBankData2);
                            $ledgBank_id = DB()->insertID();

                            //insert transaction in ledger Transaction table (end)
                            $this->transaction_entries($ledgSupId2, $ledgBank_id, 'ledger_bank', 'Dr.');

                            //insert log (start)
                            $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $ledgSupId2, $amount);
                            //insert log (end)


                            //bank id update in transaction table
                            $tranDataBank = array(
                                'bank_id' => $bankId,
                            );
                            $tranBank = DB()->table('transaction');
                            $tranBank->where('trans_id', $ledgSupId2)->update($tranDataBank);

                        }

                    }
                }

                DB()->transComplete();
                if (!empty($sms)) {
                    $message = 'Thank you for your transaction.Your transaction amount is-' . $amount;
                    $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                    send_sms($phone, $message);
                }

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            } else {

                print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid supplier<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method supplier ledger
     * @return void
     */
    public function suppData()
    {
        $suppId = $this->request->getPost('suppId');

        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledg_sup_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_suppliers')
            ->select('ledger_suppliers.*')
            ->select($mBalanceSubquery, false)
            ->where("supplier_id", $suppId); // false prevents CI4 from escaping the raw SQL window function
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("supplier_id", $suppId);
        $table->orderBy('ledg_sup_id', 'DESC')->limit(10);
        $data = $table->get()->getResult();

        $suppliersBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $suppId);

        $view = '<span class="pull-right"> Balance: ' . showWithCurrencySymbol($suppliersBalance) . '</span>';


        $view .= '<table class="table table-bordered table-striped" id="TFtable" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Purchase" : $row->particulars;
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);

            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . get_data_by_id('name', 'suppliers', 'supplier_id', $row->supplier_id) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($row->r_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Particulars</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';


        print $view;
    }

    /**
     * @description This method store Loan Provider transaction
     * @return void
     */
    public function loan_pro_transaction_action()
    {

        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        $loanProId = $this->request->getPost('loan_pro_id');
        //loan_pro Balance
        $loanProBalance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loanProId);

        //Payment Type
        $paymentType = $this->request->getPost('payment_type');
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);


        $trangactionType = $this->request->getPost('trangaction_type');

        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = $this->request->getPost('chequeAmount');
        $sms = $this->request->getPost('sms');

        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
                $bankUpDataCr = $bankCash + $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);
            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }


        $shopCheck = check_shop('loan_provider', 'loan_pro_id', $loanProId);
        if ($shopCheck == 1) {
            if ($chequeNo > 0) {

                //cheque pay amount calculate and insert cheque tabile(start)
                $chequeData = array(
                    'sch_id' => $shopId,
                    'chaque_number' => $chequeNo,
                    'to' => $userId,
                    'from_loan_provider' => $loanProId,
                    'amount' => $chequeAmount,
                    'createdDtm' => date('Y-m-d h:i:s')
                );

                $chaquetab = DB()->table('chaque');
                $chaquetab->insert($chequeData);

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                //cheque pay amount calculate and insert cheque tabile(end)

            } else {


                //Cr.
                if ($trangactionType == 2) {
                    $restBalanceDr = $loanProBalance - $amount;
                    $shopUpdateBalanceCr = $shopBalance + $amount;
                    DB()->transStart();

                    //insert Transaction table
                    $transdata = array(
                        'sch_id' => $shopId,
                        'loan_pro_id' => $loanProId,
                        'date' => $this->request->getPost('date'),
                        'title' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $transaction = DB()->table('transaction');
                    $transaction->insert($transdata);
                    $transId = DB()->insertID();

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $transId, $transId, $amount);
                    //insert log (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'         => $transId,
                        'createdDtm'       => date('Y-m-d H:i:s')
                    ]);

                    //insert data
                    $data = array(
                        'sch_id' => $shopId,
                        'trans_id' => $transId,
                        'loan_pro_id' => $loanProId,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => $restBalanceDr,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_loanTab = DB()->table('ledger_loan');
                    $ledger_loanTab->insert($data);
                    $ledg_loan_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($transId, $ledg_loan_id, 'ledger_loan', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_loan', $ledg_loan_id, $transId, $amount);
                    //insert log (end)

                    //loan_provider Balance Update
                    $dataLonProBlan = array(
                        'balance' => $restBalanceDr,
                        'updatedBy' => $userId,
                    );
                    $loan_providerTab = DB()->table('loan_provider');
                    $loan_providerTab->where('loan_pro_id', $loanProId)->update($dataLonProBlan);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('loan_provider', $loanProId, $transId, $amount);
                    //insert log (end)

                    //admin transaction
                    if ($paymentType == 2) {
                        //shop balance update
                        $shopData = array(
                            'cash' => $shopUpdateBalanceCr,
                            'updatedBy' => $userId,
                        );
                        $shopsTab = DB()->table('shops');
                        $shopsTab->where('sch_id', $shopId)->update($shopData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $transId, $amount);
                        //insert log (end)

                        //insert ledger_nagodan
                        $lgNagData = array(
                            'sch_id' => $shopId,
                            'trans_id' => $transId,
                            'trangaction_type' => 'Dr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $shopUpdateBalanceCr,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTab = DB()->table('ledger_nagodan');
                        $ledger_nagodanTab->insert($lgNagData);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledg_nagodan_id, 'ledger_nagodan', 'Dr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $transId, $amount);
                        //insert log (end)

                    } else {
                        $bankData = array(
                            'balance' => $bankUpDataCr,
                            'updatedBy' => $userId,
                        );
                        $bankTab = DB()->table('bank');
                        $bankTab->where('bank_id', $bankId)->update($bankData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $transId, $amount);
                        //insert log (end)

                        //insert ledger_bank
                        $lgBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $transId,
                            'trangaction_type' => 'Dr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankUpDataCr,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTab = DB()->table('ledger_bank');
                        $ledger_bankTab->insert($lgBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledgBank_id, 'ledger_bank', 'Dr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transId, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $transId)->update($tranDataBank);

                    }
                    DB()->transComplete();
                    if (!empty($sms)) {
                        $message = 'Thank you for your transaction.Your transaction amount is-' . $amount;
                        $phone = get_data_by_id('phone', 'loan_provider', 'loan_pro_id', $loanProId);
                        send_sms($phone, $message);
                    }
                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }
            //Dr.
            if ($trangactionType == 1) {
                $restBalance = $loanProBalance + $amount;
                $shopUpdateBalance = $shopBalance - $amount;
                if ($availableBalance == true) {
                    DB()->transStart();

                    //insert Transaction table
                    $transdata = array(
                        'sch_id' => $shopId,
                        'loan_pro_id' => $loanProId,
                        'date' => $this->request->getPost('date'),
                        'title' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $transactionTab = DB()->table('transaction');
                    $transactionTab->insert($transdata);
                    $transId = DB()->insertID();

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $transId, $transId, $amount);
                    //insert log (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'     => $transId,
                        'createdDtm'   => date('Y-m-d H:i:s')
                    ]);

                    //insert data
                    $data = array(
                        'sch_id' => $shopId,
                        'trans_id' => $transId,
                        'loan_pro_id' => $loanProId,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $restBalance,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_loanTab = DB()->table('ledger_loan');
                    $ledger_loanTab->insert($data);
                    $ledg_loan_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($transId, $ledg_loan_id, 'ledger_loan', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_loan', $ledg_loan_id, $transId, $amount);
                    //insert log (end)

                    //loan_provider Balance Update
                    $dataLonProBlan = array(
                        'balance' => $restBalance,
                        'updatedBy' => $userId,
                    );
                    $loan_providerTab = DB()->table('loan_provider');
                    $loan_providerTab->where('loan_pro_id', $loanProId)->update($dataLonProBlan);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('loan_provider', $loanProId, $transId, $amount);
                    //insert log (end)

                    //admin transaction
                    if ($paymentType == 2) {
                        //shop balance update
                        $shopData = array(
                            'cash' => $shopUpdateBalance,
                            'updatedBy' => $userId,
                        );
                        $shopsTab = DB()->table('shops');
                        $shopsTab->where('sch_id', $shopId)->update($shopData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $transId, $amount);
                        //insert log (end)

                        //insert ledger_nagodan
                        $lgNagData = array(
                            'sch_id' => $shopId,
                            'trans_id' => $transId,
                            'particulars' => $this->request->getPost('particulars'),
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $shopUpdateBalance,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTab = DB()->table('ledger_nagodan');
                        $ledger_nagodanTab->insert($lgNagData);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $transId, $amount);
                        //insert log (end)

                    } else {
                        $bankData = array(
                            'balance' => $bankUpData,
                            'updatedBy' => $userId,
                        );
                        $bankTab = DB()->table('bank');
                        $bankTab->where('bank_id', $bankId)->update($bankData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $transId, $amount);
                        //insert log (end)

                        //insert ledger_bank
                        $lgBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $transId,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankUpData,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTab = DB()->table('ledger_bank');
                        $ledger_bankTab->insert($lgBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($transId, $ledgBank_id, 'ledger_bank', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transId, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $transId)->update($tranDataBank);

                    }
                    DB()->transComplete();

                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid Account Head<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method Loan Provider ledger
     * @return void
     */
    public function lonProvData()
    {
        $lonProvId = $this->request->getPost('lonProvId');

        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledg_loan_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_loan')
            ->select('ledger_loan.*')
            ->select($mBalanceSubquery, false)
            ->where("loan_pro_id", $lonProvId); // false prevents CI4 from escaping the raw SQL window function
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where("loan_pro_id", $lonProvId);
        $table->orderBy('ledg_loan_id', 'DESC')->limit(10);
        $data = $table->get()->getResult();


        $loanProBalance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $lonProvId);

        $view = '<span class="pull-right"> Balance: ' . showWithCurrencySymbol($loanProBalance) . '</span>';
        $view .= '<table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Loan Provider</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Loan" : $row->particulars;
            $loanProId = get_data_by_id('name', 'loan_provider', "loan_pro_id", $row->loan_pro_id);
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $loanProId . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($row->r_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Loan Provider</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';


        print $view;
    }

    /**
     * @description This method store bank transaction
     * @return void
     */
    public function bank_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $bank_id = $this->request->getPost('bank_id');
        $bank_id2 = $this->request->getPost('bank_id2');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');

        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $bank_id);


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        $firstBankBalance = $bankBalance - $amount;

        $shopCheck = check_shop('bank', 'bank_id', $bank_id);
        if ($shopCheck == 1) {
            if ($bankBalance > $amount) {

                DB()->transStart();

                //insert Transaction table
                $transdata = array(
                    'sch_id' => $shopId,
                    'title' => 'Withdraw',
                    'date' => $this->request->getPost('date'),
                    'bank_id' => $bank_id,
                    'bank_to_id' => $bank_id2,
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $transactionTab = DB()->table('transaction');
                $transactionTab->insert($transdata);
                $transaction = DB()->insertID();

                //insert log (start)
                $this->transactionLog->insert_log_data('transaction', $transaction, $transaction, $amount);
                //insert log (end)

                // transaction events insert;
                DB()->table('transaction_events')->insert([
                    'sch_id' => $shopId,
                    'trans_id'     => $transaction,
                    'createdDtm'   => date('Y-m-d H:i:s')
                ]);

                //bank balance update  (start)
                $firstBankData = array(
                    'balance' => $firstBankBalance,
                    'updatedBy' => $userId,
                );
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $bank_id)->update($firstBankData);
                //bank balance update  (end)

                //insert log (start)
                $this->transactionLog->insert_log_data('bank', $bank_id, $transaction, $amount);
                //insert log (end)

                //Bank ledger create (start)
                $firstLedgerData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bank_id,
                    'trans_id' => $transaction,
                    'particulars' => 'Withdraw',
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'rest_balance' => $firstBankBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->insert($firstLedgerData);
                $ledgBank_id = DB()->insertID();
                //Bank ledgher create (end)

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($transaction, $ledgBank_id, 'ledger_bank', 'Cr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transaction, $amount);
                //insert log (end)


                //2Nd bank balance update (Start)
                $bankBalance2 = get_data_by_id('balance', 'bank', 'bank_id', $bank_id2);
                $lastBankBalance = $bankBalance2 + $amount;

                $lastBankData = array(
                    'balance' => $lastBankBalance,
                    'updatedBy' => $userId,
                );
                $bankTable = DB()->table('bank');
                $bankTable->where('bank_id', $bank_id2)->update($lastBankData);
                //2Nd bank balance update (Start)

                //insert log (start)
                $this->transactionLog->insert_log_data('bank', $bank_id2, $transaction, $amount);
                //insert log (end)

                //Bank ledger create (start)
                $lastLedgerData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bank_id2,
                    'trans_id' => $transaction,
                    'particulars' => $particulars,
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'rest_balance' => $lastBankBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_bankTable = DB()->table('ledger_bank');
                $ledger_bankTable->insert($lastLedgerData);
                $ledgBank_id = DB()->insertID();
                //Bank ledgher create (end)

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($transaction, $ledgBank_id, 'ledger_bank', 'Dr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transaction, $amount);
                //insert log (end)

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid Bank<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method store bank ledger
     * @return void
     */
    public function bankData()
    {
        $bankId = $this->request->getPost('bankId');

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
            ->where("bank_id", $bankId);
        $table->orderBy('ledgBank_id', 'DESC')->limit(10);
        $data = $table->get()->getResult();

        $view = '<table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Bank</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';
        $i = '';
        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Transaction" : $row->particulars;
            $bankId = get_data_by_id('name', 'bank', "bank_id", $row->bank_id);
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : $row->amount;
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : $row->amount;
            $view .= '<tr>
                                    <td>' . ++$i . '</td>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $bankId . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . $row->r_balance . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Bank</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';
        print $view;
    }

    /**
     * @description This method check bank balance
     * @return void
     */
    public function check_bank_balance()
    {

        $amount = $this->request->getPost('balance');
        $bankId = $this->request->getPost('bank_id');


        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
        if ($amount > $bankBalance) {
            print '<span style="color:red">Balance is too low</span>';
        } else {
            print '<span style="color:green">Balance is ok</span>';
        }

    }

    /**
     * @description This method store expense transaction
     * @return void
     */
    public function expense_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //Payment Type
        $accountId = $this->request->getPost('account_id');
        $paymentType = $this->request->getPost('payment_type');
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $exrest = get_data_by_id('expense', 'shops', 'sch_id', $shopId);

        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);
            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        $shopUpdateBalance = $shopBalance - $amount;
        $exRestbalance = $exrest + $amount;

        if ($availableBalance == true) {
            $db = DB();
            DB()->transStart();
            //insert Transaction table
            $transdata = array(
                'sch_id' => $shopId,
                'account_id'=> $accountId,
                'date' => $this->request->getPost('date'),
                'title' => $this->request->getPost('particulars'),
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->insert($transdata);
            $ledgtranId = DB()->insertID();

            //insert log (start)
            $this->transactionLog->insert_log_data('transaction', $ledgtranId, $ledgtranId, $amount);
            //insert log (end)

            // transaction events insert;
            DB()->table('transaction_events')->insert([
                'sch_id' => $shopId,
                'trans_id'     => $ledgtranId,
                'createdDtm'   => date('Y-m-d H:i:s')
            ]);

            $previousBalance = get_data_by_id('balance', 'accounts', 'account_id', $accountId);
            $restBalance = $previousBalance + $amount;

            //Accounts Balance Update
            $datavatBlan = array(
                'balance' => $restBalance,
                'updatedBy' => $userId,
            );
            $db->table('accounts')->where('account_id', $accountId)->update($datavatBlan);
            //insert log (start)
            $this->transactionLog->insert_log_data('accounts', $accountId, $ledgtranId, $amount);
            //insert log (end)

            //insert data ledger accounts
            $data = array(
                'sch_id' => $shopId,
                'trans_id' => $ledgtranId,
                'account_id' => $accountId,
                'particulars' => $this->request->getPost('particulars'),
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'rest_balance' => $restBalance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $db->table('ledger_accounts')->insert($data);
            $ledger_id = $db->insertID();
            //insert transaction in ledger Transaction table (end)
            $this->transaction_entries($ledgtranId, $ledger_id, 'ledger_accounts', 'Dr.');
            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_accounts', $ledger_id, $ledgtranId, $amount);
            //insert log (end)

            //admin transaction
            if ($paymentType == 2) {
                //shop balance update
                $shopData = array(
                    'cash' => $shopUpdateBalance,
                    'updatedBy' => $userId,
                );
                $shopsTable = DB()->table('shops');
                $shopsTable->where('sch_id', $shopId)->update($shopData);

                //insert log (start)
                $this->transactionLog->insert_log_data('shops', $shopId, $ledgtranId, $amount);
                //insert log (end)

                //insert ledger_nagodan
                $lgNagData = array(
                    'sch_id' => $shopId,
                    'trans_id' => $ledgtranId,
                    'particulars' => $this->request->getPost('particulars'),
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'rest_balance' => $shopUpdateBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_nagodanTable = DB()->table('ledger_nagodan');
                $ledger_nagodanTable->insert($lgNagData);
                $ledg_nagodan_id = DB()->insertID();

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($ledgtranId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgtranId, $amount);
                //insert log (end)

            } else {
                $bankData = array(
                    'balance' => $bankUpData,
                    'updatedBy' => $userId,
                );
                $bankTable = DB()->table('bank');
                $bankTable->where('bank_id', $bankId)->update($bankData);

                //insert log (start)
                $this->transactionLog->insert_log_data('bank', $bankId, $ledgtranId, $amount);
                //insert log (end)

                //insert ledger_bank
                $lgBankData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bankId,
                    'trans_id' => $ledgtranId,
                    'particulars' => $this->request->getPost('particulars'),
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'rest_balance' => $bankUpData,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_bankTable = DB()->table('ledger_bank');
                $ledger_bankTable->insert($lgBankData);
                $ledgBank_id = DB()->insertID();

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($ledgtranId, $ledgBank_id, 'ledger_bank', 'Cr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $ledgtranId, $amount);
                //insert log (end)

                //bank id update in transaction table
                $tranDataBank = array(
                    'bank_id' => $bankId,
                );
                $tranBank = DB()->table('transaction');
                $tranBank->where('trans_id', $ledgtranId)->update($tranDataBank);

            }

            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method store other sales transaction
     * @return void
     */
    public function otherSales_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $oldprofit = get_data_by_id('profit', 'shops', 'sch_id', $shopId);


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        $shopUpdateBalance = $shopBalance + $amount;
        $newProfite = $oldprofit - $amount;


        DB()->transStart();
        //insert Transaction table
        $transdata = array(
            'sch_id' => $shopId,
            'date' => $this->request->getPost('date'),
            'title' => $this->request->getPost('particulars'),
            'trangaction_type' => 'Dr.',
            'amount' => $amount,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $transactionTab = DB()->table('transaction');
        $transactionTab->insert($transdata);
        $ledgtranId = DB()->insertID();

        //insert log (start)
        $this->transactionLog->insert_log_data('transaction', $ledgtranId, $ledgtranId, $amount);
        //insert log (end)

        // transaction events insert;
        DB()->table('transaction_events')->insert([
            'sch_id' => $shopId,
            'trans_id'     => $ledgtranId,
            'createdDtm'   => date('Y-m-d H:i:s')
        ]);

        //insert data
        $data = array(
            'sch_id' => $shopId,
            'trans_id' => $ledgtranId,
            'particulars' => $this->request->getPost('particulars'),
            'trangaction_type' => 'Dr.',
            'amount' => $amount,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_other_salesTab = DB()->table('ledger_other_sales');
        $ledger_other_salesTab->insert($data);
        $ledg_oth_sales_id = DB()->insertID();

        //insert transaction in ledger Transaction table (end)
        $this->transaction_entries($ledgtranId, $ledg_oth_sales_id, 'ledger_other_sales', 'Dr.');

        //insert log (start)
        $this->transactionLog->insert_log_data('ledger_other_sales', $ledg_oth_sales_id, $ledgtranId, $amount);
        //insert log (end)

        //shop balance update
        $shopData = array(
            'cash' => $shopUpdateBalance,
            'profit' => $newProfite,
            'updatedBy' => $userId,
        );
        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($shopData);

        //insert log (start)
        $this->transactionLog->insert_log_data('shops', $shopId, $ledgtranId, $amount);
        //insert log (end)

        //insert ledger_nagodan
        $lgNagData = array(
            'sch_id' => $shopId,
            'trans_id' => $ledgtranId,
            'particulars' => $this->request->getPost('particulars'),
            'trangaction_type' => 'Dr.',
            'amount' => $amount,
            'rest_balance' => $shopUpdateBalance,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_nagodanTab = DB()->table('ledger_nagodan');
        $ledger_nagodanTab->insert($lgNagData);
        $ledg_nagodan_id = DB()->insertID();

        //insert transaction in ledger Transaction table (end)
        $this->transaction_entries($ledgtranId, $ledg_nagodan_id, 'ledger_nagodan', 'Dr.');

        //insert log (start)
        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgtranId, $amount);
        //insert log (end)


        DB()->transComplete();

        print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

    }

    /**
     * @description This method store salary employee transaction
     * @return void
     */
    public function salaryEmployee_transaction_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;


        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //Supplier Balance
        $employeeBalance = get_data_by_id('balance', 'employee', 'employee_id', $this->request->getPost('employee_id'));
        $restBalance = $employeeBalance + $amount;
        //Payment Type
        $paymentType = $this->request->getPost('payment_type');
        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $shopUpdateBalance = $shopBalance - $amount;

        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);

            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        $shopCheck = check_shop('employee', 'employee_id', $this->request->getPost('employee_id'));
        if ($shopCheck == 1) {
            if ($availableBalance == true) {

                DB()->transStart();

                //insert Transaction table
                $transdata = array(
                    'sch_id' => $shopId,
                    'date' => $this->request->getPost('date'),
                    'employee_id' => $this->request->getPost('employee_id'),
                    'title' => 'Salary',
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $transactionTab = DB()->table('transaction');
                $transactionTab->insert($transdata);
                $ledgSupId = DB()->insertID();

                //insert log (start)
                $this->transactionLog->insert_log_data('transaction', $ledgSupId, $ledgSupId, $amount);
                //insert log (end)

                // transaction events insert;
                DB()->table('transaction_events')->insert([
                    'sch_id' => $shopId,
                    'trans_id'     => $ledgSupId,
                    'createdDtm'   => date('Y-m-d H:i:s')
                ]);

                //insert data
                $data = array(
                    'sch_id' => $shopId,
                    'trans_id' => $ledgSupId,
                    'employee_id' => $this->request->getPost('employee_id'),
                    'particulars' => $this->request->getPost('particulars'),
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'rest_balance' => $restBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_employeeTab = DB()->table('ledger_employee');
                $ledger_employeeTab->insert($data);
                $ledg_emp_id = DB()->insertID();

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($ledgSupId, $ledg_emp_id, 'ledger_employee', 'Dr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_employee', $ledg_emp_id, $ledgSupId, $amount);
                //insert log (end)

                //Suppliers Balance Update
                $dataemployeeBlan = array(
                    'balance' => $restBalance,
                    'updatedBy' => $userId,
                );
                $employeeTab = DB()->table('employee');
                $employeeTab->where('employee_id', $this->request->getPost('employee_id'))->update($dataemployeeBlan);

                //insert log (start)
                $this->transactionLog->insert_log_data('employee', $this->request->getPost('employee_id'), $ledgSupId, $amount);
                //insert log (end)

                //admin transaction
                if ($paymentType == 2) {
                    //shop balance update
                    $shopData = array(
                        'cash' => $shopUpdateBalance,
                        'updatedBy' => $userId,
                    );
                    $shopsTab = DB()->table('shops');
                    $shopsTab->where('sch_id', $shopId)->update($shopData);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops', $shopId, $ledgSupId, $amount);
                    //insert log (end)

                    //insert ledger_nagodan
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'trans_id' => $ledgSupId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => $this->request->getPost('particulars'),
                        'amount' => $amount,
                        'rest_balance' => $shopUpdateBalance,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTab = DB()->table('ledger_nagodan');
                    $ledger_nagodanTab->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($ledgSupId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgSupId, $amount);
                    //insert log (end)

                } else {
                    $bankData = array(
                        'balance' => $bankUpData,
                        'updatedBy' => $userId,
                    );
                    $bankTab = DB()->table('bank');
                    $bankTab->where('bank_id', $bankId)->update($bankData);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank', $bankId, $ledgSupId, $amount);
                    //insert log (end)

                    //insert ledger_bank
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'trans_id' => $ledgSupId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => $this->request->getPost('particulars'),
                        'amount' => $amount,
                        'rest_balance' => $bankUpData,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($ledgSupId, $ledgBank_id, 'ledger_bank', 'Cr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $ledgSupId, $amount);
                    //insert log (end)

                    //bank id update in transaction table
                    $tranDataBank = array(
                        'bank_id' => $bankId,
                    );
                    $tranBank = DB()->table('transaction');
                    $tranBank->where('trans_id', $ledgSupId)->update($tranDataBank);

                }

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid employee<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method search salary employee
     * @return void
     */
    public function search_employeeSalary()
    {
        $employeeId = $this->request->getPost('id');
        $employeeTab = DB()->table('employee');
        $query = $employeeTab->where('employee_id', $employeeId)->get()->getRow();

        print $query->salary;
    }

    /**
     * @description This method store vat pay transaction
     * @return void
     */
    public function vat_pay_action()
    {

        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        //Vat Balance
        $previousVat = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);

        $restBalance = $previousVat + $amount;

        $vatId = $this->request->getPost('vat_id');
        //Payment Type
        $paymentType = $this->request->getPost('payment_type');

        //shop data
        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $shopUpdateBalance = $shopBalance - $amount;

        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if ($bankId) {
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $bankUpData = $bankCash - $amount;
            }
            $availableBalance = checkBankBalance($bankId, $amount);
            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }
        }

        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }

        $shopCheck = check_shop('vat_register', 'vat_id', $vatId);
        if ($shopCheck == 1) {
            if ($availableBalance == true) {
                $vat_registerT = DB()->table('vat_register');
                $vatamo = $vat_registerT->where('sch_id', $shopId)->where('is_default', '1')->get()->getRow()->balance;

                $vatTotal = -$vatamo;
                if ($vatTotal >= $amount) {

                    DB()->transStart();

                    //insert Transaction table
                    $transdata = array(
                        'sch_id' => $shopId,
                        'date' => $this->request->getPost('date'),
                        'vat_id' => $this->request->getPost('vat_id'),
                        'title' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $transactionTab = DB()->table('transaction');
                    $transactionTab->insert($transdata);
                    $ledgSupId = DB()->insertID();

                    //insert log (start)
                    $this->transactionLog->insert_log_data('transaction', $ledgSupId, $ledgSupId, $amount);
                    //insert log (end)

                    // transaction events insert;
                    DB()->table('transaction_events')->insert([
                        'sch_id' => $shopId,
                        'trans_id'     => $ledgSupId,
                        'createdDtm'   => date('Y-m-d H:i:s')
                    ]);

                    //insert data ledger_vat
                    $data = array(
                        'sch_id' => $shopId,
                        'trans_id' => $ledgSupId,
                        'vat_id' => $this->request->getPost('vat_id'),
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $restBalance,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_vatTab = DB()->table('ledger_vat');
                    $ledger_vatTab->insert($data);
                    $ledg_vat_id = DB()->insertID();

                    //insert transaction in ledger Transaction table (end)
                    $this->transaction_entries($ledgSupId, $ledg_vat_id, 'ledger_vat', 'Dr.');

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_vat', $ledg_vat_id, $ledgSupId, $amount);
                    //insert log (end)

                    //vat register Balance Update
                    $datavatBlan = array(
                        'balance' => $restBalance,
                        'updatedBy' => $userId,
                    );
                    $vat_registerTab = DB()->table('vat_register');
                    $vat_registerTab->where('vat_id', $this->request->getPost('vat_id'))->update($datavatBlan);

                    //insert log (start)
                    $this->transactionLog->insert_log_data('vat_register', $this->request->getPost('vat_id'), $ledgSupId, $amount);
                    //insert log (end)


                    //admin transaction
                    if ($paymentType == 2) {
                        //shop balance update
                        $shopData = array(
                            'cash' => $shopUpdateBalance,
                            'updatedBy' => $userId,
                        );
                        $shopsTab = DB()->table('shops');
                        $shopsTab->where('sch_id', $shopId)->update($shopData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('shops', $shopId, $ledgSupId, $amount);
                        //insert log (end)

                        //insert ledger_nagodan
                        $lgNagData = array(
                            'sch_id' => $shopId,
                            'trans_id' => $ledgSupId,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $shopUpdateBalance,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_nagodanTab = DB()->table('ledger_nagodan');
                        $ledger_nagodanTab->insert($lgNagData);
                        $ledg_nagodan_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($ledgSupId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $ledgSupId, $amount);
                        //insert log (end)

                    } else {

                        $bankData = array(
                            'balance' => $bankUpData,
                            'updatedBy' => $userId,
                        );
                        $bankTab = DB()->table('bank');
                        $bankTab->where('bank_id', $bankId)->update($bankData);

                        //insert log (start)
                        $this->transactionLog->insert_log_data('bank', $bankId, $ledgSupId, $amount);
                        //insert log (end)

                        //insert ledger_bank
                        $lgBankData = array(
                            'sch_id' => $shopId,
                            'bank_id' => $bankId,
                            'trans_id' => $ledgSupId,
                            'trangaction_type' => 'Cr.',
                            'particulars' => $this->request->getPost('particulars'),
                            'amount' => $amount,
                            'rest_balance' => $bankUpData,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_bankTab = DB()->table('ledger_bank');
                        $ledger_bankTab->insert($lgBankData);
                        $ledgBank_id = DB()->insertID();

                        //insert transaction in ledger Transaction table (end)
                        $this->transaction_entries($ledgSupId, $ledgBank_id, 'ledger_bank', 'Cr.');

                        //insert log (start)
                        $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $ledgSupId, $amount);
                        //insert log (end)

                        //bank id update in transaction table
                        $tranDataBank = array(
                            'bank_id' => $bankId,
                        );
                        $tranBank = DB()->table('transaction');
                        $tranBank->where('trans_id', $ledgSupId)->update($tranDataBank);

                    }

                    DB()->transComplete();

                    print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Vat amount to large<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please input valid Vat id<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }
    public function assets_pay_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = str_replace(',', '', $this->request->getPost('amount'));
        $accountId = $this->request->getPost('account_id');
        $paymentType = $this->request->getPost('payment_type');
        $bankUpData = 0;
        if ($paymentType == 1) {
            $bankId = $this->request->getPost('bank_id');
            if (empty($bankId)) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Please select a bank <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                die();
            }

            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankUpData = $bankCash - $amount;
            $availableBalance = checkBankBalance($bankId, $amount);
        }elseif($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }


        if ($amount < 0) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            die();
        }


        if ($availableBalance == true) {
            $db = DB();
            $db->transStart();

            //insert Transaction table
            $transdata = array(
                'sch_id' => $shopId,
                'account_id' => $accountId,
                'date' => $this->request->getPost('date'),
                'title' => $this->request->getPost('particulars'),
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $db->table('transaction')->insert($transdata);
            $transactionId = $db->insertID();

            //insert log (start)
            $this->transactionLog->insert_log_data('transaction', $transactionId, $transactionId, $amount);
            //insert log (end)

            // transaction events insert;
            $db->table('transaction_events')->insert([
                'sch_id' => $shopId,
                'trans_id'     => $transactionId,
                'createdDtm'   => date('Y-m-d H:i:s')
            ]);

            //Vat Balance
            $previousBalance = get_data_by_id('balance', 'accounts', 'account_id', $accountId);
            $restBalance = $previousBalance + $amount;

            //Accounts Balance Update
            $datavatBlan = array(
                'balance' => $restBalance,
                'updatedBy' => $userId,
            );
            $db->table('accounts')->where('account_id', $accountId)->update($datavatBlan);
            //insert log (start)
            $this->transactionLog->insert_log_data('accounts', $accountId, $transactionId, $amount);
            //insert log (end)

            //insert data ledger accounts
            $data = array(
                'sch_id' => $shopId,
                'trans_id' => $transactionId,
                'account_id' => $accountId,
                'particulars' => $this->request->getPost('particulars'),
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'rest_balance' => $restBalance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $db->table('ledger_accounts')->insert($data);
            $ledger_id = $db->insertID();
            //insert transaction in ledger Transaction table (end)
            $this->transaction_entries($transactionId, $ledger_id, 'ledger_accounts', 'Dr.');
            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_accounts', $ledger_id, $transactionId, $amount);
            //insert log (end)

            //admin transaction
            if ($paymentType == 2) {
                //shop data
                $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                $shopUpdateBalance = $shopBalance - $amount;

                //shop balance update
                $shopData = array(
                    'cash' => $shopUpdateBalance,
                    'updatedBy' => $userId,
                );
                $db->table('shops')->where('sch_id', $shopId)->update($shopData);

                //insert log (start)
                $this->transactionLog->insert_log_data('shops', $shopId, $transactionId, $amount);
                //insert log (end)

                //insert ledger_nagodan
                $lgNagData = array(
                    'sch_id' => $shopId,
                    'trans_id' => $transactionId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $shopUpdateBalance,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $db->table('ledger_nagodan')->insert($lgNagData);
                $ledg_nagodan_id = DB()->insertID();

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($transactionId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_nagodan', $ledg_nagodan_id, $transactionId, $amount);
                //insert log (end)

            } else {

                $bankData = array(
                    'balance' => $bankUpData,
                    'updatedBy' => $userId,
                );
                $db->table('bank')->where('bank_id', $bankId)->update($bankData);

                //insert log (start)
                $this->transactionLog->insert_log_data('bank', $bankId, $transactionId, $amount);
                //insert log (end)

                //insert ledger_bank
                $lgBankData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bankId,
                    'trans_id' => $transactionId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $bankUpData,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $db->table('ledger_bank')->insert($lgBankData);
                $ledgBank_id = $db->insertID();

                //insert transaction in ledger Transaction table (end)
                $this->transaction_entries($transactionId, $ledgBank_id, 'ledger_bank', 'Cr.');

                //insert log (start)
                $this->transactionLog->insert_log_data('ledger_bank', $ledgBank_id, $transactionId, $amount);
                //insert log (end)

                //bank id update in transaction table
                $tranDataBank = array(
                    'bank_id' => $bankId,
                );
                $db->table('transaction')->where('trans_id', $transactionId)->update($tranDataBank);
            }
            $db->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method ledger vat pay
     * @return void
     */
    public function vatLedgerData()
    {
        $vatId = $this->request->getPost('vatId');

        $db = DB();
        // 1. Define the window function column
        $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledg_vat_id) AS r_balance";
        // 2. Build the inner subquery
        $subquery = $db->table('ledger_vat')
            ->select('ledger_vat.*')
            ->select($mBalanceSubquery, false)
            ->where('vat_id', $vatId);
        // 3. Query from the subquery derived table
        $table = $db->newQuery()
            ->fromSubquery($subquery, 't')
            ->where('vat_id', $vatId);
        $table->orderBy('ledg_vat_id', 'DESC')->limit(10);
        $data = $table->get()->getResult();
        $vatBalance = get_data_by_id('balance', 'vat_register', 'vat_id', $vatId);

        $view = '<span class="pull-right"> Balance: ' . showWithCurrencySymbol($vatBalance) . '</span>';
        $view .= '<table class="table table-bordered table-striped" id="TFtable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Vat register no</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>';

        foreach ($data as $row) {
            $particulars = ($row->particulars == NULL) ? "Loan" : $row->particulars;
            $vat_register = get_data_by_id('name', 'vat_register', "vat_id", $row->vat_id);
            $amountCr = ($row->trangaction_type != "Cr.") ? "---" : showWithCurrencySymbol($row->amount);
            $amountDr = ($row->trangaction_type != "Dr.") ? "---" : showWithCurrencySymbol($row->amount);
            $view .= '<tr>
                                    <td>' . bdDateFormat($row->createdDtm) . '</td>
                                    <td>' . $particulars . '</td>
                                    <td>' . $vat_register . '</td>
                                    <td>' . $amountDr . '</td>
                                    <td>' . $amountCr . '</td>
                                    <td>' . showWithCurrencySymbol($row->r_balance) . '</td>
                                </tr>';
        }

        $view .= '</tbody>
                            <tfoot>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Vat register no</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </tfoot>
                        </table>';


        print $view;
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Transaction/moneyreceipt', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Transaction/salaryreceipt', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Transaction/read', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method Customer edit data view
     * @return void
     */
    public function cusDataEdit()
    {
        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $name = get_data_by_id('customer_name', 'customers', 'customer_id', $result->customer_id);
        $cr = ($result->trangaction_type == 'Cr.') ? 'selected' : '';
        $dr = ($result->trangaction_type == 'Dr.') ? 'selected' : '';
        $bank = ($result->bank_id != null) ? 'selected' : '';
        $cass = ($result->bank_id == null) ? 'selected' : '';
        $formUrl = base_url('Admin/Transaction/cusDataEditAction');
        $clickFunc = "submitForm('cusUpdateform')";


        $view = '';
        $view .= '<form id="cusUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Customer </label>
                            <select class="form-control " name="customer_id" required>
                                <option value="' . $result->customer_id . '">' . $name . '</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="enum">Transaction
                                Type</label>
                            <select class="form-control input" name="transaction_type" id="trangaction_type" required>
                                <option value="">Please Select</option>
                                <option value="1" ' . $cr . '>খরচ (Cr.)</option>
                                <option value="2" ' . $dr . ' >জমা (Dr.)</option>
                            </select>
                        </div>
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;

    }

    /**
     * @description This method Customer edit action
     * @return void
     */
    public function cusDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $transaction_type = $this->request->getPost('transaction_type');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }
        //customer needed data
        $customerInfo = $this->transactionLog->get_table_name_by_row('customers', $tanId);
        $custBalance = get_data_by_id('balance', 'customers', 'customer_id', $transaction->customer_id);

        //customer ledger needed data
        $cusledgerInfo = $this->transactionLog->get_table_name_by_row('ledger', $tanId);
        $ledgBal = get_data_by_id('rest_balance', 'ledger', 'ledg_id', $cusledgerInfo->id);

        $ledgerType = get_data_by_id('trangaction_type', 'ledger', 'ledg_id', $cusledgerInfo->id);
        if ($transaction_type == 1) {
            if ($ledgerType == 'Dr.') {
                $custRestBalan = $custBalance - $customerInfo->amount + $amount;
                $restbalLedger = $ledgBal - $cusledgerInfo->amount + $amount;
            } else {
                $custRestBalan = $custBalance + $customerInfo->amount + $amount;
                $restbalLedger = $ledgBal + $cusledgerInfo->amount + $amount;
            }
            $newLedgerType = 'Dr.';
            $paymentTransactionType = 'Cr.';
        } else {
            if ($ledgerType == 'Cr.') {
                $custRestBalan = $custBalance + $customerInfo->amount - $amount;
                $restbalLedger = $ledgBal + $cusledgerInfo->amount - $amount;
            } else {
                $custRestBalan = $custBalance - $customerInfo->amount - $amount;
                $restbalLedger = $ledgBal - $cusledgerInfo->amount - $amount;
            }
            $newLedgerType = 'Cr.';
            $paymentTransactionType = 'Dr.';
        }
//        print $restbalLedger;
//        die();
        if ($availableBalance == true) {
            DB()->transStart();
            //insert Transaction in transaction table (start)
            $transdata = array(
                'title' => $particulars,
                'trangaction_type' => $paymentTransactionType,
                'amount' => $amount,
            );

            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);
            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)

            // transaction amount calculate to customer balance and update customer balance (start)
            $dataCustBlan = array(
                'balance' => $custRestBalan,
            );
            $customersTab = DB()->table('customers');
            $customersTab->where('customer_id', $transaction->customer_id)->update($dataCustBlan);
            // transaction amount calculate to customer balance and update customer balance (end)

            //insert transaction in ledger Transaction table (start)
            $cusLedgerData = array(
                'particulars' => $particulars,
                'trangaction_type' => $newLedgerType,
                'amount' => $amount,
                'rest_balance' => $restbalLedger,
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->where('ledg_id', $cusledgerInfo->id)->update($cusLedgerData);

            //all customer ledger rest balance update

            if ($newLedgerType == 'Cr.') {
                $this->customer_ledger_rest_balance_update($tanId, $transaction->customer_id, $amount, $ledgerType);
            } else {
                $this->customer_ledger_rest_balance_update_cr($tanId, $transaction->customer_id, $amount, $ledgerType);
            }
            //insert transaction in ledger Transaction table (end)

            //transaction payment amount payment cash or bank(start)
            if ($paymentTransactionType == 'Cr.') {
                $this->cr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            } else {
                $this->dr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            }
            //transaction payment amount payment cash or bank(end)

            //transaction log all amount update
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //transaction log all amount update
            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successfully update <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method CR payment data update
     * @param int $paymentType
     * @param string $particulars
     * @param int $tanId
     * @param float $amount
     * @param int $bank_id
     * @return void
     */
    private function cr_payment_data_update($paymentType, $particulars, $tanId, $amount, $bank_id, $type)
    {
        if ($paymentType == 2) {
            //transaction cash payment calculate cash amount and update cash or create ledger (start)
            $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
            $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);

            if ($type == 'Cr.') {
                $restShopLedgBal = $shopLedBal - $shopLedInfo->amount - $amount;
            } else {
                $restShopLedgBal = $shopLedBal - $shopLedInfo->amount + $amount;
            }

            $shopedata = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'trangaction_type' => 'Cr.',
                'rest_balance' => $restShopLedgBal,
            );
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

            //shop all ledger rest balance update
            $this->shop_ledger_rest_balance_update_cr($tanId, $amount, $type);
            //shop all ledger rest balance update


            //update shops balance
            $shopLedInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
            $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);

            if ($type == 'Cr.') {
                $shopRestBalan = $shopsBalance - $shopLedInfo->amount - $amount;
            } else {
                $shopRestBalan = $shopsBalance + $shopLedInfo->amount - $amount;
            }
            $shopeupdatedata = array(
                'cash' => $shopRestBalan,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
            //transaction cash payment calculate cash amount and update cash or create ledger (end)
        } else {
            //bank amount and update bank or create ledger bank (start)
            $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
            $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);

            if ($type == 'Cr.') {
                $restbankLedgBal = $bankLedBal - $bankLedgInfo->amount - $amount;
            } else {
                $restbankLedgBal = $bankLedBal + $bankLedgInfo->amount - $amount;
            }
            $lgBankData = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'trangaction_type' => 'Cr.',
                'rest_balance' => $restbankLedgBal,
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

            //all bank ledger rest balance update
            $this->bank_ledger_rest_balance_update_cr($tanId, $bank_id, $amount, $type);
            //all bank ledger rest balance update

            //update bank balance
            $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bank_id);

            if ($type == 'Cr.') {
                $bankRestBalan = $bankCash - $bankInfo->amount - $amount;
            } else {
                $bankRestBalan = $bankCash - $bankInfo->amount + $amount;
            }
            $bankData2 = array(
                'balance' => $bankRestBalan,
            );
            $bankTab = DB()->table('bank');
            $bankTab->where('bank_id', $bank_id)->update($bankData2);
            //transaction bank payment calculate bank amount and update bank or create ledger bank (end)
        }
    }

    /**
     * @description This method DR payment data update
     * @param int $paymentType
     * @param string $particulars
     * @param int $tanId
     * @param float $amount
     * @param int $bank_id
     * @return void
     */
    private function dr_payment_data_update($paymentType, $particulars, $tanId, $amount, $bank_id, $type)
    {
        if ($paymentType == 2) {
            $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
            $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
            if ($type == 'Cr.') {
                $restShopLedgBal = ($shopLedBal - $shopLedInfo->amount) + $amount;
            } else {
                $restShopLedgBal = ($shopLedBal + $shopLedInfo->amount) + $amount;
            }

            $shopedata = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'trangaction_type' => 'Dr.',
                'rest_balance' => $restShopLedgBal,
            );
            $ledger_nagodanTable = DB()->table('ledger_nagodan');
            $ledger_nagodanTable->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

            //shop all ledger rest balance update
            $this->shop_ledger_rest_balance_update($tanId, $amount, $type);
            //shop all ledger rest balance update


            //update shops balance
            $shopInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
            $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
            if ($type == 'Cr.') {
                $shopRestBalan = ($shopsBalance - $shopInfo->amount) + $amount;
            } else {
                $shopRestBalan = ($shopsBalance + $shopInfo->amount) + $amount;
            }

            $shopeupdatedata = array(
                'cash' => $shopRestBalan,
            );
            $shopsTable = DB()->table('shops');
            $shopsTable->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
            //transaction cash payment calculate cash amount and update cash or create ledger (end)
        } else {
            //transaction bank payment calculate bank amount and update bank or create ledger bank (start)
            $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
            $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);

            if ($type == 'Cr.') {
                $restbankLedgBal = ($bankLedBal - $bankLedgInfo->amount) + $amount;
            } else {
                $restbankLedgBal = ($bankLedBal + $bankLedgInfo->amount) + $amount;
            }
            $lgBankData = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'trangaction_type' => 'Dr.',
                'rest_balance' => $restbankLedgBal,
            );
            $ledger_bankTable = DB()->table('ledger_bank');
            $ledger_bankTable->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

            //all bank ledger rest balance update
            $this->bank_ledger_rest_balance_update($tanId, $bank_id, $amount);
            //all bank ledger rest balance update

            //update bank balance
            $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bank_id);
            $bankRestBalan = ($bankCash + $bankInfo->amount) + $amount;
            if ($type == 'Cr.') {
                $bankRestBalan = ($bankCash - $bankInfo->amount) + $amount;
            } else {
                $bankRestBalan = ($bankCash + $bankInfo->amount) + $amount;
            }
            $bankData = array(
                'balance' => $bankRestBalan,
            );
            $bankTable = DB()->table('bank');
            $bankTable->where('bank_id', $bank_id)->update($bankData);
        }
    }

    /**
     * @description This method Customer ledger rest balance update
     * @param int $transactionId
     * @param int $customer_id
     * @param float $amount
     * @return void
     */
    private function customer_ledger_rest_balance_update($transactionId, $customer_id, $amount,$type)
    {
        // Get the specific ledger log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger', $transactionId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger');

        // Query all ledger entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_id >', $ledLog->id)->where('customer_id', $customer_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update

                if ($type == 'Cr.') {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger');
            $table->updateBatch($arrayUpData, 'ledg_id');
        }
//        return $arrayUpData;
    }

    private function customer_ledger_rest_balance_update_cr($transactionId, $customer_id, $amount, $type)
    {
        // Get the specific ledger log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger', $transactionId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger');

        // Query all ledger entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_id >', $ledLog->id)->where('customer_id', $customer_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update

                if ($type == 'Cr.') {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_id'] = $val->ledg_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            }
        }

        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger');
            $table->updateBatch($arrayUpData, 'ledg_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method shop ledger rest balance update
     * @param int $transactionId
     * @param float $amount
     * @return void
     */
    private function shop_ledger_rest_balance_update($transactionId, $amount, $type)
    {
        // Get information about a specific transaction row from the 'ledger_nagodan' table.
        $shopInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $transactionId);

        // Prepare a query builder for the 'ledger_nagodan' table.
        $tableShop = DB()->table('ledger_nagodan');

        // Get all transactions where the ID is greater than the current transaction's ID  and they belong to the same shop (session shop ID).
        $arrayLedData = $tableShop->where('ledg_nagodan_id >', $shopInfo->id)->where('sch_id', $this->session->shopId)->get()->getResult();

        $arrayUpData = [];

        // Loop through the retrieved ledger data to update their rest_balance
        foreach ($arrayLedData as $val) {
            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance - $shopInfo->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
//        return $arrayUpData;
    }

    private function shop_ledger_rest_balance_update_cr($transactionId, $amount, $type)
    {
        // Get information about a specific transaction row from the 'ledger_nagodan' table.
        $shopInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $transactionId);

        // Prepare a query builder for the 'ledger_nagodan' table.
        $tableShop = DB()->table('ledger_nagodan');

        // Get all transactions where the ID is greater than the current transaction's ID  and they belong to the same shop (session shop ID).
        $arrayLedData = $tableShop->where('ledg_nagodan_id >', $shopInfo->id)->where('sch_id', $this->session->shopId)->get()->getResult();

        $arrayUpData = [];

        // Loop through the retrieved ledger data to update their rest_balance
        foreach ($arrayLedData as $val) {
            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance - $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance - $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                    $data['ledg_nagodan_id'] = $val->ledg_nagodan_id;
                    $data['rest_balance'] = $val->rest_balance + $shopInfo->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method bank ledger rest balance update
     * @param int $transactionId
     * @param int $bank_id
     * @param float $amount
     * @return void
     */
    private function bank_ledger_rest_balance_update($transactionId, $bank_id, $amount, $type)
    {

        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_bank', $transactionId);


        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledLog->id)->where('bank_id', $bank_id)->get()->getResult();

        $arrayUpData = [];

        foreach ($result as $val) {

            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount-
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount;
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
    }

    private function bank_ledger_rest_balance_update_cr($transactionId, $bank_id, $amount, $type)
    {

        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_bank', $transactionId);


        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledLog->id)->where('bank_id', $bank_id)->get()->getResult();

        $arrayUpData = [];

        foreach ($result as $val) {

            if ($type == 'Cr.') {
                if ($val->trangaction_type == 'Dr.') {
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount-
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                if ($val->trangaction_type == 'Dr.') {
                    // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount;
                    $data['ledgBank_id'] = $val->ledgBank_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
    }

    /**
     * @description This method bank ledger rest balance update found transfer
     * @param int $bank_id
     * @param float $amount
     * @param int $ledgBank_id
     * @return void
     */
    private function bank_ledger_rest_balance_update_fund($bank_id, $amount, $ledgBank_id, $preAmount)
    {

        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledgBank_id)->where('bank_id', $bank_id)->get()->getResult();

        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledgBank_id'] = $val->ledgBank_id;
                $data['rest_balance'] = ($val->rest_balance + $preAmount) - $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledgBank_id'] = $val->ledgBank_id;
                $data['rest_balance'] = $val->rest_balance - ($preAmount - $amount);
                array_push($arrayUpData, $data);
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method supplier data edit view
     * @return void
     */
    public function supplierDataEdit()
    {
        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $name = get_data_by_id('name', 'suppliers', 'supplier_id', $result->supplier_id);
        $cr = ($result->trangaction_type == 'Cr.') ? 'selected' : '';
        $dr = ($result->trangaction_type == 'Dr.') ? 'selected' : '';
        $formUrl = base_url('Admin/Transaction/supplierDataEditAction');

        $view = '';
        $view .= '<form id="suppUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Supplier </label>
                            <select class="form-control " name="supplier_id" required>
                                <option value="' . $result->supplier_id . '">' . $name . '</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="enum">Transaction
                                Type</label>
                            <select class="form-control input" name="transaction_type" id="transaction_type" required>
                                <option value="">Please Select</option>
                                <option value="1" ' . $cr . '>খরচ (Cr.)</option>
                                <option value="2" ' . $dr . ' >জমা (Dr.)</option>
                            </select>
                        </div>
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method supplier data edit action
     * @return void
     */
    public function supplierDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $transaction_type = $this->request->getPost('transaction_type');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }

        $supplierLedgerInfo = $this->transactionLog->get_table_name_by_row('ledger_suppliers', $tanId);
        $ledgBal = get_data_by_id('rest_balance', 'ledger_suppliers', 'ledg_sup_id', $supplierLedgerInfo->id);

        $supplierInfo = $this->transactionLog->get_table_name_by_row('suppliers', $tanId);
        $suppBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $transaction->supplier_id);

        $ledgerType = get_data_by_id('trangaction_type', 'ledger_suppliers', 'ledg_sup_id', $supplierLedgerInfo->id);
        if ($transaction_type == 1) {
            if ($ledgerType == 'Dr.') {
                $restbalLedger = $ledgBal - $supplierLedgerInfo->amount + $amount;
                $supRestBalan = $suppBalance - $supplierInfo->amount + $amount;
            } else {
                $restbalLedger = $ledgBal + $supplierLedgerInfo->amount + $amount;
                $supRestBalan = $suppBalance + $supplierInfo->amount + $amount;
            }
            $newLedgerType = 'Dr.';
            $paymentTransactionType = 'Cr.';
        } else {
            if ($ledgerType == 'Cr.') {
                $restbalLedger = $ledgBal + $supplierLedgerInfo->amount - $amount;
                $supRestBalan = $suppBalance + $supplierInfo->amount - $amount;
            } else {
                $restbalLedger = $ledgBal - $supplierLedgerInfo->amount - $amount;
                $supRestBalan = $suppBalance - $supplierInfo->amount - $amount;
            }
            $newLedgerType = 'Cr.';
            $paymentTransactionType = 'Dr.';
        }

        if ($availableBalance == true) {
            DB()->transStart();
            //insert Transaction table
            $transdata = array(
                'title' => $particulars,
                'trangaction_type' => $paymentTransactionType,
                'amount' => $amount,
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)

            //insert data
            $data = array(
                'particulars' => $particulars,
                'trangaction_type' => $newLedgerType,
                'amount' => $amount,
                'rest_balance' => $restbalLedger,
            );
            $ledger_suppliersTab = DB()->table('ledger_suppliers');
            $ledger_suppliersTab->where('ledg_sup_id', $supplierLedgerInfo->id)->update($data);

            //all ledger rest balance update

            if ($newLedgerType == 'Cr.') {
                $this->supplier_ledger_rest_balance_update($tanId, $transaction->supplier_id, $amount, $ledgerType);
            } else {
                $this->supplier_ledger_rest_balance_update_cr($tanId, $transaction->supplier_id, $amount, $ledgerType);
            }
            //insert transaction in ledger Transaction table (end)

            //Suppliers Balance Update
            $dataSuppBlan = array(
                'balance' => $supRestBalan,
            );
            $suppliersTab = DB()->table('suppliers');
            $suppliersTab->where('supplier_id', $transaction->supplier_id)->update($dataSuppBlan);

            //transaction payment amount payment cash or bank(start)
            if ($paymentTransactionType == 'Cr.') {
                $this->cr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            } else {
                $this->dr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            }
            //transaction payment amount payment cash or bank(end)

            //transaction log all amount update
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //transaction log all amount update
            DB()->transComplete();
            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successfully Update<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method supplier ledger rest balance update
     * @param int $transactionId
     * @param int $supplier_id
     * @param float $amount
     * @return void
     */
    private function supplier_ledger_rest_balance_update($transactionId, $supplier_id, $amount, $type)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_suppliers', $transactionId);

        // Get a reference to the 'ledger_suppliers' table using the query builder
        $ledgerTable = DB()->table('ledger_suppliers');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_sup_id >', $ledLog->id)->where('supplier_id', $supplier_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = ($val->rest_balance - $ledLog->amount) - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_suppliers');
            $table->updateBatch($arrayUpData, 'ledg_sup_id');
        }
//        return $arrayUpData;
    }

    private function supplier_ledger_rest_balance_update_cr($transactionId, $supplier_id, $amount, $type)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_suppliers', $transactionId);

        // Get a reference to the 'ledger_suppliers' table using the query builder
        $ledgerTable = DB()->table('ledger_suppliers');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_sup_id >', $ledLog->id)->where('supplier_id', $supplier_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update

                if ($type == 'Cr.') {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_sup_id'] = $val->ledg_sup_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_suppliers');
            $table->updateBatch($arrayUpData, 'ledg_sup_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method account data edit view
     * @return void
     */
    public function accountDataEdit()
    {
        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $name = get_data_by_id('name', 'loan_provider', 'loan_pro_id', $result->loan_pro_id);
        $cr = ($result->trangaction_type == 'Cr.') ? 'selected' : '';
        $dr = ($result->trangaction_type == 'Dr.') ? 'selected' : '';
        $formUrl = base_url('Admin/Transaction/accountDataEditAction');


        $view = '';
        $view .= '<form id="accountUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Account Head </label>
                            <select class="form-control " name="loan_pro_id" required>
                                <option value="' . $result->loan_pro_id . '">' . $name . '</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        <div class="form-group">
                            <label for="enum">Transaction
                                Type</label>
                            <select class="form-control input" name="transaction_type" id="transaction_type" required>
                                <option value="">Please Select</option>
                                <option value="1" ' . $cr . '>খরচ (Cr.)</option>
                                <option value="2" ' . $dr . ' >জমা (Dr.)</option>
                            </select>
                        </div>
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn" >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method account data edit action
     * @return void
     */
    public function accountDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $transaction_type = $this->request->getPost('transaction_type');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }

        $accoLedgerInfo = $this->transactionLog->get_table_name_by_row('ledger_loan', $tanId);
        $ledgBal = get_data_by_id('rest_balance', 'ledger_loan', 'ledg_loan_id', $accoLedgerInfo->id);

        $accountInfo = $this->transactionLog->get_table_name_by_row('loan_provider', $tanId);
        $accountBalance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $transaction->loan_pro_id);

        $ledgerType = get_data_by_id('trangaction_type', 'ledger_loan', 'ledg_loan_id', $accoLedgerInfo->id);
        if ($transaction_type == 1) {
            if ($ledgerType == 'Dr.') {
                $restbalLedger = $ledgBal - $accoLedgerInfo->amount + $amount;
                $accountRestBalan = $accountBalance - $accountInfo->amount + $amount;
            } else {
                $restbalLedger = $ledgBal + $accoLedgerInfo->amount + $amount;
                $accountRestBalan = $accountBalance + $accountInfo->amount + $amount;
            }
            $newLedgerType = 'Dr.';
            $paymentTransactionType = 'Cr.';
        } else {
            if ($ledgerType == 'Cr.') {
                $restbalLedger = $ledgBal + $accoLedgerInfo->amount - $amount;
                $accountRestBalan = $accountBalance + $accountInfo->amount - $amount;
            } else {
                $restbalLedger = $ledgBal - $accoLedgerInfo->amount - $amount;
                $accountRestBalan = $accountBalance - $accountInfo->amount - $amount;
            }
            $newLedgerType = 'Cr.';
            $paymentTransactionType = 'Dr.';
        }

        if ($availableBalance == true) {
            DB()->transStart();

            $transdata = array(
                'title' => $particulars,
                'trangaction_type' => $paymentTransactionType,
                'amount' => $amount,
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)

            $data = array(
                'particulars' => $particulars,
                'trangaction_type' => $newLedgerType,
                'amount' => $amount,
                'rest_balance' => $restbalLedger,
            );
            $ledger_accTab = DB()->table('ledger_loan');
            $ledger_accTab->where('ledg_loan_id', $accoLedgerInfo->id)->update($data);

            //all ledger rest balance update
            if ($newLedgerType == 'Cr.') {
                $this->account_ledger_rest_balance_update($tanId, $transaction->loan_pro_id, $amount, $ledgerType);
            } else {
                $this->account_ledger_rest_balance_update_cr($tanId, $transaction->loan_pro_id, $amount, $ledgerType);
            }
            //insert transaction in ledger Transaction table (end)

            //Account Balance Update
            $dataAccBlan = array(
                'balance' => $accountRestBalan,
            );
            $accountTab = DB()->table('loan_provider');
            $accountTab->where('loan_pro_id', $transaction->loan_pro_id)->update($dataAccBlan);


            //transaction payment amount payment cash or bank(start)
            if ($paymentTransactionType == 'Cr.') {
                $this->cr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            } else {
                $this->dr_payment_data_update($paymentType, $particulars, $tanId, $amount, $transaction->bank_id, $ledgerType);
            }
            //transaction payment amount payment cash or bank(end)

            //transaction log all amount update
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //transaction log all amount update

            DB()->transComplete();
            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successfully Update<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    /**
     * @description This method account ledger rest balance update
     * @param int $transactionId
     * @param int $loan_pro_id
     * @param float $amount
     * @return void
     */
    private function account_ledger_rest_balance_update($transactionId, $loan_pro_id, $amount, $type)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_loan', $transactionId);

        // Get a reference to the 'ledger_suppliers' table using the query builder
        $ledgerTable = DB()->table('ledger_loan');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_loan_id >', $ledLog->id)->where('loan_pro_id', $loan_pro_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount - $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_loan');
            $table->updateBatch($arrayUpData, 'ledg_loan_id');
        }
//        return $arrayUpData;
    }

    private function account_ledger_rest_balance_update_cr($transactionId, $loan_pro_id, $amount, $type)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_loan', $transactionId);

        // Get a reference to the 'ledger_suppliers' table using the query builder
        $ledgerTable = DB()->table('ledger_loan');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_loan_id >', $ledLog->id)->where('loan_pro_id', $loan_pro_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                if ($type == 'Cr.') {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance + $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                } else {
                    $data['ledg_loan_id'] = $val->ledg_loan_id;
                    $data['rest_balance'] = $val->rest_balance - $ledLog->amount + $amount;
                    array_push($arrayUpData, $data);
                }
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_loan');
            $table->updateBatch($arrayUpData, 'ledg_loan_id');
        }
    }

    /**
     * @description This method fund data edit
     * @return void
     */
    public function fundDataEdit()
    {

        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $name = get_data_by_id('name', 'bank', 'bank_id', $result->bank_id);
        $name2 = get_data_by_id('name', 'bank', 'bank_id', $result->bank_to_id);
        $formUrl = base_url('Admin/Transaction/fundDataEditAction');


        $view = '';
        $view .= '<form id="fundUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Bank</label>
                            <select class="form-control input"  name="bank_id" id="bank_id" required>
                                <option value="' . $result->bank_id . '">' . $name . '</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="int">To Bank</label>
                            <select class="form-control input" name="bank_id2" required>
                                <option value="' . $result->bank_to_id . '">' . $name2 . '</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method fund data edit action
     * @return void
     */
    public function fundDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();


        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $transaction->bank_id);

        if ($bankBalance > $amount) {

            DB()->transStart();

            //insert Transaction table
            $transdata = array(
                'amount' => $amount,
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)


            //bank balance update  (start)
            $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
            $firstBankBalance = ($bankBalance + $bankInfo->amount) - $amount;
            $firstBankData = array(
                'balance' => $firstBankBalance,
            );
            $bankTab = DB()->table('bank');
            $bankTab->where('bank_id', $transaction->bank_id)->update($firstBankData);
            //bank balance update  (end)

            //Bank ledger create (start)
            $bankledgerInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
            $ledgBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankledgerInfo->id);
            $restbalLedger = $ledgBal - ($bankledgerInfo->amount - $amount);
            $firstLedgerData = array(
                'amount' => $amount,
                'rest_balance' => $restbalLedger,
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->where('ledgBank_id', $bankledgerInfo->id)->update($firstLedgerData);
            //Bank ledgher create (end)

            //all bank ledger reset balance update
            $this->bank_ledger_rest_balance_update_fund($transaction->bank_id, $amount, $bankledgerInfo->id, $transaction->amount);
            //all bank ledger reset balance update


            //2Nd bank balance update (Start)
            $tableB = DB()->table('transaction_log');
            $res = $tableB->where('trans_id', $tanId)->where('table_name', 'bank')->get()->getLastRow();
            $bankBalance2 = get_data_by_id('balance', 'bank', 'bank_id', $res->id);
            $lastBankBalance = ($bankBalance2 - $res->amount) + $amount;

            $lastBankData = array(
                'balance' => $lastBankBalance,
            );
            $bankTable = DB()->table('bank');
            $bankTable->where('bank_id', $res->id)->update($lastBankData);
            //2Nd bank balance update (Start)


            //Bank ledger create (start)
            $tableLed = DB()->table('transaction_log');
            $resLed = $tableLed->where('trans_id', $tanId)->where('table_name', 'ledger_bank')->get()->getLastRow();
            $bankLedBalance2 = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $resLed->id);
            $lastBankRestBalance = ($bankLedBalance2 - $resLed->amount) + $amount;
            $lastLedgerData = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'rest_balance' => $lastBankRestBalance,
            );
            $ledger_bankTable = DB()->table('ledger_bank');
            $ledger_bankTable->where('ledgBank_id', $resLed->id)->update($lastLedgerData);
            //Bank ledgher create (end)
            //all bank ledger reset balance update
            $this->bank_ledger_rest_balance_update_fund($transaction->bank_to_id, $amount, $resLed->id, $transaction->amount);
            //all bank ledger reset balance update

            //update new balance transaction log
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //update new balance transaction log
            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    /**
     * @description This method expense data edit view
     * @return void
     */
    public function expenseDataEdit()
    {


        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $formUrl = base_url('Admin/Transaction/expenseDataEditAction');

        $view = '';
        $view .= '<form id="expenseUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="particulars">Memo Number </label>    
                            <input type="text" class="form-control input" name="memo_number" value="' . $result->memo_number . '" required>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method expense data edit action
     * @return void
     */
    public function expenseDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');
        $memo_number = $this->request->getPost('memo_number');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }
        $ledgerType = 'Cr.';

        if ($availableBalance == true) {
            DB()->transStart();
            //insert Transaction table
            $transdata = array(
                'title' => $particulars,
                'memo_number' => $memo_number,
                'amount' => $amount,
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)

            $shopExpInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
            $shopExpBal = get_data_by_id('expense', 'shops', 'sch_id', $this->session->shopId);
            $restShopExpBal = ($shopExpBal - $shopExpInfo->amount) + $amount;
            $exData = array(
                'expense' => $restShopExpBal,
            );
            $shopsTab = DB()->table('shops');
            $shopsTab->where('sch_id', $this->session->shopId)->update($exData);

            //insert data
            $expLedInfo = $this->transactionLog->get_table_name_by_row('ledger_expense', $tanId);
            $expLedBal = get_data_by_id('rest_balance', 'ledger_expense', 'ledg_exp_id', $expLedInfo->id);
            $restExpLedgBal = $expLedBal - ($expLedInfo->amount - $amount);

            $data = array(
                'particulars' => $particulars,
                'trangaction_type' => 'Dr.',
                'amount' => $amount,
                'rest_balance' => $restExpLedgBal,
            );
            $ledger_expenseTab = DB()->table('ledger_expense');
            $ledger_expenseTab->where('ledg_exp_id', $expLedInfo->id)->update($data);

            //shop all ledger rest balance update
            $this->expense_ledger_rest_balance_update($tanId, $amount);
            //shop all ledger rest balance update


            //transaction payment amount payment cash or bank(start)
            if ($paymentType == 2) {
                //transaction cash payment calculate cash amount and update cash or create ledger (start)
                $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
                $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
                $restShopLedgBal = $shopLedBal - ($shopLedInfo->amount - $amount);
                $shopedata = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $restShopLedgBal,
                );
                $ledger_nagodanTab = DB()->table('ledger_nagodan');
                $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

                //shop all ledger rest balance update
                $this->shop_ledger_rest_balance_update($tanId, $amount, $ledgerType);
                //shop all ledger rest balance update

                //update shops balance
                $shopLedInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
                $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
                $shopRestBalan = ($shopsBalance + $shopLedInfo->amount) - $amount;
                $shopeupdatedata = array(
                    'cash' => $shopRestBalan,
                );
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
                //transaction cash payment calculate cash amount and update cash or create ledger (end)

            } else {
                //bank amount and update bank or create ledger bank (start)

                $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
                $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);
                $restbankLedgBal = $bankLedBal - ($bankLedgInfo->amount - $amount);
                $lgBankData = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $restbankLedgBal,
                );
                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

                //all bank ledger rest balance update
                $this->bank_ledger_rest_balance_update($tanId, $transaction->bank_id, $amount, $ledgerType);
                //all bank ledger rest balance update

                //update bank balance
                $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $transaction->bank_id);
                $bankRestBalan = ($bankCash + $bankInfo->amount) - $amount;
                $bankData2 = array(
                    'balance' => $bankRestBalan,
                );
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $transaction->bank_id)->update($bankData2);
                //transaction bank payment calculate bank amount and update bank or create ledger bank (end)


            }

            //transaction log all amount update
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //transaction log all amount update
            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method expense ledger rest balance update
     * @param int $transactionId
     * @param float $amount
     * @return void
     */
    private function expense_ledger_rest_balance_update($transactionId, $amount)
    {
        // Get information about a specific transaction row from the 'ledger_expense' table.
        $shopInfo = $this->transactionLog->get_table_name_by_row('ledger_expense', $transactionId);

        // Prepare a query builder for the 'ledger_expense' table.
        $tableShop = DB()->table('ledger_expense');

        // Get all transactions where the ID is greater than the current transaction's ID  and they belong to the same shop (session shop ID).
        $arrayLedData = $tableShop->where('ledg_exp_id >', $shopInfo->id)->where('sch_id', $this->session->shopId)->get()->getResult();

        $arrayUpData = [];

        // Loop through the retrieved ledger data to update their rest_balance
        foreach ($arrayLedData as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For 'Dr.' (debit) transactions, subtract the original amount and add the new amount
                $data['ledg_exp_id'] = $val->ledg_exp_id;
                $data['rest_balance'] = ($val->rest_balance - $shopInfo->amount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                // For 'Cr.' (credit) transactions, add the original amount and subtract the new amount
                $data['ledg_exp_id'] = $val->ledg_exp_id;
                $data['rest_balance'] = $val->rest_balance - ($shopInfo->amount - $amount);
                array_push($arrayUpData, $data);
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_expense');
            $table->updateBatch($arrayUpData, 'ledg_exp_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method employee data edit
     * @return void
     */
    public function employeeDataEdit()
    {

        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $formUrl = base_url('Admin/Transaction/employeeDataEditAction');
        $name = get_data_by_id('name', 'employee', 'employee_id', $result->employee_id);
        $salary = get_data_by_id('salary', 'employee', 'employee_id', $result->employee_id);

        $view = '';
        $view .= '<form id="employeeUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Employee </label>
                            <select class="form-control "  aria-hidden="true" name="employee_id" required>
                                <option selected="selected" value="' . $result->employee_id . '">' . $name . ' </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="int">Salary </label>
                            <input type="text" class="form-control input" name="salary" id="salary" value="' . $salary . '" placeholder="Salary" readonly>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method employee data edit action
     * @return void
     */
    public function employeeDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }
        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }
        $ledgerType = 'Cr.';

        if ($availableBalance == true) {

            DB()->transStart();

            //insert Transaction table
            $transdata = array(
                'amount' => $amount,
            );
            $transactionTab = DB()->table('transaction');
            $transactionTab->where('trans_id', $tanId)->update($transdata);

            //transaction edit log data insert
            $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
            //insert Transaction in transaction table (end)


            //insert data
            $employledgerInfo = $this->transactionLog->get_table_name_by_row('ledger_employee', $tanId);
            $ledgBal = get_data_by_id('rest_balance', 'ledger_employee', 'ledg_emp_id', $employledgerInfo->id);
            $restbalLedger = ($ledgBal - $employledgerInfo->amount) + $amount;
            $data = array(
                'particulars' => $particulars,
                'amount' => $amount,
                'rest_balance' => $restbalLedger
            );
            $ledger_employeeTab = DB()->table('ledger_employee');
            $ledger_employeeTab->where('ledg_emp_id', $employledgerInfo->id)->update($data);

            //all ledger rest balance update
            $this->employee_ledger_rest_balance_update($tanId, $employledgerInfo->id, $amount);
            //insert transaction in ledger Transaction table (end)

            //employee Balance Update
            $employeeInfo = $this->transactionLog->get_table_name_by_row('employee', $tanId);
            $employeeBalance = get_data_by_id('balance', 'employee', 'employee_id', $transaction->employee_id);
            $employeeRestBalan = ($employeeBalance - $employeeInfo->amount) + $amount;
            $dataemployeeBlan = array(
                'balance' => $employeeRestBalan,
            );
            $employeeTab = DB()->table('employee');
            $employeeTab->where('employee_id', $transaction->employee_id)->update($dataemployeeBlan);

            if ($paymentType == 2) {
                //transaction cash payment calculate cash amount and update cash or create ledger (start)
                $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
                $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
                if ($shopLedInfo->amount > $amount) {
                    $restShopLedgBal = ($shopLedBal + $shopLedInfo->amount) - $amount;
                } else {
                    $restShopLedgBal = $shopLedBal + ($shopLedInfo->amount - $amount);
                }
                $shopedata = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $restShopLedgBal,
                );
                $ledger_nagodanTab = DB()->table('ledger_nagodan');
                $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

                //shop all ledger rest balance update
                $this->shop_ledger_rest_balance_update($tanId, $amount, $ledgerType);
                //shop all ledger rest balance update

                //update shops balance
                $shopLedInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
                $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
                if ($shopLedInfo->amount > $amount) {
                    $shopRestBalan = ($shopsBalance + $shopLedInfo->amount) - $amount;
                } else {
                    $shopRestBalan = $shopsBalance + ($shopLedInfo->amount - $amount);
                }
                $shopeupdatedata = array(
                    'cash' => $shopRestBalan,
                );
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
                //transaction cash payment calculate cash amount and update cash or create ledger (end)

            } else {
                //bank amount and update bank or create ledger bank (start)
                $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
                $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);

                if ($bankLedgInfo->amount > $amount) {
                    $restbankLedgBal = ($bankLedBal + $bankLedgInfo->amount) - $amount;
                } else {
                    $restbankLedgBal = $bankLedBal + ($bankLedgInfo->amount - $amount);
                }
                $lgBankData = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $restbankLedgBal,
                );
                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

                //all bank ledger reset balance update
                $this->bank_ledger_rest_balance_update($tanId, $transaction->bank_id, $amount, $ledgerType);
                //all bank ledger reset balance update

                //update bank balance
                $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $transaction->bank_id);
                if ($bankInfo->amount > $amount) {
                    $bankRestBalan = ($bankCash - $bankInfo->amount) - $amount;
                } else {
                    $bankRestBalan = $bankCash + ($bankInfo->amount - $amount);
                }
                $bankData2 = array(
                    'balance' => $bankRestBalan,
                );
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $transaction->bank_id)->update($bankData2);
                //transaction bank payment calculate bank amount and update bank or create ledger bank (end)

            }
            //transaction log all amount update
            $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
            //transaction log all amount update

            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method employee ledger rest balance update
     * @param int $transactionId
     * @param int $employee_id
     * @param float $amount
     * @return void
     */
    private function employee_ledger_rest_balance_update($transactionId, $employee_id, $amount)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_employee', $transactionId);

        // Get a reference to the 'ledger_employee' table using the query builder
        $ledgerTable = DB()->table('ledger_employee');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_emp_id >', $ledLog->id)->where('employee_id', $employee_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                $data['ledg_emp_id'] = $val->ledg_emp_id;
                $data['rest_balance'] = ($val->rest_balance + $ledLog->amount) - $amount;
                array_push($arrayUpData, $data);
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                $data['ledg_emp_id'] = $val->ledg_emp_id;
                $data['rest_balance'] = $val->rest_balance - ($ledLog->amount - $amount);
                array_push($arrayUpData, $data);
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_employee');
            $table->updateBatch($arrayUpData, 'ledg_emp_id');
        }
//        return $arrayUpData;
    }

    /**
     * @description This method other sales data edit
     * @return void
     */
    public function otherSalesDataEdit()
    {

        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $formUrl = base_url('Admin/Transaction/otherSalesDataEditAction');

        $view = '';
        $view .= '<form id="otherSalesUpdateform" action="' . $formUrl . '" method="post">  
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>                       
                        
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount" required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method other sales data edit action
     * @return void
     */
    public function otherSalesDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        DB()->transStart();
        //insert Transaction table
        $transdata = array(
            'title' => $particulars,
            'amount' => $amount,
        );
        $transactionTab = DB()->table('transaction');
        $transactionTab->where('trans_id', $tanId)->update($transdata);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
        //insert Transaction in transaction table (end)

        //insert data
        $otherSealesledgerInfo = $this->transactionLog->get_table_name_by_row('ledger_other_sales', $tanId);
        $data = array(
            'particulars' => $particulars,
            'amount' => $amount,
        );
        $ledger_other_salesTab = DB()->table('ledger_other_sales');
        $ledger_other_salesTab->where('ledg_oth_sales_id', $otherSealesledgerInfo->id)->update($data);


        //update shops balance
        $shopInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
        $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
        $shopRestBalan = ($shopsBalance - $shopInfo->amount) + $amount;

        $shopsProfit = get_data_by_id('profit', 'shops', 'sch_id', $this->session->shopId);
        $shopRestProfit = ($shopsProfit + $shopInfo->amount) - $amount;

        $shopeupdatedata = array(
            'cash' => $shopRestBalan,
            'profit' => $shopRestProfit,
        );
        $shopsTable = DB()->table('shops');
        $shopsTable->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
        //transaction cash payment calculate cash amount and update cash or create ledger (end)


        //insert ledger_nagodan
        $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
        $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
        $restShopLedgBal = ($shopLedBal - $shopLedInfo->amount) + $amount;
        $lgNagData = array(
            'particulars' => $particulars,
            'amount' => $amount,
            'rest_balance' => $restShopLedgBal,
        );
        $ledger_nagodanTab = DB()->table('ledger_nagodan');
        $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($lgNagData);

        //transaction log all amount update
        $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
        //transaction log all amount update

        DB()->transComplete();

        print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

    }

    /**
     * @description This method vat data edit
     * @return void
     */
    public function vatDataEdit()
    {
        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $formUrl = base_url('Admin/Transaction/vatDataEditAction');
        $name = get_data_by_id('name', 'vat_register', 'vat_id', $result->vat_id);

        $view = '';
        $view .= '<form id="vatUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Vat </label>
                            <select class="form-control "  aria-hidden="true" name="vat_id" required>
                                <option selected="selected" value="' . $result->vat_id . '">' . $name . ' </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }
    public function assetsDataEdit()
    {
        $tanId = $this->request->getPost('id');
        $table = DB()->table('transaction');
        $result = $table->where('trans_id', $tanId)->get()->getRow();
        $formUrl = base_url('Admin/Transaction/assetsDataEditAction');
        $name = get_data_by_id('name', 'accounts', 'account_id', $result->account_id);

        $view = '';
        $view .= '<form id="assetsUpdateform" action="' . $formUrl . '" method="post">
                        <div class="form-group">
                            <label for="int">Accounts</label>
                            <select class="form-control "  aria-hidden="true" name="vat_id" required>
                                <option selected="selected" value="' . $result->account_id . '">' . $name . ' </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="particulars">Particulars </label>
                            <textarea class="form-control input" rows="3" name="particulars" id="particulars" placeholder="Particulars" required>' . $result->title . '</textarea>
                        </div>
                        
                        <div class="form-group" id="paymentCus">
                            <label for="payment_type">Payment
                                Type </label>
                            <select class="form-control input" name="payment_type" required>';
        if ($result->bank_id != null) {
            $view .= '<option value = "1"> Bank</option>';
        } else {
            $view .= '<option value = "2"> Cash</option>';
        }
        $view .= '</select>
                        </div>
                        <div class="form-group databank" id="chaque">
                            <label for="int">Amount </label>
                            <input type="hidden" name="trans_id" value="' . $tanId . '"  required/>
                            <input type="number" step=any class="form-control input"
                                   name="amount" oninput="minusValueCheck(this.value,this)" id="amount" value="' . $result->amount . '" placeholder="Amount"
                                   required/>
                        </div>';
        $view .= '</div>
                    <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary geniusSubmit-btn"  >Save changes</button>';

        $view .= '</form>';

        print $view;
    }

    /**
     * @description This method vat data edit action
     * @return void
     */
    public function vatDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }

        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }

        if ($availableBalance == true) {
            $vat_registerT = DB()->table('vat_register');
            $vatamo = $vat_registerT->where('vat_id', $transaction->vat_id)->get()->getRow()->balance;

            $vatLast = -$vatamo;
            $vatTotal = $vatLast + $transaction->amount;
            if ($vatTotal >= $amount) {

                DB()->transStart();

                //insert Transaction table
                $transdata = array(
                    'title' => $particulars,
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                );
                $transactionTab = DB()->table('transaction');
                $transactionTab->where('trans_id', $tanId)->update($transdata);

                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
                //insert Transaction in transaction table (end)


                //insert data ledger_vat
                $ledVatInfo = $this->transactionLog->get_table_name_by_row('ledger_vat', $tanId);
                $ledVatBalance = get_data_by_id('rest_balance', 'ledger_vat', 'ledg_vat_id', $ledVatInfo->id);
                $ledVatRestBalan = ($ledVatBalance - $ledVatInfo->amount) + $amount;
                $data = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $ledVatRestBalan,
                );
                $ledger_vatTab = DB()->table('ledger_vat');
                $ledger_vatTab->where('ledg_vat_id', $ledVatInfo->id)->update($data);

                //all ledger rest balance update
                $this->vat_ledger_rest_balance_update($tanId, $transaction->vat_id, $amount);
                //insert transaction in ledger Transaction table (end)

                //vat register Balance Update
                $vatInfo = $this->transactionLog->get_table_name_by_row('vat_register', $tanId);
                $vatBalance = get_data_by_id('balance', 'vat_register', 'vat_id', $transaction->vat_id);
                $vatRestBalan = $vatBalance - ($vatInfo->amount - $amount);
                $datavatBlan = array(
                    'balance' => $vatRestBalan,
                );
                $vat_registerTab = DB()->table('vat_register');
                $vat_registerTab->where('vat_id', $transaction->vat_id)->update($datavatBlan);

                if ($paymentType == 2) {
                    //transaction cash payment calculet cash amount and update cash or create ledger (start)
                    $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
                    $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
                    $restShopLedgBal = $shopLedBal - ($shopLedInfo->amount - $amount);
                    $shopedata = array(
                        'particulars' => $particulars,
                        'amount' => $amount,
                        'rest_balance' => $restShopLedgBal,
                    );
                    $ledger_nagodanTab = DB()->table('ledger_nagodan');
                    $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

                    //shop all ledger rest balance update
                    $this->shop_ledger_rest_balance_update($tanId, $amount ,'Cr.');
                    //shop all ledger rest balance update

                    //update shops balance
                    $shopLedInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
                    $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
                    $shopRestBalan = ($shopsBalance + $shopLedInfo->amount) - $amount;
                    $shopeupdatedata = array(
                        'cash' => $shopRestBalan,
                    );
                    $shopsTab = DB()->table('shops');
                    $shopsTab->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
                    //transaction cash payment calculet cash amount and update cash or create ledger (end)

                } else {
                    //bank amount and update bank or create ledger bank (start)
                    $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
                    $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);
                    $restbankLedgBal = $bankLedBal - ($bankLedgInfo->amount - $amount);
                    $lgBankData = array(
                        'particulars' => $particulars,
                        'amount' => $amount,
                        'rest_balance' => $restbankLedgBal,
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

                    //all bank ledger reset balance update
                    $this->bank_ledger_rest_balance_update($tanId, $transaction->bank_id, $amount,'Cr.');
                    //all bank ledger reset balance update

                    //update bank balance
                    $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
                    $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $transaction->bank_id);
                    $bankRestBalan = ($bankCash + $bankInfo->amount) - $amount;
                    $bankData2 = array(
                        'balance' => $bankRestBalan,
                    );
                    $bankTab = DB()->table('bank');
                    $bankTab->where('bank_id', $transaction->bank_id)->update($bankData2);
                    //transaction bank payment calculate bank amount and update bank or create ledger bank (end)

                }

                //transaction log all amount update
                $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
                //transaction log all amount update

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Vat amount to large<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }
    public function assetsDataEditAction()
    {
        $tanId = $this->request->getPost('trans_id');
        $particulars = $this->request->getPost('particulars');
        $amount = $this->request->getPost('amount');
        $paymentType = $this->request->getPost('payment_type');

        $table = DB()->table('transaction');
        $transaction = $table->where('trans_id', $tanId)->get()->getRow();

        if ($paymentType == 1) {
            $availableBalance = checkBankBalance($transaction->bank_id, $amount);
        }

        if ($paymentType == 2) {
            $availableBalance = checkNagadBalance($amount);
        }

        if ($availableBalance == true) {

                DB()->transStart();

                //insert Transaction table
                $transdata = array(
                    'title' => $particulars,
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                );
                $transactionTab = DB()->table('transaction');
                $transactionTab->where('trans_id', $tanId)->update($transdata);

                //transaction edit log data insert
                $this->transactionLog->transaction_edit_log_data_insert('transaction', '', $tanId, $this->session->userId, $transaction->amount, $amount);
                //insert Transaction in transaction table (end)


                //insert data ledger_vat
                $ledgerInfo = $this->transactionLog->get_table_name_by_row('ledger_accounts', $tanId);
                $ledVatBalance = get_data_by_id('rest_balance', 'ledger_accounts', 'ledg_account_id', $ledgerInfo->id);
                $ledVatRestBalan = ($ledVatBalance - $ledgerInfo->amount) + $amount;
                $data = array(
                    'particulars' => $particulars,
                    'amount' => $amount,
                    'rest_balance' => $ledVatRestBalan,
                );
                $ledger_vatTab = DB()->table('ledger_accounts');
                $ledger_vatTab->where('ledg_account_id', $ledgerInfo->id)->update($data);

                //all ledger rest balance update
                $this->assets_ledger_rest_balance_update($tanId, $transaction->account_id, $amount);
                //insert transaction in ledger Transaction table (end)

                //vat register Balance Update
                $vatInfo = $this->transactionLog->get_table_name_by_row('accounts', $tanId);
                $vatBalance = get_data_by_id('balance', 'accounts', 'account_id', $transaction->account_id);
                $vatRestBalan = $vatBalance - ($vatInfo->amount - $amount);
                $datavatBlan = array(
                    'balance' => $vatRestBalan,
                );
                $vat_registerTab = DB()->table('accounts');
                $vat_registerTab->where('account_id', $transaction->account_id)->update($datavatBlan);

                if ($paymentType == 2) {
                    //transaction cash payment calculate cash amount and update cash or create ledger (start)
                    $shopLedInfo = $this->transactionLog->get_table_name_by_row('ledger_nagodan', $tanId);
                    $shopLedBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $shopLedInfo->id);
                    $restShopLedgBal = $shopLedBal - ($shopLedInfo->amount - $amount);
                    $shopedata = array(
                        'particulars' => $particulars,
                        'amount' => $amount,
                        'rest_balance' => $restShopLedgBal,
                    );
                    $ledger_nagodanTab = DB()->table('ledger_nagodan');
                    $ledger_nagodanTab->where('ledg_nagodan_id', $shopLedInfo->id)->update($shopedata);

                    //shop all ledger rest balance update
                    $this->shop_ledger_rest_balance_update($tanId, $amount ,'Cr.');
                    //shop all ledger rest balance update

                    //update shops balance
                    $shopLedInfo = $this->transactionLog->get_table_name_by_row('shops', $tanId);
                    $shopsBalance = get_data_by_id('cash', 'shops', 'sch_id', $this->session->shopId);
                    $shopRestBalan = ($shopsBalance + $shopLedInfo->amount) - $amount;
                    $shopeupdatedata = array(
                        'cash' => $shopRestBalan,
                    );
                    $shopsTab = DB()->table('shops');
                    $shopsTab->where('sch_id', $this->session->shopId)->update($shopeupdatedata);
                    //transaction cash payment calculet cash amount and update cash or create ledger (end)

                } else {
                    //bank amount and update bank or create ledger bank (start)
                    $bankLedgInfo = $this->transactionLog->get_table_name_by_row('ledger_bank', $tanId);
                    $bankLedBal = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgInfo->id);
                    $restbankLedgBal = $bankLedBal - ($bankLedgInfo->amount - $amount);
                    $lgBankData = array(
                        'particulars' => $particulars,
                        'amount' => $amount,
                        'rest_balance' => $restbankLedgBal,
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->where('ledgBank_id', $bankLedgInfo->id)->update($lgBankData);

                    //all bank ledger reset balance update
                    $this->bank_ledger_rest_balance_update($tanId, $transaction->bank_id, $amount,'Cr.');
                    //all bank ledger reset balance update

                    //update bank balance
                    $bankInfo = $this->transactionLog->get_table_name_by_row('bank', $tanId);
                    $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $transaction->bank_id);
                    $bankRestBalan = ($bankCash + $bankInfo->amount) - $amount;
                    $bankData2 = array(
                        'balance' => $bankRestBalan,
                    );
                    $bankTab = DB()->table('bank');
                    $bankTab->where('bank_id', $transaction->bank_id)->update($bankData2);
                    //transaction bank payment calculate bank amount and update bank or create ledger bank (end)

                }

                //transaction log all amount update
                $this->transactionLog->transaction_log_all_amount_update($tanId, $amount);
                //transaction log all amount update

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Your transaction is successful<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';


        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method vat ledger rest balance update
     * @param int $transactionId
     * @param int $vat_id
     * @param float $amount
     * @return void
     */
    private function vat_ledger_rest_balance_update($transactionId, $vat_id, $amount)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_vat', $transactionId);

        // Get a reference to the 'ledger_employee' table using the query builder
        $ledgerTable = DB()->table('ledger_vat');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_vat_id >', $ledLog->id)->where('vat_id', $vat_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                $data['ledg_vat_id'] = $val->ledg_vat_id;
                $data['rest_balance'] = ($val->rest_balance + $ledLog->amount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                $data['ledg_vat_id'] = $val->ledg_vat_id;
                $data['rest_balance'] = $val->rest_balance + ($ledLog->amount - $amount);
                array_push($arrayUpData, $data);
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_vat');
            $table->updateBatch($arrayUpData, 'ledg_vat_id');
        }
//        return $arrayUpData;
    }
    private function assets_ledger_rest_balance_update($transactionId, $account_id, $amount)
    {
        // Get the specific ledger_suppliers log entry using a helper function that returns table data by transaction ID
        $ledLog = $this->transactionLog->get_table_name_by_row('ledger_accounts', $transactionId);

        // Get a reference to the 'ledger_employee' table using the query builder
        $ledgerTable = DB()->table('ledger_accounts');

        // Query all ledger_suppliers entries for the same customer where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_account_id >', $ledLog->id)->where('account_id', $account_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];

        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                // For Debit transactions, calculate new balance by:
                // Adding back the original transaction amount (undoing its effect)
                // Then subtracting the new amount to reflect the update
                $data['ledg_account_id'] = $val->ledg_account_id;
                $data['rest_balance'] = ($val->rest_balance + $ledLog->amount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                // For Credit transactions, calculate new balance by:
                // Subtracting the original amount (undoing its effect)
                // Then adding the new amount to reflect the update
                $data['ledg_account_id'] = $val->ledg_account_id;
                $data['rest_balance'] = $val->rest_balance + ($ledLog->amount - $amount);
                array_push($arrayUpData, $data);
            }

        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_accounts');
            $table->updateBatch($arrayUpData, 'ledg_account_id');
        }
//        return $arrayUpData;
    }

    private function transaction_entries($trans_id, $ledger_id, $table_name, $transaction_type) {
        DB()->table('transaction_entries')->insert([
            'trans_id'         => $trans_id,
            'ledger_id'        => $ledger_id,
            'table_name'       => $table_name,
            'trangaction_type' => $transaction_type,
            'createdDtm'       => date('Y-m-d H:i:s')
        ]);
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Transaction/transaction_flow', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }



    public function delete($trans_id){
        $db = DB();
        $transaction = $db->table('transaction')->where('trans_id',$trans_id)->get()->getRow();
        if ($transaction->customer_id != NULL){
            $this->customer_transaction_delete($transaction);
        }elseif ($transaction->supplier_id != NULL){
            $this->supplier_transaction_delete($transaction);
        }elseif ($transaction->loan_pro_id != NULL){
            $this->loan_provider_transaction_delete($transaction);
        }elseif (($transaction->bank_to_id != NULL) && ($transaction->bank_id != NULL)){
            $this->fund_transfer_transaction_delete($transaction);
        }
//        elseif ($transaction->loan_pro_id == NULL && $transaction->customer_id == NULL && $transaction->supplier_id == NULL && $transaction->bank_to_id == NULL && $transaction->lc_id == NULL && $transaction->employee_id == NULL && $transaction->trangaction_type == 'Cr.'){
//            $this->expense_transaction_delete($transaction);
//        }
        elseif ($transaction->loan_pro_id == NULL && $transaction->customer_id == NULL && $transaction->supplier_id == NULL && $transaction->bank_id == NULL && $transaction->lc_id == NULL && $transaction->account_id == NULL && $transaction->trangaction_type == 'Dr.'){
            $this->other_sales_transaction_delete($transaction);
        }elseif ($transaction->employee_id != NULL){
            $this->employee_salary_transaction_delete($transaction);
        }elseif ($transaction->vat_id != NULL){
            $this->vat_transaction_delete($transaction);
        }elseif ($transaction->account_id != NULL){
            $accountType = accountIdByType($transaction->account_id);
            if (!empty($accountType)) {
                if ($accountType->type_key == 'assets') {
                    $this->assets_transaction_delete($transaction);
                }
                if ($accountType->type_key == 'expenses') {
                    $this->expense_transaction_delete($transaction);
                }
            }

        }

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Delete Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Transaction'));
    }
    private function customer_transaction_delete($transactionRow)
    {
        $shopId = $this->session->shopId;
        $db = DB();
        $amount = $transactionRow->amount;
        $customerId = $transactionRow->customer_id;
        $transId = $transactionRow->trans_id;
        $bankId = $transactionRow->bank_id;

        // 1. Handle Debit (Dr.) validation check early
        if ($transactionRow->trangaction_type === 'Dr.') {
            $availableBalance = !empty($bankId) ? checkBankBalance($bankId, $amount) : checkNagadBalance($amount);

            if (!$availableBalance) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return false;
            }
        }

        // 2. Execute DB Transactions
        $db->transStart();

        // Base deletions (Common to both Cr. and Dr.)
        $db->table('transaction')->where('trans_id', $transId)->delete();
        $db->table('ledger')->where('trans_id', $transId)->delete();

        // Calculate customer balance adjustment based on type
        $customerOldBalance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
        $customerRestBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($customerOldBalance - $amount) : ($customerOldBalance + $amount);
        $db->table('customers')->where('customer_id', $customerId)->update(['balance' => $customerRestBalance]);

        // Handle Bank vs Shop adjustments
        if (!empty($bankId)) {
            $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($bankOldBalance + $amount) : ($bankOldBalance - $amount);

            $db->table('bank')->where('bank_id', $bankId)->update(['balance' => $bankBalance]);
            $db->table('ledger_bank')->where('trans_id', $transId)->delete();
        } else {
            $shopsOldBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($shopsOldBalance + $amount) : ($shopsOldBalance - $amount);

            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopBalance]);
            $db->table('ledger_nagodan')->where('trans_id', $transId)->delete();
        }

        $db->transComplete();

        return $db->transStatus(); // Returns true if transaction succeeded, false otherwise
    }
    private function supplier_transaction_delete($transactionRow)
    {
        $shopId = $this->session->shopId;
        $db = DB();
        $amount = $transactionRow->amount;
        $supplierId = $transactionRow->supplier_id;
        $transId = $transactionRow->trans_id;
        $bankId = $transactionRow->bank_id;

        // 1. Handle Debit (Dr.) validation check early
        if ($transactionRow->trangaction_type === 'Dr.') {
            $availableBalance = !empty($bankId) ? checkBankBalance($bankId, $amount) : checkNagadBalance($amount);

            if (!$availableBalance) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return false;
            }
        }

        // 2. Execute DB Transactions
        $db->transStart();

        // Core deletions (Common to both Cr. and Dr.)
        $db->table('transaction')->where('trans_id', $transId)->delete();
        $db->table('ledger_suppliers')->where('trans_id', $transId)->delete();

        // Calculate supplier balance adjustment
        $supplierOldBalance = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
        $supplierBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($supplierOldBalance - $amount) : ($supplierOldBalance + $amount);

        $db->table('suppliers')->where('supplier_id', $supplierId)->update(['balance' => $supplierBalance]);

        // Handle Bank vs Shop cash adjustments
        if (!empty($bankId)) {
            $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($bankOldBalance + $amount) : ($bankOldBalance - $amount);

            $db->table('bank')->where('bank_id', $bankId)->update(['balance' => $bankBalance]);
            $db->table('ledger_bank')->where('trans_id', $transId)->delete();
        } else {
            $shopsOldBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($shopsOldBalance + $amount) : ($shopsOldBalance - $amount);

            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopBalance]);
            $db->table('ledger_nagodan')->where('trans_id', $transId)->delete();
        }

        $db->transComplete();

        return $db->transStatus(); // Returns true on success, false on failure
    }
    private function loan_provider_transaction_delete($transactionRow)
    {
        $shopId = $this->session->shopId;
        $db = DB();
        $amount = $transactionRow->amount;
        $loanProId = $transactionRow->loan_pro_id;
        $transId = $transactionRow->trans_id;
        $bankId = $transactionRow->bank_id;

        // 1. Handle Debit (Dr.) validation check early
        if ($transactionRow->trangaction_type === 'Dr.') {
            $availableBalance = !empty($bankId) ? checkBankBalance($bankId, $amount) : checkNagadBalance($amount);
            if (!$availableBalance) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not Enough Balance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return false; // Return false so your controller handles the redirect cleanly
            }
        }

        // 2. Execute DB Transactions
        $db->transStart();

        // Core deletions (Common to both Cr. and Dr.)
        $db->table('transaction')->where('trans_id', $transId)->delete();
        $db->table('ledger_loan')->where('trans_id', $transId)->delete();

        // Calculate loan provider balance adjustment
        $loanProviderOldBalance = get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loanProId);
        $loanProviderBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($loanProviderOldBalance - $amount) : ($loanProviderOldBalance + $amount);

        $db->table('loan_provider')->where('loan_pro_id', $loanProId)->update(['balance' => $loanProviderBalance]);

        // Handle Bank vs Shop cash adjustments
        if (!empty($bankId)) {
            $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($bankOldBalance + $amount) : ($bankOldBalance - $amount);

            $db->table('bank')->where('bank_id', $bankId)->update(['balance' => $bankBalance]);
            $db->table('ledger_bank')->where('trans_id', $transId)->delete();
        } else {
            $shopsOldBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopBalance = ($transactionRow->trangaction_type == 'Cr.') ? ($shopsOldBalance + $amount) : ($shopsOldBalance - $amount);

            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopBalance]);
            $db->table('ledger_nagodan')->where('trans_id', $transId)->delete();
        }

        $db->transComplete();

        return $db->transStatus(); // Returns true on success, false on database failure
    }
    private function fund_transfer_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();
        $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $transactionRow->bank_to_id);
        if ($bankOldBalance > $transactionRow->amount) {
            $db->transStart();

            $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

            //1st bank balance update (Start)
            $bankForm = get_data_by_id('balance', 'bank', 'bank_id', $transactionRow->bank_id);
            $bankBalance = $bankForm + $transactionRow->amount;
            $db->table('bank')->where('bank_id', $transactionRow->bank_id)->update(['balance'=>$bankBalance]);
            //ledger delete
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();


            //2Nd bank balance update (Start)
            $bankOldBalanceTo = get_data_by_id('balance', 'bank', 'bank_id', $transactionRow->bank_to_id);
            $lastBankBalance = $bankOldBalanceTo - $transactionRow->amount;
            $db->table('bank')->where('bank_id', $transactionRow->bank_to_id)->update(['balance'=>$lastBankBalance]);
            // ledger delete
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();
            $db->transComplete();
        }
        return $db->transStatus();
    }
    private function expense_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();
        $amount = $transactionRow->amount;
        $accountId = $transactionRow->account_id;
        $bankId = $transactionRow->bank_id;

        $db->transStart();
        $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

        $previousBalance = get_data_by_id('balance', 'accounts', 'account_id', $accountId);
        $restBalance = $previousBalance - $amount;
        $db->table('accounts')->where('account_id', $accountId)->update(['balance' => $restBalance]);

        $db->table('ledger_accounts')->where('trans_id', $transactionRow->trans_id)->delete();

        if (!empty($bankId)) {
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankUpData = $bankCash + $amount;
            $db->table('bank')->where('bank_id', $bankId)->update(['balance' => $bankUpData]);
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();

        }else {
            $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopUpdateBalance = $shopBalance + $amount;
            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopUpdateBalance]);
            $db->table('ledger_nagodan')->where('trans_id', $transactionRow->trans_id)->delete();
        }
        $db->transComplete();

        return $db->transStatus(); // Returns true on success, false on failure
    }
    private function other_sales_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();

        $db->transStart();
        $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

        $db->table('ledger_other_sales')->where('trans_id', $transactionRow->trans_id)->delete();

        $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
        $oldProfit = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $shopUpdateBalance = $shopBalance - $transactionRow->amount;
        $newProfit = $oldProfit + $transactionRow->amount;
        $db->table('shops')->where('sch_id', $shopId)->update(['cash'=>$shopUpdateBalance,'profit'=>$newProfit]);

        $db->table('ledger_nagodan')->where('trans_id', $transactionRow->trans_id)->delete();

        $db->transComplete();
    }
    private function employee_salary_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();

        $db->transStart();
        $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

        $employeeBalance = get_data_by_id('balance', 'employee', 'employee_id', $transactionRow->employee_id);
        $restBalance = $employeeBalance - $transactionRow->amount;
        $db->table('employee')->where('employee_id', $transactionRow->employee_id)->update(['balance'=>$restBalance]);

        $db->table('ledger_employee')->where('trans_id', $transactionRow->trans_id)->delete();

        if (!empty($transactionRow->bank_id)) {
            $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $transactionRow->bank_id);
            $bankBalance = $bankOldBalance + $transactionRow->amount;
            //Bank balance update
            $db->table('bank')->where('bank_id', $transactionRow->bank_id)->update(['balance' => $bankBalance]);
            //Delete bank ledger
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();
        } else {
            $shopsOldBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopBalance = $shopsOldBalance + $transactionRow->amount;
            //Shop balance update
            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopBalance]);
            //Delete Shop ledger
            $db->table('ledger_nagodan')->where('trans_id', $transactionRow->trans_id)->delete();
        }
        $db->transComplete();


    }
    private function vat_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();

        $db->transStart();
        $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

        $previousVat = get_data_by_id('balance', 'vat_register', 'vat_id', $transactionRow->vat_id);
        $restBalance = $previousVat - $transactionRow->amount;
        $db->table('vat_register')->where('vat_id', $transactionRow->vat_id)->update(['balance' => $restBalance]);

        $db->table('ledger_vat')->where('trans_id', $transactionRow->trans_id)->delete();

        if (!empty($transactionRow->bank_id)) {
            $bankOldBalance = get_data_by_id('balance', 'bank', 'bank_id', $transactionRow->bank_id);
            $bankBalance = $bankOldBalance + $transactionRow->amount;
            //Bank balance update
            $db->table('bank')->where('bank_id', $transactionRow->bank_id)->update(['balance' => $bankBalance]);
            //Delete bank ledger
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();
        } else {
            $shopsOldBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopBalance = $shopsOldBalance + $transactionRow->amount;
            //Shop balance update
            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopBalance]);
            //Delete Shop ledger
            $db->table('ledger_nagodan')->where('trans_id', $transactionRow->trans_id)->delete();
        }
        $db->transComplete();


    }
    private function assets_transaction_delete($transactionRow){
        $shopId = $this->session->shopId;
        $db = DB();

        $amount = $transactionRow->amount;
        $accountId = $transactionRow->account_id;
        $bankId = $transactionRow->bank_id;

        $db->transStart();
        $db->table('transaction')->where('trans_id', $transactionRow->trans_id)->delete();

        $previousBalance = get_data_by_id('balance', 'accounts', 'account_id', $accountId);
        $restBalance = $previousBalance - $amount;
        $db->table('accounts')->where('account_id', $accountId)->update(['balance' => $restBalance]);
        $db->table('ledger_accounts')->where('trans_id', $transactionRow->trans_id)->delete();

        if (!empty($bankId)){
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $bankUpData = $bankCash + $amount;
            $bankTable = $db->table('bank')->where('bank_id', $bankId)->update(['balance' => $bankUpData]);
            $db->table('ledger_bank')->where('trans_id', $transactionRow->trans_id)->delete();
        }else{
            $shopBalance = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $shopUpdateBalance = $shopBalance + $amount;
            $db->table('shops')->where('sch_id', $shopId)->update(['cash' => $shopUpdateBalance]);
            $db->table('ledger_nagodan')->where('trans_id', $transactionRow->trans_id)->delete();
        }

        $db->transComplete();

        return $db->transStatus(); // Returns true on success, false on failure

    }




}