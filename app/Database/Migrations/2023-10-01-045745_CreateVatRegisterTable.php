<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateVatRegisterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'vat_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'name' => [
                'type'           => 'varchar',
                'constraint'     => 155,
            ],
            'vat_register_no' => [
                'type'           => 'varchar',
                'constraint'     => 155,
                'default' => null,
                'null' => true,
            ],
            'balance' => [
                'type'           => 'float',
            ],
            'is_default' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '1',
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
        $this->forge->addKey('vat_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->createTable('vat_register');
    }

    public function down()
    {
        $this->forge->dropTable('vat_register');
    }
}
