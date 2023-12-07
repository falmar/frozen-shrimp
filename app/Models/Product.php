<?php

namespace App\Models;

use App\Libraries\HasUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuidTrait;
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'updated_at' => 'datetime:Y-m-d H:i:s.u',
        'deleted_at' => 'datetime:Y-m-d H:i:s.u'
    ];
    protected $dateFormat = 'Y-m-d H:i:s.u';
}
