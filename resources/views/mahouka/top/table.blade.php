@extends('layouts.mahouka')

@section('title') Статистика сервера @endsection

@section('content')
<h5>Статистика сервера <a href="{{ route('mahouka.top.chart') }}">График</a></h5>
<div class="overflow">
	<table class="container-fluid rating">
		<tr>
			<th colspan=2></th>
			@foreach($sorted_users as $user)
				<th nowrap>
					{{ $user['name'] }}
					@if ($user['alias'] != null)
						<br />
						{{ $user['alias'] }}
					@endif
				</th>
			@endforeach
		</tr>
		@for ($date = clone($min_date); $date <= $max_date; $date->add($step))
			<tr>
				<?php
					$line1 = "";
					$line2 = "";
					foreach ($sorted_users as $user) {
						$d = $date->format('Y-m-d');
						$line1 .= "<td>".($rating_table[$user['id']][$d][0] ?? '')."</td>";
						$line2 .= "<td>".($rating_table[$user['id']][$d][1] ?? '')."</td>";
					}
				?>
				<th rowspan=2>{{ $date->format('Y-m-d') }}</th>
				<th>Утро</th>
				<?php echo $line1; ?>
			</tr>
			<tr>
				<th>Вечер</th>
				<?php echo $line2; ?>
			</tr>
		@endfor
	</table>
</div>
@if (Auth::check() && Auth::user()->role === 'admin')
	<a class="btn btn-outline-secondary" href="{{ route('mahouka.top.load') }}">Внести данные</a>
@endif
@endsection
