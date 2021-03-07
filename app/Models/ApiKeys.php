<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model
{
	use HasFactory;

	public $timestamps = false;

	public static function getKeyString($name) {
		$result = ApiKeys::select('key')->where('name', '=', $name)->first();
		return $result ? $result->key : null;
	}
}
