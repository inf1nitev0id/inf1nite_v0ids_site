@extends('layouts.main')

@section('title') Добавить пост @endsection

@section('content')
	<?php
		if ($id != null) {
			$path_str = "<a href=\"".route('forum', $id)."\">".$catalog->name." &gt;</a>";
			foreach ($path as $parent) {
				$path_str = "<a href=\"".route('forum', $parent['id'])."\">".$parent['name']." &gt;</a> ".$path_str;
			}
			$path_str = "<a href=\"".route('forum')."\"> &gt;</a> ".$path_str;
		}
	?>
	<h3>Добавление поста</h3>
	<b><?php echo $path_str ?></b>
	<form method="post" action="{{route('forum.add-post')}}">
		@csrf
		<input type="hidden" name="id" value="{{$id}}" />
		<div class="form-group row">
			<label for="title" class="col-md-2 col-form-label">Заголовок</label>
			<div class="col-md-10">
				<input type="text" class="form-control" name="title" id="title" required />
			</div>
		</div>
		<div class="form-group row">
			<label for="text" class="col-md-2 col-form-label">Текст</label>
			<div class="col-md-10">
				<textarea class="form-control" name="text" id="text" required></textarea>
			</div>
		</div>
		<input type="submit" class="btn btn-light" value="Добавить" />
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
