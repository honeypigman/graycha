<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::API 조회
class BlperApiCount extends Eloquent
{
    protected $connection = 'mongoblper';  
    protected $collection = 'ApiCnt';
    protected $guarded = [];
}
