<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::측정소 전체 목록
class KairspecApiMsrstnList extends Eloquent
{
    protected $connection = 'mongodb';  
    protected $collection = 'KairspecApiMsrstnList';
    protected $guarded = [];
}
?>