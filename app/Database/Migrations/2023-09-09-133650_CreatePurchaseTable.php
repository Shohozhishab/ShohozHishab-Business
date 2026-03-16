<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePurchaseTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'supplier_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'amount' => [
                'type'           => 'float',
            ],
            'nagad_paid' => [
                'type'           => 'float',
                'default' => null,
                'null' => true,
            ],
            'bank_paid' => [
                'type'           => 'float',
                'default' => null,
                'null' => true,
            ],
            'bank_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'due' => [
                'type'           => 'float',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
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
        $this->forge->addKey('purchase_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('supplier_id');
        $this->forge->createTable('purchase');
    }

    public function down()
    {
        $this->forge->dropTable('purchase');
    }
}
