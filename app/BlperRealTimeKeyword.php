<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::키워드 - 구글 트랜드 RSS / ZUM
class BlperRealTimeKeyword extends Eloquent
{
    protected $connection = 'mongoblper';  
    protected $collection = 'RealTimeKeyword';
    protected $guarded = [];
}
