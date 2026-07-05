<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;


class Stock_report_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Stock_report';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $storeId = $this->request->getGet('store_id');
            $shopId = $this->session->shopId;

            $productsTb = DB()->table('products');
            $productsTb->join('product_stock_relation relation', 'relation.product_id = products.prod_id');
            $data['result'] = $productsTb->where('relation.store_id', $storeId)->where('products.sch_id', $shopId)->orderBy('prod_id', "DESC")->get()->getResult();

            $data['quantity'] = '0';
            if (!empty($data['result'])) {
                foreach ($data['result'] as $result) {
                    $data['quantity'] += $result->quantity;
                }
            }
            $data['purchasePrice'] = 0;
            foreach ($data['result'] as  $pur) {
                $data['purchasePrice'] += $pur->quantity * $pur->purchase_price;
            }

            $data['name'] = get_data_by_id('name', 'stores', 'store_id', $storeId);
            $data['store_id'] = $storeId;


            $data['menu'] = view('Admin/menu_report');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Stock_report/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}