<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 23/11/2018
 * Time: 9:40
 */

namespace App\Exports;

use App\Models\PurchaseRequestHeader;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class PurchaseRequestExport implements FromView, ShouldAutoSize, WithStrictNullComparison, WithEvents, WithColumnFormatting
{
    use Exportable;

    private $dateStart;
    private $dateEnd;
    private $departmentId;
    private $statusId;
    private $counter = 0;

    public function __construct(string $dateStart,
                                string $dateEnd,
                                int $departmentId,
                                int $statusId)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->departmentId = $departmentId;
        $this->statusId = $statusId;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $prHeaders = PurchaseRequestHeader::whereBetween('date', array($this->dateStart, $this->dateEnd));

        // Filter department
        if($this->departmentId != 0){
            $prHeaders = $prHeaders->where('department_id', $this->departmentId);
        }

        // Filter status
        $status = $this->statusId;
        if($this->statusId != 0){
            $prHeaders = $prHeaders->where('status_id', $status);
        }

        $prHeaders = $prHeaders->orderByDesc('date')
            ->get();

        $this->counter = $prHeaders->count();

        $data =[
            'prHeaders'         => $prHeaders
        ];

        return view('documents.purchase_requests.purchase_requests_excel', $data);
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


                $columnStatus= 'E2:E'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnStatus)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnUom= 'K2:K'. $this->counter;
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
            'G' => '@',
            'H' => '@',
            'I' => '@'
        ];
    }
}