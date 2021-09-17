<?php
/**
 *  Title : Function | Honeypigman@gmail.com
 *  Date : 2021.09.01
 * 
 */

namespace App\Func;

class RestApi
{
    protected $target;
    protected $api = Array();

    function __construct($target, $API=array())
    {   
        unset($this->api);
        $this->target = $target;
        if( $target == "NAVER_SEARCH" || $target == "NAVER_DATALAB" ){
            $this->api['url'] = $API['url'];
            $this->api['id'] = $API['id'];
            $this->api['secret'] = $API['secret'];
        }

        else if( $target == "NAVER_AD" ){
            $this->api['key'] = $API['key'];
            $this->api['url'] = $API['url'];
            $this->api['id'] = $API['id'];
            $this->api['secret'] = $API['secret'];
        }

        elseif( $target == "DAUM_SEARCH" ){
            $this->api['key'] = $API['key'];
            $this->api['url'] = $API['url'];
        }

        elseif( $target == "GOOGLE_SUGGEST" || $target == "GOOGLE_TREND" || $target == "KOREA_ISSUE" ){
            $this->api['url'] = $API['url'];
        }
    }

    protected function generateSignature($timestamp, $method, $path)
    {
        $sign = $timestamp . "." . $method . "." . $path;
        $signature = hash_hmac('sha256', $sign, $this->api['secret'], true);
        return base64_encode($signature);
    }

    protected function getTimestamp()
    {
        return round(microtime(true) * 1000);
    }

    protected function getHeader($method, $uri=null)
    {
        switch($this->target){
            case 'NAVER_SEARCH':
                $header = array(
                    'Content-Type: application/json; charset=UTF-8',
                    'X-Naver-Client-Id: ' . $this->api['id'],
                    'X-Naver-Client-Secret: ' . $this->api['secret'],
                );
                break;

            case 'NAVER_DATALAB':
                $header = array(
                    'Content-Type: application/json; charset=UTF-8',
                    'X-Naver-Client-Id: ' . $this->api['id'],
                    'X-Naver-Client-Secret: ' . $this->api['secret'],
                );
                break;    
            
            case 'NAVER_AD':
                $timestamp = $this->getTimestamp();
                $header = array(
                    'Content-Type: application/json; charset=UTF-8',
                    'X-Timestamp: ' . $timestamp,
                    'X-API-KEY: ' . $this->api['key'],
                    'X-Customer: ' . $this->api['id'],
                    'X-Signature: ' . $this->generateSignature($timestamp, $method, $uri),
                );
                break;
            
            case 'DAUM_SEARCH':
                $header = array(
                    'Content-Type: application/json; charset=UTF-8',
                    'Authorization: KakaoAK ' . $this->api['key'],
                );
                break;
            default :
                $header = array();
        }
        return $header;
    }

    protected function build_http_query($query)
    {
        if (!empty ($query)) {
            $query_array = array();

            foreach ($query as $key => $key_value) {
                $query_array [] = urlencode($key) . '=' . urlencode($key_value);
            }

            return implode('&', $query_array);
        } else {
            return '';
        }
    }

    protected function parseResponse($data, $type=null)
    {
        if (!empty ($data)) {
            if($type=='XML'){
                // Xml TO Json
                $getXml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
                if( $this->target == "GOOGLE_SUGGEST" ){
                    $setJson = json_encode($getXml->CompleteSuggestion, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                }else{
                    $setJson = json_encode($getXml);
                }
                $array = json_decode($setJson,1);
            }else{
                $array = json_decode($data, true);
            }
            return array('array' => $array, 'json' => $data);
        }

        return array();
    }
    
   
    public function GET($uri, $query = array(), $type=null)
    {
        $ch = curl_init();
        if (!$ch) {
            die ("ERR>Init cURL handle");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api['url'] . $uri . (empty($query) ? '' : '?' . $this->build_http_query($query)));
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeader('GET', $uri));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec ($ch);
        $result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close ($ch);

        $res = $this->parseResponse($output, $type);

        if (!empty ($error)) {
            echo "error : $error\n";
            die("failed to request");
        }

        return $res['array'];
    }

    public function POST($query)
    {
        $ch = curl_init();
        if (!$ch) {
            die ("ERR>Init cURL handle");
        }

        $body = json_encode($query, JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api['url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeader('POST'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $output = curl_exec ($ch);
        $result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close ($ch);        
        
        $res = json_decode($output, true);

        if (!empty ($error)) {
            $res['results'][0]['data']=null;
        }

        // Naver DataLab Hmm...
        if($res['errorCode']=="010"){
            $res['results'][0]['data']=null;
        }

        return $res['results'][0]['data'];
    }
}