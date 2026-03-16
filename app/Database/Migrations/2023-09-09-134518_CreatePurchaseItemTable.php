<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePurchaseItemTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'purchase_item_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'purchase_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'prod_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
            ],
            'purchase_price' => [
                'type' => 'float',
            ],
            'quantity' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned' => true,
            ],
            'total_price' => [
                'type' => 'float',
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
        $this->forge->addKey('purchase_item_id', true);
        $this->forge->addKey('purchase_id');
        $this->forge->addKey('prod_id');
        $this->forge->createTable('purchase_item');
    }

    public function down()
    {
        $this->forge->dropTable('purchase_item');
    }
}
