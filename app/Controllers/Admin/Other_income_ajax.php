<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Loan_providerModel;
use CodeIgniter\HTTP\RedirectResponse;


class Other_income_ajax extends BaseController
{

    protected $loan_providerModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'OtherIncome';

    public function __construct()
    {
        $this->loan_providerModel = new Loan_providerModel();
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

            $data['result'] = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'other_income')
                ->get()
                ->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/OtherIncome/list', $data);
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
            $data['action'] = base_url('Admin/Other_income/create_action');
            $data['actionExisting'] = base_url('Admin/Other_income/existing_create_action');
            $data['type'] = get_data_by_id('account_type_id','account_type','type_key','other_income');

            $data['subType'] = DB()->table('account_type')->where('parent_account_type_id',$data['type'])->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['create'] == 1) {
                echo view('Admin/OtherIncome/create', $data);
            } else {
                echo view('no_permission');
            }

        }
    }

    /**
     * @description This method provides update view
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
            $data['action'] = base_url('Admin/Other_income/update_action');
            $data['accounts'] = DB()->table('accounts')->where('account_id', $id)->get()->getRow();

            $data['type'] = get_data_by_id('account_type_id','account_type','type_key','other_income');
            $data['subType'] = DB()->table('account_type')->where('parent_account_type_id',$data['type'])->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['update'] == 1) {
                echo view('Admin/OtherIncome/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}