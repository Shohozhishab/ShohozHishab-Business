<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Customer_typeModel;
use CodeIgniter\HTTP\RedirectResponse;


class Role_ajax extends BaseController
{

    protected $customer_typeModel;
    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Role';

    public function __construct()
    {
        $this->customer_typeModel = new Customer_typeModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
    }

    /**
     * @description This method provides role view
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
            $rolesTable = DB()->table('roles');
            $data['roles'] = $rolesTable->where('sch_id', $shopId)->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['mod_access'] == 1) {
                echo view('Admin/Role/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides role create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Role/create_action');

            $table = DB()->table('roles');
            $adminRole = $table->where('sch_id', $shopId)->where('is_default', '1')->get()->getRow();

            $data['permission'] = json_decode($adminRole->permission);


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['create'] == 1) {
                echo view('Admin/Role/create', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides role update view
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
            $rolesTable = DB()->table('roles');
            $table = DB()->table('roles');
            $adminRole = $table->where('sch_id', $shopId)->where('is_default', '1')->get()->getRow();

            $data['roles'] = $rolesTable->where('role_id', $id)->get()->getRow();
            $data['permission'] = json_decode($adminRole->permission);
            $data['action'] = base_url('Admin/Role/update_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['update'] == 1) {
                echo view('Admin/Role/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}