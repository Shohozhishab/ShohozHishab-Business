<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Libraries\Mycart;
use App\Libraries\TransactionLog;
use CodeIgniter\HTTP\RedirectResponse;


class Purchase extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $cart;
    private $module_name = 'Purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = new Mycart();
        $this->transactionLog = new TransactionLog();
    }

    /**
     * @description This method provides purchase view
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
            $purchaseTable = DB()->table('purchase');
            $data['purchase_data'] = $purchaseTable->where('sch_id', $shopId)->get()->getResult();


            //purchase table null value delete (start)
            $purcTable = DB()->table('purchase');
            $purchId = $purcTable->where('sch_id', $shopId)->where('due', NULL)->get()->getResult();
            foreach ($purchId as $value) {
                // purchasa itame fiend count (start)
                $purchase_itemTable = DB()->table('purchase_item');
                $purItem = $purchase_itemTable->where('purchase_id', $value->purchase_id)->countAllResults();
                // purchasa itame fiend count (end)

                //deleted Nul value in purchase (start)
                if ($purItem < 1) {
                    $purcDelTable = DB()->table('purchase');
                    $purcDelTable->where('purchase_id', $value->purchase_id)->delete();
                }
                //deleted Nul value in purchase (end)
            }
            //purchase table null value delete (end)


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides purchase create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Purchase/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store purchase
     * @return RedirectResponse
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['type_id'] = $this->request->getPost('type_id');
        $data['supplier_id'] = $this->request->getPost('supplier_id');

        $this->validation->setRules([
            'type_id' => ['label' => 'type_id', 'rules' => 'required'],
            'supplier_id' => ['label' => 'supplier_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/create'));
        } else {

            $shopCheck = check_shop('suppliers', 'supplier_id', $data['supplier_id']);
            if ($shopCheck == 1) {

                $datapur = array(
                    'sch_id' => $shopId,
                    'supplier_id' => $data['supplier_id'],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchaseTable = DB()->table('purchase');
                $purchaseTable->insert($datapur);
                $purchaseId = DB()->insertID();

                $purchaseData = array(
                    'supplierId' => $data['supplier_id'],
                    'purchaseId' => $purchaseId,
                );
                $this->session->set($purchaseData);

                if ($data['type_id'] == 1) {
                    return redirect()->to(site_url('Admin/Purchase/new_product'));
                } else {
                    return redirect()->to(site_url('Admin/Purchase/existing_product'));
                }

            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">please select a valid supplier<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/create'));
            }

        }
    }

    /**
     * @description This method new product
     * @return RedirectResponse|void
     */
    public function new_product()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/product_create_action');
            if (empty($this->session->purchaseId)) {
                return redirect()->to(site_url('Admin/Purchase/create'));
            }
            if (!empty($this->session->cartType)) {
                if ($this->session->cartType == 'sale') {
                    $this->cart->destroy();
                }
            }
            $table = DB()->table('suppliers');
            $data['supplier'] = $table->where('supplier_id',$this->session->supplierId)->get()->getRow();

            $data['supplierId'] = $this->session->supplierId;
            $data['purchaseId'] = $this->session->purchaseId;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Purchase/new_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method existing product
     * @return RedirectResponse|void
     */
    public function existing_product()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/existing_create_action');
            if (empty($this->session->purchaseId)) {
                return redirect()->to(site_url('Admin/Purchase/create'));
            }
            if (!empty($this->session->cartType)) {
                if ($this->session->cartType == 'sale') {
                    $this->cart->destroy();
                }
            }

            $data['supplierId'] = $this->session->supplierId;
            $data['purchaseId'] = $this->session->purchaseId;

            $proTable = DB()->table('products');
            $data['product'] = $proTable->where('supplier_id', $data['supplierId'])->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Purchase/existing_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }


    }

    /**
     * @description This method store purchase
     * @return RedirectResponse
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $purchaseId = $this->request->getPost('purchase_id');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $returnchecked = $this->request->getPost('returnchecked[]');
        $products = $this->request->getPost('prod_id[]');
        $productQuantity = $this->request->getPost('quantity[]');
        $productPrice = $this->request->getPost('purchase_price[]');
        //payment data
        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        DB()->transStart();
        if ($totalPrice > 0) {
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $tabsuppliers = DB()->table('suppliers');
            $tabsuppliers->where('supplier_id', $supplierId)->update($suppData);

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'particulars' => 'Purchase Cash Due',
                'trangaction_type' => 'Cr.',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_suppliers = DB()->table('ledger_suppliers');
            $tabledger_suppliers->insert($lgSuplData);
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)


            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $tabshops = DB()->table('shops');
            $tabshops->where('sch_id', $shopId)->update($purUpdata);


            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_purchase = DB()->table('ledger_purchase');
            $tabledger_purchase->insert($purLedgData);
            // purchase balance update and ledger create (end)


            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $tabshopsSt = DB()->table('shops');
            $tabshopsSt->where('sch_id', $shopId)->update($stockUpdata);


            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_stock = DB()->table('ledger_stock');
            $tabledger_stock->insert($stockLedgData);
            // stock balance update and ledger create (end)


            if ($cashAmount > 0) {

                //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $tabshops = DB()->table('shops');
                    $tabshops->where('sch_id', $shopId)->update($shopsData);

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Cash Pay',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_nagodan = DB()->table('ledger_nagodan');
                    $tabledger_nagodan->insert($lgNagData);
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)


                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $tabsuppliers = DB()->table('suppliers');
                    $tabsuppliers->where('supplier_id', $supplierId)->update($cashsuppData);

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_suppliers = DB()->table('ledger_suppliers');
                    $tabledger_suppliers->insert($lgSuplData);
                }
                //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }

            if ($bankAmount > 0) {

                //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $tabbank = DB()->table('bank');
                    $tabbank->where('bank_id', $bankId)->update($bankData);

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_bank = DB()->table('ledger_bank');
                    $tabledger_bank->insert($lgBankData);
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)


                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $tabsuppliers = DB()->table('suppliers');
                    $tabsuppliers->where('supplier_id', $supplierId)->update($banksuppData);

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $tabledger_suppliers = DB()->table('ledger_suppliers');
                    $tabledger_suppliers->insert($lgSuplData);
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }


            //purchase product insert in product table and purchase item table (start)
            foreach ($returnchecked as $value) {
                $k = array_flip($products);
                $key = $k[$value];

                $product_id = $value;
                $quantity = $productQuantity[$key];
                $price = $productPrice[$key];
                //Update each product quantity and price
                $exixQunt = get_data_by_id('quantity', 'products', 'prod_id', $product_id);
                $newQunt = $exixQunt + $quantity;

                $data = array(
                    'quantity' => $newQunt,
                    'purchase_price' => $price,
                );
                $tabproducts = DB()->table('products');
                $tabproducts->where('prod_id', $product_id)->update($data);

                //insetr purchase Item in purchase item table
                $total_price = $price * $quantity;
                $purItemData = array(
                    'prod_id' => $product_id,
                    'purchase_id' => $purchaseId,
                    'quantity' => $quantity,
                    'purchase_price' => $price,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $tabpurchase_item = DB()->table('purchase_item');
                $tabpurchase_item->insert($purItemData);
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'updatedBy' => $userId,
            );
            $tabpurchase = DB()->table('purchase');
            $tabpurchase->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)
            if (!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }
        } else {
            $this->session->setFlashdata('message', '<div style="margin-top: 12px" class="alert alert-danger" id="message">Invalid Quantity</div>');
            redirect(site_url('purchase'));
        }
        DB()->transComplete();


        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Purchase'));
    }

    /**
     * @description This method check sub cat
     * @return void
     */
    public function check_sub_cat()
    {
        $shopId = $this->session->shopId;

        $Id = $this->request->getPost('ID');
        $product_categoryTable = DB()->table('product_category');
        $query = $product_categoryTable->where('parent_pro_cat', $Id)->where('sch_id', $shopId)->where("deleted IS NULL")->get()->getResult();
        $options = '';
        foreach ($query as $row) {

            $options .= '<option value="' . $row->prod_cat_id . '" ';
            $options .= '>' . $row->product_category . '</option>';
        }
        print $options;
    }

    /**
     * @description This method add cart
     * @return void
     */
    public function addCart()
    {

        $data['subCatId'] = $this->request->getPost('subCatId');
        $data['category'] = $this->request->getPost('category');
        $data['name'] = $this->request->getPost('name');
        $data['unit'] = $this->request->getPost('unit');
        $data['price'] = $this->request->getPost('price');
        $data['salePrice'] = $this->request->getPost('salePrice');
        $data['qty'] = $this->request->getPost('qty');

        $this->validation->setRules([
            'subCatId' => ['label' => 'subCatId', 'rules' => 'required'],
            'category' => ['label' => 'category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'salePrice' => ['label' => 'salePrice', 'rules' => 'required'],
            'qty' => ['label' => 'qty', 'rules' => 'required|is_natural_no_zero'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($data['qty'] > 0) {
                $i = count($this->cart->contents());
                $data2 = array(
                    'id' => ++$i,
                    'name' => $data['name'],
                    'unit' => $data['unit'],
                    'qty' => $data['qty'],
                    'price' => $data['price'],
                    'salePrice' => $data['salePrice'],
                    'cat_id' => $data['subCatId']
                );

                $this->cart->insert($data2);
                $this->session->set('cartType', 'purchase');

            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Enter a valid quantity! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }

    }

    /**
     * @description This method remove cart
     * @return void
     */
    public function remove_cart()
    {
        $id = $this->request->getPost('id');
        $this->cart->remove($id);
    }

    /**
     * @description This method clear cart
     * @return void
     */
    public function clearCart()
    {
        $this->cart->destroy();
    }

    /**
     * @description This method check shop balance
     * @return int
     */
    public function check_shop_balance()
    {
        $shopId = $this->session->shopId;
        $cash = $this->request->getPost('cash');

        $shopsTable = DB()->table('shops');
        $query = $shopsTable->where('sch_id', $shopId)->get()->getRow();

        $balance = $query->cash;
        if ($cash > $balance) {
            return print '0';
        } else {
            return print '1';
        }
    }

    /**
     * @description This method check bank balance
     * @return int
     */
    public function check_bank_balance()
    {
        $amount = $this->request->getPost('balance');
        $bankId = $this->request->getPost('bank_id');

        $bankBalance = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
        if ($amount > $bankBalance) {
            return print '0';
        } else {
            return print '1';
        }
    }

    /**
     * @description This method store products
     * @return RedirectResponse
     */
    public function product_create_action()
    {
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $purchaseId = $this->request->getPost('purchase_id');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        $name = $this->request->getPost('name[]');

        if (!empty($this->cart->contents())) {
            $number = count($name);

            $cashbal = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $bankbal = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

            if ($cashbal < $cashAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if ($bankbal < $bankAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($cashAmount) && $cashAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }

            DB()->transStart();

            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (start)
            $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
            $newCash = $supplierCash - $totalPrice;

            $suppData = array(
                'balance' => $newCash,
                'updatedBy' => $userId,
            );
            $suppliersTable = DB()->table('suppliers');
            $suppliersTable->where('supplier_id', $supplierId)->update($suppData);

            //insert log (start)
            $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            //create suppliers ledger
            $lgSuplData = array(
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Cr.',
                'particulars' => 'Purchase Cash Due',
                'amount' => $totalPrice,
                'rest_balance' => $newCash,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_suppliersTable = DB()->table('ledger_suppliers');
            $ledger_suppliersTable->insert($lgSuplData);
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // purchase balance update and ledger create (start)
            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalPrice;


            $purUpdata = array('purchase_balance' => $restBalPurc);
            $shopPurBalTable = DB()->table('shops');
            $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$restBalPurc,'','','',$purchaseId,'purchase_balance');
            //insert log (end)

            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_purchaseTable = DB()->table('ledger_purchase');
            $ledger_purchaseTable->insert($purLedgData);
            $ledgPurch_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_purchase',$ledgPurch_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            // stock balance update and ledger create (start)
            $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $restBalStock = $stockBal + $totalPrice;


            $stockUpdata = array('stockAmount' => $restBalStock);
            $shopStoAmTable = DB()->table('shops');
            $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdata);

            //insert log (start)
            $this->transactionLog->insert_log_data('shops',$shopId,'',$totalPrice,'','','',$purchaseId,'stockAmount');
            //insert log (end)

            $stockLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New purchase amount',
                'amount' => $totalPrice,
                'rest_balance' => $restBalStock,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_stockTable = DB()->table('ledger_stock');
            $ledger_stockTable->insert($stockLedgData);
            $stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //insert log (start)
            $this->transactionLog->insert_log_data('ledger_stock',$stock_id,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            if ($cashAmount > 0) {

                //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (start)
                $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);

                if ($shopsCash >= $cashAmount) {
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops',$shopId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Cr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($cashsuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();
                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }


            if ($bankAmount > 0) {

                //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
                $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

                if ($bankCash >= $bankAmount) {

                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($banksuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();
                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)

            }


            //purchase product insert in product table and purchase item table (start)
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;

            for ($i = 0; $i < $number; $i++) {
                //insert purchase product
                $data = array(
                    'sch_id' => $shopId,
                    'store_id' => $storeId,
                    'name' => $name[$i],
                    'unit' => $this->request->getPost('unit[]')[$i],
                    'quantity' => $this->request->getPost('quantity[]')[$i],
                    'purchase_price' => $this->request->getPost('purchase_price[]')[$i],
                    'selling_price' => $this->request->getPost('selling_price[]')[$i],
                    'supplier_id' => $supplierId,
                    'prod_cat_id' => $this->request->getPost('prod_cat_id[]')[$i],
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $proTable = DB()->table('products');
                $proTable->insert($data);
                $prodId = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('products',$prodId,'',$data['purchase_price'],'','','',$purchaseId);
                //insert log (end)

                //insetr purchase Item in purchase item table
                $purchasePrice = get_data_by_id('purchase_price', 'products', 'prod_id', $prodId);
                $quantity = get_data_by_id('quantity', 'products', 'prod_id', $prodId);

                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'purchase_price' => $purchasePrice,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $purchase_itemTab = DB()->table('purchase_item');
                $purchase_itemTab->insert($purchaseData);
                $purchase_item_id = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('purchase_item',$purchase_item_id,'',$total_price,'','','',$purchaseId);
                //insert log (end)
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'updatedBy' => $userId,
            );
            $purchaseTab = DB()->table('purchase');
            $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)
            //insert log (start)
            $this->transactionLog->insert_log_data('purchase',$purchaseId,'',$totalPrice,'','','',$purchaseId);
            //insert log (end)

            DB()->transComplete();
            if(!empty($sms)) {
                $message = 'Thank you for your purchase.Your purchase amount is-' . $totalPrice;
                $phone = get_data_by_id('phone', 'suppliers', 'supplier_id', $supplierId);
                send_sms($phone, $message);
            }

            $this->cart->destroy();
            unset($_SESSION['supplier_id']);
            unset($_SESSION['purchaseId']);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/new_product'));
        }

    }

    /**
     * @description This method provides purchase view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function view($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $purchaseTable = DB()->table('purchase');
            $data['purchase'] = $purchaseTable->where('purchase_id', $id)->get()->getRow();

            $purchase_itemTable = DB()->table('purchase_item');
            $data['purchaseItame'] = $purchase_itemTable->where('purchase_id', $id)->get()->getResult();
            $data['purchaseId'] = $id;

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Purchase/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function purchaseEdit(){
        $purchaseId = $this->request->getPost('id');

        $purchaseTable = DB()->table('purchase');
        $data['purchase'] = $purchaseTable->where('purchase_id', $purchaseId)->get()->getRow();

        $purchase_itemTable = DB()->table('purchase_item');
        $data['purchaseItem'] = $purchase_itemTable->where('purchase_id', $purchaseId)->get()->getResult();

        echo view('Admin/Purchase/edit', $data);
    }

    public function purchaseEdiAction(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $purchaseId = $this->request->getPost('purchase_id');

        $prod_id = $this->request->getPost('prod_id[]');
        $qty = $this->request->getPost('qty[]');
        $price = $this->request->getPost('price[]');
        $total_price = $this->request->getPost('total_price[]');


        $totalPrice = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankId = $this->request->getPost('bank_id');
        $bankAmount = $this->request->getPost('bank');
        $totalDue = $this->request->getPost('due');


        DB()->transStart();
        //purchase total price calculate to suppliers balance and update suppliers balance or create suppliers ledger (start)
        $suppliersInfo = $this->transactionLog->get_table_name_by_row_purchase_id('suppliers',$purchaseId);
        $supplierCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $suppliersInfo->id);
        $newCash = ($supplierCash + $suppliersInfo->amount) - $totalPrice;
        $supplierId = $suppliersInfo->id;
        $suppData = array(
            'balance' => $newCash,
        );
        $suppliersTable = DB()->table('suppliers');
        $suppliersTable->where('supplier_id', $suppliersInfo->id)->update($suppData);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('suppliers','','',$this->session->userId,$suppliersInfo->amount,$totalPrice,'',$purchaseId);
        //insert Transaction in transaction table (end)


        //update suppliers ledger
        $suppliersLedgerInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_suppliers',$purchaseId);
        $supplierRestBalance = get_data_by_id('rest_balance', 'ledger_suppliers', 'ledg_sup_id', $suppliersLedgerInfo->id);

        $newRestBalance = ($supplierRestBalance + $suppliersLedgerInfo->amount) - $totalPrice;
        $lgSuplData = array(
            'amount' => $totalPrice,
            'rest_balance' => $newRestBalance,
        );
        $ledger_suppliersTable = DB()->table('ledger_suppliers');
        $ledger_suppliersTable->where('ledg_sup_id', $suppliersLedgerInfo->id)->update($lgSuplData);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_suppliers','','',$this->session->userId,$suppliersLedgerInfo->amount,$totalPrice,'',$purchaseId);
        //insert Transaction in transaction table (end)

        $this->suppliers_ledger_rest_balance_update($suppliersInfo->id,$totalPrice,$suppliersLedgerInfo->id,$suppliersLedgerInfo->amount);
        //purchase total price calculate to suppliers balance and update suppliers balance or create suppliers ledger (end)


        // purchase balance update and ledger create (start)
        $purchaseBalanceInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops',$purchaseId,'purchase_balance');
        $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $restBalPurc = ($purchaseBal - $totalPrice) + $purchaseBalanceInfo->amount;


        $purUpdata = array('purchase_balance' => $restBalPurc);
        $shopPurBalTable = DB()->table('shops');
        $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_suppliers','','',$this->session->userId,$suppliersLedgerInfo->amount,$totalPrice,'',$purchaseId,'purchase_balance');
        //insert Transaction in transaction table (end)

        $ledgerPurchaseInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_purchase',$purchaseId);
        $purchaseLedgerBal = get_data_by_id('rest_balance', 'ledger_purchase', 'ledgPurch_id', $ledgerPurchaseInfo->id);
        $newPurchaseLedgerBal = ($purchaseLedgerBal - $ledgerPurchaseInfo->amount) + $totalPrice;
        $purLedgData = array(
            'amount' => $totalPrice,
            'rest_balance' => $newPurchaseLedgerBal,
        );
        $ledgerPurchaseTable = DB()->table('ledger_purchase');
        $ledgerPurchaseTable->where('ledgPurch_id', $ledgerPurchaseInfo->id)->update($purLedgData);
        $this->purchase_ledger_rest_balance_update($purchaseId,$totalPrice);
        // purchase balance update and ledger create (end)

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_purchase','','',$this->session->userId,$ledgerPurchaseInfo->amount,$totalPrice,'',$purchaseId,'');
        //insert Transaction in transaction table (end)


        // stock balance update and ledger create (start)
        $stockAmountInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops',$purchaseId,'stockAmount');
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = ($stockBal - $stockAmountInfo->amount) + $totalPrice;
        $stockUpdata = array('stockAmount' => $restBalStock);
        $shopStoAmTable = DB()->table('shops');
        $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdata);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('shops','','',$this->session->userId,$stockAmountInfo->amount,$totalPrice,'',$purchaseId,'stockAmount');
        //insert Transaction in transaction table (end)


        $ledgerStockInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_stock',$purchaseId);
        $ledgerStockBal = get_data_by_id('rest_balance', 'ledger_stock', 'stock_id', $ledgerStockInfo->id);
        $restLedgerStockBal = ($ledgerStockBal - $ledgerStockInfo->amount) + $totalPrice;
        $stockLedgData = array(
            'amount' => $totalPrice,
            'rest_balance' => $restLedgerStockBal,
        );
        $ledger_stockTable = DB()->table('ledger_stock');
        $ledger_stockTable->where('stock_id', $ledgerStockInfo->id)->update($stockLedgData);

        //transaction edit log data insert
        $this->transactionLog->transaction_edit_log_data_insert('ledger_stock','','',$this->session->userId,$ledgerStockInfo->amount,$totalPrice,'',$purchaseId );
        //insert Transaction in transaction table (end)

        $this->stock_ledger_rest_balance_update($purchaseId,$totalPrice);
        // stock balance update and ledger create (end)

        if ($cashAmount > 0) {
            //purchase pay cash amount calculate (start)
            $shopsCash = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            if ($shopsCash >= $cashAmount) {
                $shopInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops',$purchaseId,null);
                if (!empty($shopInfo)) {
                    $upCahs = ($shopsCash + $shopInfo->amount) - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('shops','','',$this->session->userId,$shopInfo->amount,$cashAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $ledgerNagodInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_nagodan', $purchaseId);
                    $ledgerNagodBal = get_data_by_id('rest_balance', 'ledger_nagodan', 'ledg_nagodan_id', $ledgerNagodInfo->id);
                    $upRestCahs = ($ledgerNagodBal + $ledgerNagodInfo->amount) - $cashAmount;
                    //nagodan ledger create
                    $lgNagData = array(
                        'amount' => $cashAmount,
                        'rest_balance' => $upRestCahs,
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->where('ledg_nagodan_id', $ledgerNagodInfo->id)->update($lgNagData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('ledger_nagodan','','',$this->session->userId,$ledgerNagodInfo->amount,$cashAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $this->cash_ledger_rest_balance_update($shopId, $cashAmount, $ledgerNagodInfo->id, $ledgerNagodInfo->amount);
                    //purchase pay cash amount calculate to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)


                    //purchase pay cash amount calculate to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $total = DB()->table('transaction_log')->where('table_name', 'suppliers')->where('purchase_id', $purchaseId)->countAllResults();
                    $middle = floor($total / 2);

                    $query = DB()->table('transaction_log')
                        ->where('table_name', 'suppliers')
                        ->where('purchase_id', $purchaseId)
                        ->orderBy('transaction_log_id', 'ASC')
                        ->limit(1, $middle)->get();
                    $supplierMidInfo = $query->getRow();

                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierMidInfo->id);
                    $suppCash = ($supplierccCash - $supplierMidInfo->amount) + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierMidInfo->id)->update($cashsuppData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('suppliers','','',$this->session->userId,$supplierMidInfo->amount,$cashAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $total = DB()->table('transaction_log')->where('table_name', 'ledger_suppliers')->where('purchase_id', $purchaseId)->countAllResults();
                    $middle = floor($total / 2);

                    $queryLed = DB()->table('transaction_log')
                        ->where('table_name', 'ledger_suppliers')
                        ->where('purchase_id', $purchaseId)
                        ->orderBy('transaction_log_id', 'ASC')
                        ->limit(1, $middle)->get();
                    $supplierLedgerMidInfo = $queryLed->getRow();

                    $ledgerSuppOldBal = get_data_by_id('rest_balance', 'ledger_suppliers', 'ledg_sup_id', $supplierLedgerMidInfo->id);
                    $supplierLedgerRestBal = ($ledgerSuppOldBal - $supplierLedgerMidInfo->amount) + $cashAmount;
                    //suppliers ledger create
                    $lgSuplData = array(
                        'amount' => $cashAmount,
                        'rest_balance' => $supplierLedgerRestBal,
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->where('ledg_sup_id', $supplierLedgerMidInfo->id)->update($lgSuplData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('ledger_suppliers','','',$this->session->userId,$supplierLedgerMidInfo->amount,$cashAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $this->suppliers_ledger_rest_balance_update($supplierMidInfo->id, $cashAmount, $supplierLedgerMidInfo->id, $supplierLedgerMidInfo->amount);
                }else{
                    $upCahs = $shopsCash - $cashAmount;
                    $shopsData = array(
                        'cash' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $shopCasTable = DB()->table('shops');
                    $shopCasTable->where('sch_id', $shopId)->update($shopsData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('shops',$shopId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //nagodan ledger create
                    $lgNagData = array(
                        'sch_id' => $shopId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Cr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_nagodanTable = DB()->table('ledger_nagodan');
                    $ledger_nagodanTable->insert($lgNagData);
                    $ledg_nagodan_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_nagodan',$ledg_nagodan_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purshase pay cash amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierccCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppCash = $supplierccCash + $cashAmount;

                    $cashsuppData = array(
                        'balance' => $suppCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($cashsuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Cash Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $cashAmount,
                        'rest_balance' => $suppCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();
                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$cashAmount,'','','',$purchaseId);
                    //insert log (end)
                }
            }
            //purchase pay cash amount calculate (end)
        }

        if ($bankAmount > 0) {
            //purchase pay bank amount calculate to bank balance and update bank balance or create bank ledger in ledger_bank table (start)
            $bankCash = get_data_by_id('balance', 'bank', 'bank_id', $bankId);
            if ($bankCash >= $bankAmount) {
                $bankInfo = $this->transactionLog->get_table_name_by_row_purchase_id('bank',$purchaseId);
                if (!empty($bankInfo)) {
                    $upCahs = ($bankCash + $bankInfo->amount) - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('bank','','',$this->session->userId,$bankInfo->amount,$bankAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $bankLedgerInfo = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_bank', $purchaseId);
                    $bankLedCash = get_data_by_id('rest_balance', 'ledger_bank', 'ledgBank_id', $bankLedgerInfo->id);
                    $upRestCahs = ($bankLedCash + $bankLedgerInfo->amount) - $bankAmount;
                    //bank ledger create
                    $lgBankData = array(
                        'amount' => $bankAmount,
                        'rest_balance' => $upRestCahs,
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->where('ledgBank_id', $bankLedgerInfo->id)->update($lgBankData);
                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('ledger_bank','','',$this->session->userId,$bankLedgerInfo->amount,$bankAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)
                    $this->bank_ledger_rest_balance_update($bankId, $bankAmount, $bankLedgerInfo->id, $bankLedgerInfo->amount);
                    //purchase pay bank amount calculate to bank balance and update bank balance or create bank ledger in ledger_bank table (end)


                    //purchase pay bank amount calculate to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $table = DB()->table('transaction_log');
                    $supLastInfo = $table->where('purchase_id', $purchaseId)->where('table_name', 'suppliers')->get()->getLastRow();
                    $suppLierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supLastInfo->id);
                    $suppBaCash = ($suppLierbbCash - $supLastInfo->amount) + $bankAmount;

                    $bankSuppData = array(
                        'balance' => $suppBaCash,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supLastInfo->id)->update($bankSuppData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('suppliers','','',$this->session->userId,$supLastInfo->amount,$bankAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)


                    $table = DB()->table('transaction_log');
                    $supLastLedInfo = $table->where('purchase_id', $purchaseId)->where('table_name', 'ledger_suppliers')->get()->getLastRow();
                    $suppLedgereCash = get_data_by_id('rest_balance', 'ledger_suppliers', 'ledg_sup_id', $supLastLedInfo->id);
                    $suppLedRestCash = ($suppLedgereCash - $supLastLedInfo->amount) + $bankAmount;
                    //suppliers ledger create
                    $lgSuplData = array(
                        'amount' => $bankAmount,
                        'rest_balance' => $suppLedRestCash,
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->where('ledg_sup_id', $supLastLedInfo->id)->update($lgSuplData);

                    //transaction edit log data insert
                    $this->transactionLog->transaction_edit_log_data_insert('ledger_suppliers','','',$this->session->userId,$supLastLedInfo->amount,$bankAmount,'',$purchaseId );
                    //insert Transaction in transaction table (end)

                    $this->suppliers_ledger_rest_balance_update($supLastInfo->id, $bankAmount, $supLastLedInfo->id, $supLastLedInfo->amount);
                }else{
                    $upCahs = $bankCash - $bankAmount;
                    $bankData = array(
                        'balance' => $upCahs,
                        'updatedBy' => $userId,
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->where('bank_id', $bankId)->update($bankData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('bank',$bankId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //bank ledger create
                    $lgBankData = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'purchase_id' => $purchaseId,
                        'trangaction_type' => 'Cr.',
                        'particulars' => 'Purchase Bank Pay',
                        'amount' => $bankAmount,
                        'rest_balance' => $upCahs,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTab = DB()->table('ledger_bank');
                    $ledger_bankTab->insert($lgBankData);
                    $ledgBank_id = DB()->insertID();
                    //purchase pay bank amount calculate to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_bank',$ledgBank_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //purchase pay bank amount calculate to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (start)
                    $supplierbbCash = get_data_by_id('balance', 'suppliers', 'supplier_id', $supplierId);
                    $suppBaCash = $supplierbbCash + $bankAmount;

                    $banksuppData = array(
                        'balance' => $suppBaCash,
                        'updatedBy' => $userId,
                    );
                    $suppliersTab = DB()->table('suppliers');
                    $suppliersTab->where('supplier_id', $supplierId)->update($banksuppData);
                    //insert log (start)
                    $this->transactionLog->insert_log_data('suppliers',$supplierId,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)

                    //suppliers ledger create
                    $lgSuplData = array(
                        'sch_id' => $shopId,
                        'supplier_id' => $supplierId,
                        'purchase_id' => $purchaseId,
                        'particulars' => 'Purchase Bank Pay',
                        'trangaction_type' => 'Dr.',
                        'amount' => $bankAmount,
                        'rest_balance' => $suppBaCash,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_suppliersTab = DB()->table('ledger_suppliers');
                    $ledger_suppliersTab->insert($lgSuplData);
                    $ledg_sup_id = DB()->insertID();
                    //insert log (start)
                    $this->transactionLog->insert_log_data('ledger_suppliers',$ledg_sup_id,'',$bankAmount,'','','',$purchaseId);
                    //insert log (end)
                }
            }
            //purchase pay bank amount calculate to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
        }



        $number = count($prod_id);
        for ($i = 0; $i < $number; $i++) {
            //insert purchase product
            $data = array(
                'quantity' => $qty[$i],
                'purchase_price' => $price[$i],
            );
            $proTable = DB()->table('products');
            $proTable->where('prod_id',$prod_id[$i])->update($data);

            //insetr purchase Item in purchase item table
            $purchaseData = array(
                'purchase_price' => $price[$i],
                'quantity' => $qty[$i],
                'total_price' => $total_price[$i],
            );
            $purchase_itemTab = DB()->table('purchase_item');
            $purchase_itemTab->where('prod_id',$prod_id[$i])->update($purchaseData);
        }
        //purchase product insert in product table and purchase item table (end)


        //purchase all pay amount detail Update in purchase table(start)
        $parsData = array(
            'amount' => $totalPrice,
            'nagad_paid' => $cashAmount,
            'bank_paid' => $bankAmount,
            'bank_id' => $bankId,
            'due' => $totalDue,
        );
        $purchaseTab = DB()->table('purchase');
        $purchaseTab->where('purchase_id', $purchaseId)->update($parsData);
        //purchase all pay amount detail Update in purchase table(end)

        DB()->transComplete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Purchase'));
    }

    private function bank_ledger_rest_balance_update($bankId,$amount,$ledgBankId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_bank');
        $result = $ledgerTable->where('ledgBank_id >', $ledgBankId)->where('bank_id', $bankId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledgBank_id']  = $val->ledgBank_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledgBank_id']  = $val->ledgBank_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_bank');
            $table->updateBatch($arrayUpData, 'ledgBank_id');
        }
    }
    private function cash_ledger_rest_balance_update($shopId,$amount,$ledgNagodanId,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_nagodan');
        $result = $ledgerTable->where('ledg_nagodan_id >', $ledgNagodanId)->where('sch_id', $shopId)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_nagodan_id']  = $val->ledg_nagodan_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_nagodan');
            $table->updateBatch($arrayUpData, 'ledg_nagodan_id');
        }
    }
    private function suppliers_ledger_rest_balance_update($supplier_id,$amount,$ledg_sup_id,$ledgerAmount)
    {
        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_suppliers');
        // Query all ledger entries for the same suppliers where the ledger ID is greater than the current transaction's ID
        $result = $ledgerTable->where('ledg_sup_id >', $ledg_sup_id)->where('supplier_id', $supplier_id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            if ($val->trangaction_type == 'Dr.') {
                $data['ledg_sup_id']  = $val->ledg_sup_id;
                $data['rest_balance'] = ($val->rest_balance - $ledgerAmount) + $amount;
                array_push($arrayUpData, $data);
            } else {
                $data['ledg_sup_id']  = $val->ledg_sup_id;
                $data['rest_balance'] = ($val->rest_balance + $ledgerAmount) - $amount;
                array_push($arrayUpData, $data);
            }
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_suppliers');
            $table->updateBatch($arrayUpData, 'ledg_sup_id');
        }
    }
    private function purchase_ledger_rest_balance_update($purchaseId,$amount)
    {
        // Get the specific ledger log entry using a helper function that returns table data by $purchaseId
        $ledLog = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_purchase', $purchaseId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_purchase');

        // Query all ledger entries is greater than the current transaction's ID
        $result = $ledgerTable->where('ledgPurch_id >', $ledLog->id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            $data['ledgPurch_id']  = $val->ledgPurch_id;
            $data['rest_balance'] = ($val->rest_balance - $ledLog->amount) + $amount;
            array_push($arrayUpData, $data);
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_purchase');
            $table->updateBatch($arrayUpData, 'ledgPurch_id');
        }
    }
    private function stock_ledger_rest_balance_update($purchaseId,$amount)
    {
        // Get the specific ledger log entry using a helper function that returns table data by $purchaseId
        $ledLog = $this->transactionLog->get_table_name_by_row_purchase_id('ledger_stock', $purchaseId);

        // Get a reference to the 'ledger' table using the query builder
        $ledgerTable = DB()->table('ledger_stock');
        // Query all ledger entries is greater than the current transaction's ID
        $result = $ledgerTable->where('stock_id >', $ledLog->id)->get()->getResult();

        // Initialize an array to store updated ledger data
        $arrayUpData = [];
        foreach ($result as $val) {
            $data['stock_id']  = $val->stock_id;
            $data['rest_balance'] = ($val->rest_balance - $ledLog->amount) + $amount;
            array_push($arrayUpData, $data);
        }
        if (!empty($arrayUpData)) {
            $table = DB()->table('ledger_stock');
            $table->updateBatch($arrayUpData, 'stock_id');
        }
    }

}