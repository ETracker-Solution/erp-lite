<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryRequisitionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    protected $delivery, $dateRange;

    public function __construct($delivery)
    {
        $this->delivery = $delivery;

        // Getting Date Range From Request
        $startDate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::now()->format('Y-m-d');

        $this->dateRange = $startDate . ' to ' . $endDate;
        if (request()->filled('date_range')){
            $this->dateRange = (request()->date_range);
        }
    }
    public function collection()
    {
        return $this->delivery;
    }

    public function headings(): array
    {
        return [
            'Factory Name',
            'Factory Address',
            'Date',
            'Delivery No',
            'Total Quantity',
            'Total Amount',
            'Description',
            'Status',
            'Created Date',
            'Delivery By',
            'Generated On'
        ];
    }
    public function map($row): array
    {
        return $array = [
            $row->production ? $row->production->name : '',
            $row->production ? $row->production->address : '',
            $row->date,
            $row->rd_number,
            $row->total_quantity,
            $row->total_amount,
            $row->description,
            $row->status,
            $row->created_at->format('Y-m-d H:i:s'),
            $row->deliveryBy->name,
            'Generated On' => \Carbon\Carbon::now()->format('Y-m-d h:i:s a'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $alphabet = $event->sheet->getHighestDataColumn();
                $totalRow = $event->sheet->getHighestDataRow();
                $cellRange = 'A3:' . ($alphabet . ($totalRow + 2));
                // $lastCell = 'I' . ($totalRow + 2);
                $lastColumn = Coordinate::stringFromColumnIndex(6);
                $style_text_center = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ]
                ];

                // insert row 1, insert row 2
                $event->sheet->insertNewRowBefore(1,2);

                // Merge Cell for Full width
                $event->sheet->mergeCells(sprintf('A1:%s1', $lastColumn));
                $event->sheet->mergeCells(sprintf('A2:%s2', $lastColumn));

                // Assign Cell Value
                // $event->sheet->setCellValue('A1', $this->name);
                // $event->sheet->setCellValue('A2', 'DATE RANGE: ' . $this->dateRange);

                // Assign Cell Style
                $event->sheet->getStyle('A1:A2')->getFont()->setBold(true);
                $event->sheet->getStyle('A1:A2')->applyFromArray($style_text_center);
                // $event->sheet->getStyle('I3:' . $lastCell)->applyFromArray($style_text_center);

                $event->sheet->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                // $event->sheet->mergeCells('I3:' . $lastCell)->getStyle('I3:' . $lastCell)->getAlignment()->setVertical('center');
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THICK],
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ])->getAlignment()->setWrapText(true);
            }
        ];
    }
}
