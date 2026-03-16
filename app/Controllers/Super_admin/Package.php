<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\LicenseModel;
use App\Models\PackageModel;
use App\Models\RoleModel;
use App\Models\ShopsModel;
use CodeIgniter\HTTP\RedirectResponse;

class Package extends BaseController
{
    protected $validation;
    protected $session;
    protected $packageModel;
    protected $permission;
    protected $shopsModel;
    protected $roleModel;
    protected $licenseModel;
    protected $crop;

    public function __construct()
    {
        $this->packageModel = new PackageModel();
        $this->permission = new Permission();
        $this->shopsModel = new ShopsModel();
        $this->roleModel = new RoleModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->licenseModel = new LicenseModel();
    }

    /**
     * @description This method provides package view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['package'] = $this->packageModel->where('deleted IS NULL')->findAll();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Package/list', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method provides package create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['action'] = base_url('Super_admin/Package/create_action');
            $data['permission'] = json_decode($this->permission->all_permissions);

            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Package/create', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method store package
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $data['package_name'] = $this->request->getPost('package_name');
        $data['package_admin_permission'] = json_encode($this->request->getPost('package_admin_permission[][]'));

        $this->validation->setRules([
            'package_name' => ['label' => 'package_name', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Package/create');
        } else {
            $this->packageModel->insert($data);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Create successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Package/create');
        }
    }

    /**
     * @description This method update package view
     * @param int $package_id
     * @return RedirectResponse|void
     */
    public function update($package_id)
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {
            $data['action'] = base_url('Super_admin/Package/update_action');
            $data['package'] = $this->packageModel->where('package_id', $package_id)->first();
            $data['permission'] = json_decode($this->permission->all_permissions);


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Package/update', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method update package
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function update_action()
    {

        $data['package_id'] = $this->request->getPost('package_id');
        $data['package_name'] = $this->request->getPost('package_name');
        $data['package_admin_permission'] = json_encode($this->request->getPost('package_admin_permission'));

        $this->validation->setRules([
            'package_name' => ['label' => 'package_name', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Package/update/' . $data['package_id']);
        } else {
            $this->packageModel->update($data['package_id'], $data);

            $allshop = $this->shopsModel->where('package_id', $data['package_id'])->findAll();
            if (!empty($allshop)) {
                foreach ($allshop as $val) {
                    $roleupdate = [
                        'permission' => $data['package_admin_permission']
                    ];
                    $table = DB()->table('roles');
                    $table->where('sch_id', $val->sch_id)->where('is_default', '1')->update($roleupdate);
                }
            }


            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Package/update/' . $data['package_id']);

        }
    }

    /**
     * @description This method check uniq package
     * @param int $sch_id
     * @return mixed
     */
    private function check_uniq_by_id($sch_id)
    {
        $query = $this->licenseModel->where('sch_id', $sch_id)->first();
        return $query;
    }


}
