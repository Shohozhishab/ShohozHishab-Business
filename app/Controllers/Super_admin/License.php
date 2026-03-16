<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\LicenseModel;
use App\Models\ShopsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RedirectResponse;

class License extends BaseController
{
    protected $validation;
    protected $session;
    protected $adminModel;
    protected $licenseModel;
    protected $shopsModel;
    protected $usersModel;
    protected $crop;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->licenseModel = new LicenseModel();
        $this->shopsModel = new ShopsModel();
        $this->usersModel = new UsersModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides license view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['license'] = $this->licenseModel->where('deleted IS NULL')->findAll();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/License/license_list', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method provides license create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['action'] = base_url('Super_admin/License/create_action');


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/License/create', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method store license
     * @return RedirectResponse|void
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $licKey = uniqid();

        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['start_date'] = $this->request->getPost('start_date');
        $data['end_date'] = $this->request->getPost('end_date');
        $data['lic_key'] = $licKey;

        $this->validation->setRules([
            'sch_id' => ['label' => 'Shops', 'rules' => 'required'],
            'start_date' => ['label' => 'start_date', 'rules' => 'required'],
            'end_date' => ['label' => 'end_date', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/License/create');
        } else {

            $sch_id = $data['sch_id'];

            $licenseCheck = $this->check_uniq_by_id($sch_id);
            if (empty($licenseCheck)) {

                DB()->transStart();

                $this->licenseModel->insert($data);

                $shopData['status'] = '1';
                $this->shopsModel->update($data['sch_id'], $shopData);

                $userData = ['status' => 1];
                $tab = DB()->table('users');
                $tab->where('sch_id', $sch_id)->where('is_default', '1')->update($userData);

                DB()->transComplete();

                $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/Super_admin/License/create');
            }

        }
    }

    /**
     * @description This method provides license update view
     * @param int $lic_id
     * @return RedirectResponse|void
     */
    public function update($lic_id)
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['license'] = $this->licenseModel->where('lic_id', $lic_id)->first();
            $data['action'] = base_url('Super_admin/License/update_action');


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/License/update', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method update license
     * @return RedirectResponse
     */
    public function update_action()
    {
        $licKey = uniqid();

        $data['lic_id'] = $this->request->getPost('lic_id');
        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['start_date'] = $this->request->getPost('start_date');
        $data['end_date'] = $this->request->getPost('end_date');

        $this->validation->setRules([
            'sch_id' => ['label' => 'Shops', 'rules' => 'required'],
            'start_date' => ['label' => 'start_date', 'rules' => 'required'],
            'end_date' => ['label' => 'end_date', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/License/update/' . $data['lic_id']);
        } else {


            $this->licenseModel->update($data['lic_id'], $data);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/License/update/' . $data['lic_id']);


        }
    }

    /**
     * @description This method check uniq license
     * @param int $sch_id
     * @return array|object|null
     */
    private function check_uniq_by_id($sch_id)
    {
        $query = $this->licenseModel->where('sch_id', $sch_id)->first();
        return $query;
    }


}
