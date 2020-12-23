<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\MahoukaSeries;

class MahoukaServerEvent extends Model
{
    use HasFactory;

		public $timestamps = false;

		public static function getEvents() {
			$query = MahoukaServerEvent::orderBy('date', 'asc')->orderBy('type', 'asc')->orderBy('important', 'desc')->get();
			$events = [];
			foreach ($query as $e) {
				$event = [
					'name' => $e->name,
					'date' => $e->date,
					'type' => $e->type,
					'important' => $e->important,
					'series_id' => $e->series_id
				];
				switch ($e->type) {
					case 'release':
						$event['color'] = '00A387';
						break;
					case 'announcement':
						$event['color'] = '60E6CF';
						break;
					default:
						$event['color'] = '888888';
						break;
				}
				if ($e->series_id !== null) {
					$series = MahoukaSeries::find($e->series_id);
					$event['series'] = $series->name;
					$event['color'] = $series->color;
				} else {
					$event['series'] = null;
				}
				$events[] = $event;
			}
			return $events;
		}
}
