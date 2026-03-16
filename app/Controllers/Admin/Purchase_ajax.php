<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Purchase_ajax extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Purchase';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides purchase view
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
            $purchaseTable = DB()->table('purchase');
            $data['purchase_data'] = $purchaseTable->where('sch_id', $shopId)->get()->getResult();


            //purchase table null value delete (start)
            $purcTable = DB()->table('purchase');
            $purchId = $purcTable->where('sch_id', $shopId)->where('due', NULL)->get()->getResult();
            foreach ($purchId as $value) {
                // purchasa itame fiend count (start)
                $purchase_itemTable = DB()->table('purchase_item');
                $purItem = $purchase_itemTable->where('purchase_id', $value->purchase_id)->countAllResults();
                // purchasa itame fiend count (end)

                //deleted Nul value in purchase (start)
                if ($purItem < 1) {
                    $purcDelTable = DB()->table('purchase');
                    $purcDelTable->where('purchase_id', $value->purchase_id)->delete();
                }
                //deleted Nul value in purchase (end)
            }
            //purchase table null value delete (end)


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Purchase/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method provides purchase create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Purchase/create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Purchase/create', $data);
            } else {
                echo view('no_permission');
            }
        }
    }

    /**
     * @description This method store purchase
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if (!empty($_FILES['image']['name'])) {
                $target_dir = FCPATH . '/uploads/brand_image/';
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777);
                }

                //old image unlink
//                $old_img = get_data_by_id('image','users','user_id',$data['user_id']);
//                if (!empty($old_img)){
//                    unlink($target_dir.''.$old_img);
//                }

                //new image uplode
                $pic = $this->request->getFile('image');
                $namePic = $pic->getRandomName();
                $pic->move($target_dir, $namePic);
                $pro_nameimg = 'brand_' . $pic->getName();
                $this->crop->withFile($target_dir .  $namePic)->fit(300, 300, 'center')->save($target_dir . $pro_nameimg);
                unlink($target_dir .  $namePic);
                $data['image'] = $pro_nameimg;
            }

            $brandTable = DB()->table('brand');
            if ($brandTable->insert($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides purchase view
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
            $purchaseTable = DB()->table('purchase');
            $data['purchase'] = $purchaseTable->where('purchase_id', $id)->get()->getRow();

            $purchase_itemTable = DB()->table('purchase_item');
            $data['purchaseItame'] = $purchase_itemTable->where('purchase_id', $id)->get()->getResult();
            $data['purchaseId'] = $id;


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            if (isset($data['mod_access']) and $data['read'] == 1) {
                echo view('Admin/Purchase/view', $data);
            } else {
                echo view('no_permission');
            }
        }
    }


}