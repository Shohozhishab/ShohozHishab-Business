<?php
// Warranty Short List module model

namespace App\Models;
use CodeIgniter\Model;

/**
 * Class Warranty_short_listModel
 *
 * Data-access layer for the "Warranty Short List" module.
 *
 * Business rule: return product serial units whose `company_warranty_end_date`
 * is due within the next 2 months, i.e.
 *
 *     company_warranty_end_date >= CURRENT_DATE
 *     AND company_warranty_end_date <= CURRENT_DATE + INTERVAL 2 MONTH
 *
 * The window is always resolved dynamically from today's date so the module
 * never needs re-configuration. Only units that still carry an active/upcoming
 * company warranty ending soon are returned.
 */
class Warranty_short_listModel extends Model
{
    protected $table            = 'product_serial';
    protected $primaryKey       = 'product_serial_id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['product_serial_id', 'product_stock_relation_id', 'serial_number', 'company_warranty_end_date', 'customer_warranty_end_date', 'status', 'invoice_id', 'customer_id', 'sold_date', 'createdDtm', 'createdBy', 'updatedBy', 'updatedDtm', 'deleted', 'deletedRole'];
    protected $useTimestamps    = false;
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    /** @var int Number of months that define the "expiring soon" window. */
    protected $windowMonths = 2;

    /**
     * Fetch serial units whose company warranty ends within the dynamic window.
     *
     * @param int    $shopId Current tenant (sch_id) – keeps the module multi-tenant safe.
     * @param string $search Optional smart-search term (product / serial / customer).
     * @return array List of std-object rows grouped-friendly (ordered by product then date).
     */
    public function get_expiring_warranty($shopId, $search = '')
    {
        $db = $this->db;

        $builder = $db->table('product_serial ps')
            ->select('ps.product_serial_id, ps.serial_number, ps.company_warranty_end_date, ps.status, ps.invoice_id, ps.customer_id, ps.sold_date')
            ->select('psr.product_stock_relation_id, psr.product_id, psr.store_id, psr.sch_id')
            ->select('p.name AS product_name, p.prod_cat_id')
            ->join('product_stock_relation psr', 'psr.product_stock_relation_id = ps.product_stock_relation_id', 'inner')
            ->join('products p', 'p.prod_id = psr.product_id', 'inner')
            ->where('psr.sch_id', $shopId)
            ->where('ps.company_warranty_end_date IS NOT NULL', null, false)
            // Dynamic window: today <= company_warranty_end_date <= today + N months
            ->where('ps.company_warranty_end_date >= CURDATE()', null, false)
            ->where("ps.company_warranty_end_date <= DATE_ADD(CURDATE(), INTERVAL " . (int) $this->windowMonths . " MONTH)", null, false);

        if ($search !== '') {
            $builder->groupStart()
                ->like('p.name', $search)
                ->orLike('ps.serial_number', $search)
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('p.name', 'ASC')
            ->orderBy('ps.company_warranty_end_date', 'ASC')
            ->get()
            ->getResult();

        // Annotate each row with the resolved customer/partner and days remaining.
        $today = new \DateTime('today');
        foreach ($rows as $row) {
            $row->customer_name = null;
            if (!empty($row->customer_id)) {
                $row->customer_name = get_data_by_id('customer_name', 'customers', 'customer_id', $row->customer_id);
            }

            $endDate = new \DateTime($row->company_warranty_end_date);
            $diff    = $today->diff($endDate);
            $row->days_remaining = (int) $diff->days;
        }

        return $rows;
    }

    /**
     * Return the same rows organised into product-level groups.
     * Keyed by product_id so the view can render a grouped (accordion) layout.
     *
     * @param int    $shopId
     * @param string $search
     * @return array [product_id => ['product_name' => ..., 'items' => [rows...]]]
     */
    public function get_expiring_warranty_grouped($shopId, $search = '')
    {
        $grouped = [];
        foreach ($this->get_expiring_warranty($shopId, $search) as $row) {
            $pid = (int) $row->product_id;
            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'product_id'   => $pid,
                    'product_name' => $row->product_name,
                    'count'        => 0,
                    'items'        => [],
                ];
            }
            $grouped[$pid]['items'][] = $row;
            $grouped[$pid]['count']++;
        }
        return $grouped;
    }

    /**
     * Window metadata used by the view header / badges.
     *
     * @return array
     */
    public function get_window_info()
    {
        $today = new \DateTime('today');
        $end   = new \DateTime('today');
        $end->modify('+' . (int) $this->windowMonths . ' month');
        return [
            'months' => $this->windowMonths,
            'from'   => $today->format('Y-m-d'),
            'to'     => $end->format('Y-m-d'),
        ];
    }
}
