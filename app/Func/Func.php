<?php
/**
 *  Title : Function | Honeypigman@gmail.com
 *  Date : 2021.01.01
 * 
 */

namespace App\Func;

USE DB;

class Func
{
    /**
     * Request > parameter To Data
     *  PARAM[1] : Requests
     */
    static function requestToData($request){
        $_DATA=Array();
        foreach($request->request as $k=>$v){
            $_DATA[$k]=$v;
        }

        return $_DATA;
    }

    /**
     * Set Column Record
     *  PARAM[1] : Table name
     *  PARAM[2] : DATAS
     */
    static function setRecords($tbl, $_DATA){
        $_RS = Array();
        // Table Column ListUP
        if($colums =  DB::getSchemaBuilder()->getColumnListing($tbl)){            
            foreach($colums as $k=>$v){
                if(array_key_exists($v, $_DATA)){
                    
                    $columType =  DB::getSchemaBuilder()->getColumnType($tbl, $v);
                    // if($columType!='integer'){
                    //     $_DATA[$v]="'".$_DATA[$v]."'";
                    // }

                    $_RS[$v]=$_DATA[$v];
                }
            }
        }
        return $_RS;
    }

    /**
     * Set Validtaion
     *  PARAM[1] : $request
     */
    static function setValidation($request){
        $messages = [
            'email.min' => 'The Email minimum length is 5 digits!',
            'email.email' => 'Invalid Email Format!',
            'email.required' => 'Enter Your Email!',
            'password.required' => 'Enter Your password!',
            'password.min' => 'The Password minimum length is 8 digits!',
        ];

        $_RS = $request->validate([
            'email' => 'required|email|min:5',
            'password' => 'required|min:8'
        ], $messages);

        return $_RS;
    }

    /**
     * Set Validtaion
     *  PARAM[1] : $request
     */
    static function isSession($request){
        $_RS = false;
        if($request->session()->get('login_id')){
            $_RS = true;
        }
        return $_RS;
    }
    
    /**
     * Write Access Log
     *  PARAM[1] : $request
     */
    static function accLog($request){
        unset($_DATA);
        $_DATA['ip'] = $request->server('REMOTE_ADDR');
        $_DATA['login_id'] = $request->session()->get('login_id');
        $_DATA['request_uri'] = $request->server('REQUEST_URI');
        $_DATA['request_time'] = date('Y-m-d H:i:s');

        $tbl="acclog";
        DB::table($tbl)->insert([ 
            Func::setRecords($tbl, $_DATA)
        ]);
    }

    /**
     * Set Select Form
     *  PARAM[1] : set Name
     *  PARAM[2] : set Array
     */
    static function select($name, $array){

        $result = "<select class='form-select' aria-label='Default select example' name='".$name."' id='".$name."'>";
        $result.= "<option value=''> - Make a Choice - </option>";
        foreach( $array as $k=>$v ){
            $result.= "<option value='".$k."'>".$v."::".$k."</option>";
        }
        $result.= "</select>";

        return $result;
    }

    /**
     * Set PM Grade
     *  PARAM[1] : pm10
     *  PARAM[2] : pm25
     */
    static function getGrade($pm10, $pm25)
    {
        // PM_Matrix
        //  - https://www.me.go.kr/mamo/web/index.do?menuId=16201
        $_PM[10][1]['MIN']=0;
        $_PM[10][1]['MAX']=30;
        $_PM[10][2]['MIN']=31;
        $_PM[10][2]['MAX']=80;
        $_PM[10][3]['MIN']=81;
        $_PM[10][3]['MAX']=150;
        $_PM[10][4]['MIN']=151;
        $_PM[10][4]['MAX']=999;
        $_PM[25][1]['MIN']=0;
        $_PM[25][1]['MAX']=15;
        $_PM[25][2]['MIN']=16;
        $_PM[25][2]['MAX']=35;
        $_PM[25][3]['MIN']=36;
        $_PM[25][3]['MAX']=75;
        $_PM[25][4]['MIN']=76;
        $_PM[25][4]['MAX']=999;
        
        $grade = 0;
        $msg = '';
        $getGrade[10]=0;
        $getGrade[25]=0;

        foreach($_PM['10'] as $grade => $matrix){
            $is=false;
            if( $is == false && ($pm10>=$matrix['MIN'] && $matrix['MAX'] >= $pm10) ){
                $is = true;
            }
            if($is){
                $getGrade[10] = $grade;
            }
        }
        foreach($_PM['25'] as $grade => $matrix){
            $is=false;
            if( $is == false && ($pm25>=$matrix['MIN'] && $matrix['MAX'] >= $pm25) ){
                $is = true;
            }
            if($is){
                $getGrade[25] = $grade;
            }
        }

        $grade = max($getGrade);
        if( $pm10=='-' && $pm25=='-' ){
            $grade=0;
        }        
        
        // Grade Message
        $_MSG[0]="정보가 없어요..";
        $_MSG[1]="좋음";
        $_MSG[2]="보통";
        $_MSG[3]="나쁨";
        $_MSG[4]="매우나쁨";
        
        $result['grade'] = $grade;
        $result['msg'] = $_MSG[$grade];

        return $result;
    }

     /**
     * @brif get From to Distance
     * @PARAM[1] : Latitude Point 1
     * @PARAM[2] : Longitude Point 1
     * @PARAM[3] : Latitude Point 2
     * @PARAM[4] : Longitude Point 2
     * @PARAM[5] : Earth Raduis
     * @return distance KM
     * @dec 지구는 구 형태이기 때문에 점과 점사이의 직선 거리를 구하게되면 오차가 발생한다. 이때, Haversine 공식을 사용하여, 위도/경도의 구 형태에서의 두 점사이의 거리를 구한다.
     */
    static function getDistance($x1, $y1, $x2, $y2, $radius = 6371000)
    {
        $distance = 0;
        $lat1 = deg2rad($x1);
        $lon1 = deg2rad($y1);
        $lat2 = deg2rad($x2);
        $lon2 = deg2rad($y2);

        $sinDeltaLat = sin(($lat2 - $lat1)/2);
        $sinDeltaLon = sin(($lon2 - $lon1)/2);

        $sqr = 2 * asin(sqrt(pow($sinDeltaLat, 2) + cos($lat1) * cos($lat2) * pow($sinDeltaLon, 2)));
        $distance = $sqr * $radius;
        return $distance;
    }
}