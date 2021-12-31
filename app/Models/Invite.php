<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $code
 * @property int    $usages
 * @property string $description
 *
 * @mixin Builder
 */
class Invite extends Model {
    use HasFactory;

    public $timestamps = false;
}
