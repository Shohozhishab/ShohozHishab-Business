<?php

namespace App\Controllers\Admin;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Warranty Short List – AJAX body.
 *
 * Loaded into the content pane by the sidebar's showData() helper. Reuses the
 * data-building logic from Warranty_short_list but renders only the inner view.
 */
class Warranty_short_list_ajax extends Warranty_short_list
{
    /**
     * @description Warranty short list view (AJAX body only)
     * @return RedirectResponse|void
     */
    public function index()
    {
        $isLoggedIn = $this->session->isLoggedIn;
        if (!isset($isLoggedIn) || $isLoggedIn != TRUE) {
            return redirect()->to(site_url('Admin/login'));
        } else {
            $data = $this->buildData();
            if (isset($data['mod_access']) and $data['mod_access'] == 1) {
                echo view('Admin/Warranty_short_list/list', $data);
            } else {
                echo view('no_permission');
            }
        }
    }
}
