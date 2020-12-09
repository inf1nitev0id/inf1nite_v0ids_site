@extends('layouts.main')

@section('title') {{$id != null ? $current->name." - " : ""}}Форум @endsection

@section('head')
<link rel="stylesheet" href="/css/forum.css" />
<script src="/js/forum.js"></script>
@endsection

@section('content')
<?php
  $is_moderator = Auth::check() && Auth::user()->role !== 'user';

  if ($id != null) {
    $path_str = $current->type == 'catalog' ? $current->name : "";
    foreach ($path as $parent) {
      $path_str = "<a href=\"".route('forum', $parent['id'])."\">".$parent['name']." &gt;</a> ".$path_str;
    }
    $path_str = "<a href=\"".route('forum')."\"> &gt;</a> ".$path_str;
  }

  function text_to_html($text) {
    return str_replace(["\r\n", "\r", "\n"], '<br />', $text);
  }

  function isAuthor($user_id) {
    if (Auth::check())
      return Auth::user()->id == $user_id;
    else
      return null;
  }

  function printRating($id, $rating, $user_id) {
    return;
    $a = Auth::check();
    $self = isAuthor($user_id);
    if($a && !$self) { ?>
      <i class="fas fa-thumbs-up btn btn-light" data-action="like" title="Лайк"></i>
    <?php } ?>
      <span id="rating{{$id}}">
        <span class="btn disabled rating" title="+{{$rating['positive']}} | -{{$rating['negative']}}">
          {{$rating['positive'] - $rating['negative']}}
        </span>
      </span>
    <?php if($a && !$self) { ?>
      <i class="fas fa-thumbs-down btn btn-light" data-action="dislike" title="Дизлайк"></i>
    <?php }
  }
?>
<div id="forum">
  @if($errors->any())
    <div class="alert alert-danger mb-2 alert-dismissible">
      <ul>
        @foreach($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
      </ul>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif
  @if($type == 'catalog')
    <h3>Форум</h3>
    <table class="table">
      @if($id != null)
        <thead>
          <tr>
            <th colspan=3>
              <?php echo $path_str ?><br />
              <small>{{$current->text}}</small>
            </th>
          </tr>
        </thead>
      @endif
      <tbody>
        @if($posts->count() == 0)
          <tr>
            <td>Каталог пуст</td>
          </tr>
        @else
          @foreach($posts as $post)
            <tr>
              <td><a href="{{route('forum', $post->id)}}">{{$post->name}}</a></td>
              <td>{{$post->type == 'post' ? $post->user_name : ""}}</td>
              <td>
                <small>
                  <?php
                    if ($post->type == 'catalog')
                      echo ($post->c_time != null ? 'Последний пост: '.$post->c_time : 'Постов нет');
                    else
                     echo ('Пост создан: '.$post->time.'<br />'.
                      ($post->c_time != null ? 'Последний комментарий: '.$post->c_time : 'Комментарив нет'));
                  ?>
                </small>
              </td>
            </tr>
          @endforeach
        @endif
        @if(Auth::check() && $editable)
          <tr>
            <td colspan=3>
              <a class="btn btn-light" href="{{route('forum.add-post-form', $id == null ? 0 : $id)}}">Добавить пост</a>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  @else
    @if(!$current->deleted)
      <h3>{{$current->name}}</h3>
      <div class="d-md-flex justify-content">
        <div class="">
          Автор: <a>{{$current->user_name}}</a>
        </div>
        <div class="ml-auto text-muted">
          {{$current->created_at}}
        </div>
      </div>
      <h6><?php echo $path_str ?></h6>
      <hr />
      <p class="text-justify"><?php echo text_to_html($current->text); ?></p>
      <div class="text-right" data-id="{{$current->id}}">
        @if(Auth::check() && (isAuthor($current->user_id) || $is_moderator))
          <i class="fas fa-times btn btn-light ml-auto{{ !isAuthor($current->user_id) && $is_moderator ? ' red' : '' }}" title="Удалить" data-toggle="modal" data-target="#deletePostModal"></i>
          <div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Удалить пост?</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-footer">
                  <form method="post" action="{{route('forum.delete-post')}}">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}" />
                    <input type="submit" class="btn btn-danger" value="Удалить" />
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endif
        {{printRating($current->id, $current_rating, $current->user_id)}}
      </div>
      <hr />
      <?php
        function printComments($list, $is_moderator) {
          global $auth;
          foreach ($list as $comment) {
            ?>
            <div class="comment_box">
              <a name="{{$comment['comment']['id']}}" />
              <div id="comment{{$comment['comment']['id']}}">
                @if (!$comment['comment']['deleted'])
                  <div class="comment comment-header d-flex">
                    <a href="">{{$comment['comment']['user_name']}}</a>
                    <span class="mx-2 text-muted">{{$comment['comment']['time']}}</span>
                    <a href="#{{$comment['comment']['id']}}">#</a>
                  </div>
                @endif
                <div class="comment comment-body" id="comment_text{{$comment['comment']['id']}}">
                  <?php echo $comment['comment']['deleted'] ? "<span class=\"text-muted\">КОММЕНТАРИЙ УДАЛЁН</span>" : text_to_html($comment['comment']['text']); ?>
                </div>
                @if (!$comment['comment']['deleted'])
                  <div class="comment comment-footer d-flex" data-id="{{$comment['comment']['id']}}">
                    <div>
                      @if(Auth::check())
                        <i id="reply{{$comment['comment']['id']}}-btn" class="fas fa-reply btn btn-light" data-action="reply" title="Ответить"></i>
                      @endif
                    </div>
                    <div class="ml-auto">
                      @if(Auth::check() && (isAuthor($comment['comment']['user_id']) || $is_moderator))
                        <i class="fas fa-times btn btn-light ml-auto{{ !isAuthor($comment['comment']['user_id']) && $is_moderator ? ' red' : '' }}" data-id="{{$comment['comment']['id']}}" data-action="delete" title="Удалить" data-toggle="modal" data-target="#deleteModal"></i>
                      @endif
                    </div>
                    <div>
                      {{printRating($comment['comment']['id'], $comment['comment']['rating'], $comment['comment']['user_id'])}}
                    </div>
                  </div>
                @endif
              </div>
              {{printComments($comment['childs'], $is_moderator)}}
              <div class="comment_box" id="reply{{$comment['comment']['id']}}" hidden>
              </div>
            </div>
            <?php
          }
        }

        if (count($posts) == 0) {
          echo "<p>Комментариев нет</p>";
        } else {
          printComments($posts, $is_moderator);
        }
      ?>
      @if(Auth::check())
        <button id="reply{{$id}}-btn" class="btn btn-light" data-id="{{$id}}" data-action="reply">Прокомментировать пост</button>
        <div id="reply{{$id}}" hidden>
          <form id="write_comment" class="comment" method="post" action="{{route('forum.add-comment')}}">
            @csrf
            <textarea id="comment_area" class="form-control" name="text" placeholder="Введите комментарий" required></textarea>
            <input type="hidden" name="id" id="reply_id" value="{{$id}}" />
            <input type="submit" class="btn btn-light" value="Отправить" />
            <i class="fas fa-times btn btn-light" data-id=0 data-action="close" title="Закрыть"></i>
          </form>
        </div>
      @endif
    @else
      <h3>Пост удалён</h3>
    @endif
  @endif

  @if(Auth::check())
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Удалить комментарий?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="delete_text">
            ...
          </div>
          <div class="modal-footer">
            <form method="post" action="{{route('forum.delete-comment')}}">
              @csrf
              <input type="hidden" id="delete_id" name="id" value="0" />
              <input type="submit" class="btn btn-danger" value="Удалить" />
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<script>
  new Forum(forum);
  $('#comment' + location.hash.substring(1)).addClass('highlight');
</script>
@endsection
