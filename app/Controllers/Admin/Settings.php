<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\ShopsModel;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\RedirectResponse;


class Settings extends BaseController
{


    protected $shopsModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Settings';

    public function __construct()
    {
        $this->shopsModel = new ShopsModel();
        $this->permission = new Permission();
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
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $shopTable = DB()->table('shops');
            $data['stores'] = $shopTable->where('sch_id', $shopId)->get()->getRow();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Settings/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides settings update view
     * @return RedirectResponse|void
     */
    public function update()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $shopTable = DB()->table('shops');
            $data['stores'] = $shopTable->where('sch_id', $shopId)->get()->getRow();

            $gen_settingsTable = DB()->table('gen_settings');
            $data['gen_setting'] = $gen_settingsTable->where('sch_id', $shopId)->get()->getResult();

            $vat_registerTable = DB()->table('vat_register');
            $data['vat_register'] = $vat_registerTable->where('sch_id', $shopId)->get()->getRow();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Settings/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update general
     * @return void
     */
    public function general_update()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        $data['address'] = $this->request->getPost('address');
        $data['mobile'] = $this->request->getPost('mobile');
        $data['comment'] = $this->request->getPost('comment');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;
        $data['updatedDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|validusername'],
            'email' => ['label' => 'Email', 'rules' => 'required'],
            'mobile' => ['label' => 'Mobile', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'status' => ['label' => 'status', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $shopTable = DB()->table('shops');

            if ($shopTable->where('sch_id', $shopId)->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update photo
     * @return void
     * @throws \ReflectionException
     */
    public function photo_update()
    {
        $data['sch_id'] = $this->request->getPost('sch_id');


        if (!empty($_FILES['logo']['name'])) {
            $target_dir = FCPATH . '/uploads/schools/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('logo', 'shops', 'sch_id', $data['sch_id']);
            if (!empty($old_img)) {
                unlink($target_dir . $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('logo');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'profile_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(350, 100, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['logo'] = $pro_nameimg;
        }

        if (!empty($_FILES['profile_image']['name'])) {
            $target_dir = FCPATH . '/uploads/schools/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img_pro = get_data_by_id('image', 'shops', 'sch_id', $data['sch_id']);
            if (!empty($old_img_pro)) {
                unlink($target_dir .  $old_img_pro);
            }

            //new image uplode
            $picpRO = $this->request->getFile('profile_image');
            $namePicPRO = $picpRO->getRandomName();
            $picpRO->move($target_dir, $namePicPRO);
            $pro_nameimg_Pro = 'pro_' . $picpRO->getName();
            $this->crop->withFile($target_dir .  $namePicPRO)->fit(160, 160, 'center')->save($target_dir . $pro_nameimg_Pro);
            unlink($target_dir . $namePicPRO);
            $data['image'] = $pro_nameimg_Pro;
        }

        if ($this->shopsModel->update($data['sch_id'], $data)) {
            print '<div class="alert alert-success alert-dismissible" role="alert"> update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method update general settings
     * @return void
     */
    public function general_settings_update()
    {
        $id = $this->request->getPost('id[]');
        $value = $this->request->getPost('value[]');

        foreach ($id as $key => $val) {
            $gen_settingsTable = DB()->table('gen_settings');
            $gen_settingsTable->where('settings_id', $id[$key])->update(['value' => $value[$key]]);
        }
        print '<div class="alert alert-success alert-dismissible" role="alert"> update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }

    /**
     * @description This method update vat
     * @return void
     */
    public function vat_update()
    {
        $userId = $this->session->userId;

        $data['vat_id'] = $this->request->getPost('vat_id');
        $data['name'] = $this->request->getPost('name');
        $data['vat_register_no'] = $this->request->getPost('vat_register_no');
        $data['updatedBy'] = $userId;
        $data['updatedDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required'],
            'vat_register_no' => ['label' => 'vat_register_no', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $vat_registerTable = DB()->table('vat_register');

            if ($vat_registerTable->where('vat_id', $data['vat_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method database backup
     * @return DownloadResponse|null
     */
    public function databaseBackup()
    {
        helper('filesystem');

        $db = \Config\Database::connect();
        $dbname = $db->database;
        $path = FCPATH . '/uploads/';             // change path here
        $filename = $dbname . '_' . date('dMY_Hi') . '.sql';   // change file name here
        $prefs = ['filename' => $filename];              // I only set the file name, for complete prefs see below

        $util = (new \CodeIgniter\Database\Database())->loadUtils($db);
        $backup = $util->backup($prefs, $db);

        write_file($path . $filename . '.gz', $backup);
        return $this->response->download($path . $filename . '.gz', null);
    }


}