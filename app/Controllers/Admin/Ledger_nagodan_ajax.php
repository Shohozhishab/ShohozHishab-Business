<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_nagodan_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_nagodan';

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

            $db = DB();
            // 1. Define the window function column
            $mBalanceSubquery = "SUM(
                CASE 
                    WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                    WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                    ELSE 0 
                END
            ) OVER (ORDER BY ledg_nagodan_id) AS r_balance";
            // 2. Build the inner subquery
            $subquery = $db->table('ledger_nagodan')
                ->select('ledger_nagodan.*')
                ->select($mBalanceSubquery, false); // false prevents CI4 from escaping the raw SQL window function
            // 3. Query from the subquery derived table
            $data['ledger_nagodan_data'] = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId)
                ->orderBy('ledg_nagodan_id', 'ASC')
                ->get()
                ->getResult();

            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_nagodan/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}