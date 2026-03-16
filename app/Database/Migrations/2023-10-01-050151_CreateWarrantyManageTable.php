<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateWarrantyManageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'warranty_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'product_name' => [
                'type'           => 'varchar',
                'constraint'     => 55,
            ],
            'receive_date' => [
                'type'           => 'date',
            ],
            'delivery_date' => [
                'type'           => 'date',
            ],
            'customer_address' => [
                'type'           => 'varchar',
                'constraint'     => 55,
            ],
            'customer_name' => [
                'type'           => 'varchar',
                'constraint'     => 55,
            ],
            'mobile' => [
                'type'           => 'INT',
                'constraint'     => 11,
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
        $this->forge->addKey('warranty_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->createTable('warranty_manage');
    }

    public function down()
    {
        $this->forge->dropTable('warranty_manage');
    }
}
