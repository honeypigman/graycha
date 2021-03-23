<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use DB;
use Func;
use Form;
use Log;

// Controller
use App\Http\Controllers\ApiController;

// Model
use App\KairspecApiMsrstnList;
use App\KairspecApiStationList;
use App\KairspecApiStationNotFoundList;

// 시도별 실시간 측정소정보 조회
//  - 배치주기 : Hourly
class KairspecStationInfoAll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
         // 전국 시도
         $_SIDO = Array(
            "서울", "부산", "대구", "인천", "광주", "대전", "울산", "경기",
            "강원", "충북", "충남", "전북", "전남", "경북", "경남", "제주", "세종"
        );

        foreach( $_SIDO as $k=>$cityName ){
            usleep(3000);
            
            if($k==8){
                sleep(60);
            }
            
            // 기본변수
            $today = date('Y-m-d');
            $time = date('H:i:s');

            //  API - 전체 측정소 목록 (KairspecCtprvnRltmMesureDnsty) - 시작
            $_DATA['uri'] = "http://openapi.airkorea.or.kr/openapi/services/rest/ArpltnInforInqireSvc";
            $_DATA['setUri'] = "http://openapi.airkorea.or.kr/openapi/services/rest/ArpltnInforInqireSvc/getCtprvnRltmMesureDnsty";
            $_DATA['data'] = Array(
                "serviceKey"=>env('API_KEY_KAIRSPEC'),
                "numOfRows"=>"9999",
                "pageNo"=>"1",
                "sidoName"=>$cityName,
                "ver"=>"1.3"
            );
            $_DATA['setDatabase'] = "getCtprvnRltmMesureDnsty";
            $url = Form::getReqUrl($_DATA);

            Log::info('SCH KairspecStationInfoAll ['.$cityName.'] '.date('Ymd H:i:s'));
            Log::info('SCH KairspecStationInfoAll ['.$cityName.'] Req>'.$url);
            $api = app(ApiController::class);
            $result = $api->curl($url);
            Log::info('SCH KairspecStationInfoAll ['.$cityName.'] Res>'.$result);
            $_ARR = json_decode($result, 1);

            foreach( $_ARR['body']['items'] as $item=>$datas ){
                foreach($datas as $cols){
                    unset($cnt, $ex_date);
                    $ex_date = explode(' ', $cols['dataTime']);

                    // Station List Chk
                    $stat = KairspecApiStationList::where('city', '=', trim($cityName))
                    ->where('stationName', '=', trim($cols['stationName']))
                    ->where('date', '=', $ex_date[0])
                    ->where('time', '=', $ex_date[1])
                    ->count();

                    // Insert StationList
                    if(empty($stat)){


                        $api = new KairspecApiStationList();
                        $api->date = $ex_date[0];
                        $api->time = $ex_date[1];
                        $api->city = $cityName;
                        $api->stationName = $cols['stationName'];
                        $api->pm10Value=$cols['pm10Value'];
                        $api->pm10Value24=$cols['pm10Value24'];
                        $api->pm25Value=$cols['pm25Value'];
                        $api->pm25Value24=$cols['pm25Value24'];
                        $api->save();
                    }

                    // Msesure List Chk
                    $obj = KairspecApiMsrstnList::select('_id')
                    ->where('city', '=', trim($cityName))
                    ->where('today', '=', $today)
                    ->where('stationName', '=', trim($cols['stationName']))
                    ->orderBy('today', 'desc')
                    ->take(1)
                    ->get();

                    // Collection ObjectId
                    if(empty($obj[0]['_id'])){
                        $oid=null;
                    }else{
                        $oid = $obj[0]['_id'];
                    }

                    // Update MsrstnList
                    unset($api);
                    if($oid){
                        Log::info($oid." > ".$cityName."/".$today."/".$cols['stationName']);
                        $api = KairspecApiMsrstnList::find($oid);
                        $api->time = $time;
                        $api->mesure_time=$cols['dataTime'];
                        $api->so2Value=$cols['so2Value'];
                        $api->coValue=$cols['coValue'];
                        $api->o3Value=$cols['o3Value'];
                        $api->no2Value=$cols['no2Value'];
                        $api->pm10Value=$cols['pm10Value'];
                        $api->pm10Value24=$cols['pm10Value24'];
                        $api->pm25Value=$cols['pm25Value'];
                        $api->pm25Value24=$cols['pm25Value24'];
                        $api->khaiValue=$cols['khaiValue'];
                        $api->khaiGrade=$cols['khaiGrade'];
                        $api->so2Grade=$cols['so2Grade'];
                        $api->coGrade=$cols['coGrade'];
                        $api->o3Grade=$cols['o3Grade'];
                        $api->no2Grade=$cols['no2Grade'];
                        $api->pm10Grade=$cols['pm10Grade'];
                        $api->pm25Grade=$cols['pm25Grade'];
                        $api->pm10Grade1h=$cols['pm10Grade1h'];
                        $api->pm25Grade1h=$cols['pm25Grade1h'];
                        $api->save();
                        
                    }else{
                        Log::info("ERR>Invalid Station Name > ".$cityName."/".$today."/".$cols['stationName']);
                        
                        $api = new KairspecApiStationNotFoundList();
                        $api->today = $today;
                        $api->time = $time;
                        $api->city = $cityName;
                        $api->stationName = $cols['stationName'];
                        $api->save();
                    }
                }
            }
            Log::info('SCH KairspecStationInfoAll ['.$cityName.'] Res>OK');
        }
    }
}
