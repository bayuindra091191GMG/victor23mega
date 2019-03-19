<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemIssuedCalibrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 0;

    //protected $skip;
    //protected $offset;
    protected $itemStocks;
    protected $issuedDocketDetails;

    /**
     * Create a new job instance.
     *
     * @param int $skip
     * @param int $offset
     * @param Collection $itemStocks
     * @param Collection $issuedDocketDetails
     */
    public function __construct(Collection $itemStocks,
                                Collection $issuedDocketDetails)
    {
        //$this->skip = $skip;
        //$this->offset = $offset;
        $this->itemStocks = $itemStocks;
        $this->issuedDocketDetails = $issuedDocketDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $firstDocketDate = Carbon::createFromFormat('Y-m-d', '2018-08-01');
            $dateToday = Carbon::today();
            $diffInDays = $dateToday->diffInDays($firstDocketDate);

            foreach ($this->itemStocks as $itemStock){
                $itemIssuedDetails = $this->issuedDocketDetails
                    ->where('warehouse_id', $itemStock->warehouse_id)
                    ->where('item_id', $itemStock->item_id);
                $totalIssued = $itemIssuedDetails->sum('quantity');
                $totalCount = $itemIssuedDetails->count();

                //error_log("total count: ". $this->itemStocks->count());
                //error_log("total count: ". $this->issuedDocketDetails->count());
                //error_log("total issued: ". $totalIssued);

                // Get movement status
                if($totalCount === 0){
                    $movement = "DEAD";
                }
                elseif ($totalCount < 4){
                    $movement = "SLOW";
                }
                elseif ($totalCount < 9){
                    $movement = "MEDIUM";
                }
                else{
                    $movement = "FAST";
                }

                if($diffInDays < 365){
                    $totalIssued = floor($totalIssued / $diffInDays * 365);
                }

                $newMinStock = floor($totalIssued / 360 * 60);
                $newMaxStock = floor($totalIssued / 360 * 90);

                DB::transaction(function () use($itemStock, $totalIssued, $newMinStock, $newMaxStock, $movement) {
                    DB::table('item_stocks')
                        ->where('warehouse_id', $itemStock->warehouse_id)
                        ->where('item_id', $itemStock->item_id)
                        ->update(['qty_issued_12_months' => $totalIssued, 'stock_min' => $newMinStock, 'stock_max' => $newMaxStock, 'movement_status' => $movement]);
                }, 3);
            }
        }
        catch( \Exception $ex){
            error_log($ex);
            Log::error($ex->getMessage());
        }
    }
}
