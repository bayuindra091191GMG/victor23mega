<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 09/10/2018
 * Time: 10:09
 */

namespace App\Exports;


use App\Models\Item;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventoryMasterExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents, WithStrictNullComparison
{
    use Exportable;
    private $isSite;
    private $categoryId;
    private $counter;

    public function __construct(bool $isSite, string $categoryId)
    {
        $this->isSite = $isSite;
        $this->categoryId = $categoryId;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        //
        $item = Item::query();

        if($this->categoryId !== "-1"){
            $item = $item->where('group_id', $this->categoryId);
        }

        $item = $item->orderBy("code");

        $this->counter = $item->count();

        return $item;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if($this->isSite){
            return [
                'KODE',
                'NAMA',
                'PART NUMBER',
                'SATUAN UNIT',
                'KATEGORI',
                'TIPE ALAT BERAT',
                'STOCK ON HAND',
                'STOCK ON ORDER'
            ];
        }
        else{
            return [
                'KODE',
                'NAMA',
                'PART NUMBER',
                'SATUAN UNIT',
                'KATEGORI',
                'TIPE ALAT BERAT',
                'COST',
                'STOCK ON HAND',
                'STOCK ON ORDER',
                'TOTAL COST'
            ];
        }
    }

    /**
     * @param $item
     * @return array
     */
    public function map($item): array
    {
        // TODO: Implement map() method.
        if($this->isSite){
            return [
                $item->code,
                $item->name,
                $item->part_number ?? '-',
                $item->uom,
                $item->group->name ?? '-',
                $item->machinery_type ?? '-',
                $item->stock ?? 0,
                $item->stock_on_order ?? 0,
            ];
        }
        else{
            return [
                $item->code,
                $item->name,
                $item->part_number ?? '-',
                $item->uom,
                $item->group->name ?? '-',
                $item->machinery_type ?? '-',
                $item->value ?? 0,
                $item->stock ?? 0,
                $item->stock_on_order ?? 0,
                $item->total_value
            ];
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        // TODO: Implement columnFormats() method.
        // Format to string
        return [
            'A' => '@',
            'B' => '@',
            'C' => '@'
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

                $this->counter += 10;

                $columnUom = 'D2:D'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnCategory = 'E2:E'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnCategory)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnMachinery = 'F2:F'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnMachinery)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}