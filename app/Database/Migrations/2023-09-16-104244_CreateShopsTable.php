<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateShopsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'           => 'varchar',
                'constraint'     => 155,
            ],
            'email' => [
                'type'           => 'varchar',
                'constraint'     => 30,
                'default' => null,
                'null' => true,
            ],
            'cash' => [
                'type'           => 'float',
                'constraint'     => 11,
                'unsigned'       => true,
                'comment'        => 'This is the nagad cash of the shop owner.',
            ],
            'capital' => [
                'type'    => 'float',
                'default' => null,
                'null' => true,
            ],
            'profit' => [
                'type'    => 'float',
                'default' => null,
                'null' => true,
            ],
            'stockAmount' => [
                'type'    => 'float',
                'default' => null,
                'null' => true,
            ],
            'expense' => [
                'type'    => 'float',
                'default' => null,
                'null' => true,
            ],
            'discount' => [
                'type'    => 'float',
                'default' => null,
                'null' => true,
            ],
            'purchase_balance' => [
                'type'    => 'float',
            ],
            'sale_balance' => [
                'type'    => 'float',
            ],
            'address' => [
                'type'    => 'text',
                'default' => null,
                'null' => true,
            ],
            'mobile' => [
                'type'    => 'bigint',
                'default' => null,
                'null' => true,
            ],
            'comment' => [
                'type'    => 'text',
                'default' => null,
                'null' => true,
            ],
            'logo' => [
                'type'    => 'varchar',
                'constraint' => 155,
                'default' => null,
                'null' => true,
            ],
            'image' => [
                'type'    => 'varchar',
                'constraint' => 155,
                'default' => null,
                'null' => true,
            ],
            'package_id' => [
                'type'    => 'int',
                'default' => null,
                'null' => true,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '1',
            ],
            'opening_status' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '0',
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
        $this->forge->addKey('sch_id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('package_id');
        $this->forge->createTable('shops');
    }

    public function down()
    {
        $this->forge->dropTable('shops');
    }
}
