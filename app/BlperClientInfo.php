<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::클라이언트 정보
class BlperClientInfo extends Eloquent
{
    protected $connection = 'mongoblper';  
    protected $collection = 'ClientInfo';
    protected $guarded = [];
}
