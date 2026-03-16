<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ServiceInvoiceItemTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'inv_item' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'service_invoice_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'sch_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 155,
                'null' => true,
                'default' => null,
                'comment' => 'It will be used.'
            ],
            'price' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'total_price' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'discount' => [
                'type' => 'INT',
                'null' => true,
                'default' => null,
            ],
            'final_price' => [
                'type' => 'float',
                'unsigned' => true,
            ],
            'date' => [
                'type' => 'datetime',
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
        $this->forge->addKey('inv_item', true);
        $this->forge->addKey('sch_id');
        $this->forge->addKey('service_invoice_id');
        $this->forge->createTable('service_invoice_item');
    }

    public function down()
    {
        $this->forge->dropTable('service_invoice_item');
    }
}
