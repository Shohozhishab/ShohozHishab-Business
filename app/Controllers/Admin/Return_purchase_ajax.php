<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Return_purchase_ajax extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Return_purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides return purchase view
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

            $supplier_id = $this->request->getGet('supplier_id');
            $st_date = $this->request->getGet('st_date');
            $en_date = $this->request->getGet('en_date');

            $table = DB()->table('return_purchase');
            $table->where('return_purchase.sch_id', $shopId);
            $table->where('return_purchase.deleted', null);
            if (!empty($supplier_id)){
                $table->where('return_purchase.supplier_id', $supplier_id);
            }
            if (!empty($st_date) && !empty($en_date)) {
                $table->where('return_purchase.createdDtm >=', $st_date . ' 00:00:00');
                $table->where('return_purchase.createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['return_purchase_data'] = $table->get()->getResult();

            $data['supplierId'] = isset($supplier_id)?$supplier_id:'';
            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/list', $data);
            } else {
                echo view('no_permission');
            }
        }
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

            $purchase_itemTab = DB()->table('purchase_item');
            $data['purchase_item'] = $purchase_itemTab->where('purchase_id', $id)->get()->getResult();

            $purchaseTab = DB()->table('purchase');
            $data['purchase'] = $purchaseTab->where('purchase_id', $id)->where('sch_id', $shopId)->get()->getRow();

            $data['action'] = site_url('Admin/Return_purchase/create_action');
            $data['purchaseId'] = $id;

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['create'] == 1) {
                echo view('Admin/Return_purchase/return', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    public function view($id){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;

            $data['shopsName'] = get_data_by_id('name', 'shops', 'sch_id', $shopId);

            $returnPurchaseTable = DB()->table('return_purchase');
            $data['returnPurchaseData'] = $returnPurchaseTable->where('rtn_purchase_id', $id)->get()->getRow();

            $returnItem = DB()->table('return_purchase_item');
            $data['purchaseItame'] = $returnItem->where('rtn_purchase_id', $id)->get()->getResult();

            $data['menu'] = view('Admin/menu_purchase', $data);
            // All Permissions
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Return_purchase/view', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

}
