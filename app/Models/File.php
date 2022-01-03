<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @property int            $id
 * @property string         $module
 * @property int|null       $user_id
 * @property string         $name
 * @property string         $path
 * @property string         $created_at
 *
 * @property User|null      $user
 * @property AttachedFile[] $attachedFiles
 *
 * @mixin Builder
 */
class File extends Model {
    use HasFactory;

    public $timestamps = false;

    public static function boot() {
        parent::boot();

        static::creating(function($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachedFiles(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('AttachedFile');
    }

    public const MODULE_FORUM = 'forum';

    /**
     * @param string          $filename
     * @param string|resource $content
     *
     * @return void
     */
    public function setContent(string $filename, $content) {
        $disk = Storage::disk('public');
        if ($disk->exists($filename)) {
            $i = 1;
            while ($disk->exists(preg_replace('/^(.+)(\..+?)$/', '$1_'.$i.'$2', $filename))) {
                $i++;
            }
            $filename = preg_replace('/^(.+)(\..+?)$/', '$1_'.$i.'$2', $filename);
        }
        $this->path = $filename;
        $disk->put($filename, $content);
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        try {
            return Storage::disk('public')->get($this->path);
        } catch (FileNotFoundException $e) {
            return null;
        }
    }
}
