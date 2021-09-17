<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use RestApi;

// Model
use App\BlperApiCount;
use App\BlperRelationKeywordTrend;

class NaverTrendMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blper:getNaverRelationTrend';

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
        Log::info('SCH NaverTrendMonthly    Start'.date('Ymd H:i:s'));

        $date = date("Ym");

        // DB
        unset($cnt);
        $cnt = BlperRelationKeywordTrend::where('date', '=', $date)
        ->take(1)
        ->count();

        if($cnt>0){

            Log::info('SCH NaverTrendMonthly    Trend List Aleady => '.$cnt);

        }else{
            // 기본값 설정
            $result = Array();
            $setQuery = Array(
                "month"=>date('m')  // 월 1200개씩 제공
            );

            $_API['url'] = env('NAVER_AD_URL');
            $_API['key'] = env('NAVER_AD_KEY');
            $_API['secret'] = env('NAVER_AD_SECRET');
            $_API['id'] = env('NAVER_AD_ID');
            
            // stp1. Naver AD API
            unset($res, $apiAd);
            $apiAd = new RestApi('NAVER_AD', $_API);
            $res = $apiAd->GET('/keywordstool', $setQuery);

            if(isset($res['keywordList'])){
                unset($trend);
                foreach( $res['keywordList'] as $k => $datas ){
                    $trend = new BlperRelationKeywordTrend();
                    $trend->no = $k;
                    $trend->date = $date;
                    $trend->keyword = $datas['relKeyword'];
                    $trend->status = 'N';
                    $trend->period = null;
                    $trend->ratio = null;
                    $trend->save();
                }
            }

            Log::info('SCH NaverTrendMonthly    Trend List Get => '.count($res['keywordList']));
        }

        

        // 조회가능 건수 체크
        $today = date('Y-m-d');
        $apiName = "NAVER_DATALAB_SEARCH";

        unset($cnt);
        $cnt = BlperApiCount::select('cnt')
        ->where('today', '=', $today)
        ->where('name', '=', $apiName)
        ->take(1)
        ->get();

        // Collection Object Cnt
        if(empty($cnt[0]['cnt'])){
            $api_cnt=0;
        }else{
            $api_cnt = $cnt[0]['cnt'];
        }

        // 조회가능 회수
        $remaining_cnt = env('NAVER_DATALAB_SEARCH_LIMIT') - $api_cnt;
        // $remaining_cnt = 300;

        Log::info('SCH NaverTrendMonthly    DataLab Remaining Search Cnt  => '.$remaining_cnt);
        if($remaining_cnt>0){
            
            unset($list);
            $lists = BlperRelationKeywordTrend::where('date', '=', $date)
            -> where('status', '=', 'N')
            -> orderBy('no')
            ->limit($remaining_cnt)
            ->get();
            
            Log::info('SCH NaverTrendMonthly    DataLab Remaining Target Cnt  => '.count($lists));
            if(!empty($lists)){
                foreach($lists as $datas){

                    // 기본값 설정
                    $id = $datas->id;
                    $keyword = $datas->keyword;
                    $date_s = date("Y-m", strtotime("-".env('NAVER_DATALAB_SEARCH_TERM')."month"))."-01";
                    $date_e = datE("Y-m-d");
                    $query = trim($keyword);
                    
                    $setQuery = Array(
                        "startDate" => $date_s,                             //  Y yyyy-mm-dd 형식
                        "endDate" =>  $date_e,                              //  Y yyyy-mm-dd 형식
                        "timeUnit" => "month",                              //  Y date: 일간 / week: 주간 / month: 월간
                        "keywordGroups" => Array(
                            0=>Array(
                                "groupName" => $query,
                                "keywords" => Array(
                                    $query
                                )
                            )
                        ),
                    );

                    // Naver Search Contents
                    unset($_API);
                    $_API['url'] = env('NAVER_DATALAB_URL');
                    $_API['id'] = env('NAVER_DATALAB_ID');
                    $_API['secret'] = env('NAVER_DATALAB_SECRET');        
                    $apiLab = new RestApi('NAVER_DATALAB', $_API);
                    $res = $apiLab->POST($setQuery);  
                    if(isset($res)){
                        $getPeriod = $getRatio = Array();

                        foreach($res as $k=>$datas){
                            $getPeriod[$k] = substr(str_replace('-','.', $datas['period']),2);
                            $getRatio[$k] = $datas['ratio'];
                        }

                        $api = BlperRelationKeywordTrend::where('_id', '=', $id)
                        ->where('keyword', '=', $keyword)
                        ->update(['status'=>'Y', 'period'=>$getPeriod, 'ratio'=>$getRatio]);

                        Log::info('SCH NaverTrendMonthly    DataLab Update => '.$keyword);
                    } 
                }
            }
        }
        
        // if(isset($res['keywordList'])){
        //     $parent = 0;
        //     $child = 0;
        //     $group = 0;
        //     $cnt = 0;
        //     foreach( $res['keywordList'] as $k => $datas ){
        //         $result[$parent][$child]['groupName'] = 'keyword_group_'.$group;
        //         $result[$parent][$child]['keywords'][$cnt] = $datas['relKeyword'];
        //         $cnt++;

        //         if(($k+1)%20===0){
        //             $group++;
        //             $cnt = 0;
        //         }
                
        //         if(($k+1)%100===0){
        //             $child++;
        //         }
                
        //         if(($k+1)%500===0){
        //             $parent++;
        //             $child=0;
        //         }
        //     }
        // }
        
        // // stp2. Naver DataLab API
        // unset($res, $apiLab);
        // if(isset($result)){
        //     $date_s = date("Y-m", strtotime("-".env('NAVER_DATALAB_SEARCH_TERM')."month"))."-01";
        //     $date_e = datE("Y-m-d");

        //     // Naver Search Contents
        //     unset($_API);
        //     $_API['url'] = env('NAVER_DATALAB_URL');
        //     $_API['id'] = env('NAVER_DATALAB_ID');
        //     $_API['secret'] = env('NAVER_DATALAB_SECRET');        
        //     $apiLab = new RestApi('NAVER_DATALAB', $_API);
            
        //     foreach($result as $arr){
        //         $setQuery = Array(
        //             "startDate" => $date_s,                             //  Y yyyy-mm-dd 형식
        //             "endDate" =>  $date_e,                              //  Y yyyy-mm-dd 형식
        //             "timeUnit" => "month",                              //  Y date: 일간 / week: 주간 / month: 월간
        //             "keywordGroups" => $arr
        //         );

        //         // Log::info(json_encode($arr, JSON_UNESCAPED_UNICODE));
        //         $res = $apiLab->POST($setQuery);  
        //         Log::info($res);
        //     }

        //     exit;
        // }


        Log::info('SCH NaverTrendMonthly    END'.date('Ymd H:i:s'));
        return 0;
    }
}