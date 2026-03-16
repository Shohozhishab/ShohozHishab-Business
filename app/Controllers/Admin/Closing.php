<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\CustomersModel;
use App\Models\Loan_providerModel;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Closing extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $customersModel;
    protected $loan_providerModel;
    protected $suppliersModel;
    protected $crop;
    protected $cart;
    private $module_name = 'Closing';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->customersModel = new CustomersModel();
        $this->loan_providerModel = new Loan_providerModel();
        $this->suppliersModel = new SuppliersModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = \Config\Services::cart();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');

            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/list', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides cash view
     * @return RedirectResponse|void
     */
    public function cash()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/cash', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store cash
     * @return void
     */
    public function cash_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_nagodan', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                $data = array('cash' => $amount);
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($data);

                $ledger_nagodanTab = DB()->table('ledger_nagodan');
                $ledger_nagodanTab->where('sch_id', $shopId)->delete();

                $lgNagData2 = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger_nagodan');
                $ledgerTab->insert($lgNagData2);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }

    }

    /**
     * @description This method provides bank view
     * @return RedirectResponse|void
     */
    public function bank()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/bank', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store bank
     * @return void
     */
    public function bank_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['bank_id'] = $this->request->getPost('bank_id');
        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'bank_id' => ['label' => 'Bank', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $bank_id = $this->request->getPost('bank_id');
            $amount = $this->request->getPost('amount');
            $trnsType = $this->request->getPost('trangaction_type');

            $checkLedgerExists = ledger_exists('ledger_bank', 'bank_id', $bank_id);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                $data = array('balance' => $amount);
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $bank_id)->update($data);

                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->where('bank_id', $bank_id)->delete();


                $lgBankData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bank_id,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger_bank');
                $ledgerTab->insert($lgBankData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides capital view
     * @return RedirectResponse|void
     */
    public function capital()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/capital', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store capital
     * @return void
     */
    public function capital_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_capital', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                $data = array('capital' => -$amount);
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($data);

                $ledger_capitalTab = DB()->table('ledger_capital');
                $ledger_capitalTab->where('sch_id', $shopId)->delete();

                $capitalData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => -$amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger_capital');
                $ledgerTab->insert($capitalData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides stock view
     * @return RedirectResponse|void
     */
    public function stock()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/stock', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store stock
     * @return void
     */
    public function stock_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_stock', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                $data = array('stockAmount' => $amount);
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($data);

                $ledger_stockTab = DB()->table('ledger_stock');
                $ledger_stockTab->where('sch_id', $shopId)->delete();

                $stockData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_stoTab = DB()->table('ledger_stock');
                $ledger_stoTab->insert($stockData);


                $data = array('purchase_balance' => $amount);
                $shopsPurTab = DB()->table('shops');
                $shopsPurTab->where('sch_id', $shopId)->update($data);

                $ledger_purchaseTab = DB()->table('ledger_purchase');
                $ledger_purchaseTab->where('sch_id', $shopId)->delete();

                $stockData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger_purchase');
                $ledgerTab->insert($stockData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides customer view
     * @return RedirectResponse|void
     */
    public function customers()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/customers', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method action customer
     * @return void
     */
    public function cus_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['customer_id'] = $this->request->getPost('customer_id');
        $data['amount'] = $this->request->getPost('amount');
        $data['trangaction_type'] = $this->request->getPost('trangaction_type');

        $this->validation->setRules([
            'customer_id' => ['label' => 'customer_id', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'trangaction_type' => ['label' => 'trangaction_type', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $customer_id = $this->request->getPost('customer_id');
            $amount = $this->request->getPost('amount');
            $type = $this->request->getPost('trangaction_type');

            $checkLedgerExists = ledger_exists('ledger', 'customer_id', $customer_id);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                if ($type == 1) {
                    $data = array('balance' => -$amount);
                    $customersTab = DB()->table('customers');
                    $customersTab->where('customer_id', $customer_id)->update($data);

                    $ledgerTab = DB()->table('ledger');
                    $ledgerTab->where('customer_id', $customer_id)->delete();

                    $cusLedger = array(
                        'sch_id' => $shopId,
                        'customer_id' => $customer_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => -$amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledTab = DB()->table('ledger');
                    $ledTab->insert($cusLedger);
                } else {
                    $data = array('balance' => $amount);
                    $customersTab = DB()->table('customers');
                    $customersTab->where('customer_id', $customer_id)->update($data);

                    $ledgerTab = DB()->table('ledger');
                    $ledgerTab->where('customer_id', $customer_id)->delete();

                    $cusLedger = array(
                        'sch_id' => $shopId,
                        'customer_id' => $customer_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledTab = DB()->table('ledger');
                    $ledTab->insert($cusLedger);
                }

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }

    }

    /**
     * @description This method store customer bulk
     * @return void
     * @throws \ReflectionException
     */
    public function cus_bulk_action()
    {
        $shopId = $this->session->shopId;

        $cus_type_id = get_data_by_id('cus_type_id', 'customer_type', 'type_name', 'regular');

        if ($file = $this->request->getFile('file')) {
            if ($file->isValid() && !$file->hasMoved()) {

                $file2 = fopen($this->request->getFile('file'), "r");
                $i = 0;
                $numberOfFields = 3;
                $csvArr = array();

                while (($filedata = fgetcsv($file2, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    if ($i > 0 && $num == $numberOfFields) {
                        $csvArr[$i]['customer_name'] = $filedata[0];
                        $csvArr[$i]['mobile'] = $filedata[1];
                        $csvArr[$i]['balance'] = $filedata[2];
                        $csvArr[$i]['createdBy'] = $shopId;
                        $csvArr[$i]['sch_id'] = $shopId;
                        $csvArr[$i]['cus_type_id'] = $cus_type_id;
                    }

                    $i++;
                }
                fclose($file2);
                $count = 0;
                DB()->transStart();
                foreach ($csvArr as $userdata) {
                    $findRecord = $this->customersModel->where('mobile', $userdata['mobile'])->countAllResults();
                    if ($findRecord == 0) {
                        if ($this->customersModel->insert($userdata)) {
                            $insert_id = $this->customersModel->getInsertID();

                            $lgcusData = array(
                                'sch_id' => $shopId,
                                'customer_id' => $insert_id,
                                'trangaction_type' => 'Dr.',
                                'particulars' => 'Previous balance',
                                'amount' => $userdata['balance'],
                                'rest_balance' => $userdata['balance'],
                                'createdBy' => $shopId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledgerTable = DB()->table('ledger');
                            $ledgerTable->insert($lgcusData);

                            $count++;
                        }
                    }
                }

                DB()->transComplete();


                print '<div class="alert alert-success alert-dismissible" role="alert">(' . $count . ')Rows successfully added.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method provides account holder view
     * @return RedirectResponse|void
     */
    public function accountHolder()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/accountHolder', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store account holder
     * @return void
     */
    public function account_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['loan_pro_id'] = $this->request->getPost('loan_pro_id');
        $data['amount'] = $this->request->getPost('amount');
        $data['trangaction_type'] = $this->request->getPost('trangaction_type');

        $this->validation->setRules([
            'loan_pro_id' => ['label' => 'loan_pro_id', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'trangaction_type' => ['label' => 'trangaction_type', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $loan_pro_id = $this->request->getPost('loan_pro_id');
            $amount = $this->request->getPost('amount');
            $trnsType = $this->request->getPost('trangaction_type');

            $checkLedgerExists = ledger_exists('ledger_loan', 'loan_pro_id', $loan_pro_id);

            if ($checkLedgerExists == true) {

                DB()->transStart();


                if ($trnsType == 1) {
                    $data = array('balance' => -$amount);
                    $loan_providerTab = DB()->table('loan_provider');
                    $loan_providerTab->where('loan_pro_id', $loan_pro_id)->update($data);

                    $ledger_loanTab = DB()->table('ledger_loan');
                    $ledger_loanTab->where('loan_pro_id', $loan_pro_id)->delete();

                    $dataledg = array(
                        'sch_id' => $shopId,
                        'loan_pro_id' => $loan_pro_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => -$amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger_loan');
                    $ledgerTab->insert($dataledg);
                } else {

                    $data = array('balance' => $amount);
                    $loan_providerTab = DB()->table('loan_provider');
                    $loan_providerTab->where('loan_pro_id', $loan_pro_id)->update($data);

                    $ledger_loanTab = DB()->table('ledger_loan');
                    $ledger_loanTab->where('loan_pro_id', $loan_pro_id)->delete();

                    $dataledg = array(
                        'sch_id' => $shopId,
                        'loan_pro_id' => $loan_pro_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger_loan');
                    $ledgerTab->insert($dataledg);
                }


                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method store account holder bulk
     * @return void
     */
    public function acc_bulk_action()
    {
        $shopId = $this->session->shopId;


        if ($file = $this->request->getFile('file')) {
            if ($file->isValid() && !$file->hasMoved()) {

                $file2 = fopen($this->request->getFile('file'), "r");
                $i = 0;
                $numberOfFields = 3;
                $csvArr = array();

                while (($filedata = fgetcsv($file2, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    if ($i > 0 && $num == $numberOfFields) {
                        $csvArr[$i]['name'] = $filedata[0];
                        $csvArr[$i]['phone'] = $filedata[1];
                        $csvArr[$i]['balance'] = $filedata[2];
                        $csvArr[$i]['createdBy'] = $shopId;
                        $csvArr[$i]['sch_id'] = $shopId;
                    }

                    $i++;
                }
                fclose($file2);
                $count = 0;
                DB()->transStart();
                foreach ($csvArr as $userdata) {
                    $findRecord = $this->loan_providerModel->where('name', $userdata['name'])->countAllResults();
                    if ($findRecord == 0) {
                        if ($this->loan_providerModel->insert($userdata)) {
                            $insert_id = $this->loan_providerModel->getInsertID();

                            $lgedgData = array(
                                'sch_id' => $shopId,
                                'loan_pro_id' => $insert_id,
                                'trangaction_type' => 'Dr.',
                                'particulars' => 'Previous balance',
                                'amount' => $userdata['balance'],
                                'rest_balance' => $userdata['balance'],
                                'createdBy' => $shopId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_loanTab = DB()->table('ledger_loan');
                            $ledger_loanTab->insert($lgedgData);

                            $count++;
                        }
                    }
                }
                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">(' . $count . ')Rows successfully added.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    /**
     * @description This method provides supplier view
     * @return RedirectResponse|void
     */
    public function suppliers()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/suppliers', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store supplier
     * @return void
     */
    public function sup_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['supplier_id'] = $this->request->getPost('supplier_id');
        $data['amount'] = $this->request->getPost('amount');
        $data['trangaction_type'] = $this->request->getPost('trangaction_type');

        $this->validation->setRules([
            'supplier_id' => ['label' => 'supplier_id', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'trangaction_type' => ['label' => 'trangaction_type', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $supplier_id = $this->request->getPost('supplier_id');
            $amount = $this->request->getPost('amount');
            $trnsType = $this->request->getPost('trangaction_type');

            $checkLedgerExists = ledger_exists('ledger_suppliers', 'supplier_id', $supplier_id);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                if ($trnsType == 1) {
                    $data = array('balance' => -$amount);
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplier_id)->update($data);

                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->where('supplier_id', $supplier_id)->delete();


                    $dataSup = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplier_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => -$amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger_suppliers');
                    $ledgerTab->insert($dataSup);
                } else {

                    $data = array('balance' => $amount);
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplier_id)->update($data);

                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->where('supplier_id', $supplier_id)->delete();


                    $dataSup = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplier_id,
                        'particulars' => $this->request->getPost('particulars'),
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger_suppliers');
                    $ledgerTab->insert($dataSup);
                }

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method store supplier bulk
     * @return void
     * @throws \ReflectionException
     */
    public function sup_bulk_action()
    {
        $shopId = $this->session->shopId;


        if ($file = $this->request->getFile('file')) {
            if ($file->isValid() && !$file->hasMoved()) {

                $file2 = fopen($this->request->getFile('file'), "r");
                $i = 0;
                $numberOfFields = 3;
                $csvArr = array();

                while (($filedata = fgetcsv($file2, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    if ($i > 0 && $num == $numberOfFields) {
                        $csvArr[$i]['name'] = $filedata[0];
                        $csvArr[$i]['phone'] = $filedata[1];
                        $csvArr[$i]['balance'] = $filedata[2];
                        $csvArr[$i]['createdBy'] = $shopId;
                        $csvArr[$i]['sch_id'] = $shopId;
                    }

                    $i++;
                }
                fclose($file2);
                $count = 0;
                DB()->transStart();
                foreach ($csvArr as $userdata) {
                    $findRecord = $this->suppliersModel->where('phone', $userdata['phone'])->countAllResults();
                    if ($findRecord == 0) {
                        if ($this->suppliersModel->insert($userdata)) {
                            $insert_id = $this->suppliersModel->getInsertID();

                            $lgedgData = array(
                                'sch_id' => $shopId,
                                'supplier_id' => $insert_id,
                                'trangaction_type' => 'Dr.',
                                'particulars' => 'Previous balance',
                                'amount' => $userdata['balance'],
                                'rest_balance' => $userdata['balance'],
                                'createdBy' => $shopId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_suppliersTab = DB()->table('ledger_suppliers');
                            $ledger_suppliersTab->insert($lgedgData);

                            $count++;
                        }
                    }
                }
                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">(' . $count . ')Rows successfully added.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    /**
     * @description This method provides employ view
     * @return RedirectResponse|void
     */
    public function employe()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/employe', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store employ
     * @return void
     */
    public function employee_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['employee_id'] = $this->request->getPost('employee_id');
        $data['amount'] = $this->request->getPost('amount');

        $this->validation->setRules([
            'employee_id' => ['label' => 'employee_id', 'rules' => 'required'],
            'amount' => ['label' => 'amount', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');
            $employeId = $this->request->getPost('employee_id');

            $checkLedgerExists = ledger_exists('ledger_employee', 'employee_id', $employeId);

            if ($checkLedgerExists == true) {
                DB()->transStart();

                $data = array('balance' => $amount);
                $employeeTab = DB()->table('employee');
                $employeeTab->where('employee_id', $employeId)->update($data);

                $expenseData = array(
                    'sch_id' => $shopId,
                    'employee_id' => $employeId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_employeeTab = DB()->table('ledger_employee');
                $ledger_employeeTab->insert($expenseData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides expense view
     * @return RedirectResponse|void
     */
    public function expense()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/expense', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store expense
     * @return void
     */
    public function expense_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_expense', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {
                DB()->transStart();

                $data = array('expense' => $amount);
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($data);

                $expenseData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_expenseTab = DB()->table('ledger_expense');
                $ledger_expenseTab->insert($expenseData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides profit view
     * @return RedirectResponse|void
     */
    public function profit()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/profit', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store profit
     * @return void
     */
    public function profit_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_profit', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {
                DB()->transStart();

                $data = array('profit' => -$amount);
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($data);

                $profitData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => -$amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_profitTab = DB()->table('ledger_profit');
                $ledger_profitTab->insert($profitData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }

    }

    /**
     * @description This method provides vat ledger view
     * @return RedirectResponse|void
     */

    public function ledger_vat()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/ledger_vat', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides vat ledger action
     * @return void
     */
    public function ledger_vat_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['amount'] = $this->request->getPost('amount');
        $data['particulars'] = $this->request->getPost('particulars');

        $this->validation->setRules([
            'amount' => ['label' => 'amount', 'rules' => 'required'],
            'particulars' => ['label' => 'particulars', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $amount = $this->request->getPost('amount');

            $checkLedgerExists = ledger_exists('ledger_vat', 'sch_id', $shopId);

            if ($checkLedgerExists == true) {

                DB()->transStart();

                $data = array('balance' => -$amount);
                $vat_registerTab = DB()->table('vat_register');
                $vat_registerTab->where('sch_id', $shopId)->update($data);

                $ledger_vatTab = DB()->table('ledger_vat');
                $ledger_vatTab->where('sch_id', $shopId)->delete();

                $lgVatData = array(
                    'sch_id' => $shopId,
                    'trangaction_type' => 'Cr.',
                    'particulars' => $this->request->getPost('particulars'),
                    'amount' => $amount,
                    'rest_balance' => -$amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger_vat');
                $ledgerTab->insert($lgVatData);

                DB()->transComplete();

                print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">already exists! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides products view
     * @return RedirectResponse|void
     */
    public function products()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/header');
            echo view('Admin/sidebar');
            echo view('Admin/Closing/products', $data);
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update products
     * @return RedirectResponse
     */
    public function product_update()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['supplier_id'] = $this->request->getPost('supplier_id');

        $this->validation->setRules([
            'supplier_id' => ['label' => 'Suppliers', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Closing/products'));
        } else {


            $name = $this->request->getPost('name[]');
            $supplierId = $this->request->getPost('supplier_id');

            if (!empty($name)) {

                $number = count($name);

                $storesTab = DB()->table('stores');
                $store = $storesTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
                $storeId = $store->store_id;

                for ($i = 0; $i < $number; $i++) {
                    //insert purchase product
                    $data = array(
                        'sch_id' => $shopId,
                        'store_id' => $storeId,
                        'name' => $this->request->getPost('name[]')[$i],
                        'unit' => $this->request->getPost('unit[]')[$i],
                        'quantity' => $this->request->getPost('quantity[]')[$i],
                        'purchase_price' => $this->request->getPost('purchase_price[]')[$i],
                        'selling_price' => $this->request->getPost('selling_price[]')[$i],
                        'supplier_id' => $supplierId,
                        'prod_cat_id' => $this->request->getPost('prod_cat_id[]')[$i],
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $productsTab = DB()->table('products');
                    $productsTab->insert($data);
                }

                $this->cart->destroy();
                $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">successfully insert products<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Closing/products'));

            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Your cart is empty<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Closing/products'));
            }
        }

    }

    /**
     * @description This method update products bulk
     * @return void
     */
    public function pro_bulk_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;


        if ($file = $this->request->getFile('file')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $storesTab = DB()->table('stores');
                $storeId = $storesTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow()->store_id;
                $file2 = fopen($this->request->getFile('file'), "r");
                $i = 0;
                $numberOfFields = 7;
                $csvArr = array();

                while (($filedata = fgetcsv($file2, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    if ($i > 0 && $num == $numberOfFields) {
                        $csvArr[$i]['name'] = $filedata[0];
                        $csvArr[$i]['unit'] = $filedata[1];
                        $csvArr[$i]['quantity'] = $filedata[2];
                        $csvArr[$i]['purchase_price'] = $filedata[3];
                        $csvArr[$i]['selling_price'] = $filedata[4];
                        $csvArr[$i]['supplier_id'] = $filedata[5];
                        $csvArr[$i]['prod_cat_id'] = $filedata[6];
                        $csvArr[$i]['createdBy'] = $userId;
                        $csvArr[$i]['store_id'] = $storeId;
                        $csvArr[$i]['sch_id'] = $shopId;
                        $csvArr[$i]['createdDtm'] = date('Y-m-d h:i:s');
                    }

                    $i++;
                }
                fclose($file2);
                $count = 0;

                foreach ($csvArr as $userdata) {
                    $proTab = DB()->table('products');
                    $findRecord = $proTab->where('name', $userdata['name'])->countAllResults();
                    if ($findRecord == 0) {
                        $productsTab = DB()->table('products');
                        if ($productsTab->insert($userdata)) {
                            $count++;
                        }
                    }
                }

                print '<div class="alert alert-success alert-dismissible" role="alert">(' . $count . ')Rows successfully added.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">File coud not be imported<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    /**
     * @description This method update shop opening status
     * @return void
     */
    public function change_status()
    {

        $shopId = $this->session->shopId;

        $data = array(
            'opening_status' => '1',
        );

        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($data);

        print '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }

}