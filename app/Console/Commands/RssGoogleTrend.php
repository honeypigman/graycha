<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log;
use RestApi;

// Model
use App\BlperRealTimeKeyword;

class RssGoogleTrend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blper:getRssGoogleTrend';

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
        if(env('Batch_blper_getRssGoogleTrend')!="Y"){
            Log::info('SCH RealTimeKeyword-GoogleTrends    Batch Stop');
            exit;
        }

        Log::info('SCH RealTimeKeyword-GoogleTrends    Start'.date('Ymd H:i:s'));

        $setQuery = Array(
            "geo"=>"KR"
        );
        $result = Array();
        $site = "google";
        $today=date('Y-m-d');
        $_API['url'] = env('RSS_GOOGLE_TREND');

        $api = new RestApi('GOOGLE_TREND', $_API);
        $res = $api->GET('rss', $setQuery, 'XML');

        foreach($res['channel']['item'] as $k=>$data){
            $result[$k]['site'] = $site;
            $result[$k]['title'] = $data['title'];
            $result[$k]['link'] = $data['link'];
            $result[$k]['date'] = date('Y-m-d', strtotime($data['pubDate']));
        }

        try{
            // DB
            $obj = BlperRealTimeKeyword::select('_id')
            ->where('today', '=', $today)
            ->take(1)
            ->get();

            // Collection ObjectId
            if(empty($obj[0]['_id'])){
                $oid=null;
            }else{
                $oid = $obj[0]['_id'];
            }

            // Update
            if($oid){
                $issue = BlperRealTimeKeyword::find($oid);
                $issue->today = $today;
                $issue->items = json_encode($result);
                $issue->save();
            }

            // Save
            else{
                $issue = new BlperRealTimeKeyword();
                $issue->today = $today;
                $issue->items = json_encode($result);
                $issue->save();
            }  
            Log::info('SCH RealTimeKeyword-GoogleTrends    CNT : '.count($res['channel']['item']));
        }catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            Log::info("ERR>MongoDB\Driver\Exception\ConnectionTimeoutException > ".$e->getMessage());
        }
        Log::info('SCH RealTimeKeyword-GoogleTrends    END'.date('Ymd H:i:s'));
        return 0;
    }
}
