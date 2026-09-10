<?php

use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Rap2hpoutre\FastExcel\FastExcel;

if (!function_exists('wantsReportExcelExport')) {
    function wantsReportExcelExport(): bool
    {
        $format = strtolower((string) request('export_format', request('format', 'pdf')));

        return in_array($format, ['xlsx', 'xls', 'excel'], true);
    }
}

if (!function_exists('normalizeReportRows')) {
    /**
     * @param  iterable  $rows
     * @return array<int, array<string, mixed>>
     */
    function normalizeReportRows(iterable $rows, ?array $columns = null): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $arr = is_array($row) ? $row : (array) $row;
            if ($columns === null) {
                $normalized[] = $arr;
                continue;
            }
            $out = [];
            foreach ($columns as $column) {
                $out[$column] = $arr[$column] ?? '';
            }
            $normalized[] = $out;
        }

        return $normalized;
    }
}

if (!function_exists('downloadReportExcel')) {
    /**
     * @param  iterable  $rows
     * @param  array<int, string>|null  $columns
     */
    function downloadReportExcel(iterable $rows, string $filename, ?array $columns = null)
    {
        $data = normalizeReportRows($rows, $columns);
        if ($data === []) {
            $data = [['_empty' => 'No data']];
        }

        $safeName = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $filename) ?: 'report';

        return (new FastExcel(collect($data)))->download($safeName . '.xlsx');
    }
}

if (!function_exists('streamTabularReport')) {
    /**
     * Shared PDF/Excel export for reports that use common.report_main shape.
     *
     * Expected $payload keys: dateRange, data, page_title, columns, report_header
     */
    function streamTabularReport(array $payload, string $filename, array $pdfConfig = [])
    {
        $columns = $payload['columns'] ?? [];
        $rows = $payload['data'] ?? [];

        if (wantsReportExcelExport()) {
            if ($columns === [] && count($rows) > 0) {
                $first = is_array($rows[0]) ? $rows[0] : (array) $rows[0];
                $columns = array_keys($first);
            }

            return downloadReportExcel($rows, $filename, $columns ?: null);
        }

        $defaultPdfConfig = [
            'format' => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ];

        $pdf = Pdf::loadView('common.report_main', $payload, [], array_merge($defaultPdfConfig, $pdfConfig));

        return $pdf->stream($filename . '.pdf');
    }
}

if (!function_exists('downloadFinancialStatementExcel')) {
    function downloadFinancialStatementExcel(array $rows, string $reportHeader, string $dateRange, bool $showDebitCredit = false)
    {
        $exportRows = [
            $showDebitCredit
                ? ['Particulars' => $reportHeader, 'Debit' => $dateRange, 'Credit' => '']
                : ['Particulars' => $reportHeader, 'Amount' => $dateRange],
        ];

        foreach ($rows as $row) {
            $indent = str_repeat('  ', (int) ($row['level'] ?? 0));
            if ($showDebitCredit) {
                $exportRows[] = [
                    'Particulars' => $indent . ($row['name'] ?? ''),
                    'Debit' => $row['debit'] ?? '',
                    'Credit' => $row['credit'] ?? '',
                ];
            } else {
                $exportRows[] = [
                    'Particulars' => $indent . ($row['name'] ?? ''),
                    'Amount' => $row['amount'] ?? '',
                ];
            }
        }

        return downloadReportExcel($exportRows, $reportHeader);
    }
}
