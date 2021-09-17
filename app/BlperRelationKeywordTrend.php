<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::API 조회
class BlperRelationKeywordTrend extends Eloquent
{
    protected $connection = 'mongoblper';  
    protected $collection = 'RelationKeywordTrend';
    protected $guarded = [];
}
