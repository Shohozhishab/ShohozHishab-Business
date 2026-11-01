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

                        // Existing transaction data insert
                        DB()->table('existing_transaction')->insert([
                            'sch_id' => $shopId,
                            'customer_id' => $customerId,
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        ]);
                        $existing_trans_id = DB()->insertID();

                        //insert customer ledger table (start)
                        $cusLedgdata = array(
                            'sch_id' => $shopId,
                            'customer_id' => $customerId,
                            'existing_trans_id' => $existing_trans_id,
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
                            'existing_trans_id' => $existing_trans_id,
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

                            // Existing transaction data insert
                            DB()->table('existing_transaction')->insert([
                                'sch_id' => $shopId,
                                'customer_id' => $customerId,
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            ]);
                            $existing_trans_id = DB()->insertID();

                            //insert customer ledger table (start)
                            $cusLedgdata = array(
                                'sch_id' => $shopId,
                                'customer_id' => $customerId,
                                'existing_trans_id' => $existing_trans_id,
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
                                'existing_trans_id' => $existing_trans_id,
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
        $data['balance_limit'] = (float) $this->request->getPost('balance_limit');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'mobile' => ['label' => 'Mobile', 'rules' => 'required|alpha_numeric|min_length[5]|max_length[12]'],
            'customer_name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'cus_type_id' => ['label' => 'cus_type_id', 'rules' => 'required'],
            'balance_limit' => ['label' => 'Balance Limit', 'rules' => 'required|numeric|greater_than_equal_to[0]'],
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
     * Update notification date and note
     */
    public function note_update()
    {
        $userId = $this->session->userId;

        $data['customer_id']         = $this->request->getPost('customer_id');
        $data['notification_date']   = $this->request->getPost('notification_date');
        $data['note']                = $this->request->getPost('note');
        $data['updatedBy']           = $userId;

        // empty date is stored as NULL, not as an invalid zero date
        if (empty($data['notification_date'])) {
            $data['notification_date'] = null;
        }

        $this->validation->setRules([
            'notification_date' => ['label' => 'Notification Date', 'rules' => 'permit_empty|valid_date'],
            'note'              => ['label' => 'Note', 'rules' => 'max_length[2000]'],
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

    public function csv_action()
    {
        // 1. Validate the uploaded file
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]', // 2MB max
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">'.$this->validator->getErrors().'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Customers/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Customers/create'));
        }

        // 2. Move the file to a temporary location
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        // 3. Process the CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Unable to open the CSV file.');
        }

        $db      = DB();
        $builder = $db->table('customers'); // ← change table name if needed

        $header   = null;
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $rowNum   = 0;
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;


        // Cache customer types for this shop (avoids N+1 queries)
        $typeCache = [];
        $types = $db->table('customer_type')
            ->where('sch_id', $shopId)
            ->get()
            ->getResultArray();
        foreach ($types as $t) {
            $typeCache[strtolower(trim($t['type_name']))] = $t['cus_type_id'];
        }

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;

            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }

            // First row = header
            if ($header === null) {
                $header = array_map('trim', array_map('strtolower', $row));
                continue;
            }

            // Map CSV columns to associative array
            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            // Clean values
            $customerName = trim($data['customer_name'] ?? '');
            $mobile       = trim($data['mobile'] ?? '');
            $typeName       = trim($data['type_name'] ?? '');

            // Must have at least customer_name OR mobile
            if (empty($customerName) && empty($mobile)) {
                $skipped++;
                continue;
            }

            $cusTypeId = 0;
            if ($typeName !== '') {
                $key = strtolower($typeName);
                if (isset($typeCache[$key])) {
                    $cusTypeId = $typeCache[$key];
                } else {
                    $db->table('customer_type')->insert([
                        'sch_id'    => $shopId,
                        'type_name' => $typeName,
                    ]);
                    $cusTypeId = $db->insertID();
                    $typeCache[$key] = $cusTypeId;
                }
            }

            // Prepare data to insert/update (add more fields as needed)
            $saveData = [
                'sch_id' => $shopId,
                'cus_type_id' => $cusTypeId,
                'customer_name' => $customerName ?: null,
                'mobile'        => $mobile ?: null,
                'createdBy'    => $userId,
                'createdDtm'    => date('Y-m-d H:i:s'),
            ];

            // Check if record already exists (by mobile first, then by name)
            $existing = null;

            if (!empty($mobile)) {
                $existing = $builder->where('mobile', $mobile)->get()->getRow();
            }

            if (!$existing) {
                $builder->insert($saveData);
                $inserted++;
            }else{
                $skipped++;
                continue;
            }
        }

        fclose($handle);

        // 4. Delete temporary file
        @unlink($filePath);

        // 5. Return result
        $message = "CSV processed successfully. Inserted: {$inserted}, Skipped: {$skipped}";
        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Customers/create'));
    }

    public function cusDataEdit()
    {
        $customerId = $this->request->getPost('customer_id');

        $query = DB()->table('existing_transaction')
            ->where('customer_id', $customerId)
            ->get()
            ->getRow();

        // Safety check
        if (!$query) {
            print '<div class="alert alert-danger">No existing transaction found for this customer.</div>';
            return;
        }

        $name = get_data_by_id('customer_name', 'customers', 'customer_id', $customerId);
        $action = base_url('Admin/Customers/cusEditAction');

        $view  = '<form action="'.$action.'" method="post">';
        $view .= '<div class="form-group">
                <label for="customer_name_ex">Customer Name</label>
                <input type="text" class="form-control" name="customer_name" id="customer_name_ex" 
                       placeholder="Customer Name" value="' . htmlspecialchars($name) . '" readonly />
                       <input type="hidden" name="customer_id" value="'.$customerId.'">
                <div class="error"></div>
              </div>';

        $view .= '<div class="form-group">
                <label for="transaction_type">Transaction Type</label>
                <select class="form-control input" name="trangaction_type" id="trangaction_type" required>
                    <option value="">Please Select</option>
                    <option value="1" ' . (($query->trangaction_type == "Cr.") ? "selected" : "") . '>খরচ (Cr.) / পাওনাদার</option>
                    <option value="2" ' . (($query->trangaction_type == "Dr.") ? "selected" : "") . '>জমা (Dr.) / দেনাদার</option>
                </select>
                <div class="error"></div>
              </div>';

        $view .= '<div class="form-group databank" id="chaque">
                    <label for="amount">Amount</label>
                    <input type="number" class="form-control input" name="amount" id="amount" 
                           placeholder="Amount" oninput="minusValueCheck(this.value,this)" 
                           value="' . htmlspecialchars($query->amount) . '" required />
                    <div class="error"></div>
                  </div>';
        $view .='<div class="form-group">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>';

        $view .= '</form>';

        print $view;
    }

    public function cusEditAction()
    {
        $shopId     = $this->session->shopId;
        $userId     = $this->session->userId;

        $customer_id      = $this->request->getPost('customer_id');
        $amount           = (float) $this->request->getPost('amount');
        $transaction_type = $this->request->getPost('trangaction_type'); // 1 or 2

        $data = [
            'trangaction_type' => $transaction_type,
            'amount'           => $amount,
        ];

        $this->validation->setRules([
            'trangaction_type' => ['label' => 'Transaction Type', 'rules' => 'required|in_list[1,2]'],
            'amount'           => ['label' => 'Amount', 'rules' => 'required|greater_than[0]'],
        ]);

        if ($this->validation->run($data) === false) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            );
            return redirect()->to(site_url('Admin/Customers'));
        }

        // Get old transaction
        $transaction = DB()->table('existing_transaction')
            ->where('sch_id', $shopId)
            ->where('customer_id', $customer_id)
            ->get()
            ->getRow();

        if (!$transaction) {
            $this->session->setFlashdata('message','<div class="alert alert-danger">Existing transaction not found.</div>');
            return redirect()->to(site_url('Admin/Customers'));
        }

        $oldAmount = (float) $transaction->amount;
        $oldType   = $transaction->trangaction_type; // "Dr." or "Cr."

        // New type as string
        $newType = ($transaction_type == '2') ? 'Dr.' : 'Cr.';

        // Current capital
        $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);

        // ========== Capital check (only needed when new type is Cr. / পাওনাদার) ==========
        if ($newType === 'Cr.') {
            // After reversing old effect, can we support the new Cr. amount?
            $tempCap = $oldCap;

            // Reverse old effect
            if ($oldType === 'Dr.') {
                $tempCap += $oldAmount;   // was reduced, so add back
            } else {
                $tempCap -= $oldAmount;   // was increased, so subtract
            }

            // Apply new Cr. effect
            $tempCap += $amount;

            if ($tempCap < 0 && abs($tempCap) < $amount) {
                // still not enough capital
                $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">Need to update capital for this transaction<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to(site_url('Admin/Customers'));
            }
        }

        // ========== Start transaction ==========
        DB()->transStart();

        // ---------- 1. Customer balance ----------
        $customerOldBalance = (float) get_data_by_id('balance', 'customers', 'customer_id', $customer_id);

        // Reverse old effect
        if ($oldType === 'Dr.') {
            $customerOldBalance -= $oldAmount;   // was +amount, reverse
        } else {
            $customerOldBalance += $oldAmount;   // was -amount, reverse
        }

        // Apply new effect
        if ($newType === 'Dr.') {
            $newBalance = $customerOldBalance + $amount;
        } else {
            $newBalance = $customerOldBalance - $amount;
        }

        DB()->table('customers')
            ->where('customer_id', $customer_id)
            ->update(['balance' => $newBalance]);

        // ---------- 2. Customer ledger ----------
        $ledger = DB()->table('ledger')
            ->where('customer_id', $customer_id)
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledger) {
            $newRestBalance = $newBalance; // rest_balance should match current customer balance

            DB()->table('ledger')
                ->where('ledg_id', $ledger->ledg_id)
                ->update([
                    'trangaction_type' => $newType,
                    'amount'           => $amount,
                    'rest_balance'     => abs($newRestBalance), // usually stored positive
                    'particulars'      => 'Customer last balance',
                ]);
        }

        // ---------- 3. Capital ----------
        // Reverse old effect on capital
        if ($oldType === 'Dr.') {
            $oldCap += $oldAmount;   // was capital - amount
        } else {
            $oldCap -= $oldAmount;   // was capital + amount
        }

        // Apply new effect
        if ($newType === 'Dr.') {
            $newCap = $oldCap - $amount;
        } else {
            $newCap = $oldCap + $amount;
        }

        DB()->table('shops')
            ->where('sch_id', $shopId)
            ->update(['capital' => $newCap]);

        // ---------- 4. Capital ledger (UPDATE, do not insert) ----------
        $ledgerCapital = DB()->table('ledger_capital')
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledgerCapital) {
            DB()->table('ledger_capital')
                ->where('capital_id', $ledgerCapital->capital_id) // adjust primary key name if different
                ->update([
                    'trangaction_type' => ($newType === 'Dr.') ? 'Cr.' : 'Dr.', // opposite of customer
                    'amount'           => $amount,
                    'rest_balance'     => $newCap,
                    'particulars'      => 'Existing customer (' . get_data_by_id('customer_name', 'customers', 'customer_id', $customer_id) . ') is added with existing balance',
                ]);
        }

        // ---------- 5. Update existing_transaction ----------
        DB()->table('existing_transaction')
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->update([
                'amount'           => $amount,
                'trangaction_type' => $newType,
            ]);

        DB()->transComplete();

        if (DB()->transStatus() === false) {
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger">Something went wrong. Please try again.</div>'
            );
        } else {
            $this->session->setFlashdata('message',
                '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            );
        }

        return redirect()->to(site_url('Admin/Customers'));
    }

    public function csv_existing_action()
    {
        // 1. Validate file
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validator->listErrors() . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            return redirect()->to(site_url('Admin/Customers/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message','<div class="alert alert-danger">Invalid file upload.</div>');
            return redirect()->to(site_url('Admin/Customers/create'));
        }

        // 2. Move file
        $newName  = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        // 3. Open CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            @unlink($filePath);
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Unable to open CSV file.</div>');
            return redirect()->to(site_url('Admin/Customers/create'));
        }

        $db      = DB();
        $builder = $db->table('customers');
        $shopId  = $this->session->shopId;
        $userId  = $this->session->userId;
        $now     = date('Y-m-d H:i:s');

        $header   = null;
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        // Cache customer types
        $typeCache = [];
        $types = $db->table('customer_type')->where('sch_id', $shopId)->get()->getResultArray();
        foreach ($types as $t) {
            $typeCache[strtolower(trim($t['type_name']))] = $t['cus_type_id'];
        }

        while (($row = fgetcsv($handle, 0, ',')) !== false) {

            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $row);

                $required = ['customer_name', 'mobile', 'type_name', 'type', 'amount'];
                foreach ($required as $col) {
                    if (!in_array($col, $header, true)) {
                        fclose($handle);
                        @unlink($filePath);
                        $this->session->setFlashdata('message','<div class="alert alert-danger">CSV must contain: ' . implode(', ', $required) . '</div>');
                        return redirect()->to(site_url('Admin/Customers/create'));
                    }
                }
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            $customerName     = trim($data['customer_name'] ?? '');
            $mobile           = trim($data['mobile'] ?? '');
            $typeName         = trim($data['type_name'] ?? '');
            $transaction_type = trim($data['type'] ?? '');
            $amount           = trim($data['amount'] ?? '');

            if ($customerName === '' && $mobile === '') {
                $skipped++;
                continue;
            }

            if (!is_numeric($amount) || (float)$amount <= 0) {
                $errors[] = "Invalid amount for {$customerName} / {$mobile}";
                $skipped++;
                continue;
            }
            $amount = (float)$amount;

            $isDebit = in_array(strtoupper(rtrim($transaction_type, '.')), ['2', 'DR'], true);

            // Customer Type
            $cusTypeId = 0;
            if ($typeName !== '') {
                $key = strtolower($typeName);
                if (isset($typeCache[$key])) {
                    $cusTypeId = $typeCache[$key];
                } else {
                    $db->table('customer_type')->insert([
                        'sch_id'    => $shopId,
                        'type_name' => $typeName,
                    ]);
                    $cusTypeId = $db->insertID();
                    $typeCache[$key] = $cusTypeId;
                }
            }

            // Duplicate check
            $existing = null;
            if ($mobile !== '') {
                $existing = $builder->where('mobile', $mobile)
                    ->where('sch_id', $shopId)
                    ->get()
                    ->getRow();
            }
            if ($existing) {
                $errors[] = "Mobile already exists: {$mobile}";
                $skipped++;
                continue;
            }

            $db->transStart();

            try {
                if ($isDebit) {
                    // Debit
                    $builder->insert([
                        'sch_id'        => $shopId,
                        'customer_name' => $customerName,
                        'mobile'        => $mobile,
                        'cus_type_id'   => $cusTypeId,
                        'balance'       => $amount,
                        'createdBy'     => $userId,
                        'createdDtm'    => $now,
                    ]);
                    $customerId = $db->insertID();

                    $db->table('existing_transaction')->insert([
                        'sch_id'           => $shopId,
                        'customer_id'      => $customerId,
                        'trangaction_type' => 'Dr.',
                        'amount'           => $amount,
                        'createdBy'        => $userId,
                        'createdDtm'       => $now,
                    ]);
                    $existing_trans_id = $db->insertID();

                    $db->table('ledger')->insert([
                        'sch_id'            => $shopId,
                        'customer_id'       => $customerId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => 'Customer last balance',
                        'trangaction_type'  => 'Dr.',
                        'amount'            => $amount,
                        'rest_balance'      => $amount,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                    $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                    $newCap = $oldCap - $amount;
                    $db->table('shops')->where('sch_id', $shopId)->update(['capital' => $newCap]);

                    $db->table('ledger_capital')->insert([
                        'sch_id'            => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => "Existing customer ({$customerName}) is added with existing balance",
                        'trangaction_type'  => 'Cr.',
                        'amount'            => $amount,
                        'rest_balance'      => $newCap,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                } else {
                    // Credit
                    $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                    if (-$oldCap < $amount) {
                        throw new \Exception("Need to update capital ({$customerName})");
                    }

                    $builder->insert([
                        'sch_id'        => $shopId,
                        'customer_name' => $customerName,
                        'mobile'        => $mobile,
                        'cus_type_id'   => $cusTypeId,
                        'balance'       => -$amount,
                        'createdBy'     => $userId,
                        'createdDtm'    => $now,
                    ]);
                    $customerId = $db->insertID();

                    $db->table('existing_transaction')->insert([
                        'sch_id'           => $shopId,
                        'customer_id'      => $customerId,
                        'trangaction_type' => 'Cr.',
                        'amount'           => $amount,
                        'createdBy'        => $userId,
                        'createdDtm'       => $now,
                    ]);
                    $existing_trans_id = $db->insertID();

                    $db->table('ledger')->insert([
                        'sch_id'            => $shopId,
                        'customer_id'       => $customerId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => 'Customer last balance',
                        'trangaction_type'  => 'Cr.',
                        'amount'            => $amount,
                        'rest_balance'      => $amount,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                    $newCap = $oldCap + $amount;
                    $db->table('shops')->where('sch_id', $shopId)->update(['capital' => $newCap]);

                    $db->table('ledger_capital')->insert([
                        'sch_id'            => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => "Existing customer ({$customerName}) is added with existing balance",
                        'trangaction_type'  => 'Dr.',
                        'amount'            => $amount,
                        'rest_balance'      => $newCap,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);
                }

                $db->transComplete();
                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }

                $inserted++;

            } catch (\Throwable $e) {
                $db->transRollback();
                $errors[] = $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);
        @unlink($filePath);

        $msg = "CSV processed. Inserted: {$inserted}, Skipped: {$skipped}";
        if (!empty($errors)) {
            $msg .= '<br><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
            $alert = 'alert-warning';
        } else {
            $alert = 'alert-success';
        }

        $this->session->setFlashdata('message','<div class="alert ' . $alert . ' alert-dismissible">' . $msg . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        return redirect()->to(site_url('Admin/Customers/create'));
    }





}