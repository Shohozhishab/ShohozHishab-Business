<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Loan_providerModel;
use CodeIgniter\HTTP\RedirectResponse;


class Loan_provider extends BaseController
{


    protected $loan_providerModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Loan_provider';

    public function __construct()
    {
        $this->loan_providerModel = new Loan_providerModel();
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
            $data['loan_provider'] = $this->loan_providerModel->where('sch_id', $shopId)->orderBy('name', 'ASC')->findAll();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Loan_provider/list', $data);
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

            $data['action'] = base_url('Admin/Loan_provider/create_action');
            $data['action2'] = base_url('Admin/Loan_provider/existing_create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Loan_provider/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store loan provider
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['phone'] = $this->request->getPost('phone');
        $data['name'] = $this->request->getPost('name');
        $data['address'] = $this->request->getPost('address');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
            'address' => ['label' => 'address', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique('loan_provider', 'phone', $data['phone']);
            if ($phoneUnique == true) {
                if ($this->loan_providerModel->insert($data)) {
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
     * @description This method store existing loan provider
     * @return void
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mobile = $this->request->getPost('mobile');
        $is_unique = is_unique('loan_provider', 'phone', $mobile);

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
                'phone' => ['label' => 'phone', 'rules' => 'required|alpha_numeric_space|min_length[5]|max_length[12]'],
                'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
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
                            'phone' => $this->request->getPost('phone'),
                            'address' => $this->request->getPost('address'),
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );

                        $this->loan_providerModel->insert($data);
                        $loanproviderId = $this->loan_providerModel->getInsertID();


                        //insert loanprovider ledger table (start)
                        $lonLedgdata = array(
                            'sch_id' => $shopId,
                            'loan_pro_id' => $loanproviderId,
                            'particulars' => 'Account Head last balance ',
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'rest_balance' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        );
                        $ledLoneTable = DB()->table('ledger_loan');
                        $ledLoneTable->insert($lonLedgdata);
                        //insert loanprovider ledger table (end)


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
                            'particulars' => 'Existing Account Head (' . $name . ') is added with existing balance',
                            'trangaction_type' => 'Cr.',
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
                        //capital
                        $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                        if (-$oldCap >= $amount) {

                            $data = array(
                                'sch_id' => $shopId,
                                'name' => $this->request->getPost('name'),
                                'balance' => -$amount,
                                'phone' => $this->request->getPost('phone'),
                                'address' => $this->request->getPost('address'),
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );

                            $this->loan_providerModel->insert($data);
                            $loanproviderId = $this->loan_providerModel->getInsertID();


                            //insert loan provider ledger table (start)
                            $lonLedgdata = array(
                                'sch_id' => $shopId,
                                'loan_pro_id' => $loanproviderId,
                                'particulars' => 'Account head last balance ',
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'rest_balance' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            );
                            $ledLoneTable = DB()->table('ledger_loan');
                            $ledLoneTable->insert($lonLedgdata);
                            //insert loan provider ledger table (end)


                            //update capital (start)
                            $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                            $newcap = $oldCap + $amount;

                            $capData = array(
                                'capital' => $newcap
                            );
                            $shopTab = DB()->table('shops');
                            $shopTab->where('sch_id', $shopId)->update($capData);

                            $capLedgdata = array(
                                'sch_id' => $shopId,
                                'particulars' => 'Existing Account head (' . $name . ') is added with existing balance',
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
     * @description This method provides update view
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
            $data['action'] = base_url('Admin/Loan_provider/update_action');
            $data['loan_provider'] = $this->loan_providerModel->where('loan_pro_id', $id)->where('sch_id', $shopId)->first();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['update'] == 1) {
                echo view('Admin/Loan_provider/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update loan provider
     * @return void
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $data['loan_pro_id'] = $this->request->getPost('loan_pro_id');
        $data['name'] = $this->request->getPost('name');
        $data['phone'] = $this->request->getPost('phone');
        $data['address'] = $this->request->getPost('address');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space|min_length[5]|max_length[12]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
            'address' => ['label' => 'Address', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique_update('loan_provider', 'phone', $data['phone'], 'loan_pro_id', $data['loan_pro_id']);
            if ($phoneUnique == true) {
                if ($this->loan_providerModel->update($data['loan_pro_id'], $data)) {
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
     * @description This method provides transaction view
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
     * @description This method store loan provider
     * @return void
     */
    public function create_action_ajax()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['phone'] = $this->request->getPost('phoneAcc');
        $data['name'] = $this->request->getPost('nameAcc');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'phone' => ['label' => 'phone', 'rules' => 'required|is_natural_no_zero|min_length[5]|max_length[12]'],
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $phoneUnique = is_unique('loan_provider', 'phone', $data['phone']);
            if ($phoneUnique == true) {
                if ($this->loan_providerModel->insert($data)) {
                    $insertID = $this->loan_providerModel->insertID;

                    $datamess['htmlData'] = getAllListInOptionWithStatus($insertID, 'loan_pro_id', 'name', 'loan_provider','name');
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

    /**
     * @param int $loan_pro_id
     * @return void
     */
    public function delete($loan_pro_id){
        $table = DB()->table('loan_provider');
        $table->where('loan_pro_id',$loan_pro_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Loan_provider'));
    }


}