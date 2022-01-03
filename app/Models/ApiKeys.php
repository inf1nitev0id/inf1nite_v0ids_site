<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $name
 * @property string $key
 *
 * @mixin Builder
 */
class ApiKeys extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * @param $name
     *
     * @return mixed|string|null
     */
    public static function getKeyString($name) {
        $result = ApiKeys::select('key')
            ->where(
                'name',
                '=',
                $name
            )
            ->first();
        return $result?->key;
    }
}
