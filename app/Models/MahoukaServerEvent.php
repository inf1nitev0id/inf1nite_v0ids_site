<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $name
 * @property string $date
 * @property string $type
 * @property bool   $important
 * @property int    $series_id
 *
 * @mixin Builder
 */
class MahoukaServerEvent extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * Получение списка событий
     *
     * @return array
     */
    public static function getEvents(): array {
        $query  = MahoukaServerEvent
            ::orderBy(
                'date',
                'asc'
            )
            ->orderBy(
                'type',
                'asc'
            )
            ->orderBy(
                'important',
                'desc'
            )
            ->get();
        $events = [];
        foreach ($query as $e) {
            $events[] = [
                'name'      => $e->name,
                'date'      => $e->date,
                'type'      => $e->type,
                'important' => $e->important,
                'series_id' => $e->series_id,
            ];
        }
        return $events;
    }
}
