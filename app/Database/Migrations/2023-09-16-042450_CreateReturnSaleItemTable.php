<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateReturnSaleItemTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rtn_sale_item_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'rtn_sale_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'prod_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'title' => [
                'type'           => 'varchar',
                'constraint'     => 155,
                'default' => null,
                'null' => true,
                'comment' => 'It will be used, if prod_id is not inserted into the table.',
            ],
            'price' => [
                'type'           => 'float',
                'unsigned'       => true,
            ],
            'quantity' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'total_price' => [
                'type'           => 'float',
                'unsigned'       => true,
            ],
            'date' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
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
        $this->forge->addKey('rtn_sale_item_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('rtn_sale_id');
        $this->forge->addKey('prod_id');
        $this->forge->createTable('return_sale_item');
    }

    public function down()
    {
        $this->forge->dropTable('return_sale_item');
    }
}
