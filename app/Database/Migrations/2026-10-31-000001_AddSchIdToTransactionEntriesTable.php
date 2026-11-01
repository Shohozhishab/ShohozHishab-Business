<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSchIdToTransactionEntriesTable extends Migration
{
    public function up()
    {
        // Tenant column (sch_id) – same definition as the other shared tables
        if (!$this->db->fieldExists('sch_id', 'transaction_entries')) {
            $this->db->query("ALTER TABLE `transaction_entries` ADD `sch_id` INT(11) NOT NULL DEFAULT 0 AFTER `transaction_entries_id`");
        }

        // Backfill existing rows from their owning transaction document
        $this->db->query("UPDATE `transaction_entries` te
            JOIN `purchase` o ON o.`purchase_id` = te.`purchase_id`
            SET te.`sch_id` = o.`sch_id`
            WHERE te.`sch_id` = 0 AND te.`purchase_id` IS NOT NULL");

        $this->db->query("UPDATE `transaction_entries` te
            JOIN `sales` o ON o.`sales_id` = te.`sales_id`
            SET te.`sch_id` = o.`sch_id`
            WHERE te.`sch_id` = 0 AND te.`sales_id` IS NOT NULL");

        $this->db->query("UPDATE `transaction_entries` te
            JOIN `transaction` o ON o.`trans_id` = te.`trans_id`
            SET te.`sch_id` = o.`sch_id`
            WHERE te.`sch_id` = 0 AND te.`trans_id` IS NOT NULL");

        $this->db->query("UPDATE `transaction_entries` te
            JOIN `bank_deposit` o ON o.`dep_id` = te.`dep_id`
            SET te.`sch_id` = o.`sch_id`
            WHERE te.`sch_id` = 0 AND te.`dep_id` IS NOT NULL");

        $this->db->query("UPDATE `transaction_entries` te
            JOIN `bank_withdraw` o ON o.`wthd_id` = te.`wthd_id`
            SET te.`sch_id` = o.`sch_id`
            WHERE te.`sch_id` = 0 AND te.`wthd_id` IS NOT NULL");

        // Index for tenant scoped lookups
        $hasIndex = false;
        foreach ($this->db->getIndexData('transaction_entries') as $index) {
            if (strtolower($index->name) === 'sch_id') {
                $hasIndex = true;
                break;
            }
        }
        if (!$hasIndex) {
            $this->db->query("ALTER TABLE `transaction_entries` ADD INDEX `sch_id` (`sch_id`)");
        }
    }

    public function down()
    {
        foreach ($this->db->getIndexData('transaction_entries') as $index) {
            if (strtolower($index->name) === 'sch_id') {
                $this->db->query("ALTER TABLE `transaction_entries` DROP INDEX `sch_id`");
                break;
            }
        }

        if ($this->db->fieldExists('sch_id', 'transaction_entries')) {
            $this->db->query("ALTER TABLE `transaction_entries` DROP `sch_id`");
        }
    }
}
