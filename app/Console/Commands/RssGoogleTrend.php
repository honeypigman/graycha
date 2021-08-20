<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log;

// Controller
use App\Http\Controllers\BlperController;

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
        Log::info('SCH RealTimeKeyword-GoogleTrends    Start'.date('Ymd H:i:s'));

        $today=date('Y-m-d');
        $site = "gt";
        $url = env('RSS_GOOGLE_TREND');
        $blper = app(BlperController::class);
        $returnValue = $blper->curl($url);
        
        // Xml TO Array
        $getXml = simplexml_load_string($returnValue, "SimpleXMLElement", LIBXML_NOCDATA);
        $setJson = json_encode($getXml);
        $getArray = json_decode($setJson,1);

        foreach($getArray['channel']['item'] as $k=>$data){
            $result[$k]['site'] = $site;
            $result[$k]['title'] = $data['title'];
            $result[$k]['link'] = $data['link'];
            $result[$k]['date'] = date('Y-m-d', strtotime($data['pubDate']));
            // $result[$k]['content'] = $data['description'];
        }

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
        
        Log::info('SCH RealTimeKeyword-GoogleTrends    CNT : '.count($getArray['channel']['item']));
        Log::info('SCH RealTimeKeyword-GoogleTrends    END'.date('Ymd H:i:s'));
        return 0;
    }
}
