<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    use HasFactory;

    public static function getPostRating($id) {
        $rating = [
            'positive' => 0,
            'negative' => 0,
        ];
        $query  = Rating
            ::select('value')
            ->where(
                'post_id',
                '=',
                $id
            )
            ->get();
        foreach ($query as $rate) {
            if ($rate->value) {
                ++$rating['positive'];
            } else {
                ++$rating['negative'];
            }
        }
        return $rating;
    }
}
