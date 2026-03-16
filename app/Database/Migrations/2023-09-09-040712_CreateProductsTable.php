<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prod_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'store_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'name' => [
                'type'           => 'varchar',
                'constraint'     => 55,
            ],
            'quantity' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'unit' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'purchase_price' => [
                'type'           => 'FLOAT',
            ],
            'selling_price' => [
                'type'           => 'FLOAT',
            ],
            'purchase_date' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'comment' => "Last purchase price will be added here.",
            ],
            'supplier_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'size' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'serial_number' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'brand_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'default' => null,
                'null' => true,
            ],
            'picture' => [
                'type'           => 'varchar',
                'constraint'     => 155,
                'default' => null,
                'null' => true,
            ],
            'warranty' => [
                'type'           => 'varchar',
                'constraint'     => 55,
                'default' => null,
                'null' => true,
            ],
            'barcode' => [
                'type'           => 'varchar',
                'constraint'     => 55,
                'default' => null,
                'null' => true,
            ],
            'prod_cat_id' => [
                'type'           => 'int',
                'constraint'     => 11,
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
        $this->forge->addKey('prod_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('store_id');
        $this->forge->addKey('supplier_id');
        $this->forge->addKey('brand_id');
        $this->forge->addKey('prod_cat_id');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
