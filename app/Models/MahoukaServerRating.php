<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property int    $user_id
 * @property int    $rate
 * @property string $date
 * @property bool   $time
 *
 * @mixin Builder
 */
class MahoukaServerRating extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * Получение рейтинга пользователя
     *
     * @param $user_id
     *
     * @return array
     */
    public static function getUserRatingArray($user_id): array {
        $query = MahoukaServerRating::select(
            'date',
            'time',
            'rate'
        )
            ->where(
                'user_id',
                '=',
                $user_id
            )
            ->orderBy(
                'date',
                'asc'
            )
            ->orderBy(
                'time',
                'asc'
            )
            ->get();
        $result = [];
        if ($join_date = MahoukaServerUser::find($user_id)->join_date) {
            $result[$join_date][0] = 0;
        }
        foreach ($query as $row) {
            $result[$row->date][$row->time] = $row->rate;
        }
        return $result;
    }

    /**
     * Получение наименьшей указанной даты в рейтинге
     *
     * @return string
     */
    public static function getMinDate(): string {
        $join = MahoukaServerUser::select('join_date')
            ->where(
                'join_date',
                '<>',
                null
            )
            ->orderBy(
                'join_date',
                'asc'
            )
            ->first()->join_date;
        $rate = MahoukaServerRating::select('date')
            ->orderBy(
                'date',
                'asc'
            )
            ->first()->date;
        return min(
            $join,
            $rate
        );
    }

    /**
     * Получение наибольшей указанной даты в рейтинге
     *
     * @return string
     */
    public static function getMaxDate(): string {
        $join = MahoukaServerUser::select('join_date')
            ->where(
                'join_date',
                '<>',
                null
            )
            ->orderBy(
                'join_date',
                'desc'
            )
            ->first()->join_date;
        $rate = MahoukaServerRating::select('date')
            ->orderBy(
                'date',
                'desc'
            )
            ->first()->date;
        return max(
            $join,
            $rate
        );
    }

    /**
     * Получение даты и времени последнего записанного рейтинга
     *
     * @return array|null
     */
    public static function getLastRate(): ?array {
        $query = MahoukaServerRating::select(
            'date',
            'time'
        )
            ->orderBy(
                'date',
                'desc'
            )
            ->orderBy(
                'time',
                'desc'
            )
            ->first();
        return $query ? [
            'date' => $query->date,
            'time' => $query->time,
        ] : null;
    }
}
