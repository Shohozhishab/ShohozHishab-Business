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




}