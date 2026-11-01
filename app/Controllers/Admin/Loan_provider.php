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

                        // Existing transaction data insert
                        DB()->table('existing_transaction')->insert([
                            'sch_id' => $shopId,
                            'loan_pro_id' => $loanproviderId,
                            'trangaction_type' => 'Dr.',
                            'amount' => $amount,
                            'createdBy' => $userId,
                            'createdDtm' => date('Y-m-d h:i:s')
                        ]);
                        $existing_trans_id = DB()->insertID();

                        //insert loanprovider ledger table (start)
                        $lonLedgdata = array(
                            'sch_id' => $shopId,
                            'loan_pro_id' => $loanproviderId,
                            'existing_trans_id' => $existing_trans_id,
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
                            'existing_trans_id' => $existing_trans_id,
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

                            // Existing transaction data insert
                            DB()->table('existing_transaction')->insert([
                                'sch_id' => $shopId,
                                'loan_pro_id' => $loanproviderId,
                                'trangaction_type' => 'Cr.',
                                'amount' => $amount,
                                'createdBy' => $userId,
                                'createdDtm' => date('Y-m-d h:i:s')
                            ]);
                            $existing_trans_id = DB()->insertID();

                            //insert loan provider ledger table (start)
                            $lonLedgdata = array(
                                'sch_id' => $shopId,
                                'loan_pro_id' => $loanproviderId,
                                'existing_trans_id' => $existing_trans_id,
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
                                'existing_trans_id' => $existing_trans_id,
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
     * Update notification date and note
     */
    public function note_update()
    {
        $userId = $this->session->userId;

        $data['loan_pro_id']         = $this->request->getPost('loan_pro_id');
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
            if ($this->loan_providerModel->update($data['loan_pro_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
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
            return redirect()->to(site_url('Admin/Loan_provider/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Loan_provider/create'));
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
        $builder = $db->table('loan_provider'); // ← change table name if needed

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
        return redirect()->to(site_url('Admin/Loan_provider/create'));
    }

    public function loanProDataEdit()
    {
        $loanProId = $this->request->getPost('loan_pro_id');

        $query = DB()->table('existing_transaction')
            ->where('loan_pro_id', $loanProId)
            ->get()
            ->getRow();

        if (!$query) {
            print '<div class="alert alert-danger">No existing transaction found for this Account Head.</div>';
            return;
        }

        $name   = get_data_by_id('name', 'loan_provider', 'loan_pro_id', $loanProId);
        $action = base_url('Admin/Loan_provider/loanProEditAction'); // adjust path if needed

        $view  = '<form action="'.$action.'" method="post">';
        $view .= '<div class="form-group">
                <label for="loan_pro_name_ex">Account Head Name</label>
                <input type="text" class="form-control" name="name" id="loan_pro_name_ex" 
                       value="' . htmlspecialchars($name) . '" readonly />
                <input type="hidden" name="loan_pro_id" value="'.$loanProId.'">
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
    public function loanProEditAction()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $loan_pro_id      = $this->request->getPost('loan_pro_id');
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
            return redirect()->to(site_url('Admin/Loan_provider'));
        }

        // Get old transaction
        $transaction = DB()->table('existing_transaction')
            ->where('sch_id', $shopId)
            ->where('loan_pro_id', $loan_pro_id)
            ->get()
            ->getRow();

        if (!$transaction) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Existing transaction not found.</div>');
            return redirect()->to(site_url('Admin/Loan_provider'));
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

            if (-$tempCap < 0) {
                $this->session->setFlashdata('message',
                    '<div class="alert alert-danger alert-dismissible" role="alert">Need to update capital for this transaction<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                );
                return redirect()->to(site_url('Admin/Loan_provider'));
            }
        }

        // ========== Start DB transaction ==========
        DB()->transStart();

        // ---------- 1. Loan Provider balance ----------
        $oldBalance = (float) get_data_by_id('balance', 'loan_provider', 'loan_pro_id', $loan_pro_id);

        // Reverse old effect
        if ($oldType === 'Dr.') {
            $oldBalance -= $oldAmount;
        } else {
            $oldBalance += $oldAmount;
        }

        // Apply new effect
        if ($newType === 'Dr.') {
            $newBalance = $oldBalance + $amount;
        } else {
            $newBalance = $oldBalance - $amount;
        }

        DB()->table('loan_provider')
            ->where('loan_pro_id', $loan_pro_id)
            ->update(['balance' => $newBalance]);

        // ---------- 2. Loan ledger ----------
        $ledger = DB()->table('ledger_loan')
            ->where('loan_pro_id', $loan_pro_id)
            ->where('existing_trans_id', $transaction->existing_trans_id)
            ->get()
            ->getRow();

        if ($ledger) {
            DB()->table('ledger_loan')
                ->where('ledg_loan_id', $ledger->ledg_loan_id) // change if your PK name is different
                ->update([
                    'trangaction_type' => $newType,
                    'amount'           => $amount,
                    'rest_balance'     => abs($newBalance),
                    'particulars'      => 'Account Head last balance',
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
                ->where('capital_id', $ledgerCapital->capital_id) // change if your PK name is different
                ->update([
                    'trangaction_type' => ($newType === 'Dr.') ? 'Cr.' : 'Dr.',
                    'amount'           => $amount,
                    'rest_balance'     => $newCap,
                    'particulars'      => 'Existing Account Head (' . get_data_by_id('name', 'loan_provider', 'loan_pro_id', $loan_pro_id) . ') is added with existing balance',
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
            $this->session->setFlashdata('message', '<div class="alert alert-danger">Something went wrong. Please try again.</div>' );
        } else {
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Update Record Success<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        }

        return redirect()->to(site_url('Admin/Loan_provider'));
    }

    public function csv_existing_action()
    {
        // 1. Validate uploaded file
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|ext_in[file,csv]|max_size[file,2048]', // 2 MB
            ],
        ];

        if (!$this->validate($validationRule)) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validator->listErrors() . '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Loan_provider/create'));
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid() || $file->hasMoved()) {
            $this->session->setFlashdata('message','<div class="alert alert-danger alert-dismissible" role="alert">Invalid file upload.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            return redirect()->to(site_url('Admin/Loan_provider/create'));
        }

        // 2. Move to temporary location
        $newName  = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        // 3. Open CSV
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            @unlink($filePath);
            $this->session->setFlashdata('message','<div class="alert alert-danger">Unable to open the CSV file.</div>');
            return redirect()->to(site_url('Admin/Loan_provider/create'));
        }

        $db      = DB();
        $builder = $db->table('loan_provider');   // table name
        $shopId  = $this->session->shopId;
        $userId  = $this->session->userId;
        $now     = date('Y-m-d H:i:s');

        $header   = null;
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {

            // Skip empty rows
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) {
                continue;
            }

            // First non-empty row = header
            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $row);

                // Required columns
                $required = ['name', 'phone', 'type', 'amount'];
                foreach ($required as $col) {
                    if (!in_array($col, $header, true)) {
                        fclose($handle);
                        @unlink($filePath);
                        $this->session->setFlashdata('message','<div class="alert alert-danger">CSV must contain columns: ' . implode(', ', $required) . '</div>');
                        return redirect()->to(site_url('Admin/Loan_provider/create'));
                    }
                }
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            $name             = trim($data['name'] ?? '');
            $phone            = trim($data['phone'] ?? '');
            $transaction_type = trim($data['type'] ?? '');
            $amount           = trim($data['amount'] ?? '');

            // Basic required fields
            if ($name === '' || $phone === '') {
                $skipped++;
                continue;
            }

            // Amount must be a positive number
            if (!is_numeric($amount) || (float)$amount <= 0) {
                $errors[] = "Invalid amount for {$name} / {$phone}";
                $skipped++;
                continue;
            }
            $amount = (float)$amount;

            // Normalize transaction_type
            // Accepts: 2 / Dr / Dr.  → Debit
            //          anything else → Credit
            $isDebit = in_array(strtoupper(rtrim($transaction_type, '.')), ['2', 'DR'], true);

            // Unique phone check (same shop)
            $existing = $builder->where('phone', $phone)
                ->where('sch_id', $shopId)
                ->get()
                ->getRow();

            if ($existing) {
                $errors[] = "Phone already exists: {$phone}";
                $skipped++;
                continue;
            }

            // ---------- Per-row transaction ----------
            $db->transStart();

            try {
                if ($isDebit) {
                    // ===== Debit (transaction_type == 2 / Dr.) =====
                    $saveData = [
                        'sch_id'     => $shopId,
                        'name'       => $name,
                        'phone'      => $phone,
                        'balance'    => $amount,          // positive
                        'createdBy'  => $userId,
                        'createdDtm' => $now,
                    ];

                    $builder->insert($saveData);
                    $loanproviderId = $db->insertID();

                    // existing_transaction
                    $db->table('existing_transaction')->insert([
                        'sch_id'           => $shopId,
                        'loan_pro_id'      => $loanproviderId,
                        'trangaction_type' => 'Dr.',
                        'amount'           => $amount,
                        'createdBy'        => $userId,
                        'createdDtm'       => $now,
                    ]);
                    $existing_trans_id = $db->insertID();

                    // ledger_loan
                    $db->table('ledger_loan')->insert([
                        'sch_id'            => $shopId,
                        'loan_pro_id'       => $loanproviderId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => 'Account Head last balance',
                        'trangaction_type'  => 'Dr.',
                        'amount'            => $amount,
                        'rest_balance'      => $amount,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                    // Capital ↓
                    $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);
                    $newCap = $oldCap - $amount;

                    $db->table('shops')->where('sch_id', $shopId)->update(['capital' => $newCap]);

                    $db->table('ledger_capital')->insert([
                        'sch_id'            => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => "Existing Account Head ({$name}) is added with existing balance",
                        'trangaction_type'  => 'Cr.',
                        'amount'            => $amount,
                        'rest_balance'      => $newCap,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                } else {
                    // ===== Credit (transaction_type != 2) =====
                    $oldCap = (float) get_data_by_id('capital', 'shops', 'sch_id', $shopId);

                    // Same check you used in the single-record code
                    if (-$oldCap < $amount) {
                        throw new \Exception("Need to update capital for this transaction (name: {$name})");
                    }

                    $saveData = [
                        'sch_id'     => $shopId,
                        'name'       => $name,
                        'phone'      => $phone,
                        'balance'    => -$amount,          // negative
                        'createdBy'  => $userId,
                        'createdDtm' => $now,
                    ];

                    $builder->insert($saveData);
                    $loanproviderId = $db->insertID();

                    // existing_transaction
                    $db->table('existing_transaction')->insert([
                        'sch_id'           => $shopId,
                        'loan_pro_id'      => $loanproviderId,
                        'trangaction_type' => 'Cr.',
                        'amount'           => $amount,
                        'createdBy'        => $userId,
                        'createdDtm'       => $now,
                    ]);
                    $existing_trans_id = $db->insertID();

                    // ledger_loan
                    $db->table('ledger_loan')->insert([
                        'sch_id'            => $shopId,
                        'loan_pro_id'       => $loanproviderId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => 'Account head last balance',
                        'trangaction_type'  => 'Cr.',
                        'amount'            => $amount,
                        'rest_balance'      => $amount,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);

                    // Capital ↑
                    $newCap = $oldCap + $amount;

                    $db->table('shops')->where('sch_id', $shopId)->update(['capital' => $newCap]);

                    $db->table('ledger_capital')->insert([
                        'sch_id'            => $shopId,
                        'existing_trans_id' => $existing_trans_id,
                        'particulars'       => "Existing Account head ({$name}) is added with existing balance",
                        'trangaction_type'  => 'Dr.',
                        'amount'            => $amount,
                        'rest_balance'      => $newCap,
                        'createdBy'         => $userId,
                        'createdDtm'        => $now,
                    ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Database transaction failed');
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

        // 4. Final message
        $msg = "CSV processed successfully. Inserted: {$inserted}, Skipped: {$skipped}";
        if (!empty($errors)) {
            $msg .= '<br><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
            $alert = 'alert-warning';
        } else {
            $alert = 'alert-success';
        }

        $this->session->setFlashdata('message','<div class="alert ' . $alert . ' alert-dismissible" role="alert">' . $msg . '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        return redirect()->to(site_url('Admin/Loan_provider/create'));
    }


}