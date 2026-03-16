<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLedgerEmployeeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ledg_emp_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'employee_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'trans_id' => [
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
        $this->forge->addKey('ledg_emp_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('employee_id');
        $this->forge->addKey('trans_id');
        $this->forge->createTable('ledger_employee');
    }

    public function down()
    {
        $this->forge->dropTable('ledger_employee');
    }
}
