<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Loan_providerModel;
use CodeIgniter\HTTP\RedirectResponse;


class Assets extends BaseController
{


    protected $loan_providerModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Assets';

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

            $data['result'] = DB()->table('accounts')
                ->join('accounts_account_type_map', 'accounts_account_type_map.account_id = accounts.account_id')
                ->join('account_type', 'account_type.account_type_id = accounts_account_type_map.account_type_id')
                ->where('accounts.sch_id', $shopId)
                ->where('account_type.type_key', 'assets')
                ->get()
                ->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Assets/list', $data);
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

            $data['action'] = base_url('Admin/Assets/create_action');
            $data['actionExisting'] = base_url('Admin/Assets/existing_create_action');
            $data['assetsType'] = get_data_by_id('account_type_id','account_type','type_key','assets');

            $data['subType'] = DB()->table('account_type')->where('parent_account_type_id',$data['assetsType'])->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['create'] == 1) {
                echo view('Admin/Assets/create', $data);
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

        $account_type_id = $this->request->getPost('account_type_id');
        $sub_type_id = $this->request->getPost('sub_type_id');
        $data['name'] = $this->request->getPost('name');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $db = DB();

            // Start a transaction to ensure all inserts succeed together or fail together
            $db->transStart();

            $db->table('accounts')->insert($data);
            $account_id = $db->insertID();

            // Insert primary account type mapping
            $db->table('accounts_account_type_map')->insert([
                'account_id'      => $account_id,
                'account_type_id' => $account_type_id
            ]);

            // Insert sub-type mapping if it exists
            if (!empty($sub_type_id)) {
                $db->table('accounts_account_type_map')->insert([
                    'account_id'      => $account_id,
                    'account_type_id' => $sub_type_id
                ]);
            }

            // Complete the transaction (automatically commits on success, rolls back on failure)
            $db->transComplete();

            if ($db->transStatus() !== FALSE) {
                print '<div class="alert alert-success alert-dismissible" role="alert">Created data successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert">Something went wrong. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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

        $amount = $this->request->getPost('amount');
        $account_type_id = $this->request->getPost('account_type_id');
        $sub_type_id = $this->request->getPost('sub_type_id');
        $data['name'] = $this->request->getPost('name');

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($amount !== '0') {
                $db = DB();
                $db->transStart();

                $db->table('accounts')->insert([
                    'sch_id' => $shopId,
                    'name' => $data['name'],
                    'balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                ]);
                $account_id = $db->insertID();

                // Insert primary account type mapping
                $db->table('accounts_account_type_map')->insert([
                    'account_id'      => $account_id,
                    'account_type_id' => $account_type_id
                ]);

                // Insert sub-type mapping if it exists
                if (!empty($sub_type_id)) {
                    $db->table('accounts_account_type_map')->insert([
                        'account_id'      => $account_id,
                        'account_type_id' => $sub_type_id
                    ]);
                }


                //insert  ledger table (start)
                $lonLedgdata = array(
                    'sch_id' => $shopId,
                    'account_id' => $account_id,
                    'particulars' => 'Assets last balance ',
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $db->table('ledger_accounts')->insert($lonLedgdata);
                //insert ledger table (end)


                //update capital (start)
                $oldCap = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                $newcap = $oldCap - $amount;

                $capData = array(
                    'capital' => $newcap
                );
                $db->table('shops')->where('sch_id', $shopId)->update($capData);

                $capLedgdata = array(
                    'sch_id' => $shopId,
                    'particulars' => 'Existing Assets (' . $data['name'] . ') is added with existing balance',
                    'trangaction_type' => 'Cr.',
                    'amount' => $amount,
                    'rest_balance' => $newcap,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $db->table('ledger_capital')->insert($capLedgdata);

                $db->transComplete();

                if ($db->transStatus() !== FALSE) {
                    print '<div class="alert alert-success alert-dismissible" role="alert">Created data successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } else {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Something went wrong. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }else{
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

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
            $data['action'] = base_url('Admin/Assets/update_action');
            $data['accounts'] = DB()->table('accounts')->where('account_id', $id)->get()->getRow();

            $data['assetsType'] = get_data_by_id('account_type_id','account_type','type_key','assets');
            $data['subType'] = DB()->table('account_type')->where('parent_account_type_id',$data['assetsType'])->get()->getResult();

            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if ($data['update'] == 1) {
                echo view('Admin/Assets/update', $data);
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

        $account_id = $this->request->getPost('account_id');
        $data['name'] = $this->request->getPost('name');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            DB()->table('accounts')->where('account_id',$account_id)->update($data);
            print '<div class="alert alert-success alert-dismissible" role="alert">Update data successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    }
    public function type_action(){
        $shopId = $this->session->shopId;
        $data['sch_id'] = $shopId;
        $data['type_name'] = $this->request->getPost('sub_type');
        $data['parent_account_type_id'] = $this->request->getPost('account_type_id') ?? null;
        $this->validation->setRules([
            'type_name' => ['label' => 'Type', 'rules' => 'required'],
        ]);
        if ($this->validation->run($data) == FALSE) {
            $datamess['message'] = '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            DB()->table('account_type')->insert($data);
            $insertID = DB()->insertID();

            $array = DB()->table('account_type')->where('parent_account_type_id',$data['parent_account_type_id'])->get()->getResult();


            $options = '';
            foreach ($array as $key => $val) {
                $options .= '<option value="' . $val->account_type_id . '" ';
                $options .= ($val->account_type_id == $insertID ) ? ' selected="selected"' : '';
                $options .= '>' . $val->type_name . '</option>';
            }

            $datamess['htmlData'] = $options;

            $datamess['success'] = true;
            $datamess['message'] = '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        }
        return json_encode($datamess);

    }

}