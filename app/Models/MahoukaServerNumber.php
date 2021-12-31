<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $hash
 * @property int    $value
 *
 * @mixin Builder
 */
class MahoukaServerNumber extends Model {
    use HasFactory;

    public $timestamps = false;
}
