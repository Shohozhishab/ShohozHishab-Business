<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLedgerBankTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ledgBank_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'bank_id' => [
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
            'rtn_sale_id' => [
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
            'invoice_id' => [
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
                'unsigned' => true,
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
        $this->forge->addKey('ledgBank_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('bank_id');
        $this->forge->addKey('money_receipt_id');
        $this->forge->addKey('purchase_id');
        $this->forge->addKey('trans_id');
        $this->forge->addKey('rtn_purchase_id');
        $this->forge->addKey('rtn_sale_id');
        $this->forge->createTable('ledger_bank');
    }

    public function down()
    {
        $this->forge->dropTable('ledger_bank');
    }
}
