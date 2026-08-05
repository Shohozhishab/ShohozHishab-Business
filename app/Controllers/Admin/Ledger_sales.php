<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Ledger_sales extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Ledger_sales';

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

            $db = DB();
            // 1. Define the window function column
            $mBalanceSubquery = "SUM(
                    CASE 
                        WHEN LOWER(trangaction_type) LIKE 'dr%' THEN amount 
                        WHEN LOWER(trangaction_type) LIKE 'cr%' THEN -amount 
                        ELSE 0 
                    END
                ) OVER (ORDER BY ledgSale_id) AS r_balance";
            // 2. Build the inner subquery
            $subquery = $db->table('ledger_sales')
                ->select('ledger_sales.*')
                ->select($mBalanceSubquery, false)
                ->where('sch_id', $shopId); // false prevents CI4 from escaping the raw SQL window function
            // 3. Query from the subquery derived table
            $table = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId);
            if (!empty($st_date) && !empty($en_date)) {
                // Assuming your database column name is 'date'
                $table->where('createdDtm >=', $st_date . ' 00:00:00');
                $table->where('createdDtm <=', $en_date . ' 23:59:59');
            }
            $table->orderBy('ledgSale_id', 'ASC');
            $data['saleLedg'] = $table->get()->getResult();

            $data['st_date'] = isset($st_date)?$st_date:'';
            $data['en_date'] = isset($en_date)?$en_date:'';

            $data['menu'] = view('Admin/menu_ledger');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Ledger_sales/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}