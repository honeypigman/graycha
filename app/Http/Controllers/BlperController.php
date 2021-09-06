<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Func;
use RestApi;

// Model
use App\BlperRealtimeIssue;
use App\BlperRealTimeKeyword;
use App\BlperClientInfo;

class BlperController extends Controller
{
    private $headers = Array();
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
        $views = $this->getViews();

        // 실시간 검색 TOP 보여주기
        return view('blper/index')->with('views', $views);
    }

    public function curl($url){
        if(empty($url)){
            abort(404);
        }
        
        $is_post = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = $this->getHeader();

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
        $_DATA = Func::requestToData($request);
        $query = preg_replace("/[^ㄱ-ㅎㅏ-ㅣ가-힣a-z0-9-,. ]/", "", trim($_DATA['kw0']));
            
        // Report
        if(!($this->getReportInfo($query))){
            $result['code']='9998';
            return json_encode($result);
        }
        
        if(!isset($query)){
            abort(404);
        }else{
            
            // Api연동
            $result['items']['daum'] = $this->SearchApiDaum($query);
            $result['items']['naver'] = $this->SearchApiNaver($query);
                    
            // 코드
            $result['code']=((count($result['items']['daum'])>0 || count($result['items']['naver'])>0)?'0000':'9999');

            // 조회수
            $result['views'] = $this->getViews();

            if(isset($result['items'])){
                $result['word']['google'] = $this->SearchApiGoogle($query);
                $result['word']['naver'] = $this->AdApiNaver($query);
                // asort($result['items']);
            }
            
            $result['grade'] = $this->getGrade($result['word']['naver']['report'], $result['items']['daum']['report'], $result['items']['naver']['report']);

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
                    $v['date'] = substr((str_replace('-','.',$v['date'])),2);

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

    private function getReportInfo($query){

        $result = false;
        $today = date('Ymd');

        unset($_INFO);
        $_INFO['date'] = date('Ymd H:i:s');
        $_INFO['query'] = $query;
        $_INFO['agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_INFO['ip'] = $_SERVER['REMOTE_ADDR'];

        // DB
        $cnt = BlperClientInfo::where('today', '=', $today)
        ->where('ip', '=', $_INFO['ip'])
        ->count();

        // Save
        if($cnt<env('BLPER_FIND_LIMIT')){
            
            $issue = new BlperClientInfo();
            $issue->today = $today;
            $issue->date = date('Ymd H:i:s');
            $issue->ip = $_INFO['ip'];
            $issue->query = $_INFO['query'];
            $issue->agent = $_INFO['agent'];
            $issue->save();

            $result = true;
        }

        return $result;
    }

    private function getViews(){
        $today = date('Ymd');
        $views = BlperClientInfo::where('today', '=', $today)
        ->where('query', '!=', '')
        ->whereNotNull('query')
        ->count();
        
        return $views;
    }
    
    public function views(){
        $today = date('Ymd');
        $list = BlperClientInfo::select('date', 'query')
        ->where('today', '=', $today)
        ->where('query', '!=', '')
        ->whereNotNull('query')
        ->get();

        return json_encode($list);
    }

    private function SearchApiGoogle($query)
    {
        $result = Array();
        $setQuery = Array(
            "output" => "toolbar",
            "q" => $query
        );

        $_API['url'] = env('GOOGLE_SUGGEST_URL');

        $api = new RestApi('GOOGLE_SUGGEST', $_API);
        $res = $api->GET('', $setQuery, 'XML');

        $i=0;
        if(count($res)>1){
            foreach($res as $datas){                    
                if($setQuery == trim($datas['suggestion']['@attributes']['data'])){
                    continue;
                }

                $result[$i]['text'] = $datas['suggestion']['@attributes']['data'];
                $i++;
            }
        }

        return $result;
    }

    private function AdApiNaver($query){
        $result = Array();
        $setQuery = Array(
            "hintKeywords"=>str_replace(' ', '', $query),
            "showDetail"=>true
        );

        $_API['url'] = env('NAVER_AD_URL');
        $_API['key'] = env('NAVER_AD_KEY');
        $_API['secret'] = env('NAVER_AD_SECRET');
        $_API['id'] = env('NAVER_AD_ID');
        
        $api = new RestApi('NAVER_AD', $_API);
        $res = $api->GET('/keywordstool', $setQuery);

        if(isset($res['keywordList'])){
            foreach( $res['keywordList'] as $k => $datas ){
                if($k==0){
                    $result['report']['monTotalCntPc'] = ($datas['monthlyPcQcCnt']>0?number_format($datas['monthlyPcQcCnt']):0);
                    $result['report']['monTotalCntMo'] = ($datas['monthlyMobileQcCnt']>0?number_format($datas['monthlyMobileQcCnt']):0);
                }else{
                    if (strpos($datas['relKeyword'], $query) !== false) {
                        $result[$k]['text']=$datas['relKeyword'];
                        $result[$k]['pcCnt']=($datas['monthlyPcQcCnt']=='< 10'?'10':number_format($datas['monthlyPcQcCnt']));
                        $result[$k]['moCnt']=($datas['monthlyMobileQcCnt']=='< 10'?'10':number_format($datas['monthlyMobileQcCnt']));
                        $result[$k]['level']=$datas['compIdx'];
                    }
                }
            }
        }

        return $result;
    }

    private function SearchApiNaver($query)
    {
        $result = Array();
        $setQuery = Array(
            "query" => $query,
            "display" => "20"
        );
        $arrContents = Array(
            "blog"=>"b", "cafearticle"=>"c", "webkr"=>"w"
        );    

        $_API['url'] = env('NAVER_SEARCH_URL');
        $_API['id'] = env('NAVER_SEARCH_ID');
        $_API['secret'] = env('NAVER_SEARCH_SECRET');

        // Naver Search Contents
        $i=0;
        foreach ( array_Keys($arrContents) as $content ){

            $setUri = $content.".json";
            $api = new RestApi('NAVER_SEARCH', $_API);
            $res = $api->GET($setUri, $setQuery);

            $j=0;
            if(isset($res['items'])){
                foreach($res['items'] as $datas){
                    if( $content == 'blog' ){
                        $result[$arrContents[$content]][$j]['date'] = substr($datas['postdate'],2,2).".".substr($datas['postdate'],4,2).".".substr($datas['postdate'],6,2);
                    }else{
                        // $result[$content][$cnt]['date'] = substr($datas['lastBuildDate'],2,2).".".substr($datas['lastBuildDate'],4,2).".".substr($datas['lastBuildDate'],6,2);                        
                    }
                    
                    $result[$arrContents[$content]][$j]['title'] = $datas['title'];
                    $result[$arrContents[$content]][$j]['content'] = $datas['description'];
                    $result[$arrContents[$content]][$j]['link'] = $datas['link'];
                    
                    $j++;
                }
            }
            
            $result['report'][$i]['type'] = $arrContents[$content];
            $result['report'][$i]['total'] = ($res['total']>0?number_format($res['total']):0);

            $i++;
        }

        return $result;
    }

    private function SearchApiDaum($query)
    {
        $result = Array();
        $setQuery = Array(
            "query" => $query,
            "size" => "20",
            "sort" => "accuracy"
        );
        $arrContents = Array(
            "blog"=>"b", "cafe"=>"c", "web"=>"w"
        );    

        $_API['url'] = env('KAKAO_SEARCH_URL');
        $_API['key'] = env('KAKAO_SEARCH_KEY');

        // Naver Search Contents
        $i=0;
        foreach ( array_Keys($arrContents) as $content ){
            $setUri = $content;
            $api = new RestApi('DAUM_SEARCH', $_API);
            $res = $api->GET($setUri, $setQuery);
            
            $j=0;
            if(isset($res['documents'])){
                foreach($res['documents'] as $datas){
                    $result[$arrContents[$content]][$j]['title'] = $datas['title'];
                    $result[$arrContents[$content]][$j]['content'] = $datas['contents'];
                    $result[$arrContents[$content]][$j]['date'] = substr($datas['datetime'],2,2).".".substr($datas['datetime'],5,2).".".substr($datas['datetime'],8,2);
                    $result[$arrContents[$content]][$j]['link'] = $datas['url'];
                    
                    $j++;
                }
            }
            
            $result['report'][$i]['type'] = $arrContents[$content];
            $result['report'][$i]['total'] = ($res['meta']['total_count']>0?number_format($res['meta']['total_count']):0);

            $i++;
        }

        return $result;
    }

    protected function getGrade( $arrWord=Array(), $arrNaver=Array(), $arrDaum=Array() ){
        
        $monTotalCnt = 0;
        $monNaverCnt = 0;
        $monDaumCnt = 0;
        
        if(isset($arrWord)){
            $monTotalCnt = (int)(str_replace(',','',$arrWord['monTotalCntPc']))+(int)(str_replace(',','',$arrWord['monTotalCntMo']));
            if(!$monTotalCnt) $monTotalCnt=1;
        }
        if(isset($arrNaver)){
            foreach($arrNaver as $data){
                if($data['type']=='b'){
                    $monNaverCnt+=(int)(str_replace(',','',$data['total']));
                }
            }
        }
        if(isset($arrDaum)){
            foreach($arrDaum as $data){
                if($data['type']=='b'){
                    $monDaumCnt+=(int)(str_replace(',','',$data['total']));
                }
            }
        }

        // 비율 : 전체문서수 / (총 조회수)
        $rate = round(($monNaverCnt+$monDaumCnt/2)/$monTotalCnt,2);
        

        $arrGradeMatrix = Array(
            1 => array(0, 0.05),
            2 => array(0.06, 0.59),
            3 => array(0.60, 0.99),
            4 => array(1.00, 2.99),
            5 => array(3.00, 4.99),
            6 => array(5, 9.99),
            7 => array(10.00, 29.99),
            8 => array(30, 49.99),
            9 => array(50.00, 99.99),
            10 => array(100, 99999999),
        );

        foreach($arrGradeMatrix as $grade=>$area){
            if( $rate>=$area[0] && $rate<=$area[1] ){
                return $grade;
                break;
            }
        }
    }
}
