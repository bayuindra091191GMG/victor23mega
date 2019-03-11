<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ItemIssuedCalibrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $skip;
    protected $offset;
    protected $issuedDocketDetails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $skip,
                                int $offset,
                                Collection $issuedDocketDetails)
    {
        $this->skip = $skip;
        $this->offset = $offset;
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
            $itemStocks = DB::table('item_stocks')
                ->offset($this->offset)
                ->limit($this->skip)
                ->get();

            foreach ($itemStocks as $itemStock){
                $itemIssuedDetails = $this->issuedDocketDetails->where('issued_docket_details.item_id', $itemStock->item_id);
                $totalIssued = $itemIssuedDetails->sum('issued_docket_details.quantity');

                DB::table('item_stocks')
                    ->where('item_id', $itemStock->item_id)
                    ->update(['qty_issued_12_months' => $totalIssued]);
            }
        }
        catch( \Exception $ex){
            error_log($ex);
        }
    }
}
