<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MahoukaServerUser;
use App\Models\MahoukaServerHash;
use App\Models\MahoukaServerRating;
use App\Models\MahoukaServerNumber;
use App\Models\MahoukaServerEvent;
use App\Models\MahoukaSeries;

class MahoukaServerRatingController extends Controller
{
// преобразует массив в бинарную строку
	private static function arrayToString(array $array, int $char_height) {
		$bytes = ceil($char_height / 8);
		$result = "";
		foreach($array as $line) {
			$chars = array_fill(0, $bytes, 0);
			foreach($line as $i => $bit) {
				$chars[floor($i / 8)] << 1;
				$chars[floor($i / 8)] += $bit;
			}
			foreach($chars as $char) {
				$result .= chr($char);
			}
		}
		return $result;
	}

// преобразует бинарную строку в массив
	private static function stringToArray(string $string, int $char_height) {
		$bytes = ceil($char_height / 8);
		$length = strlen($string);
		$result = [];
		for($i = 0; $i < $length / $bytes; $i++) {
			$line = substr($string, $i * $bytes, $bytes);
			for($j = 0; $j < $bytes; $j++) {
				$char = ord($line[$j]);
				for($k = 0; $k < 8; $k++) {
					if($j * 8 + $k >= $char_height) break;
					$result[$i][$k] = $char & 1;
					$char >> 1;
				}
			}
		}
		return $result;
	}

// форма загрузки
	public function loadForm() {
		$last_rate = MahoukaServerRating::getLastRate() ?? ['date' => date('Y-m-d'), 'time' => 0];
		return view('mahouka.top.load', [
			'last_date' => $last_rate['date'],
			'last_time' => $last_rate['time'],
			'top' => $this->top(),
		]);

// обработка и проверка правильности данных перез загрузкой
	public function preload(Request $request) {
		$request->validate([
			'url' => 'url|required',
			'date' => 'date|required',
			'time' => 'boolean|required'
		]);
		if(!$image = imageCreateFromPNG($request->url))
			return redirect()->back()->withErrors(["Ссылка должна указывать на PNG изображение."]);
		$SIZE_X = 720;
		$SIZE_Y = 730;

		if(imageSX($image) != $SIZE_X || imageSY($image) != $SIZE_Y)
			return redirect()->back()->withErrors(["Изображение должно иметь разрешение ".$SIZE_X."*".$SIZE_Y."."]);

		$CHAR_HEIGHT = 29;
		$ROW_HEIGHT = 74;
		$NAME_END = 504;
		$RATE_START = 565;
		$white_index = imageColorExact($image, 255, 255, 255);
		$dot_index = imageColorExact($image, 111, 111, 111);

		$numbers_code = [];
		$query = MahoukaServerNumber::all();
		foreach ($query as $row) {
			$numbers_code[$row->hash] = $row->value;
		}

		$NAME_START = 0;
		for ($x = 50; $x < $NAME_END; $x++) {
			if (imageColorAt($image, $x, 32) == $dot_index) {
				$NAME_START = $x + 6;
				break;
			}
		}

		$h = 21;
		$binary = [];
		for ($i = 0; $i < 10; $i++) {
			$name_start = $NAME_START;
			$name_length = 0;
			$count = 0;
			$start = false;
			$l_num = 0;
			$empty_line = true;
			for ($x = $NAME_START; $x < $NAME_END + $NAME_START - 150; $x++) {
				$not_empty = false;
				$line = [];
				for ($y = $h; $y < $h + $CHAR_HEIGHT; $y++) {
					$color = imageColorsForIndex($image, imageColorAt($image, $x, $y));
					if ($color['red'] > 200 && $color['green'] > 200 && $color['blue'] > 200) {
						$not_empty = true;
						$line[] = true;
					} else {
						$line[] = false;
					}
				}

				if (!$start) {
					if ($not_empty) {
						$name_start = $x;
						$start = true;
						$binary[$i]['name'][$l_num] = $line;
						$l_num++;
						$empty_line = false;
					}
				} else {
					$binary[$i]['name'][$l_num] = $line;
					$l_num++;
					if ($not_empty) {
						$count = 0;
					} else {
						if ($count == 0) $name_length = $x - $name_start;
						$count++;
						if ($count > 20) {
							break;
						}
					}
				}
			}
			if($empty_line) continue;

			$rate_start = $RATE_START;
			$rate_length = 0;
			$start = false;
			$num = 0;
			for ($x = $RATE_START; $x < $SIZE_X; $x++) {
				$not_empty = false;
				$line = [];
				for ($y = $h; $y < $h + $CHAR_HEIGHT; $y++) {
					$color = imageColorsForIndex($image, imageColorAt($image, $x, $y));
					if ($color['red'] > 200 && $color['green'] > 200 && $color['blue'] > 200) {
						$not_empty = true;
						$line[] = true;
					} else {
						$line[] = false;
					}
				}
				if ($not_empty && !$start) {
					$l_num = 0;
					$binary[$i]['rate'][$num][$l_num] = $line;
					$l_num++;
					$start = true;
				} else if ($start) {
					if ($not_empty) {
						$binary[$i]['rate'][$num][$l_num] = $line;
						$l_num++;
					} else {
						$num++;
						$start = false;
					}
				}
			}
			$h += $ROW_HEIGHT;
		}

		$users = [];
		$unknown_names = [];
		$unknown_numbers = [];
		foreach($binary as $row) {
			$unknown = false;
			$hash = md5(json_encode($row['name']));
			$query = MahoukaServerHash::select('user_id')
				->where('hash', '=', $hash)
				->first();
			$user = null;
			if ($query) {
				$user_db = MahoukaServerUser::find($query->user_id);
				$user['id'] = $user_db->id;
				$user['name'] = $user_db->name;
			} else {
				$unknown = true;
				$unknown_names[$hash] = $row['name'];
			}
			$rate = 0;
			foreach($row['rate'] as $char) {
				$rate *= 10;
				$num_hash = md5(json_encode($char));
				if (array_key_exists($num_hash, $numbers_code)) {
					$rate += $numbers_code[$num_hash];
				} else {
					$unknown = true;
					if (1) {
						$unknown_numbers[$num_hash] = $char;
					}
				}
			}
			$user['rate'] = $rate;
			if (!$unknown)
				$users[] = $user;
		}

		$usernames = [];
		$query = MahoukaServerUser::all();
		foreach ($query as $row) {
			$username['id'] = $row->id;
			$username['name'] = $row->name;
			$usernames[] = $username;
		}

		imageDestroy($image);

		return view('mahouka.top.preload', [
			'url' => $request->url,
			'date' => $request->date,
			'time' => $request->time,
			'users' => $users,
			'unknown_names' => $unknown_names,
			'unknown_numbers' => $unknown_numbers,
			'char_height' => $CHAR_HEIGHT,
			'usernames' => $usernames
		]);
	}

// загрузка хешей для новых ников и цифр в БД
	public function load_hashes(Request $request) {
		$array = $request->all();
		foreach ($array as $key => $value) {
			if ($value != -1) {
				if (substr($key, 0, 4) === "name") {
					$hash = substr($key, 5);
					if (!MahoukaServerHash::select('user_id')->where('hash', '=', $hash)->first()) {
						$db_hash = new MahoukaServerHash;
						$db_hash->hash = $hash;
						$db_hash->user_id = $value;
						$db_hash->save();
					}
				} else if (substr($key, 0, 6) === "number") {
					$hash = substr($key, 7);
					if (!MahoukaServerNumber::select('value')->where('hash', '=', $hash)->first()) {
						$db_hash = new MahoukaServerNumber;
						$db_hash->hash = $hash;
						$db_hash->value = $value;
						$db_hash->save();
					}
				}
			}
		}
		return redirect()->back();
	}

// запись введённых данных в БД
	public function write_rate(Request $request) {
		$array = $request->all();
		foreach ($array as $key => $value) {
			if (is_int($key)) {
				$record = MahoukaServerRating::where([
					['user_id', '=', $key],
					['date', '=', $request->date],
					['time', '=', $request->time]
				])->first();
				if (!$record) {
					$record = new MahoukaServerRating;
					$record->user_id = $key;
					$record->date = $request->date;
					$record->time = $request->time;
				}
				$record->rate = $value;
				$record->save();
			}
		}
		return redirect()->route('mahouka.top.load');
	}

// получение данных о рейтинге из БД
	private function top() {
		$sorted_users = MahoukaServerUser::getSortedUsers();
		$min_date = \DateTime::createFromFormat('Y-m-d', MahoukaServerRating::getMinDate());
		$max_date = \DateTime::createFromFormat('Y-m-d', MahoukaServerRating::getMaxDate());
		$step = new \DateInterval('P1D');
		$ratings = [];
		$table = [];
		foreach ($sorted_users as $key => $user) {
			$ratings[$key] = MahoukaServerRating::getUserRatingArray($user['id']);
		}
		for ($date = clone($min_date), $i = 0; $date <= $max_date; $date->add($step), $i++) {
			foreach ($sorted_users as $key => $user) {
				$d = $date->format('Y-m-d');
				$table[$i][0][$key] = $ratings[$key][$d][0] ?? null;
				$table[$i][1][$key] = $ratings[$key][$d][1] ?? null;
			}
		}
		return [
			'min_date' => $min_date->format('Y-m-d'),
			'users' => $sorted_users,
			'rating' => $table
		];
	}

// вывод рейтинга в виде таблицы
	public function table() {
		$top = $this->top();
		$top['step'] = new \DateInterval('P1D');
		return view('mahouka.top.table', $top);
	}

// создание уникальных цветов для каждого графика
	private static function getColor($id) {
		$r; $g; $b;
		$n1 = 200;
		$n0 = 0;
		switch ($id % 6) {
			case 0:
				$r = $n1;
				$g = $n0;
				$b = $n0;
				break;
			case 1:
				$r = $n0;
				$g = $n1;
				$b = $n0;
				break;
			case 2:
				$r = $n0;
				$g = $n0;
				$b = $n1;
				break;
			case 3:
				$r = $n1;
				$g = $n1;
				$b = $n0;
				break;
			case 4:
				$r = $n1;
				$g = $n0;
				$b = $n1;
				break;
			case 5:
				$r = $n0;
				$g = $n1;
				$b = $n1;
				break;
		}
		while ($id >= 6) {
			$r = round($r * 0.9);
			$g = round($g * 0.9);
			$b = round($b * 0.9);
			$id -= 6;
		}
		return 'rgb('.$r.','.$g.','.$b.')';
	}

// подготовка данных для отрисовки графика
	public function chart() {
		$top = $this->top();
		$lines = [];
		$days = count($top['rating']);
		$i = 0;
		foreach ($top['users'] as $key => $user){
			$line = [];
			$line['index'] = $i++;
			$line['user'] = $user;
			$line['color'] = $this->getColor($line['user']['id'] - 1);
			$line['rating'] = [];
			$prev = null;
			for ($day = 0; $day < $days; $day++) {
				$prev = ($line['rating'][] = $top['rating'][$day][0][$key] ?? $prev);
				$prev = ($line['rating'][] = $top['rating'][$day][1][$key] ?? $prev);
			}
			$line['visible'] = true;

			$lines[] = $line;
		}

		$events = MahoukaServerEvent::getEvents();
		$series = MahoukaSeries::getSeries();
		$series_i = [];
		foreach ($series as $i => &$s) {
			$series_i[$s['id']] = [
				'id' => $i,
				'name' =>	$s['name'],
				'color' => $s['color']
			];
			$s['visible'] = true;
		}
		foreach ($events as &$event) {
			$event['name'] = str_replace(["\r\n", "\r", "\n"], '<br />', $event['name']);
			if ($event['series_id'] !== null) {
				$event['series_id'] = $series_i[$event['series_id']]['id'];
			}
			switch ($event['type']) {
				case 'release':
					$event['type'] = 0;
					break;
				case 'announcement':
					$event['type'] = 1;
					break;
				default:
					$event['type'] = 2;
					break;
			}
		}

		return view('mahouka.top.chart', [
			'min_date' => $top['min_date'],
			'days' => count($top['rating']),
			'lines' => $lines,
			'events' => $events,
			'series' => $series
		]);
	}
}
