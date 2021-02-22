@extends('layouts.mahouka')

@section('title') Статистика сервера @endsection

@section('content')
<h5>Статистика сервера <a href="{{ route('mahouka.top.chart') }}">График</a></h5>
<div class="overflow">
	<table class="container-fluid rating">
		<tr>
			<th colspan=2></th>
			@foreach ($users as $user)
				<th nowrap>
					{{ $user['name'] }}
					@if ($user['alias'] != null)
						<br />
						{{ $user['alias'] }}
					@endif
				</th>
			@endforeach
		</tr>
		@for ($day = 0, $date = clone($min_date), $days = count($rating); $day < $days; $day++, $date->add($step))
			<tr>
				<?php
					$line1 = "";
					$line2 = "";
					foreach ($rating[$day] as $line) {
						$d = $date->format('Y-m-d');
						$line1 .= "<td>".($line[0] ?? '')."</td>";
						$line2 .= "<td>".($line[1] ?? '')."</td>";
					}
				?>
				<th rowspan=2>{{ $date->format('Y-m-d') }}</th>
				<th>Утро</th>
			@foreach ($rating[$day][0] as $rate)
				<td>
					{{ $rate }}
				</td>
			@endforeach
			</tr>
			<tr>
				<th>Вечер</th>
			@foreach ($rating[$day][1] as $rate)
				<td>
					{{ $rate }}
				</td>
			@endforeach
			</tr>
		@endfor
	</table>
</div>
@if (Auth::check() && Auth::user()->role === 'admin')
	<a class="btn btn-outline-secondary" href="{{ route('mahouka.top.edit') }}">Внести данные</a>
@endif
@endsection
