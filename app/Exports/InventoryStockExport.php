<?php

namespace App\Exports;

use App\Models\ItemStock;
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

class InventoryStockExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;
    private $warehouseId;
    private $counter;

    public function __construct(string $warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    public function headings(): array
    {
        return [
            'KODE',
            'NAMA',
            'PART NUMBER',
            'GUDANG',
            'LOKASI/RAK',
            'STOCK',
            'SATUAN UNIT',
            'KATEGORI',
            'TIPE ALAT BERAT'
        ];
    }

    /**
     * @var ItemStock $itemStock
     * @return array
     */
    public function map($itemStock): array
    {
        // TODO: Implement map() method.
        return [
            $itemStock->item->code,
            $itemStock->item->name,
            $itemStock->item->part_number ?? '-',
            $itemStock->warehouse->name,
            $itemStock->location,
            $itemStock->stock ?? '0',
            $itemStock->item->uom,
            $itemStock->item->group->name ?? '-',
            $itemStock->item->machinery_type ?? '-'
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        // TODO: Implement query() method.
        if($this->warehouseId === '-1'){
            $itemStock = ItemStock::query();
            $this->counter = $itemStock->count();

            return $itemStock;
        }

        $itemStock = ItemStock::query()->where('warehouse_id', $this->warehouseId);
        $this->counter = $itemStock->count();

        return $itemStock;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
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

                $columnWarehouse = 'D2:D'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnWarehouse)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnUom = 'G2:G'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnCategory = 'H2:H'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnCategory)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
