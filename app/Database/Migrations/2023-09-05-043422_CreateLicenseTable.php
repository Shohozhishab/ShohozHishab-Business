<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateLicenseTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lic_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'lic_key' => [
                'type'           => 'TEXT',
            ],
            'start_date' => [
                'type'           => 'date',
            ],
            'end_date' => [
                'type'           => 'date',
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['0', '1'],
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
        $this->forge->addKey('lic_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->createTable('license');
    }

    public function down()
    {
        $this->forge->dropTable('license');
    }
}
