<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int  $id
 * @property int  $post_id
 * @property int  $user_id
 * @property bool $value
 *
 * @property Post $post
 * @property User $user
 *
 * @mixin Builder
 */
class Rating extends Model {
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('Post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('User');
    }

    /**
     * @param $id
     *
     * @return int[]
     */
    #[ArrayShape(['positive' => "int", 'negative' => "int"])] public static function getPostRating($id): array {
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
