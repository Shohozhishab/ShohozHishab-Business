<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSchIdToProductSerialTable extends Migration
{
    public function up()
    {
        // Tenant column (sch_id) – same definition as the other shared tables
        if (!$this->db->fieldExists('sch_id', 'product_serial')) {
            $this->db->query("ALTER TABLE `product_serial` ADD `sch_id` INT(11) NOT NULL DEFAULT 0 AFTER `product_serial_id`");
        }

        // Backfill existing rows from their owning stock relation
        $this->db->query("UPDATE `product_serial` ps
            JOIN `product_stock_relation` psr ON psr.`product_stock_relation_id` = ps.`product_stock_relation_id`
            SET ps.`sch_id` = psr.`sch_id`
            WHERE ps.`sch_id` = 0");

        // Index for tenant scoped lookups
        $hasIndex = false;
        foreach ($this->db->getIndexData('product_serial') as $index) {
            if (strtolower($index->name) === 'sch_id') {
                $hasIndex = true;
                break;
            }
        }
        if (!$hasIndex) {
            $this->db->query("ALTER TABLE `product_serial` ADD INDEX `sch_id` (`sch_id`)");
        }
    }

    public function down()
    {
        foreach ($this->db->getIndexData('product_serial') as $index) {
            if (strtolower($index->name) === 'sch_id') {
                $this->db->query("ALTER TABLE `product_serial` DROP INDEX `sch_id`");
                break;
            }
        }

        if ($this->db->fieldExists('sch_id', 'product_serial')) {
            $this->db->query("ALTER TABLE `product_serial` DROP `sch_id`");
        }
    }
}
