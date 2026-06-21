<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_capital_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_capital';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides view
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

            $table = DB()->table('ledger_capital');
            $table->where('sch_id', $shopId);
            // Apply date filters only if they are present in the request
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('createdDtm >=', $st_date . ' 00:00:00');
                $table->where('createdDtm <=', $en_date . ' 23:59:59');
            }
            $data['ledger_capital'] = $table->get()->getResult();

            $data['st_date'] = isset($st_date) ? $st_date : '';
            $data['en_date'] = isset($en_date) ? $en_date : '';

            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_capital/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }
}
