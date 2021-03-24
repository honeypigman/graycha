<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::API Misedo 호출건수
class MisedoCount extends Eloquent
{
    protected $connection = 'mongodb';  
    protected $collection = 'MisedoCount';
    protected $guarded = [];
}
?>