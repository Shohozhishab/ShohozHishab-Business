<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\EmployeeModel;
use CodeIgniter\HTTP\RedirectResponse;


class Employee extends BaseController
{


    protected $employeeModel;
    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Employee';

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
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
            $data['employee_data'] = $this->employeeModel->where('sch_id', $shopId)->where('deleted IS NULL')->orderBy('name', 'ASC')->get()->getResult();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Employee/list', $data);
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
            $data['action'] = base_url('Admin/Employee/create_action');
            $data['action2'] = base_url('Admin/Employee/existing_create_action');


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['create'] == 1) {
                echo view('Admin/Employee/create', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method store employee
     * @return void
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['name'] = $this->request->getPost('name');
        $data['salary'] = $this->request->getPost('salary');
        $data['age'] = $this->request->getPost('age');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'salary' => ['label' => 'Salary', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
            'age' => ['label' => 'age', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if ($this->employeeModel->insert($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Crate data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
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
            $data['action'] = base_url('Admin/Employee/update_action');
            $data['employee_data'] = $this->employeeModel->where('employee_id', $id)->get()->getRow();


            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Employee/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update employee
     * @return void
     * @throws \ReflectionException
     */
    public function update_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $data['employee_id'] = $this->request->getPost('employee_id');
        $data['name'] = $this->request->getPost('name');
        $data['salary'] = $this->request->getPost('salary');
        $data['age'] = $this->request->getPost('age');
        $data['status'] = $this->request->getPost('status');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'salary' => ['label' => 'Salary', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
            'age' => ['label' => 'age', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            if ($this->employeeModel->update($data['employee_id'], $data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method store existing employee
     * @return void
     * @throws \ReflectionException
     */
    public function existing_create_action()
    {
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $amount = $this->request->getPost('amount');
        $name = $this->request->getPost('name');

        $data['name'] = $this->request->getPost('name');
        $data['salary'] = $this->request->getPost('salary');
        $data['age'] = $this->request->getPost('age');
        $data['amount'] = $this->request->getPost('amount');
        $data['sch_id'] = $shopId;
        $data['createdBy'] = $userId;
        $data['createdDtm'] = date('Y-m-d h:i:s');

        $this->validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|only_numeric_not_allow|max_length[60]'],
            'salary' => ['label' => 'Salary', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
            'age' => ['label' => 'age', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space'],
            'amount' => ['label' => 'amount', 'rules' => 'required|alpha_numeric_space'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            if ($amount !== '0') {
                DB()->transStart();

                $data = array(
                    'sch_id' => $shopId,
                    'name' => $this->request->getPost('name'),
                    'salary' => $this->request->getPost('salary'),
                    'balance' => $amount,
                    'age' => $this->request->getPost('age'),
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );

                $this->employeeModel->insert($data);
                $empId = $this->employeeModel->getInsertID();


                //insert Bank ledger table (start)
                $emplLedgdata = array(
                    'sch_id' => $shopId,
                    'employee_id' => $empId,
                    'particulars' => 'Employee last balance ',
                    'trangaction_type' => 'Dr.',
                    'amount' => $amount,
                    'rest_balance' => $amount,
                    'createdBy' => $userId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $ledger_employeeTable = DB()->table('ledger_employee');
                $ledger_employeeTable->insert($emplLedgdata);
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
                    'particulars' => 'Existing Employee (' . $name . ') is added with existing balance',
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
            }else{
                print '<div class="alert alert-danger alert-dismissible" role="alert"> Invalid amount <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        }
    }


}