<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 12/10/2018
 * Time: 9:31
 */

namespace App\Exports;

use App\Models\IssuedDocketHeader;
use App\Models\Item;
use App\Models\PermissionMenu;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IssuedDocketExport implements FromView, ShouldAutoSize, WithStrictNullComparison, WithEvents, WithColumnFormatting
{
    use Exportable;

    private $dateStart;
    private $dateEnd;
    private $departmentId;
    private $warehouseId;
    private $machineryId;
    private $type;
    private $itemId;
    private $isHo;
    private $counter = 0;

    public function __construct(string $dateStart,
                                string $dateEnd,
                                int $departmentId,
                                int $warehouseId,
                                int $machineryId,
                                string $type,
                                int $itemId,
                                string $isHo)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->departmentId = $departmentId;
        $this->warehouseId = $warehouseId;
        $this->machineryId = $machineryId;
        $this->type = $type;
        $this->itemId = $itemId;
        $this->isHo = $isHo;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $idHeaders = IssuedDocketHeader::with(['issued_docket_details', 'issued_docket_details.item', 'account', 'site', 'issued_docket_details.machinery', 'createdBy', 'department'])
            ->whereBetween('date', array($this->dateStart, $this->dateEnd));

        // Filter departemen
        $department = $this->departmentId;
        if($this->departmentId != 0){
            $idHeaders = $idHeaders->where('department_id', $department);
        }

        // Filter warehouse
        if($this->warehouseId != -1){
            $idHeaders = $idHeaders->where('warehouse_id', $this->warehouseId);
        }

        // Filter Machinery
        if($this->machineryId != -1){
            $unitId = $this->machineryId;
            $idHeaders = $idHeaders->whereHas('issued_docket_details', function ($query) use($unitId){
                $query->where('machinery_id', $unitId);
            });
        }

        // Filter ID type
        $totalQty = 0;
        if($this->type === 'bbm'){
            $item = Item::find($this->itemId);
            $idHeaders = $idHeaders->whereHas('issued_docket_details', function ($query) use($item){
                $query->where('item_id', $item->id);
            })->where('type', 2);
        }
        elseif($this->type === 'non-bbm'){
            $idHeaders = $idHeaders->where('type', 1);
        }

        $idHeaders = $idHeaders->orderByDesc('date')
            ->get();

        $totalValue = 0;

        if($this->isHo === '1'){
            // Check menu permission
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];

            if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 42)->first()){
                $isHo = '0';
            }
        }

        if($this->type === 'bbm'){
            foreach ($idHeaders as $idHeader){
                foreach ($idHeader->issued_docket_details as $idDetail){
                    if($idDetail->item_id === $this->itemId){
                        if($this->machineryId > -1 && $idDetail->machinery_id !== $this->machineryId){
                            continue;
                        }
                        $totalQty += $idDetail->quantity;

                        if($this->isHo === '1'){
                            $value = $idDetail->item->value ?? 0;
                            $subTotalValue = $value * $idDetail->quantity;
                            $totalValue += $subTotalValue;
                        }
                    }
                    $this->counter++;
                }
                $this->counter++;
            }
        }
        else{
            if($this->isHo === '1'){
                foreach ($idHeaders as $idHeader){
                    foreach ($idHeader->issued_docket_details as $idDetail){
                        $this->counter++;
                        if($this->machineryId > -1 && $idDetail->machinery_id !== $this->machineryId){
                            continue;
                        }

                        $value = $idDetail->item->value ?? 0;
                        $subTotalValue = $value * $idDetail->quantity;
                        $totalValue += $subTotalValue;
                    }
                    $this->counter++;
                }
            }
        }

        $this->counter = $this->counter * 2;

        $data =[
            'idHeaders'         => $idHeaders,
            'totalQty'          => $totalQty,
            'totalValue'        => $totalValue,
            'filterMachineryId' => $this->machineryId,
            'itemId'            => $this->itemId
        ];

        if($this->type === 'bbm'){
            return view('documents.issued_dockets.issued_docket_bbm_with_price_excel', $data);
        }
        else{
            return view('documents.issued_dockets.issued_docket_with_price_excel', $data);
        }
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

                if($this->type !== 'bbm'){
                    $columnUom = 'G2:G'. $this->counter;
                    $event->sheet->getDelegate()->getStyle($columnUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                else{
                    $columnHm= 'G2:G'. $this->counter;
                    $event->sheet->getDelegate()->getStyle($columnHm)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        // TODO: Implement columnFormats() method.
        if($this->type === 'bbm'){
            return [
                'G' => NumberFormat::FORMAT_NUMBER,
                'D' => '@'
            ];
        }
        else{
            return [
                'A' => '@',
                'D' => '@'
            ];
        }
    }
}