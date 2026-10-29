<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use CodeIgniter\HTTP\RedirectResponse;


class Products extends BaseController
{

    protected $permission;
    protected $validation;
    protected $session;
    protected $crop;
    private $module_name = 'Products';

    public function __construct()
    {
        $this->permission = new Permission();
        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        $this->crop = \Config\Services::image();
    }

    /**
     * @description This method provides products view
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

            $productTable = DB()->table('products');
            $productTable->select('products.*,product_stock_relation.*,stores.store_id,stores.name as store_name,stores.is_default');
            $productTable->join('product_stock_relation','product_stock_relation.product_id = products.prod_id');
            $productTable->join('stores','stores.store_id = product_stock_relation.store_id');
            $data['products_data'] = $productTable->where('products.sch_id', $shopId)->groupBy('products.prod_id')->where('stores.is_default','1')->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update products
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
            $productsTable = DB()->table('products');
            $data['product'] = $productsTable->where('prod_id', $id)->where('sch_id', $shopId)->get()->getRow();

            $data['showUnit'] = productIdByDefaultStoreUnit($id);
            $unitCategory = get_data_by_id('unit_categories_id','units','units_id',$data['showUnit']);
            $data['units'] = DB()->table('units')->where('unit_categories_id',$unitCategory)->orderBy('conversion_factor','DESC')->get()->getResult();

            // Serial & warranty tab data (visible only when a flag is ON)
            $data['serialTab'] = product_serial_tab_data($data['product']);

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['update'] == 1) {
                echo view('Admin/Products/update', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * @description This method update products general
     * @return void
     */
    public function general_update()
    {
        $userId = $this->session->userId;

        $data['prod_id'] = $this->request->getPost('prod_id');
        $data['name'] = $this->request->getPost('name');
        $data['supplier_id'] = $this->request->getPost('supplier_id');
        $serial_status = $this->request->getPost('serial_status');
        $data['serial_status'] = ($serial_status == 1) ? '1' : '0';
        $warranty_status = $this->request->getPost('warranty_status');
        $data['warranty_status'] = ($warranty_status == 1) ? '1' : '0';
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'name' => ['label' => 'name', 'rules' => 'required|only_numeric_not_allow'],
            'supplier_id' => ['label' => 'supplier_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description Save handler for the product update Serial tab. Renames /
     *              adds in-stock serials and applies the company warranty end
     *              date per serial row (each row may hold its own date; the
     *              shared field acts as default/fallback). Locked
     *              (sold/returned/damaged) serials are never touched.
     *              Responds as JSON for the tab's AJAX form.
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    
    public function serial_update()
    {
        $json = \Config\Services::response()->setContentType('application/json');
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;

        $prodId = (int) $this->request->getPost('prod_id');
        $product = DB()->table('products')->where('prod_id', $prodId)->where('sch_id', $shopId)->get()->getRow();
        if (empty($product)) {
            return $json->setJSON(['status' => 'error', 'message' => 'Product not found.']);
        }

        // Flags come from the General tab toggle switches (posted with the serial form)
        $serialPost   = $this->request->getPost('serial_status');
        $warrantyPost = $this->request->getPost('warranty_status');
        $serialStatus   = ($serialPost === null)   ? (int) ($product->serial_status ?? 0)   : ((int) $serialPost === 1 ? 1 : 0);
        $warrantyStatus = ($warrantyPost === null) ? (int) ($product->warranty_status ?? 0) : ((int) $warrantyPost === 1 ? 1 : 0);

        if ($serialStatus !== 1 && $warrantyStatus !== 1) {
            return $json->setJSON(['status' => 'error', 'message' => 'Serial/warranty tracking is disabled for this product.']);
        }

        // Aggregate all stock relations of this product
        $relations = product_all_store_relations($prodId);
        if (empty($relations)) {
            return $json->setJSON(['status' => 'error', 'message' => 'No stock found for this product.']);
        }

        $relationIds = array_map(function ($r) {
            return (int) $r['product_stock_relation_id'];
        }, $relations);

        // New serials will be attached to the default store's stock row
        $targetRelationId = product_serial_target_relation_id($relations);

        $totalQty = 0;
        foreach ($relations as $rel) {
            $totalQty += (int) round((float) $rel['quantity']);
        }

        // Shared warranty date (acts as fallback for serial rows)
        $warrantyDate = trim((string) ($this->request->getPost('warrantyData') ?: ''));
        if ($warrantyDate !== '' && strtotime($warrantyDate) === false) {
            $warrantyDate = '';
        }

        if ($warrantyStatus === 1 && $serialStatus !== 1 && $warrantyDate === '') {
            return $json->setJSON(['status' => 'error', 'message' => 'Company warranty end date is required.']);
        }

        $warrantyValue = ($warrantyStatus === 1) ? ($warrantyDate !== '' ? $warrantyDate : null) : null;

        $errors  = [];
        $serials = [];
        $rows    = [];

        if ($serialStatus === 1) {
            $decoded = json_decode((string) $this->request->getPost('serialData'), true);
            $rows = is_array($decoded) ? $decoded : [];

            // Never process an oversized payload (memory safety)
            if (count($rows) > SERIAL_ENTRY_ROW_LIMIT) {
                return $json->setJSON(['status' => 'error', 'message' => 'Only up to ' . SERIAL_ENTRY_ROW_LIMIT . ' serial rows can be saved at a time.']);
            }

            $inStock = DB()->table('product_serial')
                ->whereIn('product_stock_relation_id', $relationIds)
                ->where('status', 'in_stock')
                ->limit(SERIAL_ENTRY_ROW_LIMIT + 1)
                ->get()
                ->getResultArray();

            $inStockMap = [];
            foreach ($inStock as $row) {
                $inStockMap[(int) $row['product_serial_id']] = $row['serial_number'];
            }

            $lockedCount = DB()->table('product_serial')
                ->whereIn('product_stock_relation_id', $relationIds)
                ->where('status !=', 'in_stock')
                ->countAllResults();

            $expectedCount = max(0, $totalQty - $lockedCount);

            // Bulk-stocked product: one row per unit is not renderable/savable
            if ($expectedCount > SERIAL_ENTRY_ROW_LIMIT) {
                return $json->setJSON(['status' => 'error', 'message' => 'The editable quantity (' . $expectedCount . ') exceeds the maximum of ' . SERIAL_ENTRY_ROW_LIMIT . ' serial rows, so serial numbers cannot be saved for this product.']);
            }

            // Validate serials
            foreach ($rows as $r) {
                $sn = trim((string) ($r['serial'] ?? ''));
                if ($sn === '') {
                    $errors[] = 'All serial numbers must be filled.';
                    break;
                }
                $serials[] = $sn;
            }

            // Warranty date validation per row
            if ($warrantyStatus === 1 && empty($errors)) {
                foreach ($rows as $r) {
                    $rw = trim((string) ($r['warranty'] ?? ''));
                    if ($rw !== '' && strtotime($rw) === false) {
                        $rw = '';
                    }
                    if ($rw === '' && $warrantyDate === '') {
                        $errors[] = 'Company warranty end date is required for all serials.';
                        break;
                    }
                }
            }

            if (empty($serials) && $expectedCount > 0) {
                $errors[] = 'Serial numbers are required.';
            } elseif (count($serials) !== $expectedCount) {
                $errors[] = 'Serial count (' . count($serials) . ') must equal the editable quantity (' . $expectedCount . ').';
            }

            if (count($serials) !== count(array_unique($serials))) {
                $errors[] = 'Duplicate serial numbers are not allowed.';
            }

            // Check existing IDs and collect new serials
            $keepIds    = [];
            $newSerials = [];

            foreach ($rows as $r) {
                $sn = trim((string) ($r['serial'] ?? ''));
                if ($sn === '') {
                    continue;
                }

                $sid = (int) ($r['id'] ?? 0);

                if ($sid > 0) {
                    if (!isset($inStockMap[$sid])) {
                        $errors[] = "Serial '{$sn}' is no longer available (it may have been sold or edited).";
                    } else {
                        $keepIds[] = $sid;
                        if ($inStockMap[$sid] !== $sn) {
                            $newSerials[] = $sn;
                        }
                    }
                } else {
                    $newSerials[] = $sn;
                }
            }

            // Global uniqueness check for new / renamed serials
            foreach ($newSerials as $ns) {
                if (DB()->table('product_serial')->where('serial_number', $ns)->countAllResults() > 0) {
                    $errors[] = "Serial '{$ns}' already exists.";
                }
            }
        }

        if (!empty($errors)) {
            return $json->setJSON([
                'status'  => 'error',
                'message' => implode('<br>', array_unique($errors))
            ]);
        }

        // ===================== DATABASE TRANSACTION =====================
        DB()->transStart();

        // Save switch status
        DB()->table('products')
            ->where('prod_id', $prodId)
            ->update([
                'serial_status'   => $serialStatus === 1 ? '1' : '0',
                'warranty_status' => $warrantyStatus === 1 ? '1' : '0',
                'updatedBy'       => $userId,
            ]);

       
        if ($serialStatus === 1) {

            // Current in-stock data
            $existingById         = [];
            $existingWarrantyById = [];
            $existingRelationById = [];

            foreach (DB()->table('product_serial')
                        ->whereIn('product_stock_relation_id', $relationIds)
                        ->where('status', 'in_stock')
                        ->get()
                        ->getResultArray() as $row) {
                $id = (int) $row['product_serial_id'];
                $existingById[$id]         = $row['serial_number'];
                $existingWarrantyById[$id] = (string) ($row['company_warranty_end_date'] ?? '');
                $existingRelationById[$id] = (int) $row['product_stock_relation_id'];
            }

            // 1. Delete dropped in-stock serials
            $builder = DB()->table('product_serial')
                ->whereIn('product_stock_relation_id', $relationIds)
                ->where('status', 'in_stock');

            if (!empty($keepIds)) {
                $builder->whereNotIn('product_serial_id', $keepIds);
            }
            $builder->delete();

            // 2. Update existing + collect new serials
            $toInsert = [];

            foreach ($rows as $r) {
                $sn  = trim((string) ($r['serial'] ?? ''));
                $sid = (int) ($r['id'] ?? 0);

                if ($sn === '') continue;

                // Effective warranty
                $rowWarranty = trim((string) ($r['warranty'] ?? ''));
                if ($rowWarranty !== '' && strtotime($rowWarranty) === false) {
                    $rowWarranty = '';
                }
                if ($rowWarranty === '') {
                    $rowWarranty = $warrantyDate;
                }
                $rowWarrantyVal = ($warrantyStatus === 1 && $rowWarranty !== '') ? $rowWarranty : null;

                if ($sid > 0 && isset($existingById[$sid])) {
                    // ===== UPDATE existing =====
                    $dateChanged = $rowWarrantyVal !== null && $existingWarrantyById[$sid] !== $rowWarrantyVal;

                    if ($existingById[$sid] !== $sn || $dateChanged) {
                        $update = [
                            'serial_number' => $sn,
                            'updatedBy'     => $userId,
                            'updatedDtm'    => date('Y-m-d H:i:s'),
                        ];
                        if ($rowWarrantyVal !== null) {
                            $update['company_warranty_end_date'] = $rowWarrantyVal;
                        }

                        DB()->table('product_serial')
                            ->where('product_serial_id', $sid)
                            ->update($update);
                    }
                } else {
                    // ===== NEW serial =====
                    $toInsert[] = [
                        'serial'   => $sn,
                        'warranty' => $rowWarrantyVal,
                    ];
                }
            }

            // ======================================================
            // 3. NEW: Distribute new serials according to quantity
            // ======================================================
            if (!empty($toInsert)) {

                // Step A: Calculate remaining capacity for each relation
                $remainingCapacity = [];

                foreach ($relations as $rel) {
                    $relId = (int) $rel['product_stock_relation_id'];
                    $qty   = (int) round((float) $rel['quantity']);

                    // Already sold/locked
                    $locked = DB()->table('product_serial')
                        ->where('product_stock_relation_id', $relId)
                        ->where('status !=', 'in_stock')
                        ->countAllResults();

                    // Currently kept in-stock serials of this relation
                    $kept = 0;
                    foreach ($keepIds as $kid) {
                        if (isset($existingRelationById[$kid]) && $existingRelationById[$kid] === $relId) {
                            $kept++;
                        }
                    }

                    $remainingCapacity[$relId] = max(0, $qty - $locked - $kept);
                }

                // Step B: Distribute new serials
                $insertIndex = 0;
                $totalNew    = count($toInsert);

                foreach ($remainingCapacity as $relId => $slots) {
                    for ($i = 0; $i < $slots; $i++) {
                        if ($insertIndex >= $totalNew) {
                            break 2;
                        }

                        $item = $toInsert[$insertIndex];

                        DB()->table('product_serial')->insert([
                            'product_stock_relation_id' => $relId,
                            'serial_number'             => $item['serial'],
                            'company_warranty_end_date' => $item['warranty'],
                            'status'                    => 'in_stock',
                            'createdDtm'                => date('Y-m-d H:i:s'),
                            'createdBy'                 => $userId,
                        ]);

                        $insertIndex++;
                    }
                }

                // Step C: Fallback (যদি কোনো serial বাকি থাকে)
                if ($insertIndex < $totalNew) {
                    // সবচেয়ে বেশি remaining capacity ওয়ালা relation খুঁজে বের করি
                    arsort($remainingCapacity);
                    $fallbackRelId = array_key_first($remainingCapacity) ?: $relationIds[0];

                    for ($i = $insertIndex; $i < $totalNew; $i++) {
                        $item = $toInsert[$i];

                        DB()->table('product_serial')->insert([
                            'product_stock_relation_id' => $fallbackRelId,
                            'serial_number'             => $item['serial'],
                            'company_warranty_end_date' => $item['warranty'],
                            'status'                    => 'in_stock',
                            'createdDtm'                => date('Y-m-d H:i:s'),
                            'createdBy'                 => $userId,
                        ]);
                    }
                }
            }
        }


        // Warranty-only mode: only update date on existing in-stock serials
        if ($serialStatus !== 1 && $warrantyStatus === 1) {
            DB()->table('product_serial')
                ->whereIn('product_stock_relation_id', $relationIds)
                ->where('status', 'in_stock')
                ->update([
                    'company_warranty_end_date' => $warrantyValue,
                    'updatedBy'                 => $userId,
                    'updatedDtm'                => date('Y-m-d H:i:s'),
                ]);
        }

        $committed = DB()->transComplete();

        if ($committed === false && DB()->transEnabled) {
            return $json->setJSON([
                'status'  => 'error',
                'message' => 'Database update failed, nothing was saved. Please try again.'
            ]);
        }

        // Return fresh data for frontend
        $serials_out = [];
        if ($serialStatus === 1) {
            $serials_out = DB()->table('product_serial')
                ->whereIn('product_stock_relation_id', $relationIds)
                ->where('status', 'in_stock')
                ->orderBy('product_serial_id', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $json->setJSON([
            'status'       => 'success',
            'message'      => 'Serial & warranty updated successfully.',
            'serials'      => $serials_out,
            'warrantyDate' => $warrantyValue ?: '',
        ]);
    }

    /**
     * @description This method update products personal
     * @return void
     */
    public function personal_update()
    {
        $userId = $this->session->userId;

        $data['prod_id'] = $this->request->getPost('prod_id');
        $data['prod_cat_id'] = $this->request->getPost('sub_cat_id');
        $data['brand_id'] = $this->request->getPost('brand_id');
        $data['size'] = $this->request->getPost('size');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'brand_id' => ['label' => 'brand_id', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }
    public function unit_update()
    {
        $userId = $this->session->userId;
        $shopId = $this->session->shopId;
        $data['prod_id'] = $this->request->getPost('prod_id');
        $sell_units = $this->request->getPost('sell_units[]');
        $data['unit'] = $this->request->getPost('unit');
        $data['updatedBy'] = $userId;

        $this->validation->setRules([
            'unit' => ['label' => 'Unit', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            $store = DB()->table('stores')->where('sch_id',$shopId)->where('is_default','1')->get()->getRow();
            DB()->table('product_stock_relation')
                ->where('store_id',$store->store_id)
                ->where('product_id',$data['prod_id'])
                ->update(['unit' => $data['unit']]);


            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update(['sale_units'=>json_encode($sell_units)])) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        }
    }

    /**
     * @description This method update products photo
     * @return void
     */
    public function photo_update()
    {

        $data['prod_id'] = $this->request->getPost('prod_id');

        if (!empty($_FILES['picture']['name'])) {
            $target_dir = FCPATH . '/uploads/product_image/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777);
            }

            //old image unlink
            $old_img = get_data_by_id('picture', 'products', 'prod_id', $data['prod_id']);
            if (!empty($old_img)) {
                unlink($target_dir . $old_img);
            }

            //new image uplode
            $pic = $this->request->getFile('picture');
            $namePic = $pic->getRandomName();
            $pic->move($target_dir, $namePic);
            $pro_nameimg = 'product_' . $pic->getName();
            $this->crop->withFile($target_dir .  $namePic)->fit(300, 300, 'center')->save($target_dir .  $pro_nameimg);
            unlink($target_dir .  $namePic);
            $data['picture'] = $pro_nameimg;


            $productTable = DB()->table('products');
            if ($productTable->where('prod_id', $data['prod_id'])->update($data)) {
                print '<div class="alert alert-success alert-dismissible" role="alert"> Update data successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else {
                print '<div class="alert alert-danger alert-dismissible" role="alert"> something went wrong  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }

        } else {
            print '<div class="alert alert-danger alert-dismissible" role="alert"> please select a image  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }


    }

    /**
     * @description This method update products barcode
     * @return void
     */
    public function barcode()
    {
        $data['barcodeqty'] = $this->request->getPost('barcodeqty');

        $data['generator'] = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $tabGenSet = DB()->table('gen_settings');

        $sizeBarcode = $tabGenSet->where('label', 'barcode_img_size')->get()->getRow()->value;
        $data['barcodeSize'] = empty($sizeBarcode) ? '100' : $sizeBarcode;

        $typeBarcode = $tabGenSet->where('label', 'barcode_type')->get()->getRow()->value;
        $data['barcodeType'] = empty($typeBarcode) ? 'C128' : $typeBarcode;

        echo view('Admin/header');
        echo view('Admin/sidebar');
        echo view('Admin/Products/barcode', $data);
        echo view('Admin/footer');
    }
    public function add_existing_product(){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $productTable = DB()->table('products');
            $data['products_data'] = $productTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id',$shopId)->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/add_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    public function add_action(){
        $shopId = $this->session->shopId;
        $userId = $this->session->userId;

        $categories_id = $this->request->getPost('categories_id');
        $data['salePrice'] = $this->request->getPost('selling_price');
        $data['sale_unit'] = $this->request->getPost('sale_unit');
        $purchase_units_price = $this->request->getPost('purchase_units_price');
        $sell_unit_price = $this->request->getPost('sell_unit_price');

        $data['prod_cat_id'] = $this->request->getPost('sub_category');
        $data['name'] = $this->request->getPost('name');
        $data['unit'] = $data['sale_unit'];
        $data['price'] = $this->request->getPost('price');
        $data['selling_price'] = $data['salePrice'] ;


        $this->validation->setRules([
            'prod_cat_id' => ['label' => 'Category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'selling_price' => ['label' => 'salePrice', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {

            // ---- Serial & Warranty validation (start) ----
            $serialStatus = (int)($this->request->getPost('serial_status') ?: 0);
            $warrantyStatus = (int)($this->request->getPost('warranty_status') ?: 0);
            $serialDataRaw = $this->request->getPost('serialData') ?: '';
            $warrantyDataVal = $this->request->getPost('warrantyData') ?: '';

            if ($serialStatus === 1) {
                $serials = json_decode($serialDataRaw, true);
                if (!is_array($serials) || count($serials) === 0) {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Serial numbers are required.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    return;
                }
                foreach ($serials as $s) {
                    if ($s === null || trim((string)$s) === '') {
                        print '<div class="alert alert-danger alert-dismissible" role="alert">All serial numbers must be filled.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        return;
                    }
                }
                if (count($serials) !== count(array_unique($serials))) {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Duplicate serial numbers are not allowed.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    return;
                }
            }

            if ($warrantyStatus === 1) {
                if (empty($warrantyDataVal) || strtotime($warrantyDataVal) === false) {
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Company warranty end date is required.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    return;
                }
            }
            // ---- Serial & Warranty validation (end) ----

            DB()->transStart();

            $totalQty = 0;
            if (!empty($categories_id)) {
                $query = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
                $units_id = json_decode($query->purchase_units);

                $unit = array();
                $units = DB()->table('units')->whereIn('units_id', $units_id)->orderBy('conversion_factor', 'DESC')->get()->getResult();
                foreach ($units as $val) {
                    $nameUnit = strtolower(str_replace(' ', '_', $val->name));
                    $unit[$nameUnit] = $this->request->getPost($nameUnit);
                    if (!empty($unit[$nameUnit])) {
                        $totalQty += $val->conversion_factor * $unit[$nameUnit];
                    }
                }
            }

            //purchase price make
            $basePurchasePrice = 0;
            $unitsPur = DB()->table('units')->where('units_id', $purchase_units_price)->get()->getRow();
            if (!empty($unitsPur)){
                $basePurchasePrice = $data['price']/$unitsPur->conversion_factor;
            }
            $purchasePrice = $basePurchasePrice;

            //sale price make
            $baseSalePrice = 0;
            $unitsSale = DB()->table('units')->where('units_id', $sell_unit_price)->get()->getRow();
            if (!empty($unitsSale)){
                $baseSalePrice = $data['salePrice']/$unitsSale->conversion_factor;
            }
            $salePrice = $baseSalePrice;


            //get default store
            $storeTab = DB()->table('stores');
            $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();

            //insert product
            $queryUnit = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
            $dataProduct['prod_cat_id'] = $data['prod_cat_id'];
            $dataProduct['name'] = $data['name'];
            $dataProduct['purchase_units'] = $queryUnit->purchase_units;
            $dataProduct['sale_units'] = $queryUnit->sell_units;
            $dataProduct['purchase_date'] = date('Y-m-d H:i:s');
            $dataProduct['sch_id'] = $shopId;
            $dataProduct['serial_status'] = $serialStatus == 1 ? '1' : '0';
            $dataProduct['warranty_status'] = $warrantyStatus == 1 ? '1' : '0';
            $dataProduct['createdBy'] = $this->session->userId;
            $dataProduct['createdDtm'] = date('Y-m-d H:i:s');
            $productTable = DB()->table('products');
            $productTable->insert($dataProduct);
            $prodId = DB()->insertID();

            //product stock relation insert
            DB()->table('product_stock_relation')->insert([
                'sch_id' => $shopId,
                'store_id' => $store->store_id,
                'product_id' => $prodId,
                'quantity' => $totalQty,
                'unit' => $data['sale_unit'],
                'purchase_price' => $purchasePrice,
                'selling_price' => $salePrice,
            ]);
            $product_stock_relation_id = DB()->insertID();

            // ---- Insert serial numbers & warranty (start) ----
            if ($serialStatus === 1 && isset($serials) && !empty($serials)) {
                // Validate serial count matches total qty.
                if (count($serials) !== (int)$totalQty) {
                    DB()->transRollback();
                    print '<div class="alert alert-danger alert-dismissible" role="alert">Serial count (' . count($serials) . ') must equal the total quantity (' . (int)$totalQty . ').<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    return;
                }
                $warrantyDate = ($warrantyStatus === 1 && !empty($warrantyDataVal)) ? $warrantyDataVal : null;
                $serialRows = [];
                foreach ($serials as $sn) {
                    $sn = trim((string)$sn);
                    if ($sn === '') { continue; }
                    $serialRows[] = [
                        'product_stock_relation_id' => $product_stock_relation_id,
                        'serial_number'             => $sn,
                        'company_warranty_end_date' => $warrantyDate,
                        'status'                    => 'in_stock',
                        'createdDtm'                => date('Y-m-d h:i:s'),
                        'createdBy'                 => $userId,
                    ];
                }
                if (!empty($serialRows)) {
                    DB()->table('product_serial')->insertBatch($serialRows);
                }
            }
            // ---- Insert serial numbers & warranty (end) ----

            //total amount product
            $totalAmountPro = $purchasePrice * $totalQty;

            //capital last balance
            $oldCapital = get_data_by_id('capital', 'shops', 'sch_id', $shopId);
            $newCapital = $oldCapital - $totalAmountPro;

            // capital ledger data insert
            $cpitalLedData = array(
                'sch_id' => $shopId,
                'particulars' => 'New Existing Products Add Amount',
                'trangaction_type' => 'Cr.',
                'amount' => $totalAmountPro,
                'rest_balance' => $newCapital,
                'createdBy' => $this->session->userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_capitalTable = DB()->table('ledger_capital');
            $ledger_capitalTable->insert($cpitalLedData);
            // capital ledger data insert


            //stock last balance
            $oldStock = get_data_by_id('stockAmount', 'shops', 'sch_id', $shopId);
            $newStock = $oldStock + $totalAmountPro;

            //Stock ledger data insert
            $stockLedgData = array(
                'sch_id' => $shopId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'New Existing Products Add Amount',
                'amount' => $totalAmountPro,
                'rest_balance' => $newStock,
                'createdBy' => $this->session->userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $tabledger_stock = DB()->table('ledger_stock');
            $tabledger_stock->insert($stockLedgData);
            //Stock ledger data insert

            //update capital and stock
            $dataCapital['stockAmount'] = $newStock;
            $dataCapital['capital'] = $newCapital;
            $tableCapital = DB()->table('shops');
            $tableCapital->where('sch_id', $shopId)->update($dataCapital);



            //purchase process
            DB()->table('purchase')->insert([
                'sch_id' => $shopId,
                'date' => date('Y-m-d'),
                'amount' => $totalAmountPro,
                'due' => '0',
                'createdBy' => $userId,
            ]);
            $purchaseId = DB()->insertID();


            $purchaseBal = get_data_by_id('purchase_balance', 'shops', 'sch_id', $shopId);
            $restBalPurc = $purchaseBal + $totalAmountPro;

            $purUpdata = array('purchase_balance' => $restBalPurc);
            $shopPurBalTable = DB()->table('shops');
            $shopPurBalTable->where('sch_id', $shopId)->update($purUpdata);

            $purLedgData = array(
                'sch_id' => $shopId,
                'purchase_id' => $purchaseId,
                'trangaction_type' => 'Dr.',
                'particulars' => 'Existing Products purchase amount',
                'amount' => $totalAmountPro,
                'rest_balance' => $restBalPurc,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $ledger_purchaseTable = DB()->table('ledger_purchase');
            $ledger_purchaseTable->insert($purLedgData);
            // purchase balance update and ledger create (end)

            //purchase item
            $total_price = $totalQty * $purchasePrice;
            $purchaseData = array(
                'purchase_id' => $purchaseId,
                'prod_id' => $prodId,
                'product_stock_relation_id' => $product_stock_relation_id,
                'purchase_price' => $purchasePrice,
                'quantity' => $totalQty,
                'total_price' => $total_price,
                'createdBy' => $userId,
                'createdDtm' => date('Y-m-d h:i:s')
            );
            $purchase_itemTab = DB()->table('purchase_item');
            $purchase_itemTab->insert($purchaseData);

            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert"> Product added successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        }
    }

    public function add_product(){
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $shopId = $this->session->shopId;
            $productTable = DB()->table('products');
            $data['products_data'] = $productTable->where('sch_id', $shopId)->where('deleted IS NULL')->get()->getResult();

            $table = DB()->table('unit_set');
            $data['unit_set'] = $table->where('sch_id',$shopId)->get()->getResult();

            $data['menu'] = view('Admin/menu_stock');
            // All Permissions
            //$perm = array('create','read','update','delete','mod_access');
            $perm = $this->permission->module_permission_list($role_id, $this->module_name);
            foreach ($perm as $key => $val) {
                $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
            }
            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Products/new_product', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }
    public function add_action_product(){
        $shopId = $this->session->shopId;

        $categories_id = $this->request->getPost('categories_id');
        $data['salePrice'] = $this->request->getPost('selling_price');
        $data['sale_unit'] = $this->request->getPost('sale_unit');
        $purchase_units_price = $this->request->getPost('purchase_units_price');
        $sell_unit_price = $this->request->getPost('sell_unit_price');

        $data['prod_cat_id'] = $this->request->getPost('sub_category');
        $data['supplier_id'] = $this->request->getPost('supplier_id');
        $data['name'] = $this->request->getPost('name');
        $data['unit'] = $data['sale_unit'];
        $data['price'] = $this->request->getPost('price');
        $data['selling_price'] = $data['salePrice'] ;


        $this->validation->setRules([
            'prod_cat_id' => ['label' => 'Category', 'rules' => 'required'],
            'name' => ['label' => 'name', 'rules' => 'required'],
            'unit' => ['label' => 'unit', 'rules' => 'required'],
            'price' => ['label' => 'price', 'rules' => 'required'],
            'selling_price' => ['label' => 'salePrice', 'rules' => 'required'],
        ]);

        if ($this->validation->run($data) == FALSE) {
            print '<div class="alert alert-danger alert-dismissible" role="alert">' . $this->validation->listErrors() . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
            DB()->transStart();

                $totalQty = 0;

                //purchase price make
                $basePurchasePrice = 0;
                $unitsPur = DB()->table('units')->where('units_id', $purchase_units_price)->get()->getRow();
                if (!empty($unitsPur)){
                    $basePurchasePrice = $data['price']/$unitsPur->conversion_factor;
                }
                $purchasePrice = $basePurchasePrice;

                //sale price make
                $baseSalePrice = 0;
                $unitsSale = DB()->table('units')->where('units_id', $sell_unit_price)->get()->getRow();
                if (!empty($unitsSale)){
                    $baseSalePrice = $data['salePrice']/$unitsSale->conversion_factor;
                }
                $salePrice = $baseSalePrice;


                //get default store
                $storeTab = DB()->table('stores');
                $store = $storeTab->where('sch_id', $shopId)->where('is_default', 1)->get()->getRow();

                //insert product
                $queryUnit = DB()->table('unit_set')->where('unit_set_id',$categories_id)->get()->getRow();
                $dataProduct['prod_cat_id'] = $data['prod_cat_id'];
                $dataProduct['name'] = $data['name'];
                $dataProduct['purchase_units'] = $queryUnit->purchase_units;
                $dataProduct['sale_units'] = $queryUnit->sell_units;
                $dataProduct['purchase_date'] = date('Y-m-d H:i:s');
                $dataProduct['sch_id'] = $shopId;
                $dataProduct['createdBy'] = $this->session->userId;
                $dataProduct['createdDtm'] = date('Y-m-d H:i:s');
                $productTable = DB()->table('products');
                $productTable->insert($dataProduct);
                $prodId = DB()->insertID();

                //product stock relation insert
                DB()->table('product_stock_relation')->insert([
                    'sch_id' => $shopId,
                    'store_id' => $store->store_id,
                    'product_id' => $prodId,
                    'supplier_id' => $data['supplier_id'],
                    'is_default' =>'1',
                    'quantity' => $totalQty,
                    'unit' => $data['sale_unit'],
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $salePrice,
                ]);

            DB()->transComplete();

            print '<div class="alert alert-success alert-dismissible" role="alert"> Product added successfully  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        }
    }

}