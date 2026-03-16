<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_sale extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_sale';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return sale view
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
            $return_saleTable = DB()->table('return_sale');
            $data['return_sale_data'] = $return_saleTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method invoice search
     * @return void
     */
    public function invoice_search()
    {

        $shopId = $this->session->shopId;
        $invoiceId = $this->request->getPost('invoiceId');

        $invoiceTable = DB()->table('invoice');
        $data['invoice_data'] = $invoiceTable->where('sch_id', $shopId)->where('invoice_id', $invoiceId)->get()->getResult();

        $data['menu'] = view('Admin/menu_sales', $data);
        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Return_sale/search', $data);
        echo view('Admin/footer');
    }

    /**
     * @description This method provides return view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function return($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $invoice_itemTab = DB()->table('invoice_item');
            $data['invoice_item'] = $invoice_itemTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();

            $invoiceTab = DB()->table('invoice');
            $data['invoice'] = $invoiceTab->where('invoice_id', $id)->where('sch_id', $shopId)->get()->getResult();


            $data['action'] = site_url('Admin/Return_sale/create_action');
            $data['invoiceId'] = $id;


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Return_sale/return', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store return sale
     * @return RedirectResponse
     */
    public function create_action()
    {

        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $customerId = $this->request->getPost('customer_id');
        $customerName = $this->request->getPost('customer_name');
        $InvId = $this->request->getPost('invoice_id');

        $proId = $this->request->getPost('prod_id[]');
        $quantity = $this->request->getPost('quantity[]');
        $proPrice = $this->request->getPost('purchase_price[]');
        $prodsaleDisc = $this->request->getPost('disc[]');
        $prodsubtotal = $this->request->getPost('subtotal[]');
        $prosubTo = $this->request->getPost('suballtotal[]');


        $amount = $this->request->getPost('totalPrice');
        $finalAmount = $this->request->getPost('grandtotal');

        $nagod = $this->request->getPost('cash');
        $bankAmount = $this->request->getPost('bank');
        $bankId = $this->request->getPost('bank_id');

        $dueAmount = $this->request->getPost('due');
        $totalVat = (empty($this->request->getPost('vatAmount'))) ? 0 : $this->request->getPost('vatAmount');

        // If customer name of Id not selected (start)
        if (empty($customerName) && empty($customerId)) {
            return redirect()->to(site_url('Admin/Return_sale/return/' . $InvId));
        }
        // If customer name of Id not selected (End)

        if (empty($amount)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please select any product <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $InvId));
        }

        // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (Start)
        if (!empty($customerName)) {
            if ($dueAmount != 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please clear due<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Return_sale/return/' . $InvId));
            }
        }
        // Validation for the new customer. New customer should only pay through cash and full payment. Other payment will not exeute. (End)


        if (($dueAmount < 0)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please valid input due<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Return_sale/return/' . $InvId));
        }

        DB()->transStart();

        //insert return Data in return_sale table(start)
        $returnData = array(
            'sch_id' => $shopId,
            'amount' => $amount,
            'nagad_paid' => $nagod,
            'bank_paid' => $bankAmount,
            'bank_id' => $bankId,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );

        if (!empty($customerId)) {
            $returnData['customer_id'] = $customerId;
        } else {
            $returnData['customer_name'] = $customerName;
        }

        $return_saleTab = DB()->table('return_sale');
        $return_saleTab->insert($returnData);
        $returnId = DB()->insertID();
        //insert return Data in return_sale table(end)


        //sale balance update and ledger create (start)
        $saleBal = get_data_by_id('sale_balance', 'shops', 'sch_id', $shopId);
        $restBalSale = $saleBal + $amount;


        $saleUpdata = array('sale_balance' => $restBalSale);
        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($saleUpdata);


        $saleLedgData = array(
            'sch_id' => $shopId,
            'rtn_sale_id' => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'return sale',
            'amount' => $amount,
            'rest_balance' => $restBalSale,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_salesTab = DB()->table('ledger_sales');
        $ledger_salesTab->insert($saleLedgData);
        //sale balance update and ledger create (end)


        //return_sale_item itame insert
        $totalPursPricr = 0;
        $number = count($proId);
        for ($i = 0; $i < $number; $i++) {

            // return_sale_item item data into return_sale_item table(Start)

            $total_price = $quantity[$i] * $proPrice[$i];

            $retuItemData = array(
                'sch_id' => $shopId,
                'rtn_sale_id' => $returnId,
                'prod_id' => $proId[$i],
                'price' => $proPrice[$i],
                'quantity' => $quantity[$i],
                'total_price' => $total_price,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $return_sale_itemTab = DB()->table('return_sale_item');
            $return_sale_itemTab->insert($retuItemData);
            //print $this->db->last_query();


            //product Qnt Update in product table (start)
            $productQnt = get_data_by_id('quantity', 'products', 'prod_id', $proId[$i]);
            $qnt = $productQnt + $quantity[$i];
            $qntProData = array(
                'quantity' => $qnt,
                'updatedBy' => $userId,
            );
            $productsTab = DB()->table('products');
            $productsTab->where('prod_id', $proId[$i])->update($qntProData);
            //product Qnt Update in product table (end)


            //calculating Total Pursess Price (start)
            $productPurPrice = get_data_by_id('purchase_price', 'products', 'prod_id', $proId[$i]);
            $purPrice = $productPurPrice * $quantity[$i];
            $totalPursPricr = $totalPursPricr + $purPrice;
            //calculating Total Pursess Price (end)

        }

        //All vat
        $vatId = get_data_by_id('vat_id', 'vat_register', 'sch_id', $shopId);
        $vatBalance = get_data_by_id('balance', 'vat_register', 'sch_id', $shopId);

        $vatrestBal = $vatBalance + $totalVat;
        $vatData = array(
            'balance' => $vatrestBal,
        );
        $vat_registerTab = DB()->table('vat_register');
        $vat_registerTab->where('sch_id', $shopId)->update($vatData);

        $vatLedData = array(
            'vat_id' => $vatId,
            'invoice_id' => $InvId,
            'sch_id' => $shopId,
            'particulars' => 'Return Sale Vat return',
            'trangaction_type' => 'Dr.',
            'amount' => $totalVat,
            'rest_balance' => $vatrestBal,
            'createdBy' => $shopId,
        );
        $ledger_vatTab = DB()->table('ledger_vat');
        $ledger_vatTab->insert($vatLedData);


        if ($customerId) {
            //return sale amount calculet and update customer balance (Start)
            $cusOldBalance = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
            $restBalance = $cusOldBalance - $amount;
            $cusData = array(
                'balance' => $restBalance,
                'createdBy' => $userId,
            );
            $customersTab = DB()->table('customers');
            $customersTab->where('customer_id', $customerId)->update($cusData);
            //return sale amount calculet and update customer balance (Start)


            // Insert customer Ledger (start)
            $cusLedData = array(
                'sch_id' => $shopId,
                'customer_id' => $customerId,
                'rtn_sale_id' => $returnId,
                'particulars' => 'Return Sale Product',
                'trangaction_type' => 'Cr.',
                'amount' => $amount,
                'rest_balance' => $restBalance,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledgerTab = DB()->table('ledger');
            $ledgerTab->insert($cusLedData);
            // Insert customer Ledger (start)
        }


        // return profit update(start)
        $rtnurnProfit = $amount - $totalPursPricr - $totalVat;

        $profitData = array('rtn_profit' => $rtnurnProfit);

        $return_saleTab = DB()->table('return_sale');
        $return_saleTab->where('rtn_sale_id', $returnId)->update($profitData);

        $profitShop = get_data_by_id('profit', 'shops', 'sch_id', $shopId);
        $restProfitShop = $profitShop + $rtnurnProfit;
        $profShopData = array('profit' => $restProfitShop, 'updatedBy' => $userId,);
        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($profShopData);


        $lgproData = array(
            'sch_id' => $shopId,
            'rtn_sale_id' => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Return Profit',
            'amount' => $rtnurnProfit,
            'rest_balance' => $restProfitShop,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_profitTab = DB()->table('ledger_profit');
        $ledger_profitTab->insert($lgproData);
        // return profit update(end)


        //stock update
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = $stockBal + $totalPursPricr;


        $stockUpdata = array('stockAmount' => $restBalStock);
        $shopsTab = DB()->table('shops');
        $shopsTab->where('sch_id', $shopId)->update($stockUpdata);


        $stockLedgData = array(
            'sch_id' => $shopId,
            'rtn_sale_id' => $returnId,
            'trangaction_type' => 'Dr.',
            'particulars' => 'Return sale',
            'amount' => $totalPursPricr,
            'rest_balance' => $restBalStock,
            'createdBy' => $userId,
            'createdDtm' => date('Y-m-d h:i:s')
        );
        $ledger_stockTab = DB()->table('ledger_stock');
        $ledger_stockTab->insert($stockLedgData);
        //Update salse profit in invoice table (end)


        //cash pay shop cash update and create nagod ledger (start)
        if ($nagod > 0) {
            //cash pay amount update shops cash (start)
            $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $upCahs = $shopsCash - $nagod;

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
                'rtn_sale_id' => $returnId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Return Sale Cash Pay',
                'amount' => $nagod,
                'rest_balance' => $upCahs,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $ledger_nagodanTab->insert($lgNagData);
            //insert ledger in ledger_nagodan cash pay amount(start)


            //return sale amount calculet and update customer balance (Start)
            if ($customerId) {
                $cusOldBalance2 = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $restBalance2 = $cusOldBalance2 + $nagod;
                $cusData2 = array(
                    'balance' => $restBalance2,
                    'createdBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($cusData2);
                //return sale amount calculet and update customer balance (Start)

                // Insert customer Ledger (start)
                $cusLedData2 = array(
                    'sch_id' => $shopId,
                    'customer_id' => $customerId,
                    'rtn_sale_id' => $returnId,
                    'particulars' => 'Return Sale Product cash Pay',
                    'trangaction_type' => 'Dr.',
                    'amount' => $nagod,
                    'rest_balance' => $restBalance2,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->insert($cusLedData2);
            }
            // Insert customer Ledger (start)

        }
        //cash pay shop cash update and create nagod ledger (end)


        // bank pay amount calculate and bank balance update (start)
        if ($bankAmount > 0) {
            //bank pay amount calculate and update bank balance (start)
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            $upCahs = $bankCash - $bankAmount;

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
                'rtn_sale_id' => $returnId,
                'particulars' => 'Return Sale Bank Pay',
                'trangaction_type' => 'Cr.',
                'amount' => $bankAmount,
                'rest_balance' => $upCahs,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_bankTab = DB()->table('ledger_bank');
            $ledger_bankTab->insert($lgBankData);
            //insert ledger in table ledger_bank (end)

            //return sale amount calculet and update customer balance (Start)
            if ($customerId) {
                $cusOldBalance3 = get_data_by_id('balance', 'customers', 'customer_id', $customerId);
                $restBalance3 = $cusOldBalance3 + $bankAmount;
                $cusData3 = array(
                    'balance' => $restBalance3,
                    'createdBy' => $userId,
                );
                $customersTab = DB()->table('customers');
                $customersTab->where('customer_id', $customerId)->update($cusData3);
                //return sale amount calculet and update customer balance (Start)

                // Insert customer Ledger (start)
                $cusLedData3 = array(
                    'sch_id' => $shopId,
                    'customer_id' => $customerId,
                    'rtn_sale_id' => $returnId,
                    'particulars' => 'Return Sale Product Bank Pay',
                    'trangaction_type' => 'Dr.',
                    'amount' => $bankAmount,
                    'rest_balance' => $restBalance3,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledgerTab = DB()->table('ledger');
                $ledgerTab->insert($cusLedData3);
            }
            // Insert customer Ledger (start)

        }
        // bank pay amount calculate and bank balance update (end)


        DB()->transComplete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Return Product Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Return_sale/'));

    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnSaleTable = DB()->table('return_sale');
            $data['returnSaleData'] = $returnSaleTable->where('rtn_sale_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_sale_item');
            $data['invoiceItame'] = $returnItem->where('rtn_sale_id', $id)->get()->getResult();


            $data['menu'] = view('Admin/menu_sales', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_sale/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

}