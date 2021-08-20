<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Func;
use Form;

// Model
use App\BlperRealtimeIssue;
use App\BlperRealTimeKeyword;

class BlperController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function index(Request $request)
    {        
        // 실시간 검색 TOP 보여주기
        return view('blper/index');
    }

    public function curl($url, $target=null){
        if(empty($url)){
            abort(404);
        }
        
        $is_post = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array();
        if( $target == 'nav' ){
            $headers[] = "X-Naver-Client-Id: ".env('NAVER_SEARCH_CLIENT_ID');
            $headers[] = "X-Naver-Client-Secret: ".env('NAVER_SEARCH_CLIENT_SECRET');
        }
        
        else if( $target == 'dau' ){
            $headers[] = "Authorization: KakaoAK ".env('KAKAO_REST_API_KEY');
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec ($ch);
        $result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        return $result;
    }

    public function find(Request $request)
    {
        // 기본값
        $setQuery = $addQuery = null;
        $_DATA = Func::requestToData($request);
        $_DATA['display'] = 20;
        $_DATA['contentType'] = "blog";
        
        // 키워드 최대 3개
        // $maxKwCnt = 3;
        // for($i=0; $i<$maxKwCnt; $i++){
        //     if(trim($_DATA['kw'.$i]))
        //     $setQuery.=$_DATA['kw'.$i]."+";
        // }
        // $setQuery = substr($setQuery,0,-1);

        $setQuery = preg_replace("/[^ㄱ-ㅎㅏ-ㅣ가-힣a-z0-9-,. ]/", "", trim($_DATA['kw0']));
    
        if(!isset($setQuery)){
            abort(404);
        }else{
            
            // Search Portal
            $target = Array('nav'=>1, 'dau'=>1);
            
            $result['code']='9999';
            $result['words'] = Array();
            foreach($target as $site=>$yn){

                if($yn){

                    // 다음
                    if( $site == 'dau' ){
                        
                        // sort : accuracy(정확도순) 또는 recency(최신순), 기본 값 accuracy
                        // size : 1~50 사이의 값, 기본 값 10
                        $encText = urlencode($setQuery).$addQuery;
                        $url = "https://dapi.kakao.com/v2/search/".$_DATA['contentType']."?size=".$_DATA['display']."&sort=accuracy&query=".$encText;
                        $returnValue = $this->curl($url, $site);
                        $getArray = json_decode($returnValue,1);

                        $cnt=0;
                        foreach($getArray['documents'] as $datas){
                            $postdate = substr($datas['datetime'],2,2).substr($datas['datetime'],5,2).substr($datas['datetime'],8,2);
                            $result['items'][$site.'_'.$cnt]['site'] = $site;
                            $result['items'][$site.'_'.$cnt]['title'] = $datas['title'];
                            $result['items'][$site.'_'.$cnt]['content'] = $datas['contents'];
                            $result['items'][$site.'_'.$cnt]['date'] = substr($datas['datetime'],2,2).".".substr($datas['datetime'],5,2).".".substr($datas['datetime'],8,2);
                            $result['items'][$site.'_'.$cnt]['link'] = $datas['url'];
                            
                            $cnt++;
                        }
                    }
                    
                    // 네이버
                    else if($site == 'nav'){
                        // 검색조건 추가
                        if($_DATA['display']>10){
                            $addQuery.="&display=".$_DATA['display'];
                        }
                        
                        $encText = urlencode($setQuery).$addQuery;
                        $url = "https://openapi.naver.com/v1/search/".$_DATA['contentType'].".json?query=".$encText;
                        $returnValue = $this->curl($url, $site);
                        $getArray = json_decode($returnValue,1);
                        
                        $cnt=0;
                        foreach($getArray['items'] as $datas){
                            $postdate = substr($datas['postdate'],2,2).substr($datas['postdate'],4,2).substr($datas['postdate'],6,2);
                            $result['items'][$site.'_'.$cnt]['site'] = $site;
                            $result['items'][$site.'_'.$cnt]['title'] = $datas['title'];
                            $result['items'][$site.'_'.$cnt]['content'] = $datas['description'];
                            $result['items'][$site.'_'.$cnt]['date'] = substr($datas['postdate'],2,2).".".substr($datas['postdate'],4,2).".".substr($datas['postdate'],6,2);
                            $result['items'][$site.'_'.$cnt]['link'] = $datas['link'];
                            
                            $cnt++;
                        }
                    }
                    
                    // 코드
                    $result['code']=($cnt>0?'0000':'9999');
                }
            }

            // 연관검색
            unset($url, $returnValue);
            $url = "https://suggestqueries.google.com/complete/search?output=toolbar&q=".urlencode($setQuery);
            $returnValue = $this->curl($url);

            
            // Xml TO Json
            $getXml = simplexml_load_string($returnValue, "SimpleXMLElement", LIBXML_NOCDATA);
            $setJson = json_encode($getXml->CompleteSuggestion, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            $getArray = json_decode($setJson,1);

            if(count($getArray)>1){
                $j=0;
                foreach($getArray as $datas){
                    
                    if($setQuery == trim($datas['suggestion']['@attributes']['data'])){
                        continue;
                    }

                    $result['words'][$j]['text'] = $datas['suggestion']['@attributes']['data'];
                    $j++;
                }
            }
            
            if(isset($result['items'])){
                asort($result['items']);
            }

            return json_encode($result);
        }
    }

    public function issue(Request $request){

        $today = date('Y-m-d');
        $list = BlperRealtimeIssue::where('today', '=', $today)
        ->get();
        $result = Array();
        foreach($list as $datas){

            $result['code']='9999';
            $getArray = json_decode($datas['items'],1);
            if(count($getArray)>0){
                foreach($getArray as $k=>$v){
                    $result['items'][$k] = $v;
                }
                $result['code']=0000;
            }
        }

        return json_encode($result);
    }

    public function keyword(Request $request){
        
        $today = date('Y-m-d');
        $list = BlperRealTimeKeyword::where('today', '=', $today)
        ->get();
        $result = Array();
        foreach($list as $datas){

            $result['code']='9999';
            $getArray = json_decode($datas['items'],1);
            if(count($getArray)>0){
                foreach($getArray as $k=>$v){
                    $result['items'][$k] = $v;
                }
                $result['code']=0000;
            }
        }

        return json_encode($result);
    }

    // public function crawling(Request $request, $site)
    // {
    //     if(!isset($_POST['link'])){
    //         abort(404);
    //     }

    //     $result = $returnValue = null;
    //     $setUrl = $_POST['link'];

    //     if($site == 'nav')
    //     {
    //         // if($_POST['type'] == 'blog'){
    //         // }
    //         // else if($_POST['type'] == 'news'){
    //         //     $setBody = "div[@id='articleBodyContents']";
    //         // }
    //         $ex_link = explode("?",$_POST['link']);
    //         $get_blogId = explode("/", $ex_link[0]);
    //         $get_logNo = explode("=", $ex_link[1]);

    //         $setUrl = "https://blog.naver.com/PostView.naver?blogId=".end($get_blogId)."&logNo=".end($get_logNo);
            
    //         $setBody = "div[@class='se-main-container']";
    //     }

    //     else if($site=='dau'){

    //         $setUrl = $_POST['link'];
    //         $setBody = "div[@class='se-main-container']";            
    //     }
        
    //     $returnValue = $this->curl($setUrl, 'nav');
        
    //     libxml_use_internal_errors(true);
    //     $doc = new \DOMDocument('1.0', 'UTF-8');
    //     $doc->loadHTML($returnValue);
        
    //     $xpath = new \DomXPath($doc);
    //     $nodeList = $xpath->query("//".$setBody);
    //     $node = $nodeList->item(0);
        
    //     $result['body'] = strip_tags(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $doc->saveHTML($node)), "<div><span><p><br>");
    //     $result['cnt'] = mb_strlen(trim(str_replace(" ", "", str_replace("\n", "", $node->nodeValue))));
 
    //     return json_encode($result);
    // }
}
