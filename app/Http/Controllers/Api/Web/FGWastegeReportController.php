<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FGWastegeReportController extends Controller
{
    public function index()
    {
        $employee = auth()->user()?->employee;

        $storesQuery = Store::query()
            ->whereType('FG')
            ->where('status', 'active')
            ->orderBy('name');

        if ($employee?->outlet_id) {
            $storesQuery->where(['doc_type' => 'outlet', 'doc_id' => $employee->outlet_id]);
        } elseif ($employee?->factory_id) {
            $storesQuery->where(['doc_type' => 'factory', 'doc_id' => $employee->factory_id]);
        }

        return view('finish_goods_wastage_report.index', [
            'stores' => $storesQuery->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '5000000');
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $startDate = sanitizeReportDate(request('from_date') ?? now());
        $endDate = sanitizeReportDate(request('to_date') ?? now());
        [$startDate, $endDate] = clampReportDateRange($startDate, $endDate, 366);

        $reportType = (string) request('report_type');
        $storeId = request()->integer('store_id') ?: null;
        $pageTitle = false;
        $reportHeader = 'FG Wastage Report | ' . $reportType;
        $allowedStoreIds = $this->accessibleFgStoreIds();

        if ($reportType === 'Store Wise Summary') {
            if (!$storeId) {
                return response('Please select a store', 422);
            }
            if (is_array($allowedStoreIds) && !in_array($storeId, $allowedStoreIds, true)) {
                return response('Unauthorized store', 403);
            }

            $store = Store::query()->whereType('FG')->find($storeId);
            if (!$store) {
                return response('Store not found', 404);
            }

            $pageTitle = 'Store Name :: ' . $store->name;
            $rows = $this->storeWiseRows($storeId, $startDate, $endDate);
        } elseif ($reportType === 'Product Wise') {
            $rows = $this->productWiseRows($startDate, $endDate, $allowedStoreIds);
        } elseif ($reportType === 'All Store') {
            $rows = $this->allStoreRows($startDate, $endDate, $allowedStoreIds);
        } else {
            return response('Invalid report type', 422);
        }

        if (count($rows) === 0) {
            return response('No Data to Generate Report', 204);
        }

        $columns = array_keys((array) $rows[0]);
        $pdf = Pdf::loadView('common.report_main', [
            'dateRange' => $startDate . ' - ' . $endDate,
            'data' => $rows,
            'page_title' => $pageTitle,
            'columns' => $columns,
            'report_header' => $reportHeader,
        ], [], [
            'format' => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);

        return $pdf->stream('FG-Wastage-Report.pdf');
    }

    /**
     * @return array<int>|null null = no store restriction (HO / super)
     */
    private function accessibleFgStoreIds(): ?array
    {
        $employee = auth()->user()?->employee;
        if (!$employee) {
            return null;
        }

        if ($employee->outlet_id) {
            return Store::query()
                ->whereType('FG')
                ->where(['doc_type' => 'outlet', 'doc_id' => $employee->outlet_id])
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($employee->factory_id) {
            return Store::query()
                ->whereType('FG')
                ->where(['doc_type' => 'factory', 'doc_id' => $employee->factory_id])
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return null;
    }

    private function storeWiseRows(int $storeId, string $startDate, string $endDate): array
    {
        $rows = DB::select(
            "SELECT
                ia.date AS Date,
                coi.name AS ItemName,
                ROUND(AVG(iat.rate), 2) AS Rate,
                ROUND(SUM(iat.quantity), 2) AS Qty,
                ROUND(SUM(iat.quantity * iat.rate), 2) AS Value
            FROM inventory_adjustments ia
            INNER JOIN inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
            INNER JOIN chart_of_inventories coi ON coi.id = iat.coi_id
            WHERE ia.store_id = ?
              AND ia.type = 'FG'
              AND ia.transaction_type = 'decrease'
              AND ia.status = 'adjusted'
              AND ia.date BETWEEN ? AND ?
            GROUP BY ia.date, iat.coi_id, coi.name
            ORDER BY ia.date, coi.name",
            [$storeId, $startDate, $endDate]
        );

        return $this->appendTotalRow($rows, ['Date' => 'Total', 'ItemName' => '', 'Rate' => '', 'Qty' => ''], 'Value');
    }

    private function productWiseRows(string $startDate, string $endDate, ?array $allowedStoreIds): array
    {
        $bindings = [$startDate, $endDate];
        $storeFilter = '';

        if (is_array($allowedStoreIds)) {
            if (count($allowedStoreIds) === 0) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($allowedStoreIds), '?'));
            $storeFilter = " AND ia.store_id IN ($placeholders) ";
            $bindings = array_merge($bindings, $allowedStoreIds);
        }

        $rows = DB::select(
            "SELECT
                coi.name AS ItemName,
                ROUND(AVG(iat.rate), 2) AS Rate,
                ROUND(SUM(iat.quantity), 2) AS Qty,
                ROUND(SUM(iat.quantity * iat.rate), 2) AS Value
            FROM inventory_adjustments ia
            INNER JOIN inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
            INNER JOIN chart_of_inventories coi ON coi.id = iat.coi_id
            WHERE ia.type = 'FG'
              AND ia.transaction_type = 'decrease'
              AND ia.status = 'adjusted'
              AND ia.date BETWEEN ? AND ?
              $storeFilter
            GROUP BY iat.coi_id, coi.name
            ORDER BY coi.name",
            $bindings
        );

        return $this->appendTotalRow($rows, ['ItemName' => 'Total', 'Rate' => '', 'Qty' => ''], 'Value');
    }

    private function allStoreRows(string $startDate, string $endDate, ?array $allowedStoreIds): array
    {
        $bindings = [$startDate, $endDate];
        $storeFilter = '';

        if (is_array($allowedStoreIds)) {
            if (count($allowedStoreIds) === 0) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($allowedStoreIds), '?'));
            $storeFilter = " AND ia.store_id IN ($placeholders) ";
            $bindings = array_merge($bindings, $allowedStoreIds);
        }

        $rows = DB::select(
            "SELECT
                s.name AS StoreName,
                ROUND(SUM(iat.quantity * iat.rate), 2) AS Value
            FROM inventory_adjustments ia
            INNER JOIN stores s ON ia.store_id = s.id
            INNER JOIN inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
            WHERE ia.type = 'FG'
              AND ia.transaction_type = 'decrease'
              AND ia.status = 'adjusted'
              AND ia.date BETWEEN ? AND ?
              $storeFilter
            GROUP BY ia.store_id, s.name
            ORDER BY s.name",
            $bindings
        );

        return $this->appendTotalRow($rows, ['StoreName' => 'Total'], 'Value');
    }

    private function appendTotalRow(array $rows, array $labels, string $valueKey): array
    {
        if (count($rows) === 0) {
            return [];
        }

        $total = 0;
        foreach ($rows as $row) {
            $total += (float) ($row->{$valueKey} ?? 0);
        }

        $totalRow = (object) array_merge($labels, [
            $valueKey => round($total, 2),
        ]);
        $rows[] = $totalRow;

        return $rows;
    }
}
