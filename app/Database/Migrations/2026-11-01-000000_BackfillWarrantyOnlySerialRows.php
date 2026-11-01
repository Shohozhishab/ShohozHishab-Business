<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillWarrantyOnlySerialRows extends Migration
{
    public function up()
    {
        // The company warranty end date must live on product_serial, not
        // purchase_item. Warranty-only lines (serial_status=0, warranty_status=1)
        // that were saved with the date on purchase_item get one placeholder
        // row per purchased unit (serial_number NULL) carrying that date.
        //
        // Idempotent: relations that already own any product_serial row
        // (placeholders or real serials) are skipped by the NOT EXISTS guard.
        // MariaDB wants the WITH clause after the INSERT column list.
        $this->db->query(
            "INSERT INTO `product_serial`
                (`sch_id`, `product_stock_relation_id`, `serial_number`,
                 `company_warranty_end_date`, `status`, `createdDtm`)
             WITH RECURSIVE `nums` (`n`) AS (
                SELECT 1 UNION ALL SELECT `n` + 1 FROM `nums` WHERE `n` < 1000
             )
             SELECT psr.`sch_id`, psr.`product_stock_relation_id`, NULL,
                    pi.`company_warranty_end_date`, 'in_stock', NOW()
             FROM `purchase_item` pi
             JOIN `product_stock_relation` psr
               ON psr.`product_stock_relation_id` = pi.`product_stock_relation_id`
             JOIN `products` p
               ON p.`prod_id` = pi.`prod_id`
             JOIN `nums` ON `nums`.`n` <= CEIL(pi.`quantity`)
             WHERE pi.`company_warranty_end_date` IS NOT NULL
               AND pi.`product_stock_relation_id` IS NOT NULL
               AND pi.`product_stock_relation_id` > 0
               AND p.`serial_status` = '0'
               AND p.`warranty_status` = '1'
               AND NOT EXISTS (
                   SELECT 1 FROM `product_serial` ps
                   WHERE ps.`product_stock_relation_id` = pi.`product_stock_relation_id`
               )"
        );
    }

    public function down()
    {
        // Remove only the date-carrying placeholder rows this migration created
        // (in-stock, no serial number, no invoice, never updated since creation).
        $this->db->query(
            "DELETE ps FROM `product_serial` ps
             JOIN `product_stock_relation` psr
               ON psr.`product_stock_relation_id` = ps.`product_stock_relation_id`
             JOIN `products` p
               ON p.`prod_id` = psr.`product_id`
             WHERE ps.`serial_number` IS NULL
               AND ps.`status` = 'in_stock'
               AND ps.`invoice_id` IS NULL
               AND ps.`updatedBy` IS NULL
               AND p.`serial_status` = '0'
               AND p.`warranty_status` = '1'"
        );
    }
}
