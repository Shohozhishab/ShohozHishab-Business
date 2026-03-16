<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ServiceInvoiceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            '	service_invoice_id' => [
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
            ],
            'pymnt_type_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'customer_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null' => true,
            ],
            'amount' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'entire_sale_discount' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'vat' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'final_amount' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'nagad_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
            ],
            'bank_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
            ],
            'bank_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'chaque_paid' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
            ],
            'chaque_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'due' => [
                'type' => 'float',
                'unsigned' => true,
                'null' => true,
            ],
            'creation_timestamp' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'null' => true,
            ],
            'payment_timestamp' => [
                'type'           => 'LONGTEXT',
                'null' => true,
            ],
            'payment_method' => [
                'type'           => 'LONGTEXT',
                'null' => true,
            ],
            'payment_details' => [
                'type'           => 'LONGTEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['1', '0'],
                'default' => '1',
            ],
            'timestamp' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'year' => [
                'type'           => 'LONGTEXT',
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
        $this->forge->addKey('service_invoice_id', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('pymnt_type_id');
        $this->forge->createTable('service_invoice');
    }

    public function down()
    {
        $this->forge->dropTable('service_invoice');
    }
}
