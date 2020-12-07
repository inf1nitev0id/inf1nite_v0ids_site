<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ForumController extends Controller
{
  private function isModerator() {
    return Auth::user()->role !== 'user';
  }

  public function forum($id = 0) {
    $page_id = intval($id);
    if ($page_id == 0) $page_id = null;
    $type = 'catalog';
    $current = null;
    $moderator_only = true;
    $path = Post::getPath($page_id);

    if ($page_id != null) {
      $current = Post::getPost($page_id);
      if ($current == null) return abort(404);
      if ($current->type == 'comment') {
        $post_id = null;
        foreach ($path as $parent)
          if ($parent['type'] == 'post') {
            $post_id = $parent['id'];
            break;
          }
        return redirect(route('forum', $post_id)."#".$page_id);
      }
      if ($current->type == 'post') $type = 'post';
      $moderator_only = $current->moderator_only;
    }

    $editable = Auth::check() && (!$moderator_only || $this->isModerator());

    return view('forum', [
      'id' => $page_id,
      'type' => $type,
      'current' => $current,
      'current_rating' => Rating::getPostRating($page_id),
      'path' => $path,
      'posts' => $type == 'catalog' ? Post::getChilds($page_id) : Post::getComments($page_id),
      'editable' => $editable
    ]);
  }

  public function addComment(Request $request) {
    if (!Auth::check()) return redirect()->back()->withErrors(['Авторизуйтесь, чтобы отправить комментарий.']);
    $request->validate([
      'id' => 'required|integer',
      'text' => 'required|max:2000',
    ]);
    $reply_to = Post::getPost($request->input('id'));
    if ($reply_to == null)
      return redirect()->back()->withErrors(['Поста с таким ID не существует.']);
    if ($reply_to->type == 'catalog')
      return redirect()->back()->withErrors(['Поста с таким ID не существует.']);

    $comment = new Post();
    $comment->text = $request->input('text');
    $comment->parent_id = $request->input('id');
    $comment->user_id = Auth::user()->id;
    $comment->type = 'comment';
    $comment->save();

    return redirect(route("forum", $comment->id))->with('status', 'Комментарий добавлен.');
  }

  public function deleteComment(Request $request) {
    if (!Auth::check()) return redirect()->back()->withErrors(['Авторизуйтесь, чтобы удалить комментарий.']);
    $request->validate([
      'id' => 'required|integer'
    ]);
    $id = $request->input('id');
    $comment = Post::getPost($id);
    if ($comment == null)
      return redirect()->back()->withErrors(['Комментария с таким ID не существует.']);
    if ($comment->type != 'comment')
      return redirect()->back()->withErrors(['Комментария с таким ID не существует.']);
    if ($comment->user_id != Auth::user()->id && !$this->isModerator())
      return redirect()->back()->withErrors(['Вы не можете удалить данный комментарий.']);
    $parent = Post::deleteComment($id);
    return redirect(route("forum", $parent))->with('status', 'Комментарий удалён.');
  }

  public function addPostForm($id) {
    if (!Auth::check()) return redirect()->back()->withErrors(['Авторизуйтесь, чтобы создать пост.']);
    $catalog = Post::getPost($id);
    if ($catalog == null)
      return redirect()->back()->withErrors(['Каталога с таким ID не существует.']);
    if ($catalog->type != 'catalog')
      return redirect()->back()->withErrors(['Каталога с таким ID не существует.']);
    if ($catalog->moderator_only && !$this->isModerator())
      return redirect()->back()->withErrors(['Вы не можете добавлять посты в этот каталог.']);
    return view('add_post_form', [
      'id' => $id,
      'path' => Post::getPath($id),
      'catalog' => $catalog
    ]);
  }

  public function addPost(Request $request) {
    if (!Auth::check()) return redirect()->back()->withErrors(['Авторизуйтесь, чтобы создать пост.']);
    $request->validate([
      'id' => 'required|integer',
      'title' => 'required|max:100',
      'text' => 'required|max:5000'
    ]);
    $id = $request->input('id');
    $catalog = Post::getPost($id);
    if ($catalog == null)
      return redirect()->back()->withErrors(['Каталога с таким ID не существует.']);
    if ($catalog->type != 'catalog')
      return redirect()->back()->withErrors(['Каталога с таким ID не существует.']);
    if ($catalog->moderator_only && !$this->isModerator())
      return redirect()->back()->withErrors(['Вы не можете добавлять посты в этот каталог.']);
    $post = new Post();
    $post->parent_id = $id;
    $post->user_id = Auth::user()->id;
    $post->type = 'post';
    $post->name = $request->input('title');
    $post->text = $request->input('text');
    $post->save();
    return redirect(route('forum', $post->id));
  }

  public function deletePost(Request $request) {
    if (!Auth::check()) return redirect()->back()->withErrors(['Авторизуйтесь, чтобы удалить пост.']);
    $request->validate([
      'id' => 'required|integer'
    ]);
    $id = $request->input('id');
    $post = Post::getPost($id);
    if ($post == null)
      return redirect()->back()->withErrors(['Поста с таким ID не существует.']);
    if ($post->type != 'post')
      return redirect()->back()->withErrors(['Поста с таким ID не существует.']);
    if ($post->user_id != Auth::user()->id && !$this->isModerator())
      return redirect()->back()->withErrors(['Вы не можете удалить данный пост.']);
    $parent = $post->parent_id;
    $post->delete();
    return redirect(route("forum", $parent))->with('status', 'Пост удалён.');
  }
}
