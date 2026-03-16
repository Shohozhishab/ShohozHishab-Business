<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateChaqueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'chaque_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'chaque_number' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'to_name' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'to' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'from_name' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'from' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'from_loan_provider' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'amount' => [
                'type' => 'float',
                'constraint' => 20,
                'unsigned' => true,
            ],
            'issue_date' => [
                'type' => 'DATE',
            ],
            'account_number' => [
                'type' => 'INT',
                'constraint' => 50,
                'null' => true,
                'default' => null,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Bounce', 'Approved'],
                'default' => 'Pending',
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
        $this->forge->addKey('chaque_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->createTable('chaque');
    }

    public function down()
    {
        $this->forge->dropTable('chaque');
    }
}
