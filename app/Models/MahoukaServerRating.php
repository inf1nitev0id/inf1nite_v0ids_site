<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahoukaServerRating extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function getUserRatingArray($user_id) {
      $query = MahoukaServerRating::select('date', 'time', 'rate')
        ->where('user_id', '=', $user_id)
        ->orderBy('date', 'asc')
        ->orderBy('time', 'asc')
        ->get();
      $result = [];
      if ($join_date = MahoukaServerUser::find($user_id)->join_date)
        $result[$join_date][0] = 0;
      foreach ($query as $row) {
        $result[$row->date][$row->time] = $row->rate;
      }
      return $result;
    }

    public static function getMinDate() {
      $join = MahoukaServerUser::select('join_date')->where('join_date', '<>', null)->orderBy('join_date', 'asc')->first()->join_date;
      $rate = MahoukaServerRating::select('date')->orderBy('date', 'asc')->first()->date;
      return min($join, $rate);
    }

    public static function getMaxDate() {
      $join = MahoukaServerUser::select('join_date')->where('join_date', '<>', null)->orderBy('join_date', 'desc')->first()->join_date;
      $rate = MahoukaServerRating::select('date')->orderBy('date', 'desc')->first()->date;
      return max($join, $rate);
    }
}
