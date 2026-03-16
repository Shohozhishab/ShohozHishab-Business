<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\UsersModel;
use CodeIgniter\HTTP\RedirectResponse;


class User extends BaseController
{

    protected $usersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'User';

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides user view
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
            $usersTable = DB()->table('users');
            $data['users_data'] = $usersTable->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('name', 'ASC')->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/User/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides user create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/User/create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/User/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store user
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['email'] = $this->request->getPost('email');
        $data['name'] = $this->request->getPost('name');
        $data['role_id'] = $this->request->getPost('role_id');
        $data['status'] = $this->request->getPost('status');
        $data['password'] = sha1($this->request->getPost('password'));
        $data['con_password'] = sha1($this->request->getPost('con_password'));
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'email' => ['label' => 'email', 'rules' => 'required|valid_emails|max_length[32]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'password' => ['label' => 'password', 'rules' => 'required'],
            'con_password' => ['label' => 'con_password', 'rules' => 'required|matches[password]'],
            'role_id' => ['label' => 'role_id', 'rules' => 'required'],
            'status' => ['label' => 'status', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $emailUnique = is_unique_super('users', 'email', $data['email']);
            if ($emailUnique == true) {
                if ($this->usersModel->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Email already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides user update view
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
            $data['user'] = $this->usersModel->where('user_id', $id)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/User/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update user register
     * @return void
     * @throws \ReflectionException
     */
    public function register_update()
    {

        $userId = $this->session->userId;

        $data['user_id'] = $this->request->getPost('user_id');
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        $data['password'] = sha1($this->request->getPost('password'));
        $data['con_password'] = sha1($this->request->getPost('con_password'));
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'email' => ['label' => 'email', 'rules' => 'required|valid_emails|max_length[32]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'password' => ['label' => 'password', 'rules' => 'required'],
            'con_password' => ['label' => 'con_password', 'rules' => 'required|matches[password]'],
            'status' => ['label' => 'status', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $emailUnique = is_unique_super_update('users', 'email', $data['email'], 'user_id', $data['user_id']);
            if ($emailUnique == true) {
                if ($this->usersModel->update($data['user_id'], $data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Email already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update user personal
     * @return void
     */
    public function personal_update()
    {
        $userId = $this->session->userId;

        $data['user_id'] = $this->request->getPost('user_id');
        $data['mobile'] = $this->request->getPost('mobile');
        $data['address'] = $this->request->getPost('address');
        $data['role_id'] = $this->request->getPost('role_id');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'role_id' => ['label' => 'role_id', 'rules' => 'required'],
            'mobile' => ['label' => 'Mobile', 'rules' => 'required|is_natural_no_zero|alpha_numeric|min_length[5]|max_length[12]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if ($this->usersModel->update($data['user_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update user photo
     * @return void
     */
    public function photo_update()
    {

        $userId = $this->session->userId;

        $data['user_id'] = $this->request->getPost('user_id');
        $data['updatedBy'] = $userId;


        if (!empty($_FILES['pic']['name'])) {
            $target_dir = FCPATH . '/uploads/users_image/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('pic', 'users', 'user_id', $data['user_id']);
            if (!empty($old_img)) {
                unlink($target_dir . $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('pic');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'users_' . $pic->getName();
            $this->crop->withFile($target_dir . $namePic)->fit(300, 300, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['pic'] = $pro_nameimg;
        }


        $this->validation->setRules([
            'user_id' => ['label' => 'user_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if ($this->usersModel->update($data['user_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }

    }

    /**
     * @description This method provides user view
     * @param int $id
     * @return RedirectResponse|void
     */
    public function view($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $data['user'] = $this->usersModel->where('user_id', $id)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/User/view', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }


}