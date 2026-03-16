<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LicenseModel;
use App\Models\ShopsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends BaseController
{
    protected $usersModel;
    protected $licenseModel;
    protected $shopsModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->licenseModel = new LicenseModel();
        $this->shopsModel = new ShopsModel();
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
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            print view('Admin/login');
        } else {
            return redirect()->to(site_url('Admin/Dashboard'));
        }
    }

    /**
     * @description This method provides login action
     * @return RedirectResponse
     */
    public function action()
    {
        $this->validation->setRule('email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->validation->setRule('password', 'Password', 'required|max_length[32]');

        if ($this->validation->withRequest($this->request)->run() == FALSE) {

            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">All field is required <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url());
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


                $license = $this->licenseCheck($result->sch_id);

                if ($license == true) {
                    $sessionArray = array('userId' => $result->user_id,
                        'shopId' => $result->sch_id,
                        'role' => $result->role_id,
                        'name' => $result->name,
                        'isLoggedIn' => TRUE
                    );
                    $this->session->set($sessionArray);

                    print 'Ok';
                    return redirect()->to(site_url('Admin/dashboard'));
                } else {
                    // License check and update Shops status (start)
                    $shopData['sch_id'] = $result->sch_id;
                    $shopData['status'] = '0';
                    $this->shopsModel->update($shopData['sch_id'], $shopData);
                    // License check and update Shops status (end)


                    // License check and update users status (start)
                    $userData['sch_id'] = $result->sch_id;
                    $userData['status'] = '0';
                    $this->usersModel->update($userData['sch_id'], $userData);
                    // License check and update users status (start)


                    $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your License Is Expired <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to(site_url());
                }

            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Your login detail not match <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url());
            }

        }
    }

    /**
     * @description This method data check
     * @param string $email
     * @param string $password
     * @return array|object
     */
    private function loginMe($email, $password)
    {
        $user = $this->usersModel->where('email', $email)->where('status', '1')->first();

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
     * @description This method license check
     * @param int $shopId
     * @return bool
     */
    private function licenseCheck($shopId)
    {
        $query = $this->licenseModel->where('sch_id', $shopId)->first();

        $today = date('Y-m-d');
        if ($query->end_date > $today) {
            $data = true;
        } else {
            $data = false;
        }
        return $data;
    }

    /**
     * @description This method session destroy
     * @return RedirectResponse
     */
    public function logout()
    {

        unset($_SESSION['userId']);
        unset($_SESSION['shopId']);
        unset($_SESSION['role']);
        unset($_SESSION['name']);
        unset($_SESSION['isLoggedIn']);

//        $this->session->destroy();
        return redirect()->to('/');
    }


}
