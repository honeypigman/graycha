<?php
namespace App\Func;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Api extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'getMsrstnAcctoRltmMesureDnsty';
    protected $guarded = [];

    static function setCollection($collection){
        $this->collection=$collection;
    }
}
?>