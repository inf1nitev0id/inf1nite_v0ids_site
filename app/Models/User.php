<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;


/**
 * @property int            $id
 * @property string         $name
 * @property string         $email
 * @property \DateTime      $email_verified_at
 * @property string         $password
 * @property int            $invite_id
 * @property string         $role
 * @property string         $remember_token
 * @property string         $created_at
 * @property string         $updated_at
 *
 * @property Invite         $invite
 * @property Post[]         $catalogs
 * @property Post[]         $posts
 * @property Post[]         $comments
 * @property AttachedFile[] $attachedFiles
 *
 * @mixin Builder
 */
class User extends Authenticatable {
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invite(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('Invite');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function catalogs(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('Post')
            ->where(
                'type',
                '=',
                'catalog'
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('Post')
            ->where(
                'type',
                '=',
                'post'
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('Post')
            ->where(
                'type',
                '=',
                'comment'
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('Rating');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachedFiles(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('AttachedFiles');
    }

    /**
     * @return bool
     */
    public function isModerator(): bool {
        return $this->role !== 'user';
    }
}
