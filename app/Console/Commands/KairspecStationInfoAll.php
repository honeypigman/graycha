<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Form;
use Log;

// Controller
use App\Http\Controllers\ApiController;

// Model
use App\KairspecApiMsrstnList;
use App\KairspecApiStationList;
//use App\KairspecApiStationNotFoundList;

// 시도별 실시간 측정소정보 조회
//  - 배치주기 : Hourly
class KairspecStationInfoAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kairspec:getStationInfoAll';

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
            // - 버전을 포함하지 않고 호출할 경우 : PM2.5 데이터가 포함되지 않은 원래 오퍼레이션 결과 표출.
            // - 버전 1.0을 호출할 경우 : PM2.5 데이터가 포함된 결과 표출.
            // - 버전 1.1을 호출할 경우 : PM10, PM2.5 24시간 예측이동 평균데이터가 포함된 결과 표출.
            // - 버전 1.2을 호출할 경우 : 측정망 정보 데이터가 포함된 결과 표출.
            // - 버전 1.3을 호출할 경우 : PM10, PM2.5 1시간 등급 자료가 포함된 결과 표출
            $_DATA['uri'] = "http://apis.data.go.kr/B552584/ArpltnInforInqireSvc";
            $_DATA['setUri'] = "http://apis.data.go.kr/B552584/ArpltnInforInqireSvc/getCtprvnRltmMesureDnsty";
            $_DATA['data'] = Array(
                "serviceKey"=>env('API_KEY_KAIRSPEC'),
                "numOfRows"=>"9999",
                "pageNo"=>"1",
                "sidoName"=>urlencode($cityName),
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

            if(isset($_ARR['body']['items'])){
                foreach( $_ARR['body']['items'] as $item=>$datas ){
                    foreach($datas as $cols){
                        unset($cnt, $ex_date);
                        if(empty($cols['dataTime'])){
                            $cols['dataTime']="0000-00-00 00:00";
                        } 
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
                            
                            // $api = new KairspecApiStationNotFoundList();
                            // $api->today = $today;
                            // $api->time = $time;
                            // $api->city = $cityName;
                            // $api->stationName = $cols['stationName'];
                            // $api->save();
                        }
                    }
                }
                Log::info('SCH KairspecStationInfoAll ['.$cityName.'] Res>OK');
            }else{
                Log::info('SCH KairspecStationInfoAll Fail >'.$_ARR['body']['items']);
            }
        }
    }
}
