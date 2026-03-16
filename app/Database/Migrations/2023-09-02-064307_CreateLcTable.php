<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLcTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lc_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'lc_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 155,
            ],
            'bank_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'amount' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'rest_balance' => [
                'type' => 'float',
                'comment' => 'Rest of the balance of the LC',
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
        $this->forge->addKey('lc_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('bank_id');
        $this->forge->createTable('lc');
    }

    public function down()
    {
        $this->forge->dropTable('lc');
    }
}
