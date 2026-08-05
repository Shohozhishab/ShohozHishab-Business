<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Daily_book_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Daily_book';

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

            //Show today all cash transaction list in ledger_nagodan table (start)
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
                ->select($mBalanceSubquery, false)
                ->where('sch_id', $shopId);
            // 3. Query from the subquery derived table
            $data['cashLedger'] = $db->newQuery()
                ->fromSubquery($subquery, 't')
                ->where('sch_id', $shopId)
                ->like('DATE(createdDtm)', date('Y-m-d'))
                ->orderBy('ledg_nagodan_id', 'DESC')
                ->get()
                ->getResult();

            $data['cashrest_balance'] = get_data_by_id('cash','shops','sch_id',$shopId);



            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();



            $data['sales'] = DB()->table('sales')
                ->where('sales.sch_id', $shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['purchase_data'] = DB()->table('purchase')
                ->where('sch_id', $shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['capital'] = DB()->table('capital')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();

            $data['transaction'] = DB()->table('transaction')
                ->where('sch_id',$shopId)
                ->where('DATE(createdDtm)', date('Y-m-d'))
                ->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Daily_book/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}