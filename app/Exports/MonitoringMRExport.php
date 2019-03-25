<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 22/03/2019
 * Time: 15:09
 */

namespace App\Exports;

use App\Models\MaterialRequestHeader;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
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

class MonitoringMRExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents, WithStrictNullComparison
{
    use Exportable;
    private $dateStart;
    private $dateEnd;
    private $status;
    private $site;
    private $priority;
    private $counter;

    public function __construct(string $dateStart,
                                string $dateEnd,
                                string $status,
                                string $site,
                                string $priority)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->status = $status;
        $this->site = $site;
        $this->priority = $priority;
    }

    public function headings(): array
    {
        return [
            'NOMOR MR',
            'TANGGAL MR',
            'SITE',
            'DEPARTEMEN',
            'PRIORITAS',
            'UNIT',
            'KODE INVENTORY BELUM PO',
            'PART NUMBER BELUM PO',
            'QTY BELUM PO',
            'SATUAN',
            'KODE INVENTORY SUDAH PO',
            'PART NUMBER SUDAH PO',
            'QTY SUDAH PO',
            'SATUAN',
            'HARGA SATUAN',
            'TOTAL',
            'PPN 10%',
            'TOTAL HARGA + PPN',
            'PEMBUAT PO',
            'NOMOR PR',
            'NOMOR PO',
            'TANGGAL PO',
            'SUPPLIER',
            'LOKASI SUPPLIER',
            'NOMOR GR',
            'LEAD TIME GR',
            'NOMOR SJ',
            'TANGAL KIRIM',
            'TANGGAL DITERIMA SITE',
            'STATUS SJ',
            'LEAD TIME SJ DIKONFIRMASI'
        ];
    }

    /**
     * @var MaterialRequestHeader $header
     * @return array
     */
    public function map($header): array
    {
        // TODO: Implement map() method.
        $date = Carbon::parse($header->date)->format('d M Y');

        // MR Tracking
        $isTrackingAvailable = true;
        if($header->issued_docket_headers->count() === 0 && $header->purchase_request_headers->count() === 0){
            $isTrackingAvailable = false;
        }

        $trackedPrHeaderStr = "";
        $trackedPoHeaderStr = "";
        $trackedGrHeaderStr = "";
        $trackedGrHeaderLeadTime = "";
        $trackedSjHeaderStr = "";
        $trackedSjStatus = "";
        $trackedSjHeaderLeadTime = "";
        $trackedInventoryIdStr = "";
        $trackedInventoryNameStr = "";
        $trackedInventoryPartStr = "";
        $trackedInventoryUnitStr = "";
        $trackedInventoryQtyStr = "";
        $trackedInventoryPricePerPieceStr = "";
        $trackedInventoryTotalPriceStr = "";
        $trackedInventoryTotalPpnStr = "";
        $trackedInventoryTotalAllStr = "";
        $trackedPoHandleBy = "";
        $trackedPoDate = "";
        $trackedSupplierName = "";
        $trackedSupplierLocation = "";
        $trackedSjStartDate = "";
        $trackedSjFinishDate = "";
        $trackedUnPoedItemCodes = "";
        $trackedUnPoedItemCodesArr = [];
        $trackedUnPoedItemPartNumbers = "";
        $trackedUnPoedItemQty = "";
        $trackedUnPoedItemUom = "";

        if($isTrackingAvailable){
            $trackedPrHeader = $header->purchase_request_headers->first();
            if(!empty($trackedPrHeader)){
                $trackedPrHeaderStr = $trackedPrHeader->code;

                $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;
                $trackedGrHeaders = new Collection();
                $trackedPoHeaderStr = "";
                foreach($trackedPoHeaders as $poHeader){
                    $trackedPoHeaderStr .= $poHeader->code. "\n";
                    foreach($poHeader->item_receipt_headers as $grHeader){
                        $trackedGrHeaders->add($grHeader);
                    }
                }

                $trackedSjHeaders = new Collection();
                $trackedGrHeaderStr = "";
                $trackedGrHeaderLeadTime = "";
                foreach($trackedGrHeaders as $trackedGrHeader){
                    $trackedGrHeaderStr .= $trackedGrHeader->code. "\n";
                    if(!empty($trackedGrHeader->lead_time)){
                        $grLeadTime = $trackedGrHeader->lead_time. " Hari\n";
                    }
                    else{
                        $mrDate = Carbon::parse($header->date);
                        $grDate = Carbon::parse($trackedGrHeader->date);
                        $grLeadTime = $mrDate->diffInDays($grDate). " Hari\n";
                    }

                    $trackedGrHeaderLeadTime .= $grLeadTime;
                    foreach ($trackedGrHeader->delivery_order_headers as $sjHeader){
                        $trackedSjHeaders->add($sjHeader);
                    }
                }

                $trackedSjHeaderStr = "";
                $trackedSjStatus = "";
                $trackedSjHeaderLeadTime = "";
                foreach($trackedSjHeaders as $trackedSjHeader){
                    $trackedSjHeaderStr .= $trackedSjHeader->code. "\n";
                    $trackedSjStatus .= strtoupper($trackedSjHeader->status->description). "\n";

                    if($trackedSjHeader->status_id === 4){
                        if(!empty($trackedSjHeader->lead_time)){
                            $sjLeadTime = $trackedSjHeader->lead_time. " Hari\n";
                        }
                        else{
                            $mrDate = Carbon::parse($header->date);
                            $sjConfirmDate = Carbon::parse($trackedSjHeader->confirm_date);
                            $sjLeadTime = $mrDate->diffInDays($sjConfirmDate). " Hari\n";
                        }
                    }
                    else{
                        $sjLeadTime = "-\n";
                    }

                    $trackedSjHeaderLeadTime .= $sjLeadTime;

                    //Add SJ Start Time and Finish Time
                    $trackedSjStartDate .= $trackedSjHeader->date_string . "\n";
                    $trackedSjFinishDate .= $trackedSjHeader->confirm_date_string . "\n";
                }
            }

//            $trackedPiHeaders = new Collection();
//            foreach($trackedPoHeaders as $poHeader){
//                foreach($poHeader->purchase_invoice_headers as $piHeader){
//                    $trackedPiHeaders->add($piHeader);
//                }
//            }

            // Add PO details
            $tmp = $header->purchase_request_headers->first();
            if(!empty($tmp)){
                $purchaseOrders = $tmp->purchase_order_headers;

                //Data Inventory
                if(!empty($purchaseOrders)){
                    foreach ($purchaseOrders as $headerData){
                        $details = $headerData->purchase_order_details;
                        if(!empty($details)){
                            foreach ($details as $detail){
                                $trackedInventoryIdStr .= $detail->item->code . "\n";
                                $trackedInventoryNameStr .= $detail->item->name . "\n";
                                $trackedInventoryPartStr .= $detail->item->part_number . "\n";
                                $trackedInventoryUnitStr .= $detail->item->uom . "\n";
                                $trackedInventoryQtyStr .= $detail->quantity . "\n";
                                $trackedInventoryPricePerPieceStr .= number_format($detail->price, 2, ",", ".") . "\n";
                                $trackedInventoryTotalPriceStr .= number_format($detail->subtotal, 2, ",", ".") . "\n";

                                //Data PPN dan total harga plus PPN
                                if($headerData->ppn_amount != null){
                                    $tmpPpn = $detail->subtotal * 10 / 100;
                                    $tmpPpnTotal = $detail->subtotal + $tmpPpn;

                                    $trackedInventoryTotalPpnStr .= number_format($tmpPpn, 2, ",", ".") . "\n";
                                    $trackedInventoryTotalAllStr .= number_format($tmpPpnTotal, 2, ",", ".") . "\n";
                                }
                            }
                        }

                        //Handle By
                        $trackedPoHandleBy .= $headerData->createdBy->name . "\n";
                        $trackedPoDate .= $headerData->date_string . "\n";
                        $trackedSupplierName .= $headerData->supplier_name . "\n";
                        $trackedSupplierLocation .= $headerData->supplier->city . "\n";
                    }
                }
            }
        }

//        $trackedIdHeaderStr = "";
//        $trackedIdHeaders = $header->issued_docket_headers;
//        foreach ($trackedIdHeaders as $idHeader){
//            $trackedIdHeaderStr .= $idHeader->code. "\n";
//        }

        // Add item not poed details
        $prHeader2 = $header->purchase_request_headers->first();
        if(!empty($prHeader2)){
            foreach ($prHeader2->purchase_request_details as $prDetail2){
                if($prDetail2->quantity_poed < $prDetail2->quantity){
                    $qtyUnPoed = $prDetail2->quantity - $prDetail2->quantity_poed;

                    $trackedUnPoedItemCodes .= $prDetail2->item->code . "\n";
                    array_push($trackedUnPoedItemCodesArr, $prDetail2->item->code);
                    $trackedUnPoedItemPartNumbers .= $prDetail2->item->part_number . "\n";
                    $trackedUnPoedItemQty .= $qtyUnPoed . "\n";
                    $trackedUnPoedItemUom .= $prDetail2->item->uom . "\n";
                }
            }
        }
        else{
            foreach ($header->material_request_details as $detail){
                $trackedUnPoedItemCodes .= $detail->item->code . "\n";
                $trackedUnPoedItemPartNumbers .= $detail->item->part_number . "\n";
                $trackedUnPoedItemQty .= $detail->quantity . "\n";
                $trackedUnPoedItemUom .= $detail->item->uom . "\n";
            }
        }

        //Add more Fields
        //Check first
        $machineCode = '';
        if($header->machinery != null){
            $machineCode = $header->machinery->code;
        }

        $mrDetails = DB::table('material_request_details')
            ->join('items', 'material_request_details.item_id', '=', 'items.id')
            ->select('items.code')
            ->where('material_request_details.header_id', '=', $header->id);

        return[
            $header->code,
            $date,
            $header->site->name,
            $header->department->name,
            $header->priority,
            $machineCode,
            $trackedUnPoedItemCodes,
            $trackedUnPoedItemPartNumbers,
            $trackedUnPoedItemQty,
            $trackedUnPoedItemUom,
            $trackedInventoryIdStr,
            //$trackedInventoryNameStr,
            $trackedInventoryPartStr,
            $trackedInventoryUnitStr,
            $trackedInventoryQtyStr,
            $trackedInventoryPricePerPieceStr,
            $trackedInventoryTotalPriceStr,
            $trackedInventoryTotalPpnStr,
            $trackedInventoryTotalAllStr,
            $trackedPoHandleBy,
            $trackedPrHeaderStr,
            $trackedPoHeaderStr,
            $trackedPoDate,
            $trackedSupplierName,
            $trackedSupplierLocation,
            $trackedGrHeaderStr,
            $trackedGrHeaderLeadTime,
            $trackedSjHeaderStr,
            $trackedSjStartDate,
            $trackedSjFinishDate,
            $trackedSjStatus,
            $trackedSjHeaderLeadTime
        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        // TODO: Implement query() method.
        $start = Carbon::createFromFormat('d M Y', $this->dateStart, 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $this->dateEnd, 'Asia/Jakarta');

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $header = MaterialRequestHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        $status = $this->status;
        if($status !== '0'){
            $header = $header->where('status_id', $status);
        }

        $site = $this->site;
        if($site !== '0'){
            $header = $header->where('site_id', $site);
        }

        $priority = $this->priority;
        if($priority !== 'ALL'){
            $header = $header->where('priority', $priority);
        }

        $header = $header->orderBy('date','desc');
        $this->counter = $header->count();

        //dd($header);

        return $header;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        // Format to string
        return [
            'A' => '@',
            'G' => '@',
            'H' => '@',
            'K' => '@',
            'L' => '@'
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
                $cellRange = 'A1:AF1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $this->counter += 10;

                $columnVertical = 'A2:AF'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnVertical)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                $columnMrDate = 'B2:B'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnMrDate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnUnPoedInventory = 'G2:G'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUnPoedInventory)->getAlignment()->setWrapText(true);

                $columnUnPoedPartNumber = 'H2:H'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUnPoedPartNumber)->getAlignment()->setWrapText(true);

                $columnUnPoedQty = 'I2:I'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUnPoedQty)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($columnUnPoedQty)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $columnUnPoedUom = 'J2:J'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUnPoedUom)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($columnUnPoedUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnPoedInventory = 'K2:K'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnPoedInventory)->getAlignment()->setWrapText(true);

                $columnPoedPartNumber = 'L2:L'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnPoedPartNumber)->getAlignment()->setWrapText(true);

                $columnPoedQty = 'M2:M'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnPoedQty)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($columnPoedQty)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $columnPoedUom = 'N2:N'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnPoedUom)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($columnPoedUom)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $columnAfterX = 'Y2:AE'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnAfterX)->getAlignment()->setWrapText(true);

                $columnUom2 = 'O2:O'. $this->counter;
                $event->sheet->getDelegate()->getStyle($columnUom2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}