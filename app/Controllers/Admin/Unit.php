<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Unit extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Unit';

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

            $unit_categories_id = $this->request->getGet('unit_categories_id');

            $table = DB()->table('units');
            $table->where('sch_id', $shopId);
            if (!empty($unit_categories_id)){
                $table->where('unit_categories_id', $unit_categories_id)->orderBy('conversion_factor','DESC');
            }
            $data['units'] = $table->get()->getResult();
            $data['unit_categories_id'] = isset($unit_categories_id) ? $unit_categories_id: '';


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Unit/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
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
            $data['action'] = base_url('Admin/Unit/create_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Unit/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store Brand
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['symbol'] = $this->request->getPost('symbol');
        $data['unit_categories_id'] = $this->request->getPost('unit_categories_id');
        $data['conversion_factor'] = $this->request->getPost('conversion_factor');
        $data['decimal_places'] = $this->request->getPost('decimal_places');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'symbol' => ['label' => 'Symbol', 'rules' => 'required'],
            'unit_categories_id' => ['label' => 'Unit Categories', 'rules' => 'required'],
            'conversion_factor' => ['label' => 'Conversion Factor', 'rules' => 'required'],
            'decimal_places' => ['label' => 'Decimal Places', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $isBase = DB()->table('units')->where('unit_categories_id',$data['unit_categories_id'])->where('is_base','1')->countAllResults();
            if (empty($isBase)){
                $data['is_base'] = '1';
            }

            $table = DB()->table('units');
            if ($table->insert($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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
            $table = DB()->table('units');
            $data['units'] = $table->where('units_id', $id)->get()->getRow();

            $data['isBase'] = DB()->table('units')->where('units_id',$id)->where('is_base','1')->countAllResults();

            $data['action'] = base_url('Admin/Unit/update_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Unit/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update Brand
     * @return void
     */
    public function update_action()
    {

        $userId = $this->session->userId;

        $data['units_id'] = $this->request->getPost('units_id');
        $data['name'] = $this->request->getPost('name');
        $data['symbol'] = $this->request->getPost('symbol');
        $data['unit_categories_id'] = $this->request->getPost('unit_categories_id');
        $data['conversion_factor'] = $this->request->getPost('conversion_factor');
        $data['decimal_places'] = $this->request->getPost('decimal_places');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'symbol' => ['label' => 'Symbol', 'rules' => 'required'],
            'unit_categories_id' => ['label' => 'Unit Categories', 'rules' => 'required'],
            'conversion_factor' => ['label' => 'Conversion Factor', 'rules' => 'required'],
            'decimal_places' => ['label' => 'Decimal Places', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            $table = DB()->table('units');
            if ($table->where('units_id', $data['units_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }
    /**
     * @param int $units_id
     * @return void
     */
    public function delete($units_id){
        $table = DB()->table('units');
        $table->where('units_id',$units_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Unit'));
    }

}