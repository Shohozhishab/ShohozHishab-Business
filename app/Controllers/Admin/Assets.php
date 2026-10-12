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
                'sch_id'      => $shopId,
                'account_id'      => $account_id,
                'account_type_id' => $account_type_id
            ]);

            // Insert sub-type mapping if it exists
            if (!empty($sub_type_id)) {
                $db->table('accounts_account_type_map')->insert([
                    'sch_id'      => $shopId,
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
                    'sch_id'      => $shopId,
                    'account_id'      => $account_id,
                    'account_type_id' => $account_type_id
                ]);

                // Insert sub-type mapping if it exists
                if (!empty($sub_type_id)) {
                    $db->table('accounts_account_type_map')->insert([
                        'sch_id'      => $shopId,
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

    public function delete($account_id){
        $table = DB()->table('accounts');
        $table->where('account_id',$account_id)->delete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert"> Delete data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Assets'));
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
            return redirect()->to(site_url('Admin/Assets/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Assets/create'));
        }

        // 2. Move the file to a temporary location
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        // 3. Process the CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Unable to open the CSV file.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Assets/create'));
        }

        $db      = DB();
        $builder = $db->table('accounts'); // ← change table name if needed

        $header   = null;
        $inserted = 0;
        $skipped  = 0;
        $rowNum   = 0;
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $mainType = get_data_by_id('account_type_id','account_type','type_key','assets');
        // Cache customer types for this shop (avoids N+1 queries)
        $typeCache = [];
        $types = $db->table('account_type')
            ->where('sch_id', $shopId)
            ->where('parent_account_type_id', $mainType)
            ->get()
            ->getResultArray();
        foreach ($types as $t) {
            $typeCache[strtolower(trim($t['type_name']))] = $t['account_type_id'];
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
            $name = trim($data['name'] ?? '');
            $typeName       = trim($data['type_name'] ?? '');

            // Must have at least customer_name OR mobile
            if (empty($name)) {
                $skipped++;
                continue;
            }

            $typeId = 0;
            if ($typeName !== '') {
                $key = strtolower($typeName);
                if (isset($typeCache[$key])) {
                    $typeId = $typeCache[$key];
                } else {
                    $db->table('account_type')->insert([
                        'sch_id'                    => $shopId,
                        'parent_account_type_id'    => $mainType,
                        'type_name'                 => $typeName,
                    ]);
                    $typeId = $db->insertID();
                    $typeCache[$key] = $typeId;
                }
            }

            // Prepare data to insert/update (add more fields as needed)
            $saveData = [
                'sch_id' => $shopId,
                'name' => $name,
                'createdBy'    => $userId,
                'createdDtm'    => date('Y-m-d H:i:s'),
            ];

            // Check if record already exists (by name)

            if (!empty($name)) {

                $builder->insert($saveData);
                $account_id = DB()->insertID();

                // Insert primary account type mapping
                $db->table('accounts_account_type_map')->insert([
                    'sch_id'      => $shopId,
                    'account_id'      => $account_id,
                    'account_type_id' => $mainType
                ]);

                // Insert sub-type mapping if it exists
                if (!empty($typeId)) {
                    $db->table('accounts_account_type_map')->insert([
                        'sch_id'      => $shopId,
                        'account_id'      => $account_id,
                        'account_type_id' => $typeId
                    ]);
                }

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
        return redirect()->to(site_url('Admin/Assets'));
    }


}