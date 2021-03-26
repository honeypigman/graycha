<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

// Model
use App\KairspecApiHis;
use App\KairspecApiMsrstnList;
use App\KairspecApiStationList;

class KairspecResetThreeDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kairspec:resetThreeDays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 기준일
        $delday = date('Y-m-d', strtotime('-3 days'));

        // 전체 측정소
        $cnt = KairspecApiMsrstnList::where('today', '<', $delday)->count();
        Log::info('SCH KairspecResetThreeDays-KairspecApiMsrstnList Del - '.$delday.'/'.$cnt);
        if($cnt>0){
            KairspecApiMsrstnList::where('today', '<', $delday)->delete();
            Log::info('SCH KairspecResetThreeDays-KairspecApiMsrstnList Del - OK');
        }
        
        // 전체 측정소 상세정보
        $cnt = KairspecApiStationList::where('date', '<', $delday)->count();
        Log::info('SCH KairspecResetThreeDays-KairspecApiStationList Del - '.$delday.'/'.$cnt);
        if($cnt>0){
            KairspecApiStationList::where('date', '<', $delday)->delete();
            Log::info('SCH KairspecResetThreeDays-KairspecApiStationList Del - OK');
        }

        // API His
        $cnt = KairspecApiHis::where('date', '<', $delday)->count();
        Log::info('SCH KairspecResetThreeDays-KairspecApiHis Del - '.$delday.'/'.$cnt);
        if($cnt>0){
            KairspecApiHis::where('date', '<', $delday)->delete();
            Log::info('SCH KairspecResetThreeDays-KairspecApiHis Del - OK');
        }
    }
}
