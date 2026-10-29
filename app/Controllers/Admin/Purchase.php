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

            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('purchase');
            $table->where('sch_id', $shopId);
            // Apply date filters only if they are present in the request
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('date >=', $st_date . ' 00:00:00');
                $table->where('date <=', $en_date . ' 23:59:59');
            }
            $data['purchase_data'] = $table->get()->getResult();

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';



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
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Purchase/product_create_action');
            if (empty($this->session->purchaseId)) {
                return redirect()->to(site_url('Admin/Purchase/create'));
            }
            if (!empty($this->session->cartType) && $this->session->cartType !== 'purchase') {
                $this->cart->destroy();
            }
            $table = DB()->table('suppliers');
            $data['supplier'] = $table->where('supplier_id',$this->session->supplierId)->get()->getRow();

            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id',$shopId)->get()->getResult();

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

        $date = $this->request->getPost('date');

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
            $ledg_sup_id = DB()->insertID();
            //purshase total price calculet to suppliers balance and update suppliers balance or create suppliers ledger (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

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
            $ledg_pur_id = DB()->insertID();
            // purchase balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_pur_id, 'ledger_purchase', 'Dr.');


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
            $ledg_stock_id = DB()->insertID();
            // stock balance update and ledger create (end)

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_stock_id, 'ledger_stock', 'Dr.');


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
                    $ledg_nagod_id = DB()->insertID();
                    //purshase pay cash amount calculet to shops cash and update shops cash or create ledger_nagodan statment in ledger_nagodan table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagod_id, 'ledger_nagodan', 'Cr.');

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
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');
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
                    $ledg_bank_id = DB()->insertID();
                    //purshase pay bank amountcalculet to bank balance and update bank balance or create bank ledger in ledger_bank table (end)

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_bank_id, 'ledger_bank', 'Cr.');


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
                    $ledg_sup_id = DB()->insertID();

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');
                }
                //purshase pay bank amount calculet to suppliers balance and update suppliers balance or create supplier ledger in ledger_suppliers table (end)
            }

            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();
            $storeId = $store->store_id;
            //purchase product insert in product table and purchase item table (start)
            foreach ($returnchecked as $value) {
                $k = array_flip($products);
                $key = $k[$value];

                $product_id = $value;
                $quantity = $productQuantity[$key];
                $price = $productPrice[$key];

                $data = array(
                    'purchase_price' => $price,
                );
                $tabproducts = DB()->table('products');
                $tabproducts->where('prod_id', $product_id)->update($data);

                $stockTable = DB()->table('product_stock_relation');
                $stock = $stockTable->where('store_id',$store->store_id)->where('product_id', $product_id)->get()->getRow();
                $newQunt = $stock->quantity + $quantity;
                $dataQty = array(
                    'quantity' => $newQunt,
                );
                $stockTableUpdate = DB()->table('product_stock_relation');
                $stockTableUpdate->where('store_id',$store->store_id)->where('product_id', $product_id)->update($dataQty);

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
                'date' => $date,
                'updatedBy' => $userId,
            );
            $tabpurchase = DB()->table('purchase');
            $tabpurchase->where('purchase_id', $purchaseId)->update($parsData);
            //purchase all pay amount detail Update in purchase table(end)

            // transaction events insert;
            DB()->table('transaction_events')->insert([
                'sch_id' => $shopId,
                'purchase_id'      => $purchaseId,
                'createdDtm'       => date('Y-m-d H:i:s')
            ]);

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
        $categories_id = $this->request->getPost('categories_id');

        $data['subCatId'] = $this->request->getPost('sub_category');
        $data['category'] = $this->request->getPost('category');
        $data['name'] = $this->request->getPost('name');
        $data['price'] = $this->request->getPost('price');
        $data['salePrice'] = $this->request->getPost('selling_price');
        $data['sale_unit'] = $this->request->getPost('sale_unit');
        $data['unit'] = $categories_id;

        $purchase_units_price = $this->request->getPost('purchase_units_price');
        $sell_unit_price = $this->request->getPost('sell_unit_price');



        $this->validation->setRules([
            'subCatId' => ['label' => 'subCatId', 'rules' => 'required'],
            'category' => ['label' => 'category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'salePrice' => ['label' => 'salePrice', 'rules' => 'required'],
            'sale_unit' => ['label' => 'Sale Unit', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $totalQty = 0;

            if (!empty($categories_id)) {
                $query = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
                $units_id = json_decode($query->purchase_units);

                $unit = array();
                $units = DB()->table('units')->whereIn('units_id', $units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();
                foreach ($units as $val) {
                    $nameUnit = strtolower(str_replace(' ', '_', $val->name));
                    $unit[$nameUnit] = $this->request->getPost($nameUnit);
                    if (!empty($unit[$nameUnit])) {
                        $totalQty += $val->conversion_factor * $unit[$nameUnit];
                    }
                }
            }

            //purchase price make
            $basePurchasePrice = 0;
            $unitsPur = DB()->table('units')->where('units_id', $purchase_units_price)->get()->getRow();
            if (!empty($unitsPur)){
                $basePurchasePrice = $data['price']/$unitsPur->conversion_factor;
            }
            $purchasePrice = $basePurchasePrice;

            //sale price make
            $baseSalePrice = 0;
            $unitsSale = DB()->table('units')->where('units_id', $sell_unit_price)->get()->getRow();
            if (!empty($unitsSale)){
                $baseSalePrice = $data['salePrice']/$unitsSale->conversion_factor;
            }
            $salePrice = $baseSalePrice;

            if ((!empty($totalQty)) && ($totalQty > 0)) {
                $i = count($this->cart->contents());
                $dataAdd = array(
                    'id' => ++$i,
                    'name' => $data['name'],
                    'unit' => $data['sale_unit'] ,
                    'qty' => $totalQty,
                    'price' => $purchasePrice,
                    'salePrice' => $salePrice,
                    'cat_id' => $data['subCatId'],
                    'unit_set_id' => $categories_id,
                    'serial_status'   => (int)($this->request->getPost('serial_status') ?: 0),
                    'warranty_status' => (int)($this->request->getPost('warranty_status') ?: 0),
                );

                $this->cart->insert($dataAdd);
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

        $date = $this->request->getPost('date');
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

            // ---- Serial number & warranty validation for new_product (start) ----
            $serialDataAll   = $this->request->getPost('serialData') ?: [];
            $warrantyDataAll = $this->request->getPost('warrantyData') ?: [];
            $qtyArr          = $this->request->getPost('quantity[]') ?: [];
            $serialStatusArr = $this->request->getPost('serialStatus[]') ?: [];
            $warrantyStatusArr = $this->request->getPost('warrantyStatus[]') ?: [];
            $nameArr         = $this->request->getPost('name[]') ?: [];
            $serialErrors    = [];

            for ($idx = 0; $idx < $number; $idx++) {
                $sStatus = (int)($serialStatusArr[$idx] ?? 0);
                $wStatus = (int)($warrantyStatusArr[$idx] ?? 0);

                if ($sStatus !== 1 && $wStatus !== 1) {
                    continue;
                }

                $prodName = $nameArr[$idx] ?? ('Product #' . ($idx + 1));
                $prodQty  = isset($qtyArr[$idx]) ? (int) $qtyArr[$idx] : 0;

                if ($sStatus === 1) {
                    $serials = json_decode($serialDataAll[$idx] ?? '', true);
                    if (!is_array($serials) || count($serials) === 0) {
                        $serialErrors[] = "Serial numbers are required for '{$prodName}'.";
                    } else {
                        $hasEmpty = false;
                        foreach ($serials as $s) {
                            if ($s === null || trim((string) $s) === '') {
                                $hasEmpty = true;
                                break;
                            }
                        }
                        if ($hasEmpty) {
                            $serialErrors[] = "All serial numbers must be filled for '{$prodName}'.";
                        }
                        if (count($serials) !== $prodQty) {
                            $serialErrors[] = 'Serial count (' . count($serials) . ") must equal the quantity ({$prodQty}) for '{$prodName}'.";
                        }
                        if (count($serials) !== count(array_unique($serials))) {
                            $serialErrors[] = "Duplicate serial numbers are not allowed for '{$prodName}'.";
                        }
                    }
                }

                if ($wStatus === 1) {
                    $wdate = $warrantyDataAll[$idx] ?? '';
                    if (empty($wdate) || strtotime($wdate) === false) {
                        $serialErrors[] = "Company warranty end date is required for '{$prodName}'.";
                    }
                }
            }

            if (!empty($serialErrors)) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . implode('<br>', $serialErrors) . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/new_product'));
            }
            // ---- Serial number & warranty validation for new_product (end) ----

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledgPurch_id, 'ledger_purchase', 'Dr.');

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $stock_id, 'ledger_stock', 'Dr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledgBank_id, 'ledger_bank', 'Cr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

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
                $queryUnit = DB()->table('unit_set')->where('unit_set_id',$this->request->getPost('unit_set_id[]')[$i])->get()->getRow();
                //insert purchase product
                $data = array(
                    'sch_id' => $shopId,
                    'name' => $name[$i],
                    'purchase_units' => $queryUnit->purchase_units,
                    'sale_units' => $queryUnit->sell_units,
                    'supplier_id' => $supplierId,
                    'prod_cat_id' => $this->request->getPost('prod_cat_id[]')[$i],
                    'serial_status' => $serialStatusArr[$i] ?? '0',
                    'warranty_status' => $warrantyStatusArr[$i] ?? '0',
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $proTable = DB()->table('products');
                $proTable->insert($data);
                $prodId = DB()->insertID();
                //insert log (start)
                $this->transactionLog->insert_log_data('products',$prodId,'',$this->request->getPost('purchase_price[]')[$i],'','','',$purchaseId);
                //insert log (end)

                //insert data in table product_stock_relation
                $dataQty = array(
                    'sch_id' => $shopId,
                    'purchase_id' => $purchaseId,
                    'store_id' => $storeId,
                    'product_id' => $prodId,
                    'quantity' => $this->request->getPost('quantity[]')[$i],
                    'unit' => $this->request->getPost('unit[]')[$i],
                    'purchase_price' => $this->request->getPost('purchase_price[]')[$i],
                    'selling_price' => $this->request->getPost('selling_price[]')[$i],
                );
                $stockRelationTable = DB()->table('product_stock_relation');
                $stockRelationTable->insert($dataQty);
                $product_stock_relation_id = DB()->insertID();
                //insert data in table product_stock_relation

                //insetr purchase Item in purchase item table
                $purchasePrice = $this->request->getPost('purchase_price[]')[$i];
                $quantity = $this->request->getPost('quantity[]')[$i];

                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'product_stock_relation_id' => $product_stock_relation_id,
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

                // ---- Insert serial numbers & company warranty (new_product) (start) ----
                if (isset($serialDataAll[$i]) && !empty($serialDataAll[$i])) {
                    $serialsForProduct = json_decode($serialDataAll[$i], true);
                    $warrantyForProduct = (isset($warrantyDataAll[$i]) && !empty($warrantyDataAll[$i]))
                        ? $warrantyDataAll[$i]
                        : null;

                    if (is_array($serialsForProduct) && !empty($serialsForProduct)) {
                        $serialRows = [];
                        foreach ($serialsForProduct as $sn) {
                            $sn = trim((string) $sn);
                            if ($sn === '') {
                                continue;
                            }
                            $serialRows[] = [
                                'product_stock_relation_id' => $product_stock_relation_id,
                                'serial_number'             => $sn,
                                'company_warranty_end_date' => $warrantyForProduct,
                                'status'                    => 'in_stock',
                                'createdDtm'                => date('Y-m-d h:i:s'),
                                'createdBy'                 => $userId,
                            ];
                        }
                        if (!empty($serialRows)) {
                            DB()->table('product_serial')->insertBatch($serialRows);
                        }
                    }
                }
                // ---- Insert serial numbers & company warranty (new_product) (end) ----
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
                'date' => $date,
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

    private function purchase_transaction_entries($purchase_id, $ledger_id, $table_name, $transaction_type) {
        DB()->table('transaction_entries')->insert([
            'purchase_id'      => $purchase_id,
            'ledger_id'        => $ledger_id,
            'table_name'       => $table_name,
            'trangaction_type' => $transaction_type,
            'createdDtm'       => date('Y-m-d H:i:s')
        ]);
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
        $shopId = $this->session->shopId;

        $purchaseTable = DB()->table('purchase');
        $data['purchase'] = $purchaseTable->where('purchase_id', $purchaseId)->where('sch_id', $shopId)->get()->getRow();

        $purchase_itemTable = DB()->table('purchase_item');
        $data['purchaseItem'] = $purchase_itemTable->where('purchase_id', $purchaseId)->get()->getResult();

        // Existing in-stock serials per product line (keyed by prod_id) for the edit modal
        $data['serialPayload'] = [];
        $data['warrantyPayload'] = [];
        $data['lockedPayload'] = [];
        foreach ($data['purchaseItem'] as $item) {
            $rows = $this->get_purchase_line_serials($item->product_stock_relation_id);
            $data['serialPayload'][$item->prod_id] = json_encode(array_map(function ($r) {
                return ['id' => (int) $r->product_serial_id, 'serial' => $r->serial_number, 'protected' => 1];
            }, $rows));
            $data['lockedPayload'][$item->prod_id] = $this->get_purchase_line_locked_serial_count($item->product_stock_relation_id);
            $wdate = null;
            foreach ($rows as $r) {
                if (!empty($r->company_warranty_end_date)) { $wdate = $r->company_warranty_end_date; break; }
            }
            $data['warrantyPayload'][$item->prod_id] = $wdate ?: '';
        }

        echo view('Admin/Purchase/edit', $data);
    }

    /**
     * @description In-stock serial rows of a stock relation (the serial pool of a
     *              purchase line). product_stock_relation.purchase_id is not reliable
     *              (the new-product purchase flow never fills it), so the relation id
     *              itself scopes the serials; sold/returned serials are excluded and
     *              therefore never editable from the purchase screen.
     * @param int $stockRelationId
     * @return array
     */
    private function get_purchase_line_serials($stockRelationId)
    {
        if (empty($stockRelationId)) {
            return [];
        }
        return DB()->table('product_serial')
            ->where('product_stock_relation_id', $stockRelationId)
            ->where('status', 'in_stock')
            ->orderBy('product_serial_id', 'ASC')
            ->get()->getResult();
    }

    /**
     * @description Count of serial rows on a stock relation that are no longer
     *              in stock (sold/returned/damaged). These are excluded from the
     *              purchase edit payload and always preserved, so they reduce the
     *              number of serials the user is expected to provide.
     * @param int $stockRelationId
     * @return int
     */
    private function get_purchase_line_locked_serial_count($stockRelationId)
    {
        if (empty($stockRelationId)) {
            return 0;
        }
        return DB()->table('product_serial')
            ->where('product_stock_relation_id', $stockRelationId)
            ->where('status !=', 'in_stock')
            ->countAllResults();
    }

    /**
     * @description Submitted serial rows of a purchase edit line as an array of
     *              ['id' => existing product_serial_id or null, 'serial' => string, 'protected' => 0|1].
     * @param array $serialDataAll
     * @param int $prodId
     * @return array|null
     */
    private function parse_purchase_edit_serial_rows($serialDataAll, $prodId)
    {
        if (empty($serialDataAll[$prodId])) {
            return null;
        }
        $decoded = json_decode($serialDataAll[$prodId], true);
        return is_array($decoded) ? $decoded : null;
    }

    public function purchaseEdiAction(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $purchaseId = $this->request->getPost('purchase_id');

        $prod_id = $this->request->getPost('prod_id[]');
        $qty = $this->request->getPost('qty[]');
        $price = $this->request->getPost('price[]');
        $salePrice = $this->request->getPost('salePrice[]');
        $total_price = $this->request->getPost('total_price[]');


        $totalPrice = $this->request->getPost('totalPrice');
        $cashAmount = $this->request->getPost('cash');
        $bankId = $this->request->getPost('bank_id');
        $bankAmount = $this->request->getPost('bank');
        $totalDue = $this->request->getPost('due');

        // ---- Serial number & warranty validation for purchase edit (start) ----
        $serialDataAll   = $this->request->getPost('serialData') ?: [];
        $warrantyDataAll = $this->request->getPost('warrantyData') ?: [];
        $serialErrors    = [];
        $purchaseItems   = DB()->table('purchase_item')->where('purchase_id', $purchaseId)->get()->getResult();
        $itemByProd      = [];
        foreach ($purchaseItems as $piRow) {
            $itemByProd[$piRow->prod_id] = $piRow;
        }
        foreach ($prod_id as $lineIdx => $lineProdId) {
            $serialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $lineProdId);
            $warrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $lineProdId);
            if ($serialStatus !== 1 && $warrantyStatus !== 1) {
                continue;
            }
            $prodName = get_data_by_id('name', 'products', 'prod_id', $lineProdId);
            $lineQty  = (int) $qty[$lineIdx];

            $rows = $this->parse_purchase_edit_serial_rows($serialDataAll, $lineProdId) ?: [];
            $serials   = [];
            $newSerials = [];
            $existingIds = [];
            foreach ($rows as $r) {
                $sn = trim((string) ($r['serial'] ?? ''));
                if ($sn === '') {
                    $serialErrors[] = "All serial numbers must be filled for '{$prodName}'.";
                    break;
                }
                $serials[] = $sn;
                if (empty($r['id'])) {
                    $newSerials[] = $sn;
                } else {
                    $existingIds[(int) $r['id']] = $sn;
                }
            }
            if ($serialStatus === 1) {
                // Serials already sold/returned/damaged are locked and never sent in the
                // payload, so the user only needs to cover the remaining quantity.
                $lockedCount = $this->get_purchase_line_locked_serial_count($itemByProd[$lineProdId]->product_stock_relation_id ?? 0);
                $expectedCount = $lineQty - $lockedCount;
                if ($expectedCount < 0) {
                    $expectedCount = 0;
                }
                if (empty($serials) && $expectedCount > 0) {
                    $serialErrors[] = "Serial numbers are required for '{$prodName}'.";
                } elseif (count($serials) !== $expectedCount) {
                    $serialErrors[] = 'Serial count (' . count($serials) . ") must equal the quantity ({$expectedCount}) for '{$prodName}'.";
                }
                if (count($serials) !== count(array_unique($serials))) {
                    $serialErrors[] = "Duplicate serial numbers are not allowed for '{$prodName}'.";
                }
            }
            // Existing serial ids must belong to this purchase line and still be in stock.
            // Their serial number may be updated; a renamed serial then goes through the
            // same global uniqueness check as a brand new serial.
            if (!empty($existingIds)) {
                $found = $this->get_purchase_line_serials($itemByProd[$lineProdId]->product_stock_relation_id ?? 0);
                $foundMap = [];
                foreach ($found as $f) {
                    $foundMap[(int) $f->product_serial_id] = $f->serial_number;
                }
                foreach ($existingIds as $eid => $esn) {
                    if (!isset($foundMap[$eid])) {
                        $serialErrors[] = "Serial '{$esn}' of '{$prodName}' is no longer available (it may have been sold or edited).";
                    } elseif ($foundMap[$eid] !== $esn) {
                        $newSerials[] = $esn;
                    }
                }
            }
            // New serial numbers must not already exist globally
            foreach ($newSerials as $ns) {
                if (DB()->table('product_serial')->where('serial_number', $ns)->countAllResults() > 0) {
                    $serialErrors[] = "Serial '{$ns}' already exists for '{$prodName}'.";
                }
            }
            if ($warrantyStatus === 1) {
                $wdate = $warrantyDataAll[$lineProdId] ?? '';
                if (empty($wdate) || strtotime($wdate) === false) {
                    $serialErrors[] = "Company warranty end date is required for '{$prodName}'.";
                }
            }
        }
        if (!empty($serialErrors)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . implode('<br>', $serialErrors) . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        }
        // ---- Serial number & warranty validation for purchase edit (end) ----

        DB()->transStart();

        // Purchases created via Admin/Products::add_action() carry no supplier and are
        // funded from shop capital (capital Cr. / stock Dr. / purchase_balance Dr.)
        // without any transaction_log rows. Merge their edits into that same model:
        // adjust the shop balances and the purchase ledger by the amount difference
        // and skip the supplier / payment ledger machinery entirely.
        $purchaseRow = DB()->table('purchase')->where('purchase_id', $purchaseId)->get()->getRow();
        $isSupplierlessPurchase = empty($purchaseRow->supplier_id);

        if ($isSupplierlessPurchase) {
            $oldAmount = (float) $purchaseRow->amount;
            $delta = (float) $totalPrice - $oldAmount;

            if (abs($delta) > 0.00000001) {
                $shopsRow = DB()->table('shops')->where('sch_id', $shopId)->get()->getRow();
                DB()->table('shops')->where('sch_id', $shopId)->update(array(
                    'capital'          => $shopsRow->capital - $delta,
                    'stockAmount'      => $shopsRow->stockAmount + $delta,
                    'purchase_balance' => $shopsRow->purchase_balance + $delta,
                    'updatedBy'        => $userId,
                ));
            }

            // The purchase ledger row is linked by purchase_id at creation time;
            // update it and shift the rest_balance of every later row by the delta.
            $ledgerPurchaseRow = DB()->table('ledger_purchase')->where('purchase_id', $purchaseId)->orderBy('ledgPurch_id', 'DESC')->get()->getRow();
            if (!empty($ledgerPurchaseRow)) {
                DB()->table('ledger_purchase')->where('ledgPurch_id', $ledgerPurchaseRow->ledgPurch_id)->update(array(
                    'amount'       => $totalPrice,
                    'rest_balance' => $ledgerPurchaseRow->rest_balance - $ledgerPurchaseRow->amount + $totalPrice,
                    'updatedBy'    => $userId,
                ));
                if (abs($delta) > 0.00000001) {
                    $laterLedgerRows = DB()->table('ledger_purchase')->where('ledgPurch_id >', $ledgerPurchaseRow->ledgPurch_id)->get()->getResult();
                    foreach ($laterLedgerRows as $lr) {
                        DB()->table('ledger_purchase')->where('ledgPurch_id', $lr->ledgPurch_id)->update(array('rest_balance' => $lr->rest_balance + $delta));
                    }
                }
            }
        } else {
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
        $this->transactionLog->transaction_log_data_update($suppliersInfo->transaction_log_id,$totalPrice);
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
        $this->transactionLog->transaction_log_data_update($suppliersLedgerInfo->transaction_log_id,$totalPrice);
        $this->transactionLog->transaction_edit_log_data_insert('ledger_suppliers','','',$this->session->userId,$suppliersLedgerInfo->amount,$totalPrice,'',$purchaseId);
        //insert Transaction in transaction table (end)

        $this->suppliers_ledger_rest_balance_update($suppliersInfo->id,$totalPrice,$suppliersLedgerInfo->id,$suppliersLedgerInfo->amount);
        //purchase total price calculate to suppliers balance and update suppliers balance or create suppliers ledger (end)


        // purchase balance update and ledger create (start)
        $purchaseBalanceInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops',$purchaseId,'purchase_balance');
        $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
        $restBalPurc = ($purchaseBal - $purchaseBalanceInfo->amount) + $totalPrice;


        $purUpdata = array('purchase_balance' => $restBalPurc);
        $shopPurBalTable = DB()->table('shops');
        $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

        //transaction edit log data insert
        $this->transactionLog->transaction_log_data_update($purchaseBalanceInfo->transaction_log_id,$totalPrice);
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
        $this->transactionLog->transaction_log_data_update($ledgerPurchaseInfo->transaction_log_id,$totalPrice);
        $this->transactionLog->transaction_edit_log_data_insert('ledger_purchase','','',$this->session->userId,$ledgerPurchaseInfo->amount,$totalPrice,'',$purchaseId,'');
        //insert Transaction in transaction table (end)


        // stock balance update and ledger create (start)
        $stockAmountInfo = $this->transactionLog->get_table_name_by_row_purchase_id_by_colum_name('shops',$purchaseId,'stockAmount');
        $stockBal = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
        $restBalStock = ($stockBal - $stockAmountInfo->amount) + $totalPrice;
        $stockUpdate = array('stockAmount' => $restBalStock);
        $shopStoAmTable = DB()->table('shops');
        $shopStoAmTable->where('sch_id', $shopId)->update($stockUpdate);

        //transaction edit log data insert
        $this->transactionLog->transaction_log_data_update($stockAmountInfo->transaction_log_id,$totalPrice);
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
        $this->transactionLog->transaction_log_data_update($ledgerStockInfo->transaction_log_id,$totalPrice);
        $this->transactionLog->transaction_edit_log_data_insert('ledger_stock','','',$this->session->userId,$ledgerStockInfo->amount,$totalPrice,'',$purchaseId );
        //insert Transaction in transaction table (end)

        $this->stock_ledger_rest_balance_update($purchaseId,$totalPrice);
        // stock balance update and ledger create (end)
        }

        if (!$isSupplierlessPurchase && $cashAmount > 0) {
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
                    $this->transactionLog->transaction_log_data_update($shopInfo->transaction_log_id,$cashAmount);
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
                    $this->transactionLog->transaction_log_data_update($ledgerNagodInfo->transaction_log_id,$cashAmount);
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
                    $this->transactionLog->transaction_log_data_update($supplierMidInfo->transaction_log_id,$cashAmount);
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
                    $this->transactionLog->transaction_log_data_update($supplierLedgerMidInfo->transaction_log_id,$cashAmount);
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

        if (!$isSupplierlessPurchase && $bankAmount > 0) {
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
                    $this->transactionLog->transaction_log_data_update($bankInfo->transaction_log_id,$bankAmount);
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
                    $this->transactionLog->transaction_log_data_update($bankLedgerInfo->transaction_log_id,$bankAmount);
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
                    $this->transactionLog->transaction_log_data_update($supLastInfo->transaction_log_id,$bankAmount);
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
                    $this->transactionLog->transaction_log_data_update($supLastLedInfo->transaction_log_id,$bankAmount);
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

            $purchaseItem = DB()->table('purchase_item')->where('purchase_id',$purchaseId)->where('prod_id',$prod_id[$i])->get()->getRow();
            if(!empty($purchaseItem)) {
                $purchaseDate = DB()->table('product_stock_relation')->where('product_stock_relation_id', $purchaseItem->product_stock_relation_id)->get()->getRow();
                $newQty = $purchaseDate->quantity - $purchaseItem->quantity;
                $dataQty = array(
                    'quantity' => $newQty + $qty[$i],
                    'purchase_price' => $price[$i],
                    'selling_price' => $salePrice[$i],
                );
                DB()->table('product_stock_relation')->where('product_stock_relation_id', $purchaseItem->product_stock_relation_id)->update($dataQty);
            }


            //update purchase Item in purchase item table
            $purchaseData = array(
                'purchase_price' => $price[$i],
                'quantity' => $qty[$i],
                'total_price' => $total_price[$i],
            );
            $purchase_itemTab = DB()->table('purchase_item');
            $purchase_itemTab->where('purchase_id',$purchaseId)->where('prod_id',$prod_id[$i])->update($purchaseData);

            // ---- Sync serial numbers & company warranty (purchase edit) (start) ----
            $lineSerialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $prod_id[$i]);
            $lineWarrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $prod_id[$i]);
            $lineRows = $this->parse_purchase_edit_serial_rows($serialDataAll, $prod_id[$i]);
            if (($lineSerialStatus === 1 || $lineWarrantyStatus === 1) && $lineRows !== null && !empty($purchaseItem->product_stock_relation_id)) {
                $relationId = $purchaseItem->product_stock_relation_id;
                $warrantyForProduct = !empty($warrantyDataAll[$prod_id[$i]]) ? $warrantyDataAll[$prod_id[$i]] : null;

                $keepSerials = [];
                $newSerialRows = [];
                foreach ($lineRows as $r) {
                    $sn = trim((string) ($r['serial'] ?? ''));
                    if ($sn === '') {
                        continue;
                    }
                    if (empty($r['id'])) {
                        $newSerialRows[] = [
                            'product_stock_relation_id' => $relationId,
                            'serial_number'             => $sn,
                            'company_warranty_end_date' => $warrantyForProduct,
                            'status'                    => 'in_stock',
                            'createdDtm'                => date('Y-m-d h:i:s'),
                            'createdBy'                 => $userId,
                        ];
                    } else {
                        $keepSerials[(int) $r['id']] = $sn;
                    }
                }

                // Delete serials removed from this line, only while they are still
                // in stock on this relation (sold/returned serials are always preserved).
                $currentRows = $this->get_purchase_line_serials($relationId);
                $currentById = [];
                foreach ($currentRows as $cr) {
                    $currentById[(int) $cr->product_serial_id] = $cr->serial_number;
                }
                $toDelete = array_diff(array_map('intval', array_keys($currentById)), array_keys($keepSerials));
                foreach ($toDelete as $delId) {
                    DB()->table('product_serial')
                        ->where('product_serial_id', $delId)
                        ->where('product_stock_relation_id', $relationId)
                        ->where('status', 'in_stock')
                        ->where('invoice_id', null)
                        ->delete();
                }

                // Update serial numbers of existing serials that were renamed
                foreach ($keepSerials as $keepId => $keepSn) {
                    if (isset($currentById[$keepId]) && $currentById[$keepId] !== $keepSn) {
                        DB()->table('product_serial')
                            ->where('product_serial_id', $keepId)
                            ->where('product_stock_relation_id', $relationId)
                            ->where('status', 'in_stock')
                            ->update(['serial_number' => $keepSn, 'updatedBy' => $userId]);
                    }
                }

                // Insert newly added serials
                if (!empty($newSerialRows)) {
                    DB()->table('product_serial')->insertBatch($newSerialRows);
                }

                // Warranty date is shared across the whole line; apply it to all
                // in-stock serials of the relation (covers other lines reusing it)
                if ($warrantyForProduct !== null) {
                    DB()->table('product_serial')
                        ->where('product_stock_relation_id', $relationId)
                        ->where('status', 'in_stock')
                        ->update(['company_warranty_end_date' => $warrantyForProduct, 'updatedBy' => $userId]);
                }
            }
            // ---- Sync serial numbers & company warranty (purchase edit) (end) ----
        }
        //purchase product insert in product table and purchase item table (end)


        //purchase all pay amount detail Update in purchase table(start)
        if ($isSupplierlessPurchase) {
            // Capital-funded purchase (created without a supplier): only the amount
            // changes; payments were never cash/bank based and due stays as created.
            $parsData = array(
                'amount' => $totalPrice,
            );
        } else {
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $totalDue,
            );
        }
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
    public function transaction_flow($purchase_id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['flow'] = DB()->table('transaction_entries')
                ->where('purchase_id',$purchase_id)
                ->get()
                ->getResult();

            $data['purchaseData'] = DB()->table('purchase')
                ->join('suppliers', 'suppliers.supplier_id = purchase.supplier_id')
                ->where('purchase.purchase_id', $purchase_id)
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
                echo view('Admin/Purchase/transaction_flow', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
    public function unitShow(){
        $unit_set_id = $this->request->getPost('unit_set_id');
        $query = DB()->table('unit_set')->where('unit_set_id',$unit_set_id)->get()->getRow();

        $units_id = json_decode($query->purchase_units);
        $data['units'] = DB()->table('units')->whereIn('units_id',$units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

        $array = json_decode($query->sell_units);
        $lastKey = array_key_last($array);
        $data['saleUnit'] = DB()->table('units')->where('units_id',$array[$lastKey])->get()->getRow();
        $data['unitsArray'] = DB()->table('units')->where('unit_categories_id',$query->unit_categories_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

        $data['unitSet'] = $query;
        $data['noPurchase'] = $this->request->getPost('noPurchase');

        echo view('Admin/Purchase/unitShow', $data);
    }

    public function purchase_product(){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Purchase/purchase_product_action');

            if (!empty($this->session->cartType) && $this->session->cartType !== 'purchaseNew') {
                $this->cart->destroy();
            }
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/purchase_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function search_prod()
    {
        $shopId = $this->session->shopId;

        $keyWord = $this->request->getPost("keyWord");

        $storeTab = DB()->table('stores');
        $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();

        $proTable = DB()->table('products');
        $data = $proTable
            ->join('product_stock_relation', 'product_stock_relation.product_id = products.prod_id')
            ->where('products.sch_id', $shopId)
            ->where('product_stock_relation.store_id', $store->store_id)
//            ->where('product_stock_relation.is_default', '1')
            ->groupBy('products.prod_id')
            ->groupStart()
            ->like('products.name', $keyWord)
            ->orLike('products.prod_id', $keyWord)
            ->groupEnd()
            ->get()
            ->getResult();


        $view = '';
        foreach ($data as $sval) {
            $image = ($sval->picture == NULL) ? 'no_image.jpg' : $sval->picture;
            $unit = $sval->unit;
            $qty = $sval->quantity;
            $availQty = unitOrQtyByUnitQty($unit,$qty);
            $purchasePrice = unitOrBasePriceByUnitPrice($unit,$sval->purchase_price);

            $units_id = json_decode($sval->purchase_units);
            $unitsArray = DB()->table('units')->whereIn('units_id',$units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();

            $view .= '<li>
                        <form action="' . site_url('Admin/Purchase/add_cart') . '" method="post">
                            <div class="col-xs-12" style="padding:15px; border-bottom: 1px solid;color: #d2d6de;" ><a>
                            <div class="col-xs-2">
                                <img class="img-circle" src="' . base_url() . '/uploads/product_image/' . $image . '" width="60" height="60">
                            </div>
                            <div class="col-xs-4"><label for="usr">Name /Price:</label><h4 style="color:black;">' . $sval->name . '/' . showWithCurrencySymbol($purchasePrice) . 'Tk. </h4><p>Available Quantity: '.$availQty.'/'.showUnitName($unit).'</p><input class="form-control" type="hidden" readonly id="name" name="name" value="' . $sval->name . '"><input class="form-control" type="hidden" readonly id="price" name="price" value="' . $sval->purchase_price . '"><input class="form-control" type="hidden" readonly id="sale_price" name="sale_price" value="' . $sval->selling_price . '"><input class="form-control" type="hidden" readonly id="prod_id" name="prod_id" value="' . $sval->prod_id . '"></div>
                            <div class="col-md-5 row">';
            foreach ($unitsArray as $val){
                $view .='<div class="form-group col-xs-6">
                        <label for="int" class="text-capitalize">'. $val->name.' </label>
                        <input type="text" class="form-control" name="'. strtolower(str_replace(' ', '_', $val->name)).'" placeholder="'. $val->name.'" value="" >
                     </div>';
            }
            $view .='</div>';

            $view .='<div class="col-xs-1" >
                        <span>Category:</span><br><h4 style="color:black;">' . get_data_by_id('product_category', 'product_category', 'prod_cat_id', $sval->prod_cat_id) . '</h4>
                                <button  type="subbmit" class="add_cart btn btn-success btn-xs" >Add</button>
                            </div></a></div>
                        </form>
                     </li>';

        }
        echo $view;

    }

    public function add_cart()
    {

        $shopId = $this->session->shopId;

        $proId = $this->request->getPost('prod_id');
        $proName = $this->request->getPost('name');
        $proPrice = $this->request->getPost('price');
        $salePrice = $this->request->getPost('sale_price');


        $qty = 0;
        foreach ($this->cart->contents() as $row) {
            if ($proId == $row['id']) {
                $qty = $row['qty'];
            }
        }

        //unit qty
        $unitIdJson = get_data_by_id('purchase_units', 'products', 'prod_id', $proId);
        $units_id = json_decode($unitIdJson);
        $unitQty = 0;
        $unit = array();
        $units = DB()->table('units')->whereIn('units_id', $units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();
        foreach ($units as $val) {
            $nameUnit = strtolower(str_replace(' ', '_', $val->name));
            $unit[$nameUnit] = $this->request->getPost($nameUnit);
            if (!empty($unit[$nameUnit])) {
                $unitQty += $val->conversion_factor * $unit[$nameUnit];
            }
        }

        $totalquantity = $unitQty + $qty;

        if ($totalquantity > 0) {
            $data = array(
                'id' => $proId,
                'name' => strval($proName),
                'qty' => $totalquantity,
                'price' => $proPrice,
                'sale_price' => $salePrice
            );
            $this->cart->insert($data);
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid Quantity  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/purchase_product'));
        }

        $this->session->set('cartType', 'purchaseNew');
        return redirect()->to(site_url('Admin/Purchase/purchase_product'));
    }

    public function removeCart($id)
    {
        $this->cart->remove($id);
        return redirect()->to(site_url('Admin/Purchase/purchase_product'));
    }
    public function purchase_product_action(){
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $date = $this->request->getPost('date');
        $supplierId = $this->request->getPost('supplier_id');
        $sms = $this->request->getPost('sms');

        $totalPrice = str_replace(',', '', $this->request->getPost('totalPrice'));
        $cashAmount = str_replace(',', '', $this->request->getPost('cash'));
        $bankAmount = str_replace(',', '', $this->request->getPost('bank'));
        $dueAmount = str_replace(',', '', $this->request->getPost('due'));
        $bankId = $this->request->getPost('bank_id');

        $productId = $this->request->getPost('productId[]');

        if (!empty($this->cart->contents())) {
            $number = count($productId);

            $cashbal = get_data_by_id('cash', 'shops', 'sch_id', $shopId);
            $bankbal = get_data_by_id('balance', 'bank', 'bank_id', $bankId);

            if ($cashbal < $cashAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if ($bankbal < $bankAmount) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Not enough balance! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if (!empty($cashAmount) && $cashAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            if (!empty($bankAmount) && $bankAmount < 0) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Please enter valid amount<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }
            if (empty($supplierId)) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Supplier is required!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }

            // ---- Serial number & warranty validation (start) ----
            $serialDataAll   = $this->request->getPost('serialData') ?: [];
            $warrantyDataAll = $this->request->getPost('warrantyData') ?: [];
            $qtyArr          = $this->request->getPost('qty[]') ?: [];
            $serialErrors    = [];

            foreach ($productId as $idx => $prodId) {
                $serialStatus   = (int) get_data_by_id('serial_status', 'products', 'prod_id', $prodId);
                $warrantyStatus = (int) get_data_by_id('warranty_status', 'products', 'prod_id', $prodId);

                // A product with neither flag active needs no serial/warranty data.
                if ($serialStatus !== 1 && $warrantyStatus !== 1) {
                    continue;
                }

                $prodName = get_data_by_id('name', 'products', 'prod_id', $prodId);
                $prodQty  = isset($qtyArr[$idx]) ? (int) $qtyArr[$idx] : 0;

                // Serial numbers: mandatory, all filled, count == qty, unique.
                if ($serialStatus === 1) {
                    $serials = json_decode($serialDataAll[$prodId] ?? '', true);
                    if (!is_array($serials) || count($serials) === 0) {
                        $serialErrors[] = "Serial numbers are required for '{$prodName}'.";
                    } else {
                        $hasEmpty = false;
                        foreach ($serials as $s) {
                            if ($s === null || trim((string) $s) === '') {
                                $hasEmpty = true;
                                break;
                            }
                        }
                        if ($hasEmpty) {
                            $serialErrors[] = "All serial numbers must be filled for '{$prodName}'.";
                        }
                        if (count($serials) !== $prodQty) {
                            $serialErrors[] = 'Serial count (' . count($serials) . ") must equal the quantity ({$prodQty}) for '{$prodName}'.";
                        }
                        if (count($serials) !== count(array_unique($serials))) {
                            $serialErrors[] = "Duplicate serial numbers are not allowed for '{$prodName}'.";
                        }
                    }
                }

                // Company warranty end date: mandatory whenever warranty_status is
                // on, independent of whether serial tracking is enabled.
                if ($warrantyStatus === 1) {
                    $wdate = $warrantyDataAll[$prodId] ?? '';
                    if (empty($wdate) || strtotime($wdate) === false) {
                        $serialErrors[] = "Company warranty end date is required for '{$prodName}'.";
                    }
                }
            }

            if (!empty($serialErrors)) {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . implode('<br>', $serialErrors) . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Purchase/purchase_product'));
            }
            // ---- Serial number & warranty validation (end) ----

            DB()->transStart();

            DB()->table('purchase')->insert([
                'sch_id' => $shopId,
                'supplier_id' => $supplierId,
                'date' => $date,
                'createdBy' => $userId,
            ]);
            $purchaseId = DB()->insertID();

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Cr.');

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $ledgPurch_id, 'ledger_purchase', 'Dr.');

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

            //Insert Purchase Transaction Entries
            $this->purchase_transaction_entries($purchaseId, $stock_id, 'ledger_stock', 'Dr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_nagodan_id, 'ledger_nagodan', 'Cr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledgBank_id, 'ledger_bank', 'Cr.');

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

                    //Insert Purchase Transaction Entries
                    $this->purchase_transaction_entries($purchaseId, $ledg_sup_id, 'ledger_suppliers', 'Dr.');

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
                $prodId = $productId[$i];
                //insert log (start)
                $purchasePrice = $this->request->getPost('price[]')[$i];
                $sellingPrice = $this->request->getPost('salePrice[]')[$i];
                $quantity = $this->request->getPost('qty[]')[$i];

                $oldStock = DB()->table('product_stock_relation')->where('sch_id',$shopId)->where('store_id',$storeId)->where('product_id',$prodId)->where('purchase_price',$purchasePrice)->where('selling_price',$sellingPrice)->where('is_default','0')->countAllResults();
                $product_stock_relation_id = 0;
                if (empty($oldStock)) {
                    //insert data in table product_stock_relation
                    $dataQty = array(
                        'sch_id' => $shopId,
                        'store_id' => $storeId,
                        'purchase_id' => $purchaseId,
                        'product_id' => $prodId,
                        'supplier_id' => $supplierId,
                        'quantity' => $quantity,
                        'unit' => $this->request->getPost('unit[]')[$i],
                        'purchase_price' => $purchasePrice,
                        'selling_price' => $sellingPrice,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $stockRelationTable = DB()->table('product_stock_relation');
                    $stockRelationTable->insert($dataQty);
                    $product_stock_relation_id = DB()->insertID();
                    //insert data in table product_stock_relation
                }else{
                    $stock = DB()->table('product_stock_relation')->where('sch_id',$shopId)->where('store_id',$storeId)->where('product_id',$prodId)->where('purchase_price',$purchasePrice)->where('selling_price',$sellingPrice)->where('is_default','0')->get()->getRow();
                    $dataQty['quantity'] = $stock->quantity + $quantity;
                    $dataQty['supplier_id'] = $supplierId;
                    DB()->table('product_stock_relation')->where('product_stock_relation_id',$stock->product_stock_relation_id)->update($dataQty);
                    $product_stock_relation_id = $stock->product_stock_relation_id;
                }

                //insert purchase Item in purchase item table
                $total_price = $quantity * $purchasePrice;

                $purchaseData = array(
                    'purchase_id' => $purchaseId,
                    'prod_id' => $prodId,
                    'product_stock_relation_id' => $product_stock_relation_id,
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

                // ---- Insert serial numbers & company warranty (start) ----
                if (isset($serialDataAll[$prodId]) && !empty($serialDataAll[$prodId])) {
                    $serialsForProduct = json_decode($serialDataAll[$prodId], true);
                    $warrantyForProduct = (isset($warrantyDataAll[$prodId]) && !empty($warrantyDataAll[$prodId]))
                        ? $warrantyDataAll[$prodId]
                        : null;

                    if (is_array($serialsForProduct) && !empty($serialsForProduct)) {
                        $serialRows = [];
                        foreach ($serialsForProduct as $sn) {
                            $sn = trim((string) $sn);
                            if ($sn === '') {
                                continue;
                            }
                            $serialRows[] = [
                                'product_stock_relation_id' => $product_stock_relation_id,
                                'serial_number'             => $sn,
                                'company_warranty_end_date' => $warrantyForProduct,
                                'status'                    => 'in_stock',
                                'createdDtm'                => date('Y-m-d h:i:s'),
                                'createdBy'                 => $userId,
                            ];
                        }
                        if (!empty($serialRows)) {
                            DB()->table('product_serial')->insertBatch($serialRows);
                        }
                    }
                }
                // ---- Insert serial numbers & company warranty (end) ----
            }
            //purchase product insert in product table and purchase item table (end)


            //purchase all pay amount detail Update in purchase table(start)
            $parsData = array(
                'amount' => $totalPrice,
                'nagad_paid' => $cashAmount,
                'bank_paid' => $bankAmount,
                'bank_id' => $bankId,
                'due' => $dueAmount,
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

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase'));
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your cart is empty! <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Purchase/new_product'));
        }
    }


}