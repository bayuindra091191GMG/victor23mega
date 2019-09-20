<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 11/6/2018
 * Time: 11:56 AM
 */

namespace App\Exports;

use App\Models\PurchaseOrderHeader;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PurchaseOrderExport implements FromView, ShouldAutoSize, WithStrictNullComparison, WithEvents, WithColumnFormatting
{
    use Exportable;

    private $dateStart;
    private $dateEnd;
    private $departmentId;
    private $supplierId;
    private $statusId;
    private $createdBy;
    private $counter = 0;

    public function __construct(string $dateStart,
                                string $dateEnd,
                                int $departmentId,
                                int $supplierId,
                                int $statusId,
                                int $createdBy)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->departmentId = $departmentId;
        $this->supplierId = $supplierId;
        $this->statusId = $statusId;
        $this->createdBy = $createdBy;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $poHeaders = PurchaseOrderHeader::whereBetween('date', array($this->dateStart, $this->dateEnd));

        // Filter department
        $department = $this->departmentId;
        if($this->departmentId != 0){
            $poHeaders = $poHeaders->whereHas('purchase_request_header', function($query) use($department) {
                $query->where('department_id', $department);
                }
            );
        }

        // Filter supplier
        $supplier = $this->supplierId;
        if($this->statusId != 0){
            $poHeaders = $poHeaders->where('supplier_id', $supplier);
        }

        // Filter status
        $status = $this->statusId;
        if($this->statusId != 0){
            $poHeaders = $poHeaders->where('status_id', $status);
        }

        // Filter created by
        if($this->createdBy != -1){
            $poHeaders = $poHeaders->where('created_by', $this->createdBy);
        }

        $poHeaders = $poHeaders->orderByDesc('date')
            ->get();

        $total = $poHeaders->sum('total_payment');

        $this->counter = $poHeaders->count();

        $data =[
            'poHeaders'         => $poHeaders,
            'total'             => $total
        ];

        return view('documents.purchase_orders.purchase_orders_excel', $data);
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

                $columnUom= 'I2:I'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        // TODO: Implement columnFormats() method.
        return [
            'A' => '@',
            'C' => '@',
            'E' => '@',
            'F' => '@',
            'G' => '@'
        ];
    }
}