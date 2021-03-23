<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::API 송수신 전체내역
class KairspecApiHis extends Eloquent
{
    protected $connection = 'mongodb';  
    protected $collection = 'KairspecApiHis';
    protected $guarded = [];
}
?>