<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    protected $validation;
    protected $session;
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
    }

    /**
     * @description This method provides dashboard view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/dashboard');
            echo view('Super_admin/footer');
        }
    }


}
