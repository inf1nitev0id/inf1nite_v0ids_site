<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahoukaServerUser extends Model
{
  use HasFactory;

  public $timestamps = false;

  public static function getSortedUsers($date = null, $with_hidden = false) {
    $query_users = null;
    if ($with_hidden) {
      $query_users = MahoukaServerUser::all();
    } else {
  		$query_users = MahoukaServerUser::where('hidden', '=', 'false')->get();
    }
		$users = [];
		foreach($query_users as $q_user) {
			$user = [];
			$user['id'] = $q_user->id;
			$user['name'] = $q_user->name;
			$user['alias'] = $q_user->alias;
			$user['join_date'] = $q_user->join_date;
      $where = [['user_id', '=', $user['id']]];
      if ($date != null)
        $where[] = ['date', '=', $date];
      $query_rate = MahoukaServerRating::select('rate')->where($where)->orderBy('date', 'desc')->orderBy('time', 'desc')->first();
			if($query_rate) {
				$user['rate'] = $query_rate->rate;
			} else {
				$user['rate'] = 0;
			}
			$users[] = $user;
		}
		usort($users, function($a, $b) {
			return $a['rate'] < $b['rate'];
		});
		return $users;
	}
}
