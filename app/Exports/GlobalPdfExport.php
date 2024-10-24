<?php

namespace App\Exports;

use Dompdf\Dompdf;
use Dompdf\Options;


/**
 *  Generate report and export it
 *  if there is no data, it will be blank.
 */
class GlobalPdfExport
{
    protected $viewFileName, $fileName, $pageOrientation = 'portrait';
    protected $data;

    /**
     * @param string $viewFileName only first part of a blade file . ex: only users from users.blade.php
     * @param mixed $exportableData this should be collection data which will export
     * @param string $fileName which will show after download
     * @param string $orientation Page orientation default value is Portrait, if Landscape needed, just pass L
     */
    public function __construct(string $viewFileName, $exportableData, string $fileName, string $orientation = 'P')
    {
        $this->viewFileName = $viewFileName;
        $this->data = $exportableData;
        $this->fileName = $fileName;
        if ($orientation === 'L') {
            $this->pageOrientation = 'landscape';
        }
    }

    /**
     * Generate and Stream as PDF file
     */
    public function generatePdf()
    {
        $options = new Options();
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('exports.' . $this->viewFileName, $this->data)->render());
        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', $this->pageOrientation);
        // Render the HTML as PDF
        $dompdf->render();
        $dompdf->stream($this->fileName, ["Attachment" => false]);
        exit();
    }
}
