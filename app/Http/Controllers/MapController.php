<?php

namespace App\Http\Controllers;

use Func;
use Illuminate\Http\Request;

// Model
use App\MisedoCount;
use App\KairspecApiMsrstnList;
use App\KairspecApiStationList;

class MapController extends Controller
{
    public function index(Request $request, $code)
    {
        if($code == "misedo"){

            $today = date('Y-m-d');
            $obj = MisedoCount::where('today',$today)->count();
            if($obj>0){
                MisedoCount::where('today',$today)->increment('count', 1);
            }else{
                $views = new MisedoCount();
                $views->today = $today;
                $views->count+= 1 ;
                $views->save();
            }


            $list = KairspecApiMsrstnList::where('today', '=', $today)
            ->orderBy('mesure_time', 'desc')
            ->get();

            $_MARKER = Array();
            foreach($list as $datas){

                $cnt = KairspecApiStationList::where('date', '=', substr($datas['mesure_time'],0,10))
                ->where('city', $datas['city'])
                ->where('stationName', $datas['stationName'])
                ->count();
                
                if($cnt>0)
                {
                    // Set Grade Info
                    $getInfo = Func::getGrade($datas['pm10Value'], $datas['pm25Value']);
        
                    $_MARKER[$datas['stationName']]['grade'] = $getInfo['grade'];
                    $_MARKER[$datas['stationName']]['msg'] = $getInfo['msg'];
                    $_MARKER[$datas['stationName']]['city'] = $datas['city']; 
                    $_MARKER[$datas['stationName']]['dmX'] = $datas['dmX'];
                    $_MARKER[$datas['stationName']]['dmY'] = $datas['dmY'];
                    $_MARKER[$datas['stationName']]['mesure_date'] = $datas['mesure_time'];
                    $_MARKER[$datas['stationName']]['mesure_pm10'] = ($datas['pm10Value']?$datas['pm10Value']:'x');  //  미세먼지 농도
                    $_MARKER[$datas['stationName']]['mesure_pm25'] = ($datas['pm25Value']?$datas['pm25Value']:'x');  //  초미세먼지 농도
                }
            }

            //Views
            $getViews = MisedoCount::where('today',$today)->get();

            return view('map/index')->with('marker', $_MARKER)->with('views', $getViews[0]['count']);
        }else{
            abort(404);
        }
    }
}
