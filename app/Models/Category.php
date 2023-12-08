<?php

namespace App\Models;

use App\Libraries\HasUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasUuidTrait;
    use SoftDeletes;

    protected $casts = [
        'last_crawl_at' => 'datetime:Y-m-d H:i:s.u',
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'updated_at' => 'datetime:Y-m-d H:i:s.u',
        'deleted_at' => 'datetime:Y-m-d H:i:s.u'
    ];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * @return string[]
     */
    public function getDates(): array
    {
        return ['last_crawl_at', 'created_at', 'updated_at', 'deleted_at'];
    }
}
