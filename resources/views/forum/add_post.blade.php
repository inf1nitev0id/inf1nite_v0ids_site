@extends('layouts.main')
<?php
/**
 * @var int              $id
 * @var string           $path
 * @var \App\Models\Post $catalog
 */
?>

@section('title') Добавить пост @endsection

@section('head')
    <script src="/js/vue.js"></script>
    <script src="/js/add_post.js"></script>
@endsection

@section('content')
    <h3>Добавление поста</h3>
    <b>{!!$path!!}</b>
    <form id="addPost" method="post" action="{{route('forum.add-post')}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$id}}"/>
        <div class="form-group row">
            <label for="title" class="col-md-2 col-form-label">Заголовок</label>
            <div class="col-md-10">
                <input type="text" class="form-control" name="title" id="title" required/>
            </div>
        </div>
        <div class="form-group row">
            <label for="text" class="col-md-2 col-form-label">Текст</label>
            <div class="col-md-10">
                <textarea class="form-control" name="text" id="text" required></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <file-input name="attachments" can-change-name="true" accept="image/*"></file-input>
            </div>
        </div>
        <input type="submit" class="btn btn-light" value="Добавить"/>
    </form>
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
@endsection
