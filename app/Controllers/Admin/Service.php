<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Mycart;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Service extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $cart;
    private $module_name = 'Service';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = new Mycart();
    }

    /**
     * @description This method provides bank view
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
            $servicesTable = DB()->table('services');
            $data['services'] = $servicesTable->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('service_id', 'ASC')->get()->getResult();


            $data['menu'] = view('Admin/menu_service', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Service/index', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides bank create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/service/create_action');


            $data['menu'] = view('Admin/menu_service', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Service/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function addCart(){
        $data['service_name'] = $this->request->getPost('service_name');
        $data['price'] = $this->request->getPost('price');

        $this->validation->setRules([
            'service_name' => ['label' => 'Service Name', 'rules' => 'required'],
            'price' => ['label' => 'Price', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $i = count($this->cart->contents());
            $data2 = array(
                'id' => ++$i,
                'name' => $data['service_name'],
                'qty' => 1,
                'price' => $data['price'],
            );

            $this->cart->insert($data2);
            $this->session->set('cartType', 'service');
            print '<div class="alert alert-success alert-dismissible" role="alert">Add to cart success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }

    public function remove_cart($id){
        $this->cart->remove($id);
        return redirect()->to(site_url('Admin/Service/create'));
    }
    /**
     * @description This method store bank
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['title'] = $this->request->getPost('title');
        $data['service_type'] = $this->request->getPost('service_type');
        $customerId = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('name');
        $entiresaleDisc = $this->request->getPost('saleDisc');
        $data['saleDiscshow'] = $this->request->getPost('saleDiscshow');
        $amount = $this->request->getPost('grandtotal2');
        $finalAmount = $this->request->getPost('grandtotal');
        $nagod = $this->request->getPost('nagod');
        $bankId = $this->request->getPost('bank_id');
        $bankAmount = $this->request->getPost('bankAmount');
        $chequeNo = $this->request->getPost('chequeNo');
        $chequeAmount = $this->request->getPost('chequeAmount');
        $data['grandtotallast'] = $this->request->getPost('grandtotallast');
        $dueAmount = $this->request->getPost('grandtotaldue');
        $sms = $this->request->getPost('sms');

//        $data['sch_id'] = $shopId;
//        $data['createdBy'] = $userId;
//        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'title' => ['label' => 'title', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'service_type' => ['label' => 'Service Type', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Service/create'));
        } else {

            //customer shop check(start)
            if (!empty($customerId)) {
                $shopCheck = check_shop('customers', 'customer_id', $customerId);
                if ($shopCheck != 1) {
                    $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid customer <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to(site_url('Admin/Service/create'));
                }
            }
            //customer shop check(end)

            // If customer name of Id not selected (start)
            if (empty($customerName) && empty($customerId)) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">please enter valid customer!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }
            // If customer name of Id not selected (End)


            // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (Start)
            if (!empty($customerName)) {
                if (($chequeAmount > 0) || ($dueAmount > 0)) {
                    $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please Clear Due!.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to(site_url('Admin/Service/create'));
                }
            }
            // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (End)


            $toAm = (double)$nagod + (double)$bankAmount + (double)$chequeAmount + (double)$dueAmount;

            if ($toAm != $finalAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">wrong input!! please correct inputs to proceed.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }

            if (!empty($nagod) && $nagod < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }

            if (!empty($chequeAmount) && $chequeAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }

            if (empty($this->cart->contents())) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Service/create'));
            }



            DB()->transStart();

            //create invoice in invoice table (start)
            $invData = array(
                'sch_id' => $shopId,
                'amount' => $amount,
                'entire_sale_discount' => $entiresaleDisc,
                'final_amount' => $finalAmount,
                'nagad_paid' => $nagod,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'chaque_paid' => $chequeAmount,
                'due' => $dueAmount,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );


            if (!empty($customerId)) {
                $invData['customer_id'] = $customerId;
            } else {
                $invData['customer_name'] = $customerName;
            }
            $invoiceTab = DB()->table('service_invoice');
            $invoiceTab->insert($invData);
            $invoiceId = DB()->insertID();
            //create invoice in invoice table (end)

            //invoice item insert
            foreach ($this->cart->contents() as $row){
                $invItemData = array(
                    'sch_id' => $shopId,
                    'service_invoice_id' => $invoiceId,
                    'title' => $row['name'],
                    'price' => $row['price'],
                    'quantity' => $row['qty'],
                    'total_price' => $row['price'],
                    'final_price' => $row['price'],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $invoice_itemTab = DB()->table('service_invoice_item');
                $invoice_itemTab->insert($invItemData);
            }
            //invoice item insert

            //create service in service table(start)
            $saleData = array(
                'sch_id' => $shopId,
                'service_invoice_id' => $invoiceId,
                'title' => $data['title'],
                'service_type' => $data['service_type'],
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $salesTab = DB()->table('services');
            $salesTab->insert($saleData);
            //create service in service table(end)


            //service charge
            $serviceCharge = get_data_by_id('service_charge', 'shops', 'sch_id', $shopId);
            $restServiceCharge = $serviceCharge - $finalAmount;
            $dataServiceCharge = array(
                'service_charge' => $restServiceCharge,
                'updatedBy' => $userId,
                'updatedDtm' => date('Y-m-d h:i:s')
            );
            $shopsTable = DB()->table('shops');
            $shopsTable->where('sch_id', $shopId)->update($dataServiceCharge);
            //service charge



            //service charge ledger
            $dataServiceChargeLedger = array(
                'sch_id' => $shopId,
                'service_invoice_id' => $invoiceId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Service new create',
                'amount' => $finalAmount,
                'rest_balance' => $restServiceCharge,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_service_chargeTable = DB()->table('ledger_service_charge');
            $ledger_service_chargeTable->insert($dataServiceChargeLedger);
            //service charge ledger



            //existing customer balance update and customer ledger create (start)
            if ($customerId) {

                //customer balance update in customer table (start)
                $customerCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $newCash = $customerCash + $finalAmount;
                //update balance
                $custData = array(
                    'balance' => $newCash,
                    'updatedBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($custData);
                //customer balance update in customer table (end)


                //insert customer ledger in ledger(start)
                $ledgerData = array(
                    'sch_id' => $shopId,
                    'customer_id' => $customerId,
                    'service_invoice_id' => $invoiceId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => 'Service Cash Due',
                    'amount' => $finalAmount,
                    'rest_balance' => $newCash,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->insert($ledgerData);
                //insert customer ledger in ledger(end)
                if(!empty($sms)) {
                    $message = 'Thank you for your order.Your order amount is-' . $finalAmount;
                    $phone = get_data_by_id('mobile', 'customers', 'customer_id', $customerId);
                    send_sms($phone, $message);
                }

            }
            //existing customer balance update and customer ledger create (end)

            //cash pay shop cash update and create nagod ledger (start)
            if ($nagod > 0) {
                //cash pay amount update shops cash (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
                $upCahs = $shopsCash + $nagod;

                $shopsData = array(
                    'cash' => $upCahs,
                    'updatedBy' => $userId,
                );
                $shopsTab = DB()->table('shops');
                $shopsTab->where('sch_id', $shopId)->update($shopsData);
                //cash pay amount update shops cash (end)


                //insert ledger in ledger_nagodan cash pay amount(start)
                $lgNagData = array(
                    'sch_id' => $shopId,
                    'service_invoice_id' => $invoiceId,
                    'trangaction_type' => 'Dr.',
                    'particulars' => 'Service Cash Pay',
                    'amount' => $nagod,
                    'rest_balance' => $upCahs,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_nagodanTab = DB()->table('ledger_nagodan');
                $ledger_nagodanTab->insert($lgNagData);
                //insert ledger in ledger_nagodan cash pay amount(start)


                //cash pay amount and customer balance amount calculate and update customer balance (start)
                if ($customerId) {
                    //customer balance calculate (start)
                    $custCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                    $newcastCash = $custCash - $nagod;
                    //customer balance calculate (end)


                    //update calculate balance in customer table(start)
                    $custnewData = array(
                        'balance' => $newcastCash,
                        'updatedBy' => $userId,
                    );
                    $customersTab = DB()->table('customers');
                    $customersTab->where('customer_id', $customerId)->update($custnewData);
                    //update calculate balance in customer table(end)


                    //create ledger in ledger table
                    $ledgernogodData = array(
                        'sch_id' => $shopId,
                        'customer_id' => $customerId,
                        'service_invoice_id' => $invoiceId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Service Cash Pay',
                        'amount' => $nagod,
                        'rest_balance' => $newcastCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger');
                    $ledgerTab->insert($ledgernogodData);
                }
                //cash pay amount and customer balance amount calculate and update customer balance (end)
            }
            //cash pay shop cash update and create nagod ledger (end)


            // bank pay amount calculate and bank balance update (start)
            if ($bankAmount > 0) {
                //bank pay amount calculate and update bank balance (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
                $upCahs = $bankCash + $bankAmount;

                $bankData = array(
                    'balance' => $upCahs,
                    'updatedBy' => $userId,
                );
                $bankTab = DB()->table('bank');
                $bankTab->where('bank_id', $bankId)->update($bankData);
                //bank pay amount calculate and update bank balance (end)


                //insert ledger in table ledger_bank (start)
                $lgBankData = array(
                    'sch_id' => $shopId,
                    'bank_id' => $bankId,
                    'service_invoice_id' => $invoiceId,
                    'particulars' => 'Service Bank Pay',
                    'trangaction_type' => 'Dr.',
                    'amount' => $bankAmount,
                    'rest_balance' => $upCahs,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_bankTab = DB()->table('ledger_bank');
                $ledger_bankTab->insert($lgBankData);
                //insert ledger in table ledger_bank (end)


                if ($customerId) {
                    //bank pay amount calculate and customer balance update (start)
                    $cusCash = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                    $bankastCash = $cusCash - $bankAmount;

                    $custnewData = array(
                        'balance' => $bankastCash,
                        'updatedBy' => $userId,
                    );
                    $customersTab = DB()->table('customers');
                    $customersTab->where('customer_id', $customerId)->update($custnewData);
                    //bank pay amount calculate and customer balance update (start)


                    //insert ledger in table ledger (start)
                    $ledgerbankData = array(
                        'sch_id' => $shopId,
                        'customer_id' => $customerId,
                        'service_invoice_id' => $invoiceId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Service Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $bankastCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledgerTab = DB()->table('ledger');
                    $ledgerTab->insert($ledgerbankData);
                    //insert ledger in table ledger (start)

                }

            }
            // bank pay amount calculate and bank balance update (end)


            // cheque pay amount calculate and insert cheque table (end)
            if ($chequeAmount > 0) {

                //cheque pay amount calculate and insert cheque table(start)
                $chequeData = array(
                    'sch_id' => $shopId,
                    'chaque_number' => $chequeNo,
                    'to' => $userId,
                    'from' => $customerId,
                    'amount' => $chequeAmount,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                if (!empty($customerId)) {
                    $chequeData ['from'] = $customerId;
                } else {
                    $chequeData ['from_name'] = $customerName;
                }
                $chaqueTab = DB()->table('chaque');
                $chaqueTab->insert($chequeData);
                $chaqueId = DB()->insertID();
                //cheque pay amount calculate and insert cheque table(end)


                //chaque id update in invoice table(start)
                $invChaqueId = array(
                    'chaque_id' => $chaqueId,
                    'updatedBy' => $userId,
                );
                $invoiceTab = DB()->table('service_invoice');
                $invoiceTab->where('service_invoice_id', $invoiceId)->update($invChaqueId);
                //chaque id update in invoice table(end)
            }

            DB()->transComplete();

            $this->cart->destroy();
            return redirect()->to(site_url('Admin/Service_invoice/view/' . $invoiceId));

        }
    }




}