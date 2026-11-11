<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentFeeLedgerService
{
    /**
     * Due (cart_management) + paid (orders/order_items) ledger for one student since $since.
     * Includes donations. Sorted newest first.
     */
    public function forStudent(int $userId, Carbon $since): Collection
    {
        $sinceStr = $since->copy()->startOfDay()->toDateTimeString();

        $due = DB::table('cart_management as cm')
            ->leftJoin('courses as c', 'c.id', '=', 'cm.course_id')
            ->where('cm.user_id', $userId)
            ->where('cm.created_at', '>=', $sinceStr)
            ->whereIn('cm.created_by_type', [1, 2])
            ->selectRaw("
                'due' AS ledger_source,
                cm.id AS ref_id,
                cm.user_id AS user_id,
                cm.course_id AS course_id,
                c.title AS course_title,
                cm.price AS amount,
                'due' AS payment_status,
                CAST(NULL AS CHAR) AS payment_method,
                CAST(NULL AS CHAR) AS order_number,
                CAST(NULL AS CHAR) AS transaction_number,
                CAST(NULL AS UNSIGNED) AS cart_id,
                cm.is_monthly_cron AS is_monthly_cron,
                cm.month AS month,
                cm.year AS year,
                cm.created_by_type AS created_by_type,
                CASE
                    WHEN IFNULL(cm.is_donation, 0) = 1 THEN 'Donation'
                    WHEN cm.is_monthly_cron = 1 THEN 'Monthly Fee'
                    WHEN cm.created_by_type = 2 AND cm.month IS NOT NULL AND cm.year IS NOT NULL THEN 'Manual Fee'
                    WHEN cm.created_by_type = 1 THEN 'Cart'
                    ELSE 'Other'
                END AS fee_type,
                cm.created_at AS event_at
            ");

        $paid = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('courses as c', 'c.id', '=', 'oi.course_id')
            ->where('oi.user_id', $userId)
            ->where('o.payment_status', 'paid')
            ->whereRaw('COALESCE(o.created_at, oi.created_at) >= ?', [$sinceStr])
            ->selectRaw("
                'paid' AS ledger_source,
                oi.id AS ref_id,
                oi.user_id AS user_id,
                oi.course_id AS course_id,
                c.title AS course_title,
                oi.unit_price AS amount,
                o.payment_status AS payment_status,
                o.payment_method AS payment_method,
                o.order_number AS order_number,
                COALESCE(
                    NULLIF(o.meezan_transaction_id, ''),
                    NULLIF(o.faysal_transaction_id, ''),
                    NULLIF(o.payment_id, ''),
                    NULLIF(o.meezan_order_number, ''),
                    NULLIF(o.meezan_order_id, ''),
                    NULLIF(o.faysal_order_id, '')
                ) AS transaction_number,
                oi.cart_id AS cart_id,
                oi.is_monthly_cron AS is_monthly_cron,
                oi.month AS month,
                oi.year AS year,
                oi.created_by_type AS created_by_type,
                CASE
                    WHEN IFNULL(oi.is_donation, 0) = 1 THEN 'Donation'
                    WHEN oi.is_monthly_cron = 1 THEN 'Monthly Fee'
                    ELSE 'Challan Payment'
                END AS fee_type,
                COALESCE(o.created_at, oi.created_at) AS event_at
            ");

        $union = $due->unionAll($paid);

        $rows = DB::query()
            ->fromSub($union, 'ledger')
            ->orderByDesc('event_at')
            ->orderBy('ledger_source')
            ->orderByDesc('ref_id')
            ->get();

        return collect($rows);
    }

    public function sinceFromMonths(int $months): Carbon
    {
        $months = max(1, min(120, $months));

        return Carbon::now()->subMonths($months)->startOfDay();
    }

    public function totals(Collection $rows): array
    {
        $due = $rows->where('ledger_source', 'due')->sum(fn ($r) => (float) $r->amount);
        $paid = $rows->where('ledger_source', 'paid')->sum(fn ($r) => (float) $r->amount);

        return [
            'total_due' => round($due, 2),
            'total_paid' => round($paid, 2),
            'total_rows' => $rows->count(),
        ];
    }

    /**
     * CSV column headers for Excel export.
     */
    public function exportHeaders(): array
    {
        return [
            'ledger_source',
            'ref_id',
            'user_id',
            'course_id',
            'course_title',
            'amount',
            'payment_status',
            'payment_method',
            'order_number',
            'transaction_number',
            'cart_id',
            'is_monthly_cron',
            'month',
            'year',
            'created_by_type',
            'fee_type',
            'event_at',
        ];
    }
}
