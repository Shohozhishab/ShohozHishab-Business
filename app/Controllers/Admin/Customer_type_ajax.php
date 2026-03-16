<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Customer_typeModel;
use CodeIgniter\HTTP\RedirectResponse;

class Customer_type_ajax extends BaseController
{

    protected $customer_typeModel;
    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Customer_type';

    public function __construct()
    {
        $this->customer_typeModel = new Customer_typeModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
    }

    /**@description This method provides view
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
            $data['cusType'] = $this->customer_typeModel->where('sch_id', $shopId)->findAll();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Customer_type/list', $data);
            } else {
                echo view('no_permission');
            }

        }
    }

    /**
     * @description This method provides create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Customer_type/create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Customer_type/create', $data);
            } else {
                echo view('no_permission');
            }

        }
    }

    /**
     * @description This method provides update view
     * @param $id
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
            $data['cusType'] = $this->customer_typeModel->where('cus_type_id', $id)->where('sch_id', $shopId)->first();
            $data['action'] = base_url('Admin/Customer_type/update_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Customer_type/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}