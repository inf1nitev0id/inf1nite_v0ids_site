<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int  $post_id
 * @property int  $file_id
 *
 * @property Post $post
 * @property File $file
 *
 * @mixin Builder
 */
class AttachedFile extends Model {
    use HasFactory;

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('Post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('File');
    }
}
