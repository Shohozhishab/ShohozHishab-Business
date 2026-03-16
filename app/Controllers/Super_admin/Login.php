<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController
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
     * @description This method provides login view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {

            print view('Super_admin/login');

        } else {
            return redirect()->to(site_url("/Super_admin/Shops"));
        }

    }

    /**
     * @description This method login action
     * @return RedirectResponse
     */
    public function action()
    {
        $this->validation->setRule('email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->validation->setRule('password', 'Password', 'required|max_length[32]');

        if ($this->validation->withRequest($this->request)->run() == FALSE) {

            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">All field is required <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Super_admin/login'));
        } else {

            $email = strtolower($this->request->getPost('email'));
            $password = $this->request->getPost('password');

            $result = $this->loginMe($email, $password);

            if (!empty($result)) {

                // Remember me (Remembering the user email and password) Start
                if (!empty($this->request->getPost("remember"))) {

                    setcookie('login_email', $email, time() + (86400 * 30), "/");
                    setcookie('login_password', $password, time() + (86400 * 30), "/");

                } else {
                    if (isset($_COOKIE['login_email'])) {
                        setcookie('login_email', '', 0, "/");
                    }
                    if (isset($_COOKIE['login_password'])) {
                        setcookie('login_password', '', 0, "/");
                    }
                }
                // Remember me (Remembering the user email and password) End

                print $result->email;

                $sessionArray = array(
                    'userIdSuper' => $result->user_id,
                    'sup_name' => $result->name,
                    'isLoggedInSuperAdmin' => TRUE
                );

                $this->session->set($sessionArray);

                return redirect()->to(site_url("/Super_admin/Shops"));

            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Email or password mismatch <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url("/Super_admin/login"));
            }

        }
    }

    /**
     * @description This method check data exists
     * @param string $email
     * @param string $password
     * @return array|object
     */
    private function loginMe($email, $password)
    {
        $user = $this->adminModel->where('email', $email)->first();

        if (!empty($user)) {
            if (SHA1($password) == $user->password) {
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * @description This method destroy login session
     * @return RedirectResponse
     */
    public function logout()
    {

        unset($_SESSION['suo_user_id']);
        unset($_SESSION['sup_name']);
        unset($_SESSION['isLoggedInSuperAdmin']);

//        $this->session->destroy();
        return redirect()->to('/Super_admin');
    }


}
