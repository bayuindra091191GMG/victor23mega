<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 10/25/2018
 * Time: 9:28 AM
 */

namespace App\Exports;

use App\Models\Supplier;
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

class SupplierExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;
    private $counter;

    public function headings(): array
    {
        return [
            'KODE',
            'NAMA',
            'KATEGORI',
            'EMAIL 1',
            'EMAIL 2',
            'TELPON 1',
            'TELPON 2',
            'FAX',
            'NOMOR PONSEL',
            'CONTACT PERSON',
            'KOTA',
            'ALAMAT',
            'NPWP',
            'NAMA BANK',
            'NOMOR AKUN',
            'NAMA AKUN',
            'STATUS'
        ];
    }

    /**
     * @var Supplier $supplier
     * @return array
     */
    public function map($supplier): array
    {
        // TODO: Implement map() method.
        return [
            $supplier->code ?? '-',
            $supplier->name,
            $supplier->category ?? '-',
            $supplier->email1 ?? '-',
            $supplier->email2 ?? '-',
            $supplier->phone1 ?? '-',
            $supplier->phone2 ?? '-',
            $supplier->fax ?? '-',
            $supplier->cellphone ?? '-',
            $supplier->contact_person ?? '-',
            $supplier->city ?? '-',
            $supplier->address ?? '-',
            $supplier->npwp ?? '-',
            $supplier->bank_name ?? '-',
            $supplier->bank_account_number ?? '-',
            $supplier->bank_account_name ?? '-',
            strtoupper($supplier->status->description)
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        // TODO: Implement query() method.

        $supplier = Supplier::orderBy('name');

        $this->counter = $supplier->count();

        return $supplier;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        // Format to string
        return [
            'F' => '@',
            'G' => '@',
            'H' => '@',
            'I' => '@',
            'M' => '@',
            'O' => '@'
        ];
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