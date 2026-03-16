<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Customer_typeModel;
use App\Models\CustomersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Customers_ajax extends BaseController
{

    protected $customersModel;
    protected $customer_typeModel;
    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Customers';

    public function __construct()
    {
        $this->customersModel = new CustomersModel();
        $this->customer_typeModel = new Customer_typeModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
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
            $data['customer'] = $this->customersModel->where('sch_id', $shopId)->orderBy('customer_name', 'ASC')->findAll();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Customers/list', $data);
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
            $data['action'] = base_url('Admin/Customers/create_action');
            $data['action2'] = base_url('Admin/Customers/existing_create_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Customers/create', $data);
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
            $data['customer'] = $this->customersModel->where('customer_id', $id)->where('sch_id', $shopId)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Customers/update', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides update action
     * @return void
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $data['cus_type_id'] = $this->request->getPost('cus_type_id');
        $data['type_name'] = $this->request->getPost('type_name');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'type_name' => ['label' => 'Type name', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($this->customer_typeModel->update($data['cus_type_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides transaction view
     * @param $id
     * @return RedirectResponse|void
     */
    public function transaction($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['id'] = $id;

            $table = DB()->table('ledger');
            $data['transaction'] = $table->where('customer_id', $id)->orderBy('ledg_id', 'DESC')->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if ($data['mod_access'] == 1) {
                echo view('Admin/Customers/transaction', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}