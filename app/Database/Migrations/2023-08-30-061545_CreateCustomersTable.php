<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'customer_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'customer_name' => [
                'type' => 'varchar',
                'constraint' => 55,
            ],
            'father_name' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
                'null' => true,
                'default' => null,
            ],
            'mother_name' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
                'null' => true,
                'default' => null,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'present_address' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
                'unsigned' => true,
            ],
            'mobile' => [
                'type' => 'BIGINT',
                'null' => true,
                'default' => null,
            ],
            'pic' => [
                'type' => 'varchar',
                'constraint' => 55,
                'null' => true,
                'default' => null,
            ],
            'nid' => [
                'type' => 'varchar',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'cus_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1'],
                'default' => '1',
            ],
            'balance' => [
                'type' => 'FLOAT',
                'constraint' => 50,
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
        $this->forge->addKey('customer_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('mobile');
        $this->forge->addKey('cus_type_id');
        $this->forge->createTable('customers');
    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}
