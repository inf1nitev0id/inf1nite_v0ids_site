<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahoukaSeries extends Model
{
    use HasFactory;

		public $timestamps = false;

		public static function getSeries() {
			$query = MahoukaSeries::get();
			$series = [];
			foreach($query as $s) {
				$series[] = [
					'id' => $s->id,
					'name' => $s->name,
					'color' => $s->color
				];
			}
			return $series;
		}
}
