<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomerDueExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->customers as $customer) {
            // Customer Header Row
            $data->push([
                $customer->name,
                $customer->mobile,
                'Opening Balance/Summary',
                '',
                '',
                '',
                number_format($customer->due_amount, 2)
            ]);

            $balance = 0;
            foreach ($customer->customerTransactions as $transaction) {
                $debit = $transaction->transaction_type == 1 ? $transaction->amount : 0;
                $credit = $transaction->transaction_type == -1 ? $transaction->amount : 0;
                $balance += ($debit - $credit);

                $data->push([
                    '',
                    '',
                    $transaction->date,
                    $transaction->description,
                    $debit > 0 ? number_format($debit, 2) : '',
                    $credit > 0 ? number_format($credit, 2) : '',
                    number_format($balance, 2)
                ]);
            }

            // Separator row
            $data->push(['', '', '', '', '', '', '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Phone',
            'Date',
            'Description',
            'Debit',
            'Credit',
            'Balance'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']]],
        ];
    }
}
