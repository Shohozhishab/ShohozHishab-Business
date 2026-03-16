<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Role extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Role';

    public function __construct()
    {
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Role/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
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
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Role/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store role
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['role'] = $this->request->getPost('role');
        $data['permission'] = $this->request->getPost('permission[][]');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'role' => ['label' => 'Role', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {


            $perm = json_encode($data['permission']);

            $dataRole = array(
                'sch_id' => $shopId,
                'role' => $data['role'],
                'permission' => $perm,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s'),
            );

            $roleTable = DB()->table('roles');
            if ($roleTable->insert($dataRole)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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

            if ($data['roles']->is_default == 1) {
                return redirect()->to(site_url('Admin/Role'));
            }


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['update'] == 1) {
                echo view('Admin/Role/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update role
     * @return void
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $data['role_id'] = $this->request->getPost('role_id');
        $data['role'] = $this->request->getPost('role');
        $data['permission'] = $this->request->getPost('permission[][]');

        $this->validation->setRules([
            'role' => ['label' => 'Role', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $perm = json_encode($data['permission']);

            $dataRloe = array(
                'role' => $data['role'],
                'permission' => $perm,
                'updatedBy' => $userId,
            );
            $roleTable = DB()->table('roles');

            if ($roleTable->where('role_id', $data['role_id'])->update($dataRloe)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }


}