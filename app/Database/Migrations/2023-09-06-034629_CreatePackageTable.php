<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePackageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'package_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'package_name' => [
                'type'           => 'varchar',
                'constraint'     => 55,
            ],
            'package_all_permission' => [
                'type'           => 'text',
            ],
            'package_admin_permission' => [
                'type'           => 'text',
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['Active', 'Inactive'],
                'default' => 'Active',
            ],
            'createdDtm' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'updatedDtm DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'deletedRole' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addKey('package_id', true);
        $this->forge->createTable('package');
    }

    public function down()
    {
        $this->forge->dropTable('package');
    }
}
