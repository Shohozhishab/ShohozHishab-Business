<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Permission;
use App\Models\Warranty_short_listModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Warranty Short List module.
 *
 * Shows product serial units whose company warranty (company_warranty_end_date)
 * expires within the next 2 months. Available both as a flat list (tree view)
 * and grouped by product.
 */
class Warranty_short_list extends BaseController
{
    protected $warrantyModel;
    protected $permission;
    protected $validation;
    protected $session;
    private $module_name = 'Warranty_short_list';

    public function __construct()
    {
        $this->warrantyModel = new Warranty_short_listModel();
        $this->permission    = new Permission();
        $this->validation    = \Config\Services::validation();
        $this->session       = \Config\Services::session();
    }

    /**
     * @description Warranty short list view (full page)
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        $role_id    = $this->session->role;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data = $this->buildData();

            echo view('Admin/header');
            echo view('Admin/sidebar');
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Warranty_short_list/list', $data);
            } else {
                echo view('no_permission');
            }
            echo view('Admin/footer');
        }
    }

    /**
     * Shared data builder used by both the full-page and AJAX controllers.
     *
     * @return array
     */
    protected function buildData()
    {
        $shopId = $this->session->shopId;
        $role_id = $this->session->role;

        // Smart-search + view mode come from the query string so filters survive reloads.
        $search   = trim((string) $this->request->getGet('search'));
        $viewMode = $this->request->getGet('view') === 'list' ? 'list' : 'group';

        $data = [
            'search'    => $search,
            'viewMode'  => $viewMode,
            'windowInfo' => $this->warrantyModel->get_window_info(),
            'rows'      => $this->warrantyModel->get_expiring_warranty($shopId, $search),
            'grouped'   => $this->warrantyModel->get_expiring_warranty_grouped($shopId, $search),
            'menu'      => view('Admin/menu_report'),
        ];

        // All Permissions
        $perm = $this->permission->module_permission_list($role_id, $this->module_name);
        foreach ($perm as $key => $val) {
            $data[$key] = $this->permission->have_access($role_id, $this->module_name, $key);
        }

        return $data;
    }
}
