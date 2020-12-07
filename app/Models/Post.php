<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;

class Post extends Model
{
  use HasFactory;

  public static function getPost($id) {
    return Post
      ::select('posts.id as id', 'posts.name as name', 'text',
        'users.id as user_id', 'users.name as user_name', 'type',
        'parent_id', 'moderator_only', 'deleted', 'posts.created_at')
      ->where('posts.id', '=', $id)
      ->join('users', 'user_id', '=', 'users.id')->first();
  }

  public static function getChilds($id) {
    return Post
      ::selectRaw('p.`id` as `id`, p.`name` as `name`, p.`text`, '.
        'u.`id` as `user_id`, u.`name` as `user_name`, p.`type`, '.
        'p.`created_at` as `time`, MAX(c.`created_at`) as `c_time`')
      ->from('posts as p')
      ->join('users as u', 'p.user_id', '=', 'u.id')
      ->leftJoin('posts as c', 'p.id', '=', 'c.parent_id')
      ->where('p.parent_id', '=', $id)
      ->groupBy('p.id')
      ->orderBy('p.type', 'asc')
      ->orderBy('c_time', 'desc')
      ->orderBy('time', 'desc')->get();
  }

  public static function getComments($id) {
    $comments = Post
      ::select('posts.id as id', 'posts.text as text',
        'users.id as user_id', 'users.name as user_name',
        'posts.created_at as time', 'deleted')
      ->where('posts.parent_id' , '=', $id)
      ->where('type', '=', 'comment')
      ->join('users', 'user_id', '=', 'users.id')->get();
    $result = [];
    foreach ($comments as $comment) {
      $result[] = [
        'comment' => [
          'id' => $comment->id,
          'text' => $comment->text,
          'user_id' => $comment->user_id,
          'user_name' => $comment->user_name,
          'deleted' => $comment->deleted,
          'time' => $comment->time,
          'rating' => Rating::getPostRating($comment->id)
        ],
        'childs' => Post::getComments($comment->id)
      ];
    }
    return $result;
  }

  public static function getPath($id) {
    $path = [];
    $id_d = $id;
    while ($id_d != null) {
      $post = Post::select('name', 'parent_id', 'type')->where('id', '=', $id_d)->first();
      if ($post == null) break;
      if ($id_d != $id) $path[] = ['id' => $id_d, 'name' => $post->name, 'type' => $post->type];
      $id_d = $post->parent_id;
    }
    return $path;
  }

  public static function deleteComment($id, $hard = true) {
    $comment = Post::getPost($id);
    $undeleted = $id;
    if ($comment->type == 'comment')
      if (Post::getChilds($id)->count() > 0) {
        $comment->deleted = true;
        $comment->save();
      }
      else if ($comment->deleted || $hard) {
        $parent = $comment->parent_id;
        $comment->delete();
        $undeleted = Post::deleteComment($parent, false);
      }
    return $undeleted;
  }
}
