<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Products_ajax extends BaseController
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
            $productTable->select('products.*,product_stock_relation.*');
            $productTable->join('product_stock_relation','product_stock_relation.product_id = products.prod_id');
            $productTable->join('stores','stores.store_id = product_stock_relation.store_id');
            $data['products_data'] = $productTable->where('products.sch_id', $shopId)->groupBy('products.prod_id')->where('stores.is_default','1')->get()->getResult();


            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/list', $data);
            } else {
                echo view('no_permission');
            }
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
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Products/update', $data);
            } else {
                echo view('no_permission');
            }
        }
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
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/add_product', $data);
            } else {
                echo view('no_permission');
            }
        }
    }




}