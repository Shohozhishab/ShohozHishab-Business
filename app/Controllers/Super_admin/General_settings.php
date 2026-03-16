<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\Gen_settings_superModel;
use CodeIgniter\HTTP\RedirectResponse;

class General_settings extends BaseController
{
    protected $validation;
    protected $session;
    protected $adminModel;
    protected $gen_settings_superModel;
    protected $crop;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->gen_settings_superModel = new Gen_settings_superModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides general settings view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['settings'] = $this->gen_settings_superModel->findAll();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/General_settings/settings_list', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method provides general settings create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['action'] = base_url('Super_admin/General_settings/create_action');


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/General_settings/create', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method store general settings
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function create_action()
    {

        $data['label'] = $this->request->getPost('label');
        $data['value'] = $this->request->getPost('value');

        $this->validation->setRules([
            'label' => ['label' => 'label', 'rules' => 'required'],
            'value' => ['label' => 'value', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/General_settings/create');
        } else {
            $this->gen_settings_superModel->insert($data);
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/General_settings/create');
        }
    }

    /**
     * @description This method provides general settings update view
     * @param int $settings_id_sup
     * @return RedirectResponse|void
     */
    public function update($settings_id_sup)
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['settings'] = $this->gen_settings_superModel->where('settings_id_sup', $settings_id_sup)->first();
            $data['action'] = base_url('Super_admin/General_settings/update_action');

            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/General_settings/update', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method update general settings
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function update_action()
    {
        $data['settings_id_sup'] = $this->request->getPost('settings_id_sup');
        $data['label'] = $this->request->getPost('label');
        $data['value'] = $this->request->getPost('value');

        $this->validation->setRules([
            'label' => ['label' => 'label', 'rules' => 'required'],
            'value' => ['label' => 'value', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/General_settings/update/' . $data['settings_id_sup']);
        } else {
            $this->gen_settings_superModel->update($data['settings_id_sup'], $data);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/General_settings/update/' . $data['settings_id_sup']);
        }
    }


}
