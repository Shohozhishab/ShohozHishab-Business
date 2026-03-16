<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Admin extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id' => 1,
                'email' => 'imranertaza12@gmail.com',
                'password' => sha1(12345678),
                'name' => 'Syed Imran Ertaza',
                'mobile' => '01924329315',
                'address' => 'Noapara, Abhaynagar, Jessore',
                'pic' => 'profile_1664976903_39bd3bf1ddc2da4682f0.jpg',
                'country' => null,
                'ComName' => null,
                'role_id' => 1,
                'status' => 1,
                'createdBy' => 1,
                'updatedBy' => 1,
            ]
        ];

        // Using Query Builder
        $this->db->table('admin')->insertBatch($data);
    }
}
