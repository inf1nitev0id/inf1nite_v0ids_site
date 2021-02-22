@extends('layouts.mahouka')

@section('title') Данные рейтинга @endsection

@section('head')
<script>
	var min_date = new Date('{{ $min_date }}')
	var users = @json($users)

	var rating = @json($rating)

</script>
<script src="/js/vue.js"></script>
<script src="/js/edit_rate.js"></script>
@endsection

@section('content')
<h5>Изменение данных рейтинга</h5>
<div id="edit_form" class="row">
	<div class="col-xs-12 col-md-6">
		<form method="post" action="{{ route('mahouka.top.scan') }}">
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
				<input class="form-check-input" type="radio" name="time" id="time-radio0-input" value=0 />
				<label class="form-check-label" for="time-radio0-input">
					Утро
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="time" id="time-radio1-input" value=1 />
				<label class="form-check-label" for="time-radio1-input">
					Вечер
				</label>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Загрузить" />
			</div>
		</form>
	</div>
	<div class="col-xs-12 col-md-6">
		<table>
			<tr v-for="(user, index) in users">
				<td>
					@{{ user.name }}
				</td>
			</tr>
		</table>
	</div>
</div>
@endsection
