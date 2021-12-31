<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $name
 * @property int    $discord_id
 * @property string $alias
 * @property bool   $hidden
 * @property string $join_date
 *
 * @mixin Builder
 */
class MahoukaServerUser extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * Получение массива пользователей, отсортированных по последнему рейтингу
     *
     * @param $date
     * @param $with_hidden
     * @param $only_last
     *
     * @return array
     */
    public static function getSortedUsers($date = null, $with_hidden = false, $only_last = false): array {
        $query_users = null;
        if ($with_hidden) {
            $query_users = MahoukaServerUser::all();
        } else {
            $query_users = MahoukaServerUser
                ::where(
                    'hidden',
                    '=',
                    'false'
                )
                ->get();
        }
        $users     = [];
        $last_date = null;
        foreach ($query_users as $q_user) {
            $user               = [];
            $user['id']         = $q_user->id;
            $user['name']       = $q_user->name;
            $user['discord_id'] = $q_user->discord_id;
            $user['alias']      = $q_user->alias;
            $user['join_date']  = $q_user->join_date;
            $where              = [['user_id', '=', $user['id']]];
            if ($date != null) {
                $where[] = ['date', '=', $date];
            }
            $query_rate = MahoukaServerRating
                ::select(
                    'rate',
                    'date'
                )
                ->where($where)
                ->orderBy(
                    'date',
                    'desc'
                )
                ->orderBy(
                    'time',
                    'desc'
                )
                ->first();
            if ($query_rate) {
                $user['rate']      = $query_rate->rate;
                $time              = strtotime($query_rate->date);
                $user['last_date'] = $time;
                if ($time > $last_date ?? 0) {
                    $last_date = $time;
                }
            } else {
                $user['rate'] = 0;
            }
            $users[] = $user;
        }
        usort(
            $users,
            function($a, $b) {
                return $a['rate'] < $b['rate'];
            }
        );
        if ($only_last) {
            $users = array_filter(
                $users,
                function($item) use ($last_date) {
                    return $item['last_date'] == $last_date;
                }
            );
        }
        return $users;
    }

    /**
     * @param $id
     * @param $discord_id
     *
     * @return void
     */
    public static function setDiscordId($id, $discord_id) {
        MahoukaServerUser
            ::where(
                'id',
                $id
            )
            ->update(['discord_id' => $discord_id]);
    }
}
