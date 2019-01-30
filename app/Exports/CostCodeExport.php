<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 1/26/2019
 * Time: 9:41 AM
 */

namespace App\Exports;

use App\Models\Account;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class CostCodeExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;
    private $counter;

    public function headings(): array
    {
        return [
            'KODE',
            'LOKASI',
            'DEPARTEMEN',
            'DIVISI',
            'KETERANGAN',
            'REMARK',
            'BRAND',
        ];
    }

    /**
     * @var Account $supplier
     * @return array
     */
    public function map($account): array
    {
        // TODO: Implement map() method.
        return [
            $account->code ?? '-',
            $account->location ?? '-',
            $account->department ?? '-',
            $account->division ?? '-',
            $account->description ?? '-',
            $account->remark ?? '-',
            $account->brand ?? '-'
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        // TODO: Implement query() method.

        $account = Account::orderBy('code');

        $this->counter = $account->count();

        return $account;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        // TODO: Implement registerEvents() method.
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}