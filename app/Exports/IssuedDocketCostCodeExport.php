<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 10/12/2018
 * Time: 10:16
 */

namespace App\Exports;

use App\Models\Account;
use App\Models\IssuedDocketHeader;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IssuedDocketCostCodeExport implements FromView, ShouldAutoSize, WithStrictNullComparison, WithEvents, WithColumnFormatting
{
    use Exportable;

    private $dateStart;
    private $dateEnd;
    private $costCode;
    private $department;
    private $warehouse;
    private $type;
    private $isHo;
    private $counter = 0;

    public function __construct(string $dateStart,
                                string $dateEnd,
                                string $costCode,
                                int $departmentId,
                                int $warehouseId,
                                string $isHo)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->costCode = $costCode;
        $this->department = $departmentId;
        $this->warehouse = $warehouseId;
        $this->isHo = $isHo;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $costCodes = new Collection();
        $allIdHeaders = new Collection();

//        dd($this->costCode);

        $accounts = Account::all();
        foreach($accounts as $account){

            if($account->issued_docket_headers->count() === 0){
                continue;
            }

            // Filter cost code
            if($this->costCode !== '0'){
                if($account->code !== $this->costCode){
                    continue;
                }
            }

            $code = new Account();
            $code->code = $account->code;
            $code->location = $account->location;
            $code->department = $account->department;
            $code->division = $account->division;
            $code->description = $account->description;
            $code->status_id = $account->status_id;
            $code->created_by = $account->created_by;
            $code->created_at = $account->created_at;
            $code->updated_by = $account->updated_by;
            $code->updated_at = $account->updated_at;
            $code->createdBy = $account->createdBy;
            $code->updatedBy = $account->updatedBy;

            $idHeaders = IssuedDocketHeader::whereBetween('date', array($this->dateStart, $this->dateEnd))
                ->where('account_id', $account->id);

            if($this->department !== 0){
                $department = $this->department;
                $idHeaders = $idHeaders->where('department_id', $department);
            }

            if($this->warehouse !== -1){
                $warehouse = $this->warehouse;
                $idHeaders = $idHeaders->where('warehouse_id', $warehouse);
            }

            $idHeaders = $idHeaders->orderByDesc('date')
                ->get();

            $code->issued_docket_headers = $idHeaders;
            $costCodes->add($code);

            foreach ($idHeaders as $idHeader){
                $allIdHeaders->add($idHeader);
            }

            $this->counter++;
        }

        $totalValue = 0;

        foreach ($allIdHeaders as $idHeader){
            foreach ($idHeader->issued_docket_details as $idDetail){
                $value = $idDetail->item->value ?? 0;
                $subTotalValue = $value * $idDetail->quantity;
                $totalValue += $subTotalValue;
            }
        }

        $this->counter = $this->counter * 2;

//        dd($costCodes);

        $data =[
            'costCodes'         => $costCodes,
            'totalValue'        => $totalValue
        ];

        return view('documents.issued_dockets.issued_docket_cost_code_excel', $data);
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
        ];
    }
}