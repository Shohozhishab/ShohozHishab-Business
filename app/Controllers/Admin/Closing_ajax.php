<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Closing_ajax extends BaseController
{


    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    protected $cart;
    private $module_name = 'Closing';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
        $this->cart = \Config\Services::cart();
    }

    /**
     * @description This method provides view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $data['menu'] = view('Admin/Closing/menu');

            echo view('Admin/Closing/list', $data);

        }
    }

    /**
     * @description This method provides cash view
     * @return RedirectResponse|void
     */
    public function cash()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/cash', $data);
        }
    }

    /**
     * @description This method provides bank view
     * @return RedirectResponse|void
     */
    public function bank()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/bank', $data);
        }
    }

    /**
     * @description This method provides capital view
     * @return RedirectResponse|void
     */
    public function capital()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/capital', $data);
        }
    }

    /**
     * @description This method provides stock view
     * @return RedirectResponse|void
     */
    public function stock()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/stock', $data);
        }
    }

    /**
     * @description This method provides customer view
     * @return RedirectResponse|void
     */
    public function customers()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/customers', $data);
        }
    }

    /**
     * @description This method provides account holder view
     * @return RedirectResponse|void
     */
    public function accountHolder()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/accountHolder', $data);
        }
    }

    /**
     * @description This method provides supplier view
     * @return RedirectResponse|void
     */
    public function suppliers()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/suppliers', $data);
        }
    }

    /**
     * @description This method provides employ view
     * @return RedirectResponse|void
     */
    public function employe()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/employe', $data);
        }
    }

    /**
     * @description This method provides expense view
     * @return RedirectResponse|void
     */
    public function expense()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/expense', $data);
        }
    }

    /**
     * @description This method provides profit view
     * @return RedirectResponse|void
     */
    public function profit()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/profit', $data);
        }
    }

    /**
     * @description This method provides vat ledger view
     * @return RedirectResponse|void
     */
    public function ledger_vat()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/ledger_vat', $data);
        }
    }

    /**
     * @description This method provides products view
     * @return RedirectResponse|void
     */
    public function products()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {


            $data['menu'] = view('Admin/Closing/menu');
            echo view('Admin/Closing/products', $data);
        }
    }


}