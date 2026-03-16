<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLedgerSuppliersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ledg_sup_id' => [
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
            'money_receipt_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'purchase_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'trans_id' => [
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
            'chaque_id' => [
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
                'unsigned' => true,
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
        $this->forge->addKey('ledg_sup_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('money_receipt_id');
        $this->forge->addKey('purchase_id');
        $this->forge->addKey('trans_id');
        $this->forge->addKey('rtn_purchase_id');
        $this->forge->createTable('ledger_suppliers');
    }

    public function down()
    {
        $this->forge->dropTable('ledger_suppliers');
    }
}
