<?php

namespace App\Services;

use App\Exports\GlobalExports;
use App\Exports\GlobalPdfExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * This service will help to export file as XLSX, CSV and PDF
 */
class ExportService
{
    public function exportFile($type, $viewFileName, $exportableData, $downloadedFileName, $pdfPageOrientation = 'P')
    {
        switch ($type) {
            case 'xlsx':
                return Excel::download(new GlobalExports($viewFileName,$exportableData), $downloadedFileName.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            case 'csv':
                return Excel::download(new GlobalExports($viewFileName,$exportableData), $downloadedFileName.'.csv', \Maatwebsite\Excel\Excel::CSV);
            case 'pdf':
                $pdfExport = new GlobalPdfExport($viewFileName,$exportableData, $downloadedFileName.'.pdf',$pdfPageOrientation);
                $pdfExport->generatePdf();
                break;
            default:
                abort(400, 'Invalid file type');
        }
    }


}
