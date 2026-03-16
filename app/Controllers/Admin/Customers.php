<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Customer_typeModel;
use App\Models\CustomersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Customers extends BaseController
{

    protected $customersModel;
    protected $customer_typeModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Customers';

    public function __construct()
    {
        $this->customersModel = new CustomersModel();
        $this->customer_typeModel = new Customer_typeModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides view
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
            $data['customer'] = $this->customersModel->where('sch_id', $shopId)->orderBy('customer_name', 'ASC')->findAll();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Customers/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Customers/create_action');
            $data['action2'] = base_url('Admin/Customers/existing_create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Customers/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store customer
     * @return void
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('customers', 'mobile', $mobile);
        if ($is_unique == true) {
            $data['mobile'] = $this->request->getPost('mobile');
            $data['customer_name'] = $this->request->getPost('customer_name');
            $data['cus_type_id'] = $this->request->getPost('cus_type_id');
            $data['sch_id'] = $shopId;
            $data['createdBy'] = $userId;
            $data['createdDtm'] = date('Y-m-d h:i:s');

            $this->validation->setRules([
                'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|is_natural_no_zero|min_length[5]|max_length[12]'],
                'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {


                if ($this->customersModel->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">This phone number is already in use<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method store existing customer
     * @return void
     * @throws \ReflectionException
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('customers', 'mobile', $mobile);
        if ($is_unique == true) {
            $amount = $this->request->getPost('amount');
            $transaction_type = $this->request->getPost('transaction_type');
            $name = $this->request->getPost('customer_name');


            $data['mobile'] = $this->request->getPost('mobile');
            $data['customer_name'] = $this->request->getPost('customer_name');
            $data['cus_type_id'] = $this->request->getPost('cus_type_id');
            $data['transaction_type'] = $this->request->getPost('transaction_type');
            $data['amount'] = $this->request->getPost('amount');

            $this->validation->setRules([
                'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|min_length[5]|max_length[12]'],
                'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
                'transaction_type' => ['label' => 'transaction_type', 'rules' => 'required'],
                'amount' => ['label' => 'Amount', 'rules' => 'required|alpha_numeric'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                if ($amount !== '0') {
                    DB()->transStart();

                    if ($transaction_type == '2') {
                        $cusdata = array(
                            'sch_id' => $shopId,
                            'customer_name' => $this->request->getPost('customer_name'),
                            'balance' => $amount,
                            'mobile' => $this->request->getPost('mobile'),
                            'cus_type_id' => $this->request->getPost('cus_type_id'),
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $this->customersModel->insert($cusdata);
                        $customerId = $this->customersModel->getInsertID();


                        //insert customer ledger table (start)
                        $cusLedgdata = array(
                            'sch_id' => $shopId,
                            'customer_id' => $customerId,
                            'particulars' => 'Customer last balance ',
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $cusLedTable = DB()->table('ledger');
                        $cusLedTable->insert($cusLedgdata);
                        //insert customer ledger table (end)


                        //update capital (start)
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        $newcap = $oldCap - $amount;

                        $capData = array(
                            'capital' => $newcap
                        );
                        $shopTable = DB()->table('shops');
                        $shopTable->where('sch_id', $shopId)->update($capData);

                        $capLedgdata = array(
                            'sch_id' => $shopId,
                            'particulars' => 'Existing customer (' . $name . ') is added with existing balance',
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $newcap,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_capitalTable = DB()->table('ledger_capital');
                        $ledger_capitalTable->insert($capLedgdata);
                        //update capital (end)

                        print '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    } else {
                        //capital
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        if (-$oldCap >= $amount) {

                            $cusdata2 = array(
                                'sch_id' => $shopId,
                                'customer_name' => $this->request->getPost('customer_name'),
                                'balance' => -$amount,
                                'mobile' => $this->request->getPost('mobile'),
                                'cus_type_id' => $this->request->getPost('cus_type_id'),
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $this->customersModel->insert($cusdata2);
                            $customerId = $this->customersModel->getInsertID();


                            //insert customer ledger table (start)
                            $cusLedgdata = array(
                                'sch_id' => $shopId,
                                'customer_id' => $customerId,
                                'particulars' => 'Customer last balance ',
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'rest_balance' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $cusLedTable = DB()->table('ledger');
                            $cusLedTable->insert($cusLedgdata);
                            //insert customer ledger table (end)


                            //update capital (start)
                            $newcap = $oldCap + $amount;

                            $capData = array(
                                'capital' => $newcap
                            );
                            $shopTable = DB()->table('shops');
                            $shopTable->where('sch_id', $shopId)->update($capData);

                            $capLedgdata = array(
                                'sch_id' => $shopId,
                                'particulars' => 'Existing customer (' . $name . ') is added with existing balance',
                                'trangaction_type' => 'Dr.',
                                'amount' => $amount,
                                'rest_balance' => $newcap,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_capitalTable = DB()->table('ledger_capital');
                            $ledger_capitalTable->insert($capLedgdata);
                            //update capital (end)

                            print '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                        } else {
                            print '<div class="alert alert-danger alert-dismissible" role="alert">Need to update capital for this transaction<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                            die();
                        }
                    }

                    DB()->transComplete();
                }else{
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }

            }
        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert">This phone number is already in use<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

    }

    /**
     * @description This method provides update view
     * @param $id
     * @return RedirectResponse|void
     */
    public function update($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $data['customer'] = $this->customersModel->where('customer_id', $id)->where('sch_id', $shopId)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Customers/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update general customer
     * @return void
     * @throws \ReflectionException
     */
    public function general_update()
    {
        $userId = $this->session->userId;

        $data['customer_id'] = $this->request->getPost('customer_id');
        $data['cus_type_id'] = $this->request->getPost('cus_type_id');
        $data['customer_name'] = $this->request->getPost('customer_name');
        $data['mobile'] = $this->request->getPost('mobile');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|min_length[5]|max_length[12]'],
            'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $is_unique = is_unique_update('customers', 'mobile', $data['mobile'], 'customer_id', $data['customer_id']);
            if ($is_unique == true) {

                if ($this->customersModel->update($data['customer_id'], $data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">This phone number is already in use<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }
    }

    /**
     * @description This method update personal customer
     * @return void
     * @throws \ReflectionException
     */
    public function personal_update()
    {
        $userId = $this->session->userId;

        $data['customer_id'] = $this->request->getPost('customer_id');
        $data['father_name'] = $this->request->getPost('father_name');
        $data['mother_name'] = $this->request->getPost('mother_name');
        $data['address'] = $this->request->getPost('address');
        $data['present_address'] = $this->request->getPost('present_address');
        $data['age'] = $this->request->getPost('age');
        $data['nid'] = $this->request->getPost('nid');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'father_name' => ['label' => 'father_name', 'rules' => 'required'],
            'nid' => ['label' => 'Nid', 'rules' => 'alpha_numeric_space|max_length[32]'],
            'mother_name' => ['label' => 'Mother Name', 'rules' => 'only_numeric_not_allow|max_length[60]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if ($this->customersModel->update($data['customer_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }
    }

    /**
     * @description This method update photo customer
     * @return void
     */
    public function photo_update()
    {

        $data['customer_id'] = $this->request->getPost('customer_id');

        if (!empty($_FILES['pic']['name'])) {
            $target_dir = FCPATH . '/uploads/customer_image/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('pic', 'customers', 'customer_id', $data['customer_id']);
            if (!empty($old_img)) {
                unlink($target_dir . $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('pic');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'customer_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(300, 300, 'center')->save($target_dir . $pro_nameimg);
            unlink($target_dir . $namePic);
            $data['pic'] = $pro_nameimg;


            if ($this->customersModel->update($data['customer_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert"> please select a image  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method transaction
     * @param $id
     * @return RedirectResponse|void
     */
    public function transaction($id)
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {

            $data['id'] = $id;

            $table = DB()->table('ledger');
            $data['transaction'] = $table->where('customer_id', $id)->orderBy('ledg_id', 'DESC')->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Customers/transaction', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store customer
     * @return void
     * @throws \ReflectionException
     */
    public function create_action_ajax()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('customers', 'mobile', $mobile);
        if ($is_unique == true) {
            $data['mobile'] = $this->request->getPost('mobile');
            $data['customer_name'] = $this->request->getPost('customer_name');
            $data['cus_type_id'] = $this->request->getPost('cus_type_id');
            $data['sch_id'] = $shopId;
            $data['createdBy'] = $userId;
            $data['createdDtm'] = date('Y-m-d h:i:s');

            $this->validation->setRules([
                'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|is_natural_no_zero|min_length[5]|max_length[12]'],
                'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                if ($this->customersModel->insert($data)) {
                    $insertID = $this->customersModel->insertID;
                    $datamess['htmlData'] = getAllListInOptionWithStatus($insertID, 'customer_id', 'customer_name', 'customers','customer_name');

                    $datamess['success'] = true;
                    $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }

        } else {
            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">This phone number is already in use<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

        return json_encode($datamess);

    }

    public function create_action_existing_ajax()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('customers', 'mobile', $mobile);
        if ($is_unique == true) {
            $amount = $this->request->getPost('amount');
            $transaction_type = $this->request->getPost('transaction_type');
            $name = $this->request->getPost('customer_name');


            $data['mobile'] = $this->request->getPost('mobile');
            $data['customer_name'] = $this->request->getPost('customer_name');
            $data['cus_type_id'] = $this->request->getPost('cus_type_id');
            $data['transaction_type'] = $this->request->getPost('transaction_type');
            $data['amount'] = $this->request->getPost('amount');

            $this->validation->setRules([
                'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|min_length[5]|max_length[12]'],
                'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
                'transaction_type' => ['label' => 'transaction_type', 'rules' => 'required'],
                'amount' => ['label' => 'Amount', 'rules' => 'required|alpha_numeric'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                if ($amount !== '0') {
                    DB()->transStart();

                    if ($transaction_type == '2') {
                        $cusdata = array(
                            'sch_id' => $shopId,
                            'customer_name' => $this->request->getPost('customer_name'),
                            'balance' => $amount,
                            'mobile' => $this->request->getPost('mobile'),
                            'cus_type_id' => $this->request->getPost('cus_type_id'),
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $this->customersModel->insert($cusdata);
                        $customerId = $this->customersModel->getInsertID();


                        //insert customer ledger table (start)
                        $cusLedgdata = array(
                            'sch_id' => $shopId,
                            'customer_id' => $customerId,
                            'particulars' => 'Customer last balance ',
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $cusLedTable = DB()->table('ledger');
                        $cusLedTable->insert($cusLedgdata);
                        //insert customer ledger table (end)


                        //update capital (start)
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        $newcap = $oldCap - $amount;

                        $capData = array(
                            'capital' => $newcap
                        );
                        $shopTable = DB()->table('shops');
                        $shopTable->where('sch_id', $shopId)->update($capData);

                        $capLedgdata = array(
                            'sch_id' => $shopId,
                            'particulars' => 'Existing customer (' . $name . ') is added with existing balance',
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $newcap,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledger_capitalTable = DB()->table('ledger_capital');
                        $ledger_capitalTable->insert($capLedgdata);
                        //update capital (end)

                        $datamess['htmlData'] = getAllListInOptionWithStatus($customerId, 'customer_id', 'customer_name', 'customers','customer_name');

                        $datamess['success'] = true;
                        $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    } else {
                        //capital
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        if (-$oldCap >= $amount) {

                            $cusdata2 = array(
                                'sch_id' => $shopId,
                                'customer_name' => $this->request->getPost('customer_name'),
                                'balance' => -$amount,
                                'mobile' => $this->request->getPost('mobile'),
                                'cus_type_id' => $this->request->getPost('cus_type_id'),
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $this->customersModel->insert($cusdata2);
                            $customerId = $this->customersModel->getInsertID();


                            //insert customer ledger table (start)
                            $cusLedgdata = array(
                                'sch_id' => $shopId,
                                'customer_id' => $customerId,
                                'particulars' => 'Customer last balance ',
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'rest_balance' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $cusLedTable = DB()->table('ledger');
                            $cusLedTable->insert($cusLedgdata);
                            //insert customer ledger table (end)


                            //update capital (start)
                            $newcap = $oldCap + $amount;

                            $capData = array(
                                'capital' => $newcap
                            );
                            $shopTable = DB()->table('shops');
                            $shopTable->where('sch_id', $shopId)->update($capData);

                            $capLedgdata = array(
                                'sch_id' => $shopId,
                                'particulars' => 'Existing customer (' . $name . ') is added with existing balance',
                                'trangaction_type' => 'Dr.',
                                'amount' => $amount,
                                'rest_balance' => $newcap,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledger_capitalTable = DB()->table('ledger_capital');
                            $ledger_capitalTable->insert($capLedgdata);
                            //update capital (end)
                            $datamess['htmlData'] = getAllListInOptionWithStatus($customerId, 'customer_id', 'customer_name', 'customers','customer_name');

                            $datamess['success'] = true;
                            $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                        } else {
                            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">Need to update capital for this transaction<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        }
                    }

                    DB()->transComplete();
                }else{
                    $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }
        } else {
            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">This phone number is already in use<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }

        return json_encode($datamess);

    }

    /**
     * @param int $customer_id
     * @return void
     */
    public function delete($customer_id){
        $table = DB()->table('customers');
        $table->where('customer_id',$customer_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Customers'));
    }


}