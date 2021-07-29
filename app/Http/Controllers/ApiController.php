<?php
/**
 *  Title : Api Controller | Honeypigman@gmail.com
 *  Date : 2021.02.25
 * 
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use DB;
use Func;
use Form;
use App\KairspecApiHis;
use App\KairspecApiMsrstnList;
use App\KairspecApiStationList;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function main()
    {
        // Index
        //$this->index();
        return view('api/index');
    }

    public function setForm(Request $reqeust, $api_code)
    {
        $_DATA = Form::getData($api_code);
        return view('api/layout')->with('result', $_DATA);
    }

    public function send(Request $request)
    {
        $_DATA = Func::requestToData($request);
        $url = Form::getReqUrl($_DATA);
        $result = $this->curl($url);
        $isSave = true;

        // DB Save
        if($isSave){
            $this->store($result, $_DATA['setOperation']);
        }

        return $result;
    }

    public function curl($url)
    {
        if(empty($url)){
            abort(404);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec ($ch);
        $err = curl_error($ch);  //if you need
        curl_close ($ch);

        // Xml To Json
        $result = Form::xmlToJson($result);

        return $result;
    }

    public function findStation(Request $request, $dmX, $dmY)
    {
        if( empty($dmX) || empty($dmX) ){
            abort(404);
        }

        $distance='';
        $today = date('Y-m-d');
        $all = KairspecApiMsrstnList::where('today', '=', $today)
        ->get();

        foreach($all as $datas){

            $setDistance = Func::getDistance($dmX, $dmY, $datas['dmX'], $datas['dmY']);

            // $_DATA[$datas['_id']]['city'] = $datas['city'];
            // $_DATA[$datas['_id']]['station'] = $datas['stationName'];
            // $_DATA[$datas['_id']]['distance'] = $setDistance;

            if(empty($distance)){
                $distance = $setDistance;
            }

            if($distance>$setDistance){
                unset($_DATA);   
                $_DATA['city'] = $datas['city'];
                $_DATA['stationName'] = $datas['stationName'];
                $_DATA['mesure_time'] = $datas['mesure_time'];
                $_DATA['pm10Value'] = ($datas['pm10Value']?$datas['pm10Value']:'x');  //  미세먼지 농도
                $_DATA['pm25Value'] = ($datas['pm25Value']?$datas['pm25Value']:'x');  //  초미세먼지 농도
                $_DATA['dmX'] = $datas['dmX'];
                $_DATA['dmY'] = $datas['dmY'];
                $_DATA['distance'] = $setDistance;

                $distance = $setDistance;
            }
        }

        // MongoDB - 비교(Comparison) 연산자
        // operator	설명
        // $eq	(equals) 주어진 값과 일치하는 값
        // $gt	(greater than) 주어진 값보다 큰 값
        // $gte	(greather than or equals) 주어진 값보다 크거나 같은 값
        // $lt	(less than) 주어진 값보다 작은 값
        // $lte	(less than or equals) 주어진 값보다 작거나 같은 값
        // $ne	(not equal) 주어진 값과 일치하지 않는 값
        // $in	주어진 배열 안에 속하는 값
        // $nin	주어빈 배열 안{{에 속하지 않는 값

        // db.KairspecApiMsrstnList.find({'dmX':{$eq:'37.544656'}, 'dmY':{$eq:'126.835094'} ,'today':'2021-03-14'}).pretty()
        // db.KairspecApiMsrstnList.find( { $and: [ {'dmX':{$lte:'37.546362099999996'}}, {'dmY':{$lte:'126.86998949999999'}}, {'today':'2021-03-14'} ] } ).pretty()
        // db.KairspecApiMsrstnList.findOne( { $and: [ {'dmX':{$lte:'37.95994899497005'}}, {'dmY':{$lte:'124.72368552268148'}}, {'today':'2021-03-14'} ] } )
        //  - 검색 : 37.168443, 126.9829
        // 	- 금암로 : 37.171081, 127.052049
        // Set Grade Info
        $getInfo = Func::getGrade($_DATA['pm10Value'], $_DATA['pm25Value']);

        $_MARKER = Array();
        $_MARKER['grade'] = $getInfo['grade'];
        $_MARKER['msg'] = $getInfo['msg'];
        $_MARKER['city'] = $_DATA['city']; 
        $_MARKER['stationName'] = $_DATA['stationName'];
        $_MARKER['dmX'] = $_DATA['dmX'];
        $_MARKER['dmY'] = $_DATA['dmY'];
        $_MARKER['mesure_date'] = $_DATA['mesure_time'];
        $_MARKER['mesure_pm10'] = ($_DATA['pm10Value']?$_DATA['pm10Value']:'x');  //  미세먼지 농도
        $_MARKER['mesure_pm25'] = ($_DATA['pm25Value']?$_DATA['pm25Value']:'x');  //  초미세먼지 농도
        
        return json_encode($_MARKER);
    }

    public function findStationTimeflow(Request $request, $date, $city, $station)
    {
        if( empty($date) || empty($city) || empty($station) ){
            abort(404);
        }

        $today = date('Y-m-d');

        $list = KairspecApiStationList::where('date', '=', $date)
        ->where('city', $city)
        ->where('stationName', $station)
        ->orderBy('time', 'asc')
        ->get();

        $cnt=1;
        $_TIME = Array();
        foreach($list as $datas){
            // Set Grade Info
            $getInfo = Func::getGrade($datas['pm10Value'], $datas['pm25Value']);
            $_TIME[$cnt]['time'] =substr($datas['time'],0,2);
            $_TIME[$cnt]['data']['grade'] = $getInfo['grade'];
            $_TIME[$cnt]['data']['pm10'] = ($datas['pm10Value']?$datas['pm10Value']:'x');  //  미세먼지 농도
            $_TIME[$cnt]['data']['pm25'] = ($datas['pm25Value']?$datas['pm25Value']:'x');  //  초미세먼지 농도
            $cnt++;
        }
        
        return json_encode($_TIME);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type='json')
    {
        $result = KairspecApiHis::orderBy('date', 'desc')->get();        

        if($type=='json'){
            return $result = $result->toJson(JSON_UNESCAPED_UNICODE);
        }else{
            return $result = $result->toArray();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($result=null, $operation)
    {
        if($result){
            $custom = "";
            $_ARR = array();
            $_CUST = array();

            $_ARR = json_decode($result, 1);
            $_CUST['resultCode']=$_ARR['header']['resultCode'];
            $_CUST['resultMsg']=$_ARR['header']['resultMsg'];
            if(empty($_ARR['body'])){
                $_RS['resultCode'] = $_CUST['resultCode'];
                $_RS['resultMsg'] = $_CUST['resultMsg'];

            }else{
                foreach($_ARR['body']['items'] as $k=>$datas){
                    $_CUST['data'][]=$datas;
                }
                $custom = json_encode($_CUST, JSON_UNESCAPED_UNICODE);

                $api = new KairspecApiHis();
                $api->date = date('Y-m-d');
                $api->time = date('H:i:s');
                $api->operation = $operation;
                $api->origin = $result;
                $api->custom = $custom;
                $api->save();        

                $_RS['resultCode'] = "00";
                $_RS['resultMsg'] = "Save Success";
            }
        }

        return $_RS;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id=null)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}