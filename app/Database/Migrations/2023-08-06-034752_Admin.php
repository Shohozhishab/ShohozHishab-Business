<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Admin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 155,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
            ],
            'mobile' => [
                'type' => 'int',
                'constraint' => 11,
                'unique' => true,
            ],
            'address' => [
                'type' => 'text',
                'null' => true,
                'default' => null,
            ],
            'pic' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
                'default' => null,
            ],
            'country' => [
                'type' => 'varchar',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'ComName' => [
                'type' => 'varchar',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'ENUM',
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
        $this->forge->addKey('user_id', true);
        $this->forge->addKey('role_id');
        $this->forge->createTable('admin');
    }

    public function down()
    {
        $this->forge->dropTable('admin');
    }
}
