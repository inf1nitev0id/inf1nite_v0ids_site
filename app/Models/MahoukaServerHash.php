<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string hash
 *
 * @mixin Builder
 */
class MahoukaServerHash extends Model {
    use HasFactory;

    public $timestamps = false;
}
