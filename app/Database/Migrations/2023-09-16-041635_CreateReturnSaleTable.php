<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateReturnSaleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rtn_sale_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'customer_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'pymnt_type_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'customer_name' => [
                'type'           => 'varchar',
                'constraint'     => 155,
                'default' => null,
                'null' => true,
            ],
            'amount' => [
                'type'     => 'float',
            ],
            'rtn_profit' => [
                'type'    => 'float',
                'comment' => 'Profit on the sale',
            ],
            'nagad_paid' => [
                'type'     => 'float',
                'unsigned' => true,
                'default' => null,
                'null' => true,
            ],
            'bank_paid' => [
                'type'     => 'float',
                'unsigned' => true,
                'default' => null,
                'null' => true,
            ],
            'bank_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'creation_timestamp' => [
                'type'           => 'INT',
                'constraint'     => 20,
                'default' => null,
                'null' => true,
            ],
            'payment_timestamp' => [
                'type'    => 'longtext',
                'default' => null,
                'null' => true,
            ],
            'payment_method' => [
                'type'    => 'longtext',
                'default' => null,
                'null' => true,
            ],
            'payment_details' => [
                'type'    => 'longtext',
                'default' => null,
                'null' => true,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '1',
            ],
            'timestamp' => [
                'type' => 'timestamp',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'year' => [
                'type'    => 'longtext',
                'default' => null,
                'null' => true,
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
        $this->forge->addKey('rtn_sale_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('pymnt_type_id');
        $this->forge->createTable('return_sale');
    }

    public function down()
    {
        $this->forge->dropTable('return_sale');
    }
}
