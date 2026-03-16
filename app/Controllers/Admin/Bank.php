<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Bank extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Bank';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides bank view
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
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('name', 'ASC')->get()->getResult();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }

            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Bank/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method provides bank create view
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data['action'] = base_url('Admin/Bank/create_action');
            $data['action2'] = base_url('Admin/Bank/existing_create_action');


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Bank/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store bank
     * @return void
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[20]|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $accCheck = is_unique_bank('bank', 'account_no', $data['account_no'], 'name', $data['name']);
            if ($accCheck == true) {
                $bankTable = DB()->table('bank');
                if ($bankTable->insert($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }


        }
    }

    /**
     * @description This method provides bank update view
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
            $data['action'] = base_url('Admin/Bank/update_action');
            $bankTable = DB()->table('bank');
            $data['bank'] = $bankTable->where('bank_id', $id)->get()->getRow();


            $data['menu'] = view('Admin/menu_bank', $data);
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Bank/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update bank
     * @return void
     */
    public function update_action()
    {
        $userId = $this->session->userId;

        $bank_id = $this->request->getPost('bank_id');
        $data['name'] = $this->request->getPost('name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['status'] = $this->request->getPost('status');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[20]|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $accCheck = is_unique_update('bank', 'account_no', $data['account_no'], 'name', $data['name']);
            if ($accCheck == true) {
                $bankTable = DB()->table('bank');
                if ($bankTable->where('bank_id', $bank_id)->update($data)) {
                    print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }


        }
    }

    /**
     * @description This method store existing bank
     * @return void
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = $this->request->getPost('amount');
        $name = $this->request->getPost('name');

        $data['name'] = $this->request->getPost('name');
        $data['account_no'] = $this->request->getPost('account_no');
        $data['amount'] = $this->request->getPost('amount');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'account_no' => ['label' => 'account_no', 'rules' => 'required|is_natural_no_zero|max_length[32]|alpha_numeric_space'],
            'amount' => ['label' => 'amount', 'rules' => 'required|max_length[32]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($amount !== '0') {
                $accCheck = is_unique_bank('bank', 'account_no', $data['account_no'], 'name', $data['name']);
                if ($accCheck == true) {
                    DB()->transStart();

                    $data = array(
                        'sch_id' => $shopId,
                        'name' => $this->request->getPost('name'),
                        'account_no' => $this->request->getPost('account_no'),
                        'balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $bankTable = DB()->table('bank');
                    $bankTable->insert($data);
                    $bankId = DB()->insertID();


                    //insert Bank ledger table (start)
                    $bankLedgdata = array(
                        'sch_id' => $shopId,
                        'bank_id' => $bankId,
                        'particulars' => 'Bank last balance ',
                        'trangaction_type' => 'Dr.',
                        'amount' => $amount,
                        'rest_balance' => $amount,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_bankTable = DB()->table('ledger_bank');
                    $ledger_bankTable->insert($bankLedgdata);
                    //insert Bank ledger table (end)


                    //update capital (start)
                    $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                    $newcap = $oldCap - $amount;

                    $capData = array(
                        'capital' => $newcap
                    );
                    $shopsTable = DB()->table('shops');
                    $shopsTable->where('sch_id', $shopId)->update($capData);

                    $capLedgdata = array(
                        'sch_id' => $shopId,
                        'particulars' => 'Existing Bank (' . $name . ') is added with existing balance',
                        'trangaction_type' => 'Cr.',
                        'amount' => $amount,
                        'rest_balance' => $newcap,
                        'createdBy' => $userId,
                        'createdDtm' => date('Y-m-d h:i:s')
                    );
                    $ledger_capitalTable = DB()->table('ledger_capital');
                    $ledger_capitalTable->insert($capLedgdata);
                    //update capital (end)

                    print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

                    DB()->transComplete();

                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert"> Account on already exists <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }else{
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }
    }
    /**
     * @param int $bank_id
     * @return void
     */
    public function delete($bank_id){
        $table = DB()->table('bank');
        $table->where('bank_id',$bank_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Bank'));
    }






}