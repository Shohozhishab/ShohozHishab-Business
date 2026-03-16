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
            $ledger_nagodanTab = DB()->table('ledger_nagodan');
            $data['cashLedger'] = $ledger_nagodanTab->where('sch_id', $shopId)->like('createdDtm', date('Y-m-d'))->orderBy("createdDtm", "DESC")->get()->getResult();

            $nagodTab = DB()->table('ledger_nagodan');
            $rest = $nagodTab->where('sch_id', $shopId)->like('createdDtm', date('Y-m-d'))->orderBy("createdDtm", "DESC")->limit(1)->get()->getRow();
            $data['cashrest_balance'] = empty($rest) ? 0 : $rest->rest_balance;

            //Show today all cash transaction list in ledger_nagodan table (end)

            $prevTab = DB()->table('ledger_nagodan');
            $prevbalance = $prevTab->where("sch_id", $shopId)->where('createdDtm <', date('Y-m-d'))->limit(1)->orderBy("createdDtm", "DESC")->get()->getRow();
            $data['prev_balance'] = empty($prevbalance) ? 0 : $prevbalance->rest_balance;


            $bankTab = DB()->table('bank');
            $data['allBank'] = $bankTab->where("sch_id", $shopId)->get()->getResult();


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