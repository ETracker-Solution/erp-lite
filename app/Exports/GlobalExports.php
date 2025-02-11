<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 *
 */
class GlobalExports implements FromView, WithStyles, ShouldAutoSize
{
    /**
     * @var
     */
    protected $viewFileName;
    /**
     * @var
     */
    protected  $data;

    /**
     * @param $viewFileName
     * @param $exportableData
     */
    public function __construct($viewFileName, $exportableData)
    {
        $this->viewFileName = $viewFileName;
        $this->data = $exportableData;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.'.$this->viewFileName, $this->data);
    }

    /**
     * @param Worksheet $sheet
     * @return array[]
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
