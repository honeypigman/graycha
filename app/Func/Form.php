<?php
/**
 *  Title : Form | Honeypigman@gmail.com
 *  Date : 2021.02.26
 * 
 */

namespace App\Func;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Form extends Eloquent
{
    /**
     * Json To Data
     *  PARAM[1] : Api Code
     *  PARAM[2] : Json Return Data Array or Object
     */
    static function getData($api_code, $isArray=1){
        $arr = json_decode(file_get_contents('storage/form/'.$api_code.'.json'), $isArray);
        // Set - Code
        $arr = Form::setCode($arr);
        return $arr;
    }

     /**
     * Json Set Array
     *  - API 별 Json 기준 코드 항목 셋팅
     *  PARAM[1] : setData
     */
    static function setCode($arr){

        if(empty($arr['api'])){
            abort(404);
        }

        switch($arr['api']){
            // K-AirSpec
            //  - Operation
            case 'kairspec':
                foreach($arr['spec']['operation'] as $k=>$v){
                    $arr['code']['operation'][$k]=$v['title'];
                    $arr['spec']['operation'][$k]['req']['serviceKey']['val']=env('API_KEY'."_".strtoupper($arr['api']));
                }
                break;
        }


        return $arr;
    }

    /**
     * set Request Url
     *  PARAM[1] : array
     */
    static function getReqUrl($array){
        $url = "";
        $query_string = Form::setQueryString($array);
        $url = $array['setUri'] ."?".$query_string;

        return $url;
    }

    /**
     * set Query String
     *  PARAM[1] : Array
     */
    static function setQueryString($array){        
        $query_string="";
        foreach($array['data'] as $name=>$data){
            if(!empty($data)){
                $query_string.=$name."=".$data."&";
            }
        }
        return substr($query_string,0,-1);;
    }

    /**
     * xml To Json
     *  PARAM[1] : xml
     */
    static function xmlToJson($str){
        $getJson = "";
        $getXml = simplexml_load_string($str);
        $getJson = json_encode($getXml, JSON_UNESCAPED_UNICODE);

        return $getJson;
    }
} 