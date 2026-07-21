<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Unit_set extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Unit_set';

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
            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id', $shopId)->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Unit_set/list', $data);
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
            $data['action'] = base_url('Admin/Unit_set/create_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Unit_set/create', $data);
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
        $data['unit_categories_id'] = $this->request->getPost('unit_categories_id');
        $purchase_units = $this->request->getPost('purchase_units[]');
        $sell_units = $this->request->getPost('sell_units[]');
        $data['default_set'] = $this->request->getPost('default_set');
        $data['purchase_units_price'] = $this->request->getPost('purchase_price');
        $data['sell_unit_price'] = $this->request->getPost('sell_price');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'unit_categories_id' => ['label' => 'Unit Categories', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if($data['default_set'] == 1){
                DB()->table('unit_set')->where('default_set','1')->update(['default_set' => '0']);
            }

            $data['purchase_units'] = json_encode($purchase_units);
            $data['sell_units'] = json_encode($sell_units);
            $table = DB()->table('unit_set');
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
            $table = DB()->table('unit_set');
            $data['units_set'] = $table->where('unit_set_id', $id)->get()->getRow();

            $data['unit'] = DB()->table('units')->where('unit_categories_id',$data['units_set']->unit_categories_id)->orderBy('conversion_factor','DESC')->get()->getResult();

            $data['action'] = base_url('Admin/Unit_set/update_action');

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Unit_set/update', $data);
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

        $data['unit_set_id'] = $this->request->getPost('unit_set_id');
        $data['name'] = $this->request->getPost('name');
        $data['unit_categories_id'] = $this->request->getPost('unit_categories_id');
        $purchase_units = $this->request->getPost('purchase_units[]');
        $sell_units = $this->request->getPost('sell_units[]');
        $data['default_set'] = $this->request->getPost('default_set');
        $data['purchase_units_price'] = $this->request->getPost('purchase_price');
        $data['sell_unit_price'] = $this->request->getPost('sell_price');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'unit_categories_id' => ['label' => 'Unit Categories', 'rules' => 'required'],
            'purchase_units_price' => ['label' => 'Purchase Units Price', 'rules' => 'required'],
            'sell_unit_price' => ['label' => 'Sell Units Price', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if($data['default_set'] == 1){
                DB()->table('unit_set')->where('default_set','1')->update(['default_set' => '0']);
            }
            $data['purchase_units'] = json_encode($purchase_units);
            $data['sell_units'] = json_encode($sell_units);

            $table = DB()->table('unit_set');
            if ($table->where('unit_set_id', $data['unit_set_id'])->update($data)) {
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
    public function delete($unit_set_id){
        $table = DB()->table('unit_set');
        $table->where('unit_set_id',$unit_set_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Unit_set'));
    }
    public function unitShowInOption(){
        $unit_categories_id = $this->request->getPost('unit_categories_id');
        $query = DB()->table('units')->where('unit_categories_id',$unit_categories_id)->orderBy('conversion_factor','DESC')->get()->getResult();

        return $this->response->setJSON($query);
    }
}