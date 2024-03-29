<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int                  $id
 * @property string               $name
 * @property string               $color
 *
 * @property MahoukaServerEvent[] $events
 *
 * @mixin Builder
 */
class MahoukaSeries extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('MahoukaServerEvent');
    }

    /**
     * @return array
     */
    public static function getSeries(): array {
        $query  = MahoukaSeries::get();
        $series = [];
        foreach ($query as $s) {
            $series[] = [
                'id'    => $s->id,
                'name'  => $s->name,
                'color' => $s->color,
            ];
        }
        return $series;
    }
}
