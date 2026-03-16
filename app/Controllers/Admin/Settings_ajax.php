<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Settings_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Settings';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides settings view
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
            $shopTable = DB()->table('shops');
            $data['stores'] = $shopTable->where('sch_id', $shopId)->get()->getRow();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Settings/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides settings update view
     * @return RedirectResponse|void
     */
    public function update()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $shopTable = DB()->table('shops');
            $data['stores'] = $shopTable->where('sch_id', $shopId)->get()->getRow();

            $gen_settingsTable = DB()->table('gen_settings');
            $data['gen_setting'] = $gen_settingsTable->where('sch_id', $shopId)->get()->getResult();

            $vat_registerTable = DB()->table('vat_register');
            $data['vat_register'] = $vat_registerTable->where('sch_id', $shopId)->get()->getRow();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Settings/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}