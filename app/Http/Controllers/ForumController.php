<?php

namespace App\Http\Controllers;

use App\Models\AttachedFile;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

/**
 * Констроллер форума
 */
class ForumController extends Controller {
    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function forum(int $id = 0): \Illuminate\Contracts\View\View | \Illuminate\Contracts\View\Factory | \Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse | \Illuminate\Contracts\Foundation\Application {
        if ($id === 0) {
            $id = null;
        }
        $type          = 'catalog';
        $current       = null;
        $moderatorOnly = true;
        $path          = Post::getPath($id);

        if ($id !== null) {
            $current = Post::getPost($id);
            if ($current === null) {
                return abort(404);
            }
            if ($current->type === 'comment') {
                $post_id = null;
                foreach ($path as $parent) {
                    if ($parent['type'] === 'post') {
                        $post_id = $parent['id'];
                        break;
                    }
                }
                return redirect(
                    route(
                        'forum',
                        $post_id
                    )."#".$id
                );
            }
            if ($current->type === 'post') {
                $type = 'post';
            }
            $moderatorOnly = $current->moderator_only;
        }

        $editable = Auth::check() && (!$moderatorOnly || self::isModerator());

        if ($id !== null) {
            $pathStr = $current->type == 'catalog' ? $current->name : "";
            foreach ($path as $parent) {
                $pathStr = "<a href=\"".route(
                        'forum',
                        $parent['id']
                    )."\">".$parent['name']." &gt;</a> ".$pathStr;
            }
            $pathStr = "<a href=\"".route('forum')."\"> &gt;</a> ".$pathStr;
        } else {
            $pathStr = null;
        }

        return view(
            'forum/index',
            [
                'id'             => $id,
                'type'           => $type,
                'current'        => $current,
                'current_rating' => Rating::getPostRating($id),
                'path'           => $pathStr,
                'posts'          => $type == 'catalog'
                    ? Post::getChildren($id)
                    : Post::getComments(
                        $id
                    ),
                'editable'       => $editable,
            ]
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function addComment(Request $request): \Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse | \Illuminate\Contracts\Foundation\Application {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->withErrors(['Авторизуйтесь, чтобы отправить комментарий.']);
        }
        $request->validate([
                               'id'   => 'required|integer',
                               'text' => 'required|max:2000',
                           ]);
        $reply_to = Post::getPost($request->input('id'));
        if ($reply_to == null) {
            return redirect()
                ->back()
                ->withErrors(['Поста с таким ID не существует.']);
        }
        if ($reply_to->type == 'catalog') {
            return redirect()
                ->back()
                ->withErrors(['Поста с таким ID не существует.']);
        }

        $comment            = new Post();
        $comment->text      = $request->input('text');
        $comment->parent_id = $request->input('id');
        $comment->user_id   = Auth::user()->id;
        $comment->type      = 'comment';
        $comment->save();

        return redirect(
            route(
                "forum",
                $comment->id
            )
        )->with(
            'status',
            'Комментарий добавлен.'
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteComment(Request $request): \Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse | \Illuminate\Contracts\Foundation\Application {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->withErrors(['Авторизуйтесь, чтобы удалить комментарий.']);
        }
        $request->validate([
                               'id' => 'required|integer',
                           ]);
        $id      = $request->input('id');
        $comment = Post::getPost($id);
        if ($comment == null) {
            return redirect()
                ->back()
                ->withErrors(['Комментария с таким ID не существует.']);
        }
        if ($comment->type != 'comment') {
            return redirect()
                ->back()
                ->withErrors(['Комментария с таким ID не существует.']);
        }
        if ($comment->user_id != Auth::user()->id && !self::isModerator()) {
            return redirect()
                ->back()
                ->withErrors(['Вы не можете удалить данный комментарий.']);
        }
        $parent = Post::deleteComment($id);
        return redirect(
            route(
                "forum",
                $parent
            )
        )->with(
            'status',
            'Комментарий удалён.'
        );
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function addPostForm($id): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->withErrors(['Авторизуйтесь, чтобы создать пост.']);
        }
        $catalog = Post::getPost($id);
        if ($catalog == null || $catalog->type !== 'catalog') {
            return redirect()
                ->back()
                ->withErrors(['Каталога с таким ID не существует.']);
        }
        if ($catalog->moderator_only && !self::isModerator()) {
            return redirect()
                ->back()
                ->withErrors(['Вы не можете добавлять посты в этот каталог.']);
        }
        $path = "<a href=\"".route('forum')."\"> &gt;</a> <a href=\"".route('forum', $id)."\">".$catalog->name." &gt;</a>";
        foreach (Post::getPath($id) as $parent) {
            $path = "<a href=\"".route('forum', $parent['id'])."\">".$parent['name']." &gt;</a> ".$path;
        }
        return view(
            'forum/add_post',
            [
                'id'      => $id,
                'path'    => $path,
                'catalog' => $catalog,
            ]
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
     */
    public function addPost(Request $request): \Illuminate\Routing\Redirector | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->withErrors(['Авторизуйтесь, чтобы создать пост.']);
        }
        $request->validate([
                               'id'    => 'required|integer',
                               'title' => 'required|max:100',
                               'text'  => 'required|max:5000',
                           ]);
        $id      = $request->input('id');
        $catalog = Post::getPost($id);
        if ($catalog == null) {
            return redirect()
                ->back()
                ->withErrors(['Каталога с таким ID не существует.']);
        }
        if ($catalog->type != 'catalog') {
            return redirect()
                ->back()
                ->withErrors(['Каталога с таким ID не существует.']);
        }
        if ($catalog->moderator_only && !self::isModerator()) {
            return redirect()
                ->back()
                ->withErrors(['Вы не можете добавлять посты в этот каталог.']);
        }
        $post            = new Post();
        $post->parent_id = $id;
        $post->user_id   = Auth::user()->id;
        $post->type      = 'post';
        $post->name      = $request->input('title');
        $post->text      = $request->input('text');
        $post->save();

        foreach ($request->file('attachments') as $key => $file) {
            $dbFile = new File();
            $dbFile->name = $request->input('attachments-names')[$key] ?: $file->getClientOriginalName();
            $dbFile->setContent('forum/'.md5($dbFile->name.time()).'.'.$file->getClientOriginalExtension(), $file->getContent());
            $dbFile->user_id = Auth::user()->id;
            $dbFile->module = File::MODULE_FORUM;
            $dbFile->save();

            $attachedFile = new AttachedFile();
            $attachedFile->post_id = $post->id;
            $attachedFile->file_id = $dbFile->id;
            $attachedFile->save();
        }

        return redirect(
            route(
                'forum',
                $post->id
            )
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
     */
    public function deletePost(Request $request): \Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse | \Illuminate\Contracts\Foundation\Application {
        if (!Auth::check()) {
            return redirect()
                ->back()
                ->withErrors(['Авторизуйтесь, чтобы удалить пост.']);
        }
        $request->validate([
                               'id' => 'required|integer',
                           ]);
        $id   = $request->input('id');
        $post = Post::getPost($id);
        if ($post == null) {
            return redirect()
                ->back()
                ->withErrors(['Поста с таким ID не существует.']);
        }
        if ($post->type != 'post') {
            return redirect()
                ->back()
                ->withErrors(['Поста с таким ID не существует.']);
        }
        if ($post->user_id != Auth::user()->id && !self::isModerator()) {
            return redirect()
                ->back()
                ->withErrors(['Вы не можете удалить данный пост.']);
        }
        $parent = $post->parent_id;
        $post->delete();
        return redirect(
            route(
                "forum",
                $parent
            )
        )->with(
            'status',
            'Пост удалён.'
        );
    }
}
