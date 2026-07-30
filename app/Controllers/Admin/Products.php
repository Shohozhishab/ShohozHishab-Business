<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Products extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Products';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides products view
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

            $productTable = DB()->table('products');
            $productTable->join('product_stock_relation','product_stock_relation.product_id = products.prod_id');
            $productTable->join('stores','stores.store_id = product_stock_relation.store_id');
            $data['products_data'] = $productTable->where('products.sch_id', $shopId)->where('stores.is_default','1')->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update products
     * @param int $id
     * @return RedirectResponse|void
     */
    public function update($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $productsTable = DB()->table('products');
            $data['product'] = $productsTable->where('prod_id', $id)->where('sch_id', $shopId)->get()->getRow();

            $data['showUnit'] = productIdByDefaultStoreUnit($id);
            $unitCategory = get_data_by_id('unit_categories_id','units','units_id',$data['showUnit']);
            $data['units'] = DB()->table('units')->where('unit_categories_id',$unitCategory)->orderBy('conversion_factor','DESC')->get()->getResult();


            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Products/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update products general
     * @return void
     */
    public function general_update()
    {
        $userId = $this->session->userId;

        $data['prod_id'] = $this->request->getPost('prod_id');
        $data['name'] = $this->request->getPost('name');
        $data['supplier_id'] = $this->request->getPost('supplier_id');
        $data['serial_number'] = empty($this->request->getPost('serial_number')) ? null : $this->request->getPost('serial_number');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
            'supplier_id' => ['label' => 'supplier_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update products personal
     * @return void
     */
    public function personal_update()
    {
        $userId = $this->session->userId;

        $data['prod_id'] = $this->request->getPost('prod_id');
        $data['prod_cat_id'] = $this->request->getPost('sub_cat_id');
        $data['brand_id'] = $this->request->getPost('brand_id');
        $data['size'] = $this->request->getPost('size');
        $data['warranty'] = $this->request->getPost('warranty');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'brand_id' => ['label' => 'brand_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }
    public function unit_update()
    {
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;
        $data['prod_id'] = $this->request->getPost('prod_id');
        $sell_units = $this->request->getPost('sell_units[]');
        $data['unit'] = $this->request->getPost('unit');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'unit' => ['label' => 'Unit', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $store = DB()->table('stores')->where('sch_id',$shopId)->where('is_default','1')->get()->getRow();
            DB()->table('product_stock_relation')
                ->where('store_id',$store->store_id)
                ->where('product_id',$data['prod_id'])
                ->update(['unit' => $data['unit']]);


            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update(['sale_units'=>json_encode($sell_units)])) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update products photo
     * @return void
     */
    public function photo_update()
    {

        $data['prod_id'] = $this->request->getPost('prod_id');

        if (!empty($_FILES['picture']['name'])) {
            $target_dir = FCPATH . '/uploads/product_image/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('picture', 'products', 'prod_id', $data['prod_id']);
            if (!empty($old_img)) {
                unlink($target_dir . $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('picture');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'product_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(300, 300, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['picture'] = $pro_nameimg;


            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert"> please select a image  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method update products barcode
     * @return void
     */
    public function barcode()
    {
        $data['barcodeqty'] = $this->request->getPost('barcodeqty');

        $data['generator'] = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $tabGenSet = DB()->table('gen_settings');

        $sizeBarcode = $tabGenSet->where('label', 'barcode_img_size')->get()->getRow()->value;
        $data['barcodeSize'] = empty($sizeBarcode) ? '100' : $sizeBarcode;

        $typeBarcode = $tabGenSet->where('label', 'barcode_type')->get()->getRow()->value;
        $data['barcodeType'] = empty($typeBarcode) ? 'C128' : $typeBarcode;

        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Products/barcode', $data);
        echo view('Admin/footer');
    }
    public function add_existing_product(){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $productTable = DB()->table('products');
            $data['products_data'] = $productTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id',$shopId)->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/add_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function add_action(){
        $shopId = $this->session->shopId;

        $categories_id = $this->request->getPost('categories_id');
        $data['salePrice'] = $this->request->getPost('selling_price');
        $data['sale_unit'] = $this->request->getPost('sale_unit');
        $purchase_units_price = $this->request->getPost('purchase_units_price');
        $sell_unit_price = $this->request->getPost('sell_unit_price');

        $data['prod_cat_id'] = $this->request->getPost('sub_category');
        $data['name'] = $this->request->getPost('name');
        $data['unit'] = $data['sale_unit'];
        $data['price'] = $this->request->getPost('price');
        $data['selling_price'] = $data['salePrice'] ;


        $this->validation->setRules([
            'prod_cat_id' => ['label' => 'Category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'selling_price' => ['label' => 'salePrice', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {


            DB()->transStart();

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


            //get default store
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();

            //insert product
            $queryUnit = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
            $dataProduct['prod_cat_id'] = $data['prod_cat_id'];
            $dataProduct['name'] = $data['name'];
            $dataProduct['purchase_units'] = $queryUnit->purchase_units;
            $dataProduct['sale_units'] = $queryUnit->sell_units;
            $dataProduct['purchase_date'] = date('Y-m-d H:i:s');
            $dataProduct['sch_id'] = $shopId;
            $dataProduct['createdBy'] = $this->session->userId;
            $dataProduct['createdDtm'] = date('Y-m-d H:i:s');
            $productTable = DB()->table('products');
            $productTable->insert($dataProduct);
            $prodId = DB()->insertID();

            //product stock relation insert
            DB()->table('product_stock_relation')->insert([
                'store_id' => $store->store_id,
                'product_id' => $prodId,
                'quantity' => $totalQty,
                'unit' => $data['sale_unit'],
                'purchase_price' => $purchasePrice,
                'selling_price' => $salePrice,
            ]);

            //total amount product
            $totalAmountPro = $purchasePrice * $totalQty;

            //capital last balance
            $oldCapital = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
            $newCapital = $oldCapital - $totalAmountPro;

            // capital ledger data insert
            $cpitalLedData = array(
                'sch_id' => $shopId,
                'particulars' => 'New Existing Products Add Amount',
                'trangaction_type' => 'Cr.',
                'amount' => $totalAmountPro,
                'rest_balance' => $newCapital,
                'createdBy' => $this->session->userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_capitalTable = DB()->table('ledger_capital');
            $ledger_capitalTable->insert($cpitalLedData);
            // capital ledger data insert


            //stock last balance
            $oldStock = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $newStock = $oldStock + $totalAmountPro;

            //Stock ledger data insert
            $stockLedgData = array(
                'sch_id' => $shopId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New Existing Products Add Amount',
                'amount' => $totalAmountPro,
                'rest_balance' => $newStock,
                'createdBy' => $this->session->userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_stock = DB()->table('ledger_stock');
            $tabledger_stock->insert($stockLedgData);
            //Stock ledger data insert


            //update capital and stock
            $dataCapital['stockAmount'] = $newStock;
            $dataCapital['capital'] = $newCapital;
            $tableCapital = DB()->table('shops');
            $tableCapital->where('sch_id', $shopId)->update($dataCapital);
            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert"> Product added successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        }
    }


}