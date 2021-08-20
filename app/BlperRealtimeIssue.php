<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::실시간 이슈
class BlperRealtimeIssue extends Eloquent
{
    protected $connection = 'mongoblper';  
    protected $collection = 'RealTimeIssue';
    protected $guarded = [];
}
