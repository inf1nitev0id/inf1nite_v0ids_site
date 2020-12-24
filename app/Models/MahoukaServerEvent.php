<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahoukaServerEvent extends Model
{
    use HasFactory;

		public $timestamps = false;

		public static function getEvents() {
			$query = MahoukaServerEvent::orderBy('date', 'asc')->orderBy('type', 'asc')->orderBy('important', 'desc')->get();
			$events = [];
			foreach ($query as $e) {
				$events[] = [
					'name' => $e->name,
					'date' => $e->date,
					'type' => $e->type,
					'important' => $e->important,
					'series_id' => $e->series_id
				];
			}
			return $events;
		}
}
