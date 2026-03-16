<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Bank_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Bank';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
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
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('sch_id', $shopId)->orderBy('name', 'ASC')->where('deleted IS NULL')->get()->getResult();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Bank/list', $data);
            } else {
                echo view('no_permission');
            }
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
            $data['action'] = base_url('Admin/Bank/create_action');
            $data['action2'] = base_url('Admin/Bank/existing_create_action');


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Bank/create', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides bank update view
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
            $data['action'] = base_url('Admin/Bank/update_action');
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('bank_id', $id)->get()->getRow();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Bank/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}