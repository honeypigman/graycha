<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use RestApi;

// Model
use App\BlperRealtimeIssue;

class RssKoreaIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blper:getRssKoreaIssue';

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
        Log::info('SCH RssKoreaIssue    Start'.date('Ymd H:i:s'));

        $setQuery = Array();
        $result = Array();

        $site = "kor";
        $today=date('Y-m-d');
        $_API['url'] = env('RSS_KOREA_ISSUE');
        
        $api = new RestApi('KOREA_ISSUE', $_API);
        $res = $api->GET('policy.xml', $setQuery, 'XML');

        foreach($res['channel']['item'] as $k=>$data){
            $result[$k]['site'] = $site;
            $result[$k]['title'] = $data['title'];
            $result[$k]['link'] = $data['link'];
            $result[$k]['date'] = date('Y-m-d', strtotime($data['pubDate']));
        }

        try{
            // DB
            $obj = BlperRealtimeIssue::select('_id')
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
                $issue = BlperRealtimeIssue::find($oid);
                $issue->today = $today;
                $issue->items = json_encode($result);
                $issue->save();
            }

            // Save
            else{
                $issue = new BlperRealtimeIssue();
                $issue->today = $today;
                $issue->items = json_encode($result);
                $issue->save();
            }
            Log::info('SCH RssKoreaIssue    CNT : '.count($res['channel']['item']));
        }catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            Log::info("ERR>MongoDB\Driver\Exception\ConnectionTimeoutException > ".$e->getMessage());
        }
        Log::info('SCH RssKoreaIssue    END'.date('Ymd H:i:s'));
        return 0;
    }
}