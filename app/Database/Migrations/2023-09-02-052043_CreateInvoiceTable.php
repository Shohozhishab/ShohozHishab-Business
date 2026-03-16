<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateInvoiceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'invoice_id' => [
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
                'null' => true,
                'default' => null,
            ],
            'pymnt_type_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
                'default' => null,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 155,
                'null' => true,
                'default' => null,
            ],
            'amount' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'entire_sale_discount' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'vat' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'final_amount' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'profit' => [
                'type' => 'float',
                'comment' => 'Profit on the sale'
            ],
            'nagad_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'bank_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'bank_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'chaque_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'chaque_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'due' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'creation_timestamp' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'payment_timestamp' => [
                'type' => 'longtext',
                'null' => true,
                'default' => null,
            ],
            'payment_method' => [
                'type' => 'longtext',
                'null' => true,
                'default' => null,
            ],
            'payment_details' => [
                'type' => 'longtext',
                'null' => true,
                'default' => null,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'default' => '1',
            ],
            'timestamp' => [
                'type' => 'timestamp',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'year' => [
                'type' => 'longtext',
                'null' => true,
                'default' => null,
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
        $this->forge->addKey('invoice_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('pymnt_type_id');
        $this->forge->createTable('invoice');
    }

    public function down()
    {
        $this->forge->dropTable('invoice');
    }
}
