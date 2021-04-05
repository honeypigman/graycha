<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Func;

// Model
use App\DtdCount;
use App\DtdDrawList;

class KidsController extends Controller
{
    /**
     * Dot To Dot Index
     */
    public function dtd(Request $request)
    {
        $today = date('Y-m-d');
        $obj = DtdCount::where('today',$today)->count();
        if($obj>0){
            DtdCount::where('today',$today)->increment('count', 1);
        }else{
            $views = new DtdCount();
            $views->today = $today;
            $views->count+= 1 ;
            $views->save();
        }

        //Views
        $getViews = DtdCount::where('today',$today)->get();

        return view('kids.dtd')->with('views', $getViews[0]['count']);
    }

    /**
     * Dot To Dot SampleList
     */
    public function dtdSampleList(Request $request)
    {
        $_SAMPLE = Array();
        $list = DtdDrawList::orderBy('date','asc','time','desc')->get();
        foreach($list as $datas){

            if($datas['level']==1){
                $setColor="success";
            }else if($datas['level']==2){
                $setColor="primary";
            }else if($datas['level']==3){
                $setColor="danger";
            }

            $_SAMPLE[$datas['_id']]['color'] = $setColor;
            $_SAMPLE[$datas['_id']]['level'] = $datas['level'];
            $_SAMPLE[$datas['_id']]['title'] = $datas['title'];
            $_SAMPLE[$datas['_id']]['spot'] = $datas['spot'];
        }

        return json_encode($_SAMPLE);
    }

    /**
     * Dot To Dot Save
     */
    public function dtdSave(Request $request)
    {
        $_DATA = Func::requestToData($request);
        if(!empty($_DATA['level']) && !empty($_DATA['lines'])){
            $line = new DtdDrawList();
            $line->date = date('Y-m-d');
            $line->time = date('H:i:s');
            $line->title= $_DATA['title'];
            $line->level= $_DATA['level'];
            $line->spot= implode('|', $_DATA['lines']);
            $line->save();

            $_RS['result'] = "Y";
        }else{
            $_RS['result'] = "N";
        }
        return json_encode($_RS);
    }
}
