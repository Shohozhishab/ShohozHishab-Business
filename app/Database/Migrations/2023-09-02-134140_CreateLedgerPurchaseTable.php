<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLedgerPurchaseTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ledgPurch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'purchase_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'rtn_purchase_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'particulars' => [
                'type'  => 'text',
            ],
            'trangaction_type' => [
                'type' => 'enum',
                'constraint' => ['Dr.', 'Cr.'],
                'default' => 'Cr.',
            ],
            'amount' => [
                'type' => 'float',
            ],
            'rest_balance' => [
                'type' => 'float',
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
        $this->forge->addKey('ledgPurch_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('purchase_id');
        $this->forge->addKey('rtn_purchase_id');
        $this->forge->createTable('ledger_purchase');
    }

    public function down()
    {
        $this->forge->dropTable('ledger_purchase');
    }
}
