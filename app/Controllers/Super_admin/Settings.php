<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\RedirectResponse;

class Settings extends BaseController
{
    protected $validation;
    protected $session;
    protected $adminModel;
    protected $crop;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides settings view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['settings'] = $this->adminModel->findAll();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Settings/settings_list', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method provides settings update view
     * @param int $user_id
     * @return RedirectResponse|void
     */
    public function update($user_id)
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['settings'] = $this->adminModel->where('user_id', $user_id)->first();

            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Settings/update', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @description This method update general settings
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function general_update()
    {
        $data['user_id'] = $this->request->getPost('user_id');
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        if (!empty($data['password'])) {
            $data['password'] = sha1($this->request->getPost('password'));
            $data['con_password'] = sha1($this->request->getPost('con_password'));
        }

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required'],
            'email' => ['label' => 'email', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Settings/update/' . $data['user_id'] . '?active=general');
        } else {

            $this->adminModel->update($data['user_id'], $data);
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Settings/update/' . $data['user_id'] . '?active=general');
        }
    }

    /**
     * @description This method update personal settings
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function personal_update()
    {
        $data['user_id'] = $this->request->getPost('user_id');
        $data['ComName'] = $this->request->getPost('ComName');
        $data['country'] = $this->request->getPost('country');
        $data['mobile'] = $this->request->getPost('mobile');
        $data['address'] = $this->request->getPost('address');

        $this->adminModel->update($data['user_id'], $data);
        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to('/Super_admin/Settings/update/' . $data['user_id'] . '?active=personal');
    }

    /**
     * @description This method update photo settings
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function photo_update()
    {
        $data['user_id'] = $this->request->getPost('user_id');

        if (!empty($_FILES['pic']['name'])) {
            $target_dir = FCPATH . '/uploads/admin_image/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('pic', 'admin', 'user_id', $data['user_id']);
            if (!empty($old_img)) {
                unlink($target_dir .  $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('pic');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'profile_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(160, 160, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['pic'] = $pro_nameimg;
        }

        $this->adminModel->update($data['user_id'], $data);
        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to('/Super_admin/Settings/update/' . $data['user_id'] . '?active=photo');
    }


}
