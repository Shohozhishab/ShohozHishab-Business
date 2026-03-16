<?php

namespace App\Controllers\Super_admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\AdminModel;
use App\Models\Customer_typeModel;
use App\Models\Gen_settingsModel;
use App\Models\RoleModel;
use App\Models\ShopsModel;
use App\Models\StoresModel;
use App\Models\UsersModel;
use App\Models\Vat_registerModel;
use CodeIgniter\HTTP\RedirectResponse;

class Shops extends BaseController
{
    protected $validation;
    protected $session;
    protected $adminModel;
    protected $shopsModel;
    protected $permission;
    protected $roleModel;
    protected $usersModel;
    protected $vat_registerModel;
    protected $storesModel;
    protected $gen_settingsModel;
    protected $customer_typeModel;
    protected $crop;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->shopsModel = new ShopsModel();
        $this->permission = new Permission();
        $this->usersModel = new UsersModel();
        $this->vat_registerModel = new Vat_registerModel();
        $this->roleModel = new RoleModel();
        $this->storesModel = new StoresModel();
        $this->gen_settingsModel = new Gen_settingsModel();
        $this->customer_typeModel = new Customer_typeModel();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides shops view
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['shops'] = $this->shopsModel->where('deleted IS NULL')->findAll();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Shops/shop_list', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @return RedirectResponse|void
     */
    public function create()
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['action'] = base_url('Super_admin/Shops/create_action');


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Shops/create', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function create_action()
    {
        $supuserId = $this->session->userIdSuper;

        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        $data['password'] = $this->request->getPost('password');
        $data['con_password'] = $this->request->getPost('con_password');
        $data['status'] = $this->request->getPost('status');

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required'],
            'email' => ['label' => 'email', 'rules' => 'required'],
            'password' => ['label' => 'Password', 'rules' => 'required|max_length[155]'],
            'con_password' => ['label' => 'Con password', 'rules' => 'required|matches[password]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/create');
        } else {
            $emailUnique = is_unique_super('users', 'email', $data['email']);
            if ($emailUnique == true) {
                DB()->transStart();
                //shop create query
                $this->shopsModel->insert($data);
                $shopsId = $this->shopsModel->getInsertID();
                //shop create query


                //roles insert in roles table(start)
                $role['sch_id'] = $shopsId;
                $role['role'] = 'Admin';
                $role['is_default'] = '1';
                $role['permission'] = $this->permission->admin_permissions;
                $role['createdBy'] = $supuserId;
                $role['createdDtm'] = date('Y-m-d h:i:s');
                $this->roleModel->insert($role);
                $roleId = $this->roleModel->getInsertID();
                //roles insert in roles table(start)


                //create users in users table (start)
                $userData['sch_id'] = $shopsId;
                $userData['role_id'] = $roleId;
                $userData['is_default'] = 1;
                $userData['name'] = $this->request->getPost('name');
                $userData['email'] = $this->request->getPost('email');
                $userData['password'] = sha1($this->request->getPost('password'));
                $userData['status'] = $this->request->getPost('status');
                $userData['createdBy'] = $supuserId;
                $userData['createdDtm'] = date('Y-m-d h:i:s');
                $this->usersModel->insert($userData);
                //create users in users table (end)


                //create Vat in vat_register table (start)
                $vatData['sch_id'] = $shopsId;
                $vatData['is_default'] = 1;
                $vatData['name'] = "Default Vat Name";
                $vatData['vat_register_no'] = "BIN-0000-01";
                $vatData['createdBy'] = $supuserId;
                $vatData['createdDtm'] = date('Y-m-d h:i:s');
                $this->vat_registerModel->insert($vatData);
                //create Vat in vat_register table (end)


                // create default store in stores table(start)
                $storeData['sch_id'] = $shopsId;
                $storeData['name'] = 'Default';
                $storeData['description'] = 'Default Store';
                $storeData['is_default'] = '1';
                $storeData['createdDtm'] = date('Y-m-d h:i:s');
                $this->storesModel->insert($storeData);
                // create default store in stores table(end)


                //general settings insert in gen_settings table (start)
                $gen_settingsData = array(
                    array('sch_id' => $shopsId, 'label' => 'barcode_img_size', 'value' => '100'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'barcode_type', 'value' => 'C128A'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'business_type', 'value' => 'Ownership business'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'currency', 'value' => 'BDT'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'currency_before_symbol', 'value' => '৳'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'currency_after_symbol', 'value' => '/-'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'running_year', 'value' => '2018-2019'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'disable_frontend', 'value' => '0'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'phone_code', 'value' => '880'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'country', 'value' => 'Bangladesh'
                    ),
                    array('sch_id' => $shopsId, 'label' => 'edit_permission_expire', 'value' => '10'
                    ),

                );
                $this->gen_settingsModel->insertBatch($gen_settingsData);
                //general settings insert in gen_settings table (end)

                //customer type create(start)
                $cusTypeData = array(
                    'sch_id' => $shopsId,
                    'type_name' => 'regular',
                    'createdBy' => $shopsId,
                    'createdDtm' => date('Y-m-d h:i:s')
                );
                $this->customer_typeModel->insert($cusTypeData);
                //customer type create(end)

                DB()->transComplete();

                $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Create Record Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/Super_admin/Shops/create');
            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Email already in use <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/Super_admin/Shops/create');
            }

        }
    }

    /**
     * @param $shopId
     * @return RedirectResponse|void
     */
    public function update($shopId)
    {
        $isLoggedInSuperAdmin = $this->session->isLoggedInSuperAdmin;
        if (!isset($isLoggedInSuperAdmin) || $isLoggedInSuperAdmin != TRUE) {
            return redirect()->to(site_url("/Super_admin/Login"));
        } else {

            $data['shops'] = $this->shopsModel->where('sch_id', $shopId)->first();
            $data['users'] = $this->usersModel->where('sch_id', $shopId)->first();


            echo view('Super_admin/header');
            echo view('Super_admin/sidebar');
            echo view('Super_admin/Shops/update', $data);
            echo view('Super_admin/footer');
        }
    }

    /**
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function general_update()
    {

        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['name'] = $this->request->getPost('name');
        $data['email'] = $this->request->getPost('email');
        $data['status'] = $this->request->getPost('status');

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required'],
            'email' => ['label' => 'email', 'rules' => 'required'],
            'status' => ['label' => 'status', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=general');
        } else {

            $this->shopsModel->update($data['sch_id'], $data);
            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=general');

        }
    }

    /**
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function personal_update()
    {

        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['mobile'] = $this->request->getPost('mobile');
        $data['address'] = $this->request->getPost('address');
        $data['comment'] = $this->request->getPost('comment');
        $data['package_id'] = $this->request->getPost('package_id');

        $this->validation->setRules([
            'mobile' => ['label' => 'mobile', 'rules' => 'required|is_natural_no_zero|alpha_numeric_space|min_length[5]|max_length[12]'],
            'address' => ['label' => 'address', 'rules' => 'required'],
            'comment' => ['label' => 'comment', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=personal');
        } else {
            $this->shopsModel->update($data['sch_id'], $data);

            if (!empty($data['package_id'])) {
                $packtable = DB()->table('package');
                $packPermission = $packtable->where('package_id', $data['package_id'])->get()->getRow();
                $per = $packPermission->package_admin_permission;
                $roleUpdate = ['permission' => $per];
            } else {
                $per = $this->permission->admin_permissions;
                $roleUpdate = ['permission' => $per];
            }

            $table = DB()->table('roles');
            $table->where('sch_id', $data['sch_id'])->where('is_default', '1')->update($roleUpdate);

            $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=personal');
        }
    }

    /**
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function photo_update()
    {
        $data['sch_id'] = $this->request->getPost('sch_id');


        if (!empty($_FILES['logo']['name'])) {
            $target_dir = FCPATH . '/uploads/schools/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('logo', 'shops', 'sch_id', $data['sch_id']);
            if (!empty($old_img)) {
                unlink($target_dir .  $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('logo');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'profile_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(350, 100, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['logo'] = $pro_nameimg;
        }

        if (!empty($_FILES['profile_image']['name'])) {
            $target_dir = FCPATH . '/uploads/schools/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img_pro = get_data_by_id('image', 'shops', 'sch_id', $data['sch_id']);
            if (!empty($old_img_pro)) {
                unlink($target_dir .  $old_img_pro);
            }

            //new image uplode
            $picpRO = $this->request->getFile('profile_image');
            $namePicPRO = $picpRO->getRandomName();
            $picpRO->move($target_dir, $namePicPRO);
            $pro_nameimg_Pro = 'pro_' . $picpRO->getName();
            $this->crop->withFile($target_dir .  $namePicPRO)->fit(160, 160, 'center')->save($target_dir .  $pro_nameimg_Pro);
            unlink($target_dir .  $namePicPRO);
            $data['image'] = $pro_nameimg_Pro;
        }


        $this->shopsModel->update($data['sch_id'], $data);

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=photo');
    }

    /**
     * @return RedirectResponse
     * @throws \ReflectionException
     */
    public function user_update()
    {
        $data['sch_id'] = $this->request->getPost('sch_id');
        $data['email'] = $this->request->getPost('email');
        $data['password'] = sha1($this->request->getPost('password'));
        $data['con_password'] = sha1($this->request->getPost('con_password'));

        $this->validation->setRules([
            'email' => ['label' => 'email', 'rules' => 'required'],
            'password' => ['label' => 'Password', 'rules' => 'required|max_length[155]'],
            'con_password' => ['label' => 'Con password', 'rules' => 'required|matches[password]'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=user');
        } else {

            $emailUnique = is_unique_super_update('users', 'email', $data['email'], 'sch_id', $data['sch_id']);
            if ($emailUnique == true) {
                $this->usersModel->update($data['sch_id'], $data);
                $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Data update successfully <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=user');
            } else {
                $this->session->setFlashdata('message', '<div class="alert alert-danger alert-dismissible" role="alert">Email already in use <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/Super_admin/Shops/update/' . $data['sch_id'] . '?active=user');
            }
        }
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function resetdb($id)
    {
        DB()->transStart();

        $ledgertab = DB()->table('ledger');
        $ledgertab->where('sch_id', $id)->delete();

        $ledger_banktab = DB()->table('ledger_bank');
        $ledger_banktab->where('sch_id', $id)->delete();

        $ledger_employeetab = DB()->table('ledger_employee');
        $ledger_employeetab->where('sch_id', $id)->delete();

        $ledger_expensetab = DB()->table('ledger_expense');
        $ledger_expensetab->where('sch_id', $id)->delete();

        $ledger_loantab = DB()->table('ledger_loan');
        $ledger_loantab->where('sch_id', $id)->delete();

        $ledger_nagodantab = DB()->table('ledger_nagodan');
        $ledger_nagodantab->where('sch_id', $id)->delete();

        $ledger_other_salestab = DB()->table('ledger_other_sales');
        $ledger_other_salestab->where('sch_id', $id)->delete();

        $ledger_supplierstab = DB()->table('ledger_suppliers');
        $ledger_supplierstab->where('sch_id', $id)->delete();

        $ledger_vattab = DB()->table('ledger_vat');
        $ledger_vattab->where('sch_id', $id)->delete();

        $ledger_salestab = DB()->table('ledger_sales');
        $ledger_salestab->where('sch_id', $id)->delete();

        $ledger_purchasetab = DB()->table('ledger_purchase');
        $ledger_purchasetab->where('sch_id', $id)->delete();

        $ledger_profittab = DB()->table('ledger_profit');
        $ledger_profittab->where('sch_id', $id)->delete();

        $ledger_stocktab = DB()->table('ledger_stock');
        $ledger_stocktab->where('sch_id', $id)->delete();

        $ledger_capitaltab = DB()->table('ledger_capital');
        $ledger_capitaltab->where('sch_id', $id)->delete();

        $ledger_discounttab = DB()->table('ledger_discount');
        $ledger_discounttab->where('sch_id', $id)->delete();


        $invoice_itemtab = DB()->table('invoice_item');
        $invoice_itemtab->where('sch_id', $id)->delete();

        $return_purchase_itemtab = DB()->table('return_purchase_item');
        $return_purchase_itemtab->where('sch_id', $id)->delete();

        $return_sale_itemtab = DB()->table('return_sale_item');
        $return_sale_itemtab->where('sch_id', $id)->delete();


        $productstab = DB()->table('products');
        $pro = $productstab->where('sch_id', $id)->get()->getResult();
        foreach ($pro as $row) {
            $purchase_itemtab = DB()->table('purchase_item');
            $purchase_itemtab->where('prod_id', $row->prod_id)->delete();
        }

        $return_purchasetab = DB()->table('return_purchase');
        $return_purchasetab->where('sch_id', $id)->delete();

        $purchasetab = DB()->table('purchase');
        $purchasetab->where('sch_id', $id)->delete();


        $money_receipttab = DB()->table('money_receipt');
        $money_receipttab->where('sch_id', $id)->delete();

        $transactiontab = DB()->table('transaction');
        $transactiontab->where('sch_id', $id)->delete();

        $bank_withdrawtab = DB()->table('bank_withdraw');
        $bank_withdrawtab->where('sch_id', $id)->delete();

        $bank_deposittab = DB()->table('bank_deposit');
        $bank_deposittab->where('sch_id', $id)->delete();

        $chaquetab = DB()->table('chaque');
        $chaquetab->where('sch_id', $id)->delete();

        $banktab = DB()->table('bank');
        $banktab->where('sch_id', $id)->delete();


        $return_saletab = DB()->table('return_sale');
        $return_saletab->where('sch_id', $id)->delete();


        $salestab = DB()->table('sales');
        $salestab->where('sch_id', $id)->delete();

        $invoicetab = DB()->table('invoice');
        $invoicetab->where('sch_id', $id)->delete();


        $productstab2 = DB()->table('products');
        $productstab2->where('sch_id', $id)->delete();

        $product_categorytab = DB()->table('product_category');
        $product_categorytab->where('sch_id', $id)->delete();

        $brandtab = DB()->table('brand');
        $brandtab->where('sch_id', $id)->delete();

        $customerstab = DB()->table('customers');
        $customerstab->where('sch_id', $id)->delete();

        $customer_typetab = DB()->table('customer_type');
        $customer_typetab->where('sch_id', $id)->delete();

        $employeetab = DB()->table('employee');
        $employeetab->where('sch_id', $id)->delete();

        $loan_providertab = DB()->table('loan_provider');
        $loan_providertab->where('sch_id', $id)->delete();

        $supplierstab = DB()->table('suppliers');
        $supplierstab->where('sch_id', $id)->delete();

        $warranty_managetab = DB()->table('warranty_manage');
        $warranty_managetab->where('sch_id', $id)->delete();

        $lctab = DB()->table('lc');
        $lctab->where('sch_id', $id)->delete();


        $vat_registertab = DB()->table('vat_register');
        $vat_registertab->where('sch_id', $id)->where('is_default', '0')->delete();

        $storestab = DB()->table('stores');
        $storestab->where('sch_id', $id)->where('is_default', '0')->delete();

        $userstab = DB()->table('users');
        $userstab->where('sch_id', $id)->where('is_default', '0')->delete();

        $ledger_service_charge = DB()->table('ledger_service_charge');
        $ledger_service_charge->where('sch_id', $id)->delete();

        $service_invoice_item = DB()->table('service_invoice_item');
        $service_invoice_item->where('sch_id', $id)->delete();

        $service_invoice = DB()->table('service_invoice');
        $service_invoice->where('sch_id', $id)->delete();

        $services = DB()->table('services');
        $services->where('sch_id', $id)->delete();

        $transactionLog = DB()->table('transaction_log');
        $transactionLog->where('sch_id', $id)->delete();

        $transactionEditLog = DB()->table('transaction_edit_log');
        $transactionEditLog->where('sch_id', $id)->delete();


        $cash = array(
            'cash' => 0,
            'capital' => 0,
            'profit' => 0,
            'stockAmount' => 0,
            'expense' => 0,
            'purchase_balance' => 0,
            'discount' => 0,
            'sale_balance' => 0,
            'service_charge' => 0,
        );
        $shopstabUp = DB()->table('shops');
        $shopstabUp->where('sch_id', $id)->update($cash);


        $vatData = array('balance' => 0);
        $vat_registertabUp = DB()->table('vat_register');
        $vat_registertabUp->where('sch_id', $id)->update($vatData);


        DB()->transComplete();

        $this->session->setFlashdata('message', '<div class="alert alert-success alert-dismissible" role="alert">Reset shop Success <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect()->to('/Super_admin/Shops');
    }


}
