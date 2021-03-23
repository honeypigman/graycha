<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::API 측정소 조회 시 누락 데이터
class KairspecApiStationNotFoundList extends Eloquent
{
    protected $connection = 'mongodb';  
    protected $collection = 'KairspecApiStationNotFoundList';
    protected $guarded = [];
}
?>