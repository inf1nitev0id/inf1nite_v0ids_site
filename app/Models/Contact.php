<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $content
 * @property string|null $link
 * @property string      $type
 * @property int         $order
 *
 * @mixin Builder
 */
class Contact extends Model
{
    use HasFactory;
    public $timestamps = false;
}
