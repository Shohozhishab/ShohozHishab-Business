<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateProductSerialTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_serial_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_stock_relation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'company_warranty_end_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'customer_warranty_end_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['in_stock', 'sold', 'returned', 'purchase_returned'],
                'default'    => 'in_stock',
            ],
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'sold_date' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'rtn_purchase_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'rtn_sale_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'createdDtm' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'updatedDtm DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'deletedRole' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        $this->forge->addKey('product_serial_id', true);
        $this->forge->addKey('product_stock_relation_id');
        $this->forge->addKey('invoice_id');
        $this->forge->addKey('status');
        $this->forge->addKey('serial_number');

        $this->forge->createTable('product_serial');
    }

    public function down()
    {
        $this->forge->dropTable('product_serial');
    }
}
