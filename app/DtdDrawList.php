<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

// 모델::Dot to Dot 그리기 내역
class DtdDrawList extends Eloquent
{
    protected $connection = 'mongodtd';  
    protected $collection = 'DtdDrawList';
    protected $guarded = [];
}
?>