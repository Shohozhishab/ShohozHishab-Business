<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GeneralSettingsSuper extends Seeder
{
    public function run()
    {
        $data = [
            [
                'settings_id_sup' => 1,
                'label' => 'loading_message',
                'value' => 'Please wait until it is processing...',
                'createdBy' => 0,
            ],
            [
                'settings_id_sup' => 2,
                'label' => 'site_title',
                'value' => 'Shohoz Hishab | Accounting management system',
                'createdBy' => 0,
            ]
        ];

        // Using Query Builder
        $this->db->table('gen_settings_super')->insertBatch($data);
    }
}
