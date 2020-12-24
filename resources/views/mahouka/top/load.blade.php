@extends('layouts.mahouka')

@section('title') Загрузка рейтинга @endsection

@section('content')
<h5>Ввод данных рейтинга</h5>
<form method="post" action="{{ route('mahouka.top.preload') }}">
	@csrf
	<div class="form-group">
		<label for="url-input">Ссылка на картинку</label>
		<input type="url" class="form-control" id="url-input" name="url" placeholder="URL" required />
	</div>
	<div class="form-group">
		<label for="date-input">Дата</label>
		<input type="date" class="form-control" id="date-input" name="date" value="{{ $last_date }}" required />
	</div>
	<div class="form-check">
		<input class="form-check-input" type="radio" name="time" id="time-radio0-input" value=0 {{ !$last_time ? "checked" : "" }} />
		<label class="form-check-label" for="time-radio0-input">
			Утро
		</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="radio" name="time" id="time-radio1-input" value=1 {{ $last_time ? "checked" : "" }} />
		<label class="form-check-label" for="time-radio1-input">
			Вечер
		</label>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-primary" value="Загрузить" />
	</div>
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
