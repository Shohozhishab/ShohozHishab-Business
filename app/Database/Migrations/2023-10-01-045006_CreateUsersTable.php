<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsersTable extends Migration
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
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'email' => [
                'type'           => 'varchar',
                'constraint'     => 30,
            ],
            'password' => [
                'type'           => 'varchar',
                'constraint'     => 155,
            ],
            'name' => [
                'type'           => 'varchar',
                'constraint'     => 40,
            ],
            'mobile' => [
                'type'    => 'bigint',
                'default' => null,
                'null' => true,
            ],
            'address' => [
                'type'    => 'text',
                'default' => null,
                'null' => true,
            ],
            'pic' => [
                'type'           => 'varchar',
                'constraint'     => 100,
            ],
            'role_id' => [
                'type'  => 'int',
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
            ],
            'is_default' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '0',
            ],
            'permission' => [
                'type' => 'text',
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
        $this->forge->addKey('sch_id');
        $this->forge->addKey('email');
        $this->forge->addKey('role_id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
