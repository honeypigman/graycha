<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::Dot to Dot 조회수
class DtdCount extends Eloquent
{
    protected $connection = 'mongodtd';  
    protected $collection = 'DtdCount';
    protected $guarded = [];
}
?>