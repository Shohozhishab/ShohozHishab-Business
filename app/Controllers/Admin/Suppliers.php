<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\SuppliersModel;
use CodeIgniter\HTTP\RedirectResponse;


class Suppliers extends BaseController
{


    protected $suppliersModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Suppliers';

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides suppliers view
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
            $data['supplier'] = $this->suppliersModel->where('sch_id', $shopId)->orderBy('name', 'ASC')->findAll();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Suppliers/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides suppliers create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Suppliers/create_action');
            $data['action2'] = base_url('Admin/Suppliers/existing_create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Suppliers/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store suppliers
     * @return void
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');

        $data['phone'] = $this->request->getPost('phone');
        $data['name'] = $this->request->getPost('name');
        $data['address'] = $this->request->getPost('address');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'address' => ['label' => 'address', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique('suppliers', 'phone', $data['phone']);
            if ($phoneUnique == true) {
                if ($this->suppliersModel->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Phone number already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }


    }

    /**
     * @description This method store existing suppliers
     * @return void
     * @throws \ReflectionException
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('suppliers', 'phone', $mobile);
        if ($is_unique == true) {
            $amount = $this->request->getPost('amount');
            $transaction_type = $this->request->getPost('transaction_type');
            $name = $this->request->getPost('name');


            $data['name'] = $this->request->getPost('name');
            $data['phone'] = $this->request->getPost('phone');
            $data['address'] = $this->request->getPost('address');
            $data['transaction_type'] = $this->request->getPost('transaction_type');
            $data['amount'] = $this->request->getPost('amount');

            $this->validation->setRules([
                'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
                'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'address' => ['label' => 'address', 'rules' => 'required'],
                'transaction_type' => ['label' => 'transaction_type', 'rules' => 'required'],
                'amount' => ['label' => 'Amount', 'rules' => 'required'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                if ($amount !== '0') {
                    DB()->transStart();

                    if ($transaction_type == '2') {
                        $data = array(
                            'sch_id' => $shopId,
                            'name' => $this->request->getPost('name'),
                            'balance' => $amount,
                            'address' => $this->request->getPost('address'),
                            'phone' => $this->request->getPost('name'),
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $this->suppliersModel->insert($data);
                        $supplierId = $this->suppliersModel->getInsertID();

                        // Existing transaction data insert
                        DB()->table('existing_transaction')->insert([
                            'sch_id' => $shopId,
                            'supplier_id' => $supplierId,
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        ]);
                        $existing_trans_id = DB()->insertID();

                        //insert Supplier ledger table (start)
                        $supLedgdata = array(
                            'sch_id' => $shopId,
                            'existing_trans_id' => $existing_trans_id,
                            'supplier_id' => $supplierId,
                            'particulars' => 'Supplier last balance ',
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledSup = DB()->table('ledger_suppliers');
                        $ledSup->insert($supLedgdata);
                        //insert Supplier ledger table (end)


                        //update capital (start)
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        $newcap = $oldCap - $amount;

                        $capData = array(
                            'capital' => $newcap
                        );
                        $shopTab = DB()->table('shops');
                        $shopTab->where('sch_id', $shopId)->update($capData);

                        $capLedgdata = array(
                            'sch_id' => $shopId,
                            'existing_trans_id' => $existing_trans_id,
                            'particulars' => 'Existing supplier (' . $name . ') is added with existing balance',
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $newcap,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledCapTab = DB()->table('ledger_capital');
                        $ledCapTab->insert($capLedgdata);
                        //update capital (end)

                        print '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    } else {
                        //capital
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        if (-$oldCap >= $amount) {

                            $data = array(
                                'sch_id' => $shopId,
                                'name' => $this->request->getPost('name'),
                                'balance' => -$amount,
                                'address' => $this->request->getPost('address'),
                                'phone' => $this->request->getPost('phone'),
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $this->suppliersModel->insert($data);
                            $supplierId = $this->suppliersModel->getInsertID();

                            // Existing transaction data insert
                            DB()->table('existing_transaction')->insert([
                                'sch_id' => $shopId,
                                'customer_id' => $supplierId,
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            ]);
                            $existing_trans_id = DB()->insertID();

                            //insert Supplier ledger table (start)
                            $supLedgdata = array(
                                'sch_id' => $shopId,
                                'existing_trans_id' => $existing_trans_id,
                                'supplier_id' => $supplierId,
                                'particulars' => 'Supplier last balance ',
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'rest_balance' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledSupTable = DB()->table('ledger_suppliers');
                            $ledSupTable->insert($supLedgdata);
                            //insert Supplier ledger table (end)


                            //update capital (start)
                            $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                            $newcap = $oldCap + $amount;

                            $capData = array(
                                'capital' => $newcap
                            );
                            $shopTable = DB()->table('shops');
                            $shopTable->where('sch_id', $shopId)->update($capData);

                            $capLedgdata = array(
                                'sch_id' => $shopId,
                                'existing_trans_id' => $existing_trans_id,
                                'particulars' => 'Existing supplier (' . $name . ') is added with existing balance',
                                'trangaction_type' => 'Dr.',
                                'amount' => $amount,
                                'rest_balance' => $newcap,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledCapTable = DB()->table('ledger_capital');
                            $ledCapTable->insert($capLedgdata);
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
     * @description This method provides suppliers update view
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
            $shopId = $this->session->shopId;
            $data['action'] = base_url('Admin/Suppliers/update_action');
            $data['supplier'] = $this->suppliersModel->where('supplier_id', $id)->where('sch_id', $shopId)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Suppliers/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update suppliers
     * @return void
     * @throws \ReflectionException
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $data['supplier_id'] = $this->request->getPost('supplier_id');
        $data['name'] = $this->request->getPost('name');
        $data['phone'] = $this->request->getPost('phone');
        $data['address'] = $this->request->getPost('address');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'address' => ['label' => 'Address', 'rules' => 'required|only_numeric_not_allow'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique_update('suppliers', 'phone', $data['phone'], 'supplier_id', $data['supplier_id']);
            if ($phoneUnique == true) {
                if ($this->suppliersModel->update($data['supplier_id'], $data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Phone number already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method provides suppliers transaction view
     * @param int $id
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

            $table = DB()->table('ledger_suppliers');
            $data['transaction'] = $table->where('supplier_id', $id)->orderBy('ledg_sup_id', 'DESC')->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['mod_access'] == 1) {
                echo view('Admin/Suppliers/transaction', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
    /**
     * @description This method store suppliers
     * @return void
     * @throws \ReflectionException
     */
    public function create_action_ajax()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['phone'] = $this->request->getPost('phone');
        $data['name'] = $this->request->getPost('name');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique('suppliers', 'phone', $data['phone']);
            if ($phoneUnique == true) {
                if ($this->suppliersModel->insert($data)) {
                    $insertID = $this->suppliersModel->insertID;
                    $datamess['htmlData'] = getAllListInOptionWithStatus($insertID,'supplier_id','name','suppliers','name');;

                    $datamess['success'] = true;

                    $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                } else {
                    $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">Phone number already in use  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }

        return json_encode($datamess);
    }
    public function create_existing_action_ajax(){
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('suppliers', 'phone', $mobile);
        if ($is_unique == true) {
            $amount = $this->request->getPost('amount');
            $transaction_type = $this->request->getPost('transaction_type');
            $name = $this->request->getPost('name');


            $data['name'] = $this->request->getPost('name');
            $data['phone'] = $this->request->getPost('phone');
            $data['address'] = $this->request->getPost('address');
            $data['transaction_type'] = $this->request->getPost('transaction_type');
            $data['amount'] = $this->request->getPost('amount');

            $this->validation->setRules([
                'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
                'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
                'address' => ['label' => 'address', 'rules' => 'required'],
                'transaction_type' => ['label' => 'transaction_type', 'rules' => 'required'],
                'amount' => ['label' => 'Amount', 'rules' => 'required'],
            ]);
            if ($this->validation->run($data) == FALSE) {
                $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                if ($amount !== '0') {
                    DB()->transStart();

                    if ($transaction_type == '2') {
                        $data = array(
                            'sch_id' => $shopId,
                            'name' => $this->request->getPost('name'),
                            'balance' => $amount,
                            'address' => $this->request->getPost('address'),
                            'phone' => $this->request->getPost('name'),
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $this->suppliersModel->insert($data);
                        $supplierId = $this->suppliersModel->getInsertID();


                        //insert Supplier ledger table (start)
                        $supLedgdata = array(
                            'sch_id' => $shopId,
                            'supplier_id' => $supplierId,
                            'particulars' => 'Supplier last balance ',
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledSup = DB()->table('ledger_suppliers');
                        $ledSup->insert($supLedgdata);
                        //insert Supplier ledger table (end)


                        //update capital (start)
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        $newcap = $oldCap - $amount;

                        $capData = array(
                            'capital' => $newcap
                        );
                        $shopTab = DB()->table('shops');
                        $shopTab->where('sch_id', $shopId)->update($capData);

                        $capLedgdata = array(
                            'sch_id' => $shopId,
                            'particulars' => 'Existing supplier (' . $name . ') is added with existing balance',
                            'trangaction_type' => 'Cr.',
                            'amount' => $amount,
                            'rest_balance' => $newcap,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledCapTab = DB()->table('ledger_capital');
                        $ledCapTab->insert($capLedgdata);
                        //update capital (end)

                        $datamess['htmlData'] = getAllListInOptionWithStatus($supplierId,'supplier_id','name','suppliers','name');;
                        $datamess['success'] = true;
                        $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    } else {
                        //capital
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        if (-$oldCap >= $amount) {

                            $data = array(
                                'sch_id' => $shopId,
                                'name' => $this->request->getPost('name'),
                                'balance' => -$amount,
                                'address' => $this->request->getPost('address'),
                                'phone' => $this->request->getPost('phone'),
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $this->suppliersModel->insert($data);
                            $supplierId = $this->suppliersModel->getInsertID();


                            //insert Supplier ledger table (start)
                            $supLedgdata = array(
                                'sch_id' => $shopId,
                                'supplier_id' => $supplierId,
                                'particulars' => 'Supplier last balance ',
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'rest_balance' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledSupTable = DB()->table('ledger_suppliers');
                            $ledSupTable->insert($supLedgdata);
                            //insert Supplier ledger table (end)


                            //update capital (start)
                            $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                            $newcap = $oldCap + $amount;

                            $capData = array(
                                'capital' => $newcap
                            );
                            $shopTable = DB()->table('shops');
                            $shopTable->where('sch_id', $shopId)->update($capData);

                            $capLedgdata = array(
                                'sch_id' => $shopId,
                                'particulars' => 'Existing supplier (' . $name . ') is added with existing balance',
                                'trangaction_type' => 'Dr.',
                                'amount' => $amount,
                                'rest_balance' => $newcap,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledCapTable = DB()->table('ledger_capital');
                            $ledCapTable->insert($capLedgdata);
                            //update capital (end)
                            $datamess['htmlData'] = getAllListInOptionWithStatus($supplierId,'supplier_id','name','suppliers','name');;
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
     * @param int $supplier_id
     * @return void
     */
    public function delete($supplier_id){
        $table = DB()->table('suppliers');
        $table->where('supplier_id',$supplier_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Suppliers'));
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
            return redirect()->to(site_url('Admin/Suppliers/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Suppliers/create'));
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
        $builder = $db->table('suppliers'); // ← change table name if needed

        $header   = null;
        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;
        $rowNum   = 0;
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;
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
            $name = trim($data['name'] ?? '');
            $mobile       = trim($data['phone'] ?? '');

            // Must have at least customer_name OR mobile
            if (empty($name) && empty($mobile)) {
                $skipped++;
                continue;
            }

            // Prepare data to insert/update (add more fields as needed)
            $saveData = [
                'sch_id' => $shopId,
                'name' => $name ?: null,
                'phone'        => $mobile ?: null,
                'createdBy'    => $userId,
                'createdDtm'    => date('Y-m-d H:i:s'),
            ];

            // Check if record already exists (by mobile first, then by name)
            $existing = null;

            if (!empty($mobile)) {
                $existing = $builder->where('phone', $mobile)->get()->getRow();
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
        return redirect()->to(site_url('Admin/Suppliers/create'));
    }

    public function supDataEdit()
    {
        $supplierId = $this->request->getPost('supplier_id');

        $query = DB()->table('existing_transaction')
            ->where('supplier_id', $supplierId)
            ->get()
            ->getRow();

        if (!$query) {
            print '<div class="alert alert-danger">No existing transaction found for this supplier.</div>';
            return;
        }

        $name   = get_data_by_id('name', 'suppliers', 'supplier_id', $supplierId);
        $action = base_url('Admin/Suppliers/supEditAction');   // adjust controller path if needed

        $view  = '<form action="'.$action.'" method="post">';
        $view .= '<div class="form-group">
                <label for="supplier_name_ex">Supplier Name</label>
                <input type="text" class="form-control" name="name" id="supplier_name_ex" 
                       value="' . htmlspecialchars($name) . '" readonly />
                <input type="hidden" name="supplier_id" value="'.$supplierId.'">
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

        $view .= '<div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control input" name="amount" id="amount" 
                       placeholder="Amount" oninput="minusValueCheck(this.value,this)" 
                       value="' . htmlspecialchars($query->amount) . '" required />
                <div class="error"></div>
              </div>';

        $view .= '<div class="form-group">
                <button type="submit" class="btn btn-success">Submit</button>
              </div>';

        $view .= '</form>';

        print $view;
    }

    public function supEditAction()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $supplier_id      = $this->request->getPost('supplier_id');
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
            $this->session->setFlashdata('message',
                '<div class="alert alert-danger alert-dismissible" role="alert">' .
                $this->validation->listErrors() .
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
            );
            return redirect()->to(site_url('Admin/Suppliers'));
        }

        // Get old transaction
        $transaction = DB()->table('existing_transaction')
            ->where('sch_id', $shopId)
            ->where('supplier_id', $supplier_id)
            ->get()
            ->getRow();

        if (!$transaction) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Existing transaction not found.</div>');
            return redirect()->to(site_url('Admin/Suppliers'));
        }

        $oldAmount = (float) $transaction->amount;
        $oldType   = $transaction->trangaction_type; // "Dr." or "Cr."
        $newType   = ($transaction_type == '2') ? 'Dr.' : 'Cr.';

        // Current capital
        $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);

        // ========== Capital check (only when new type is Cr.) ==========
        if ($newType === 'Cr.') {
            $tempCap = $oldCap;

            // Reverse old effect
            if ($oldType === 'Dr.') {
                $tempCap += $oldAmount;
            } else {
                $tempCap -= $oldAmount;
            }

            // Apply new Cr. effect
            $tempCap += $amount;


            if ( -$tempCap < 0) {
                $this->session->setFlashdata('message',
                    '<div class="alert alert-danger alert-dismissible" role="alert">Need to update capital for this transaction<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                );
                return redirect()->to(site_url('Admin/Suppliers'));
            }
        }

        // ========== Start DB transaction ==========
        DB()->transStart();

        // ---------- 1. Supplier balance ----------
        $supplierOldBalance = (float) get_data_by_id('balance', 'suppliers', 'supplier_id', $supplier_id);

        // Reverse old effect
        if ($oldType === 'Dr.') {
            $supplierOldBalance -= $oldAmount;
        } else {
            $supplierOldBalance += $oldAmount;
        }

        // Apply new effect
        if ($newType === 'Dr.') {
            $newBalance = $supplierOldBalance + $amount;
        } else {
            $newBalance = $supplierOldBalance - $amount;
        }

        DB()->table('suppliers')
            ->where('supplier_id', $supplier_id)
            ->update(['balance' => $newBalance]);

        // ---------- 2. Supplier ledger ----------
        $ledger = DB()->table('ledger_suppliers')
            ->where('supplier_id', $supplier_id)
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledger) {
            DB()->table('ledger_suppliers')
                ->where('ledg_sup_id', $ledger->ledg_sup_id)   // change if your PK is different
                ->update([
                    'trangaction_type' => $newType,
                    'amount'           => $amount,
                    'rest_balance'     => abs($newBalance),
                    'particulars'      => 'Supplier last balance',
                ]);
        }

        // ---------- 3. Capital ----------
        // Reverse old effect
        if ($oldType === 'Dr.') {
            $oldCap += $oldAmount;
        } else {
            $oldCap -= $oldAmount;
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

        // ---------- 4. Capital ledger (UPDATE) ----------
        $ledgerCapital = DB()->table('ledger_capital')
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledgerCapital) {
            DB()->table('ledger_capital')
                ->where('capital_id', $ledgerCapital->capital_id) // change if your PK is different
                ->update([
                    'trangaction_type' => ($newType === 'Dr.') ? 'Cr.' : 'Dr.',
                    'amount'           => $amount,
                    'rest_balance'     => $newCap,
                    'particulars'      => 'Existing supplier (' . get_data_by_id('name', 'suppliers', 'supplier_id', $supplier_id) . ') is added with existing balance',
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

        return redirect()->to(site_url('Admin/Suppliers'));
    }

    public function csv_existing_action()
    {
        // 1. Validate
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">'. $this->validator->listErrors(). '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Suppliers/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Suppliers/create'));
        }

        // 2. Move file
        $newName  = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        // 3. Process CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            @unlink($filePath);
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Unable to open the CSV file.</div>');
            return redirect()->to(site_url('Admin/Suppliers/create'));
        }

        $db       = DB();
        $builder  = $db->table('suppliers');
        $shopId   = $this->session->shopId;
        $userId   = $this->session->userId;
        $now      = date('Y-m-d H:i:s');

        $header   = null;
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {

            // Skip completely empty rows
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            // Header row
            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $row);
                // Optional: enforce required columns
                $required = ['name', 'phone', 'type', 'amount'];
                foreach ($required as $col) {
                    if (!in_array($col, $header, true)) {
                        fclose($handle);
                        @unlink($filePath);
                        $this->session->setFlashdata('message','<div class="alert alert-danger">CSV must contain columns: ' . implode(', ', $required) . '</div>');
                        return redirect()->to(site_url('Admin/Suppliers/create'));
                    }
                }
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            $name   = trim($data['name']   ?? '');
            $mobile = trim($data['phone']  ?? '');
            $type   = trim($data['type']   ?? '');
            $address = trim($data['address'] ?? '');
            $amount = trim($data['amount'] ?? '');


            // Must have at least one identifying field
            if ($name === '' && $mobile === '') {
                $skipped++;
                continue;
            }

            // Amount must be numeric
            if (!is_numeric($amount) || (float)$amount < 0) {
                $errors[] = "Invalid amount for {$name}/{$mobile}";
                $skipped++;
                continue;
            }
            $amount = (float)$amount;

            // Normalize type
            $type = strtoupper(rtrim($type, '.')); // "Dr." / "Cr." → "DR" / "CR"
            if (!in_array($type, ['DR', 'CR'], true)) {
                $errors[] = "Invalid type '{$data['type']}' for {$name}";
                $skipped++;
                continue;
            }

            // Check duplicate by phone (preferred) or name
            $existing = null;
            if ($mobile !== '') {
                $existing = $builder->where('phone', $mobile)
                    ->where('sch_id', $shopId)
                    ->get()
                    ->getRow();
            }
            if (!$existing && $name !== '') {
                $existing = $builder->where('name', $name)
                    ->where('sch_id', $shopId)
                    ->get()
                    ->getRow();
            }

            if ($existing) {
                $skipped++;
                continue; // or implement update logic if you want
            }
            if ($type == 'DR'){
                $balance = $amount;
            }else{
                $balance = -$amount;
            }
            // ---------- Transaction per row ----------
            $db->transStart();

            try {
                // Insert supplier
                $saveData = [
                    'sch_id'     => $shopId,
                    'name'       => $name ?: null,
                    'phone'      => $mobile ?: null,
                    'address'      => $address ?: null,
                    'balance'    => $balance,
                    'createdBy'  => $userId,
                    'createdDtm' => $now,
                ];
                $builder->insert($saveData);
                $supplierId = $db->insertID();

                // Existing transaction
                $db->table('existing_transaction')->insert([
                    'sch_id'           => $shopId,
                    'supplier_id'      => $supplierId,
                    'trangaction_type' => $type === 'DR' ? 'Dr.' : 'Cr.',
                    'amount'           => $amount,
                    'createdBy'        => $userId,
                    'createdDtm'       => $now,
                ]);
                $existing_trans_id = $db->insertID();

                // Supplier ledger
                $db->table('ledger_suppliers')->insert([
                    'sch_id'           => $shopId,
                    'existing_trans_id'=> $existing_trans_id,
                    'supplier_id'      => $supplierId,
                    'particulars'      => 'Supplier last balance',
                    'trangaction_type' => $type === 'DR' ? 'Dr.' : 'Cr.',
                    'amount'           => $amount,
                    'rest_balance'     => $amount,
                    'createdBy'        => $userId,
                    'createdDtm'       => $now,
                ]);

                // Capital update
                $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);

                if ($type === 'DR') {
                    // Debit supplier → capital decreases
                    $newCap = $oldCap - $amount;
                    $capType = 'Cr.';
                } else {
                    // Credit supplier → capital increases
                    // Optional safety check (adjust logic to your business rule)
                    if ($oldCap >= $amount) { throw new \Exception('Insufficient capital'); }
                    $newCap = $oldCap + $amount;
                    $capType = 'Dr.';
                }

                $db->table('shops')
                    ->where('sch_id', $shopId)
                    ->update(['capital' => $newCap]);

                $db->table('ledger_capital')->insert([
                    'sch_id'           => $shopId,
                    'existing_trans_id'=> $existing_trans_id,
                    'particulars'      => "Existing supplier ({$name}) added with existing balance",
                    'trangaction_type' => $capType,
                    'amount'           => $amount,
                    'rest_balance'     => $newCap,
                    'createdBy'        => $userId,
                    'createdDtm'       => $now,
                ]);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }

                $inserted++;

            } catch (\Throwable $e) {
                $db->transRollback();
                $errors[] = "Failed for {$name}/{$mobile}: " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);
        @unlink($filePath);

        // 5. Result message
        $msg = "CSV processed. Inserted: {$inserted}, Skipped: {$skipped}";
        if (!empty($errors)) {
            $msg .= '<br><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
            $alertClass = 'alert-warning';
        } else {
            $alertClass = 'alert-success';
        }

        $this->session->setFlashdata('message','<div class="alert ' . $alertClass . ' alert-dismissible" role="alert">'. $msg . '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Suppliers/create'));
    }

}