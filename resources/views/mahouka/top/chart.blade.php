@extends('layouts.mahouka')

@section('title') Статистика сервера @endsection

@section('head')
<link rel="stylesheet" href="/css/chart.css" />
<script>
	var min_date = new Date('{{ $min_date }}')
	var max_date = new Date('{{ $max_date }}')
	var lines = @json($lines)

	var events = @json($events)

	var series = @json($series)

</script>
<script src="/js/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.20/lodash.min.js"></script>
<script src="/js/chart.js"></script>
@endsection

@section('content')
<h5 id="page-title">Статистика сервера <a href="{{ route('mahouka.top.table') }}">Таблица</a></h5>
<div id="chart">
	<p v-if="false">Для работы этой страницы необходим JS, если вы видете эту надпись, значит он не работает в вашем браузере.</p>
	<div v-cloak style="position: relative;">
{{-- подписи шкала рейтинга --}}
		<div v-for="y in horizontalDivisions" class="axis-text" :style="{position: 'absolute', top: y.y + 5 + 'px', left: '5px'}">
			@{{ y.value }}
		</div>
		<div class="axis-text" :style="{position: 'absolute', top: sizeY - 14 + 'px', left: '5px'}">
			@{{ bottom }}
		</div>
		<div class="overflow">
{{--
	начало SVG
--}}
			<svg
				version="1.1"
				baseProfile="full"
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				xmlns:ev="http://www.w3.org/2001/xml-events"
				xmlns="http://www.w3.org/2000/svg"
				:vievBox="'0 0 ' + sizeX + ' ' + sizeY + 31 + 5"
				:width="sizeX + 'px'"
				:height="sizeY + 31 + 5 + 'px'"
				@click.self="selected = 0"
				>
				<g transform="translate(0, 5)">
					<g>
{{-- события --}}
						<template v-for="day in eventsDays">
							<template v-for="(event, index) in day.events">
								<text @click="selected_event = event; selected_event.date = day.date" class="event" :class="{important: event.important}" :fill="event.color" :x="day.x" :y="11 + index * 10" text-anchor="middle">
									@{{ event.type }}
								</text>
								<line class="event" :class="{important: event.important}" :stroke="event.color" :x1="day.x" :y1="day.events.length * 10 + 4" :x2="day.x" :y2="sizeY" />
							</template>
						</template>
					</g>
					<g class="chart-grid">
{{-- сетка --}}
						<line v-for="x in verticalDivisions" :x1="x.x" y1="0" :x2="x.x" :y2="x.y" />
						<line v-for="y in horizontalDivisions" x1="0" :y1="y.y" :x2="sizeX" :y2="y.y" />
						<line x1="0" :y1="sizeY" :x2="sizeX" :y2="sizeY" />
						<line x1="0" :y1="sizeY + 15" :x2="sizeX" :y2="sizeY + 15" />
						<line x1="0" :y1="sizeY + 30" :x2="sizeX" :y2="sizeY + 30" />
						<text v-for="(date, index) in dates" class="axis-text" :x="index * dayWidth + dayWidth / 2" :y="sizeY + 13" text-anchor="middle">
							@{{ date }}
						</text>
						<text v-for="month in months" class="axis-text" :x="month.x" :y="sizeY + 28" text-anchor="middle">
							@{{ month.text }}
						</text>
					</g>
					<g :transform="'translate(0,' + sizeY + ')'">
{{-- графики --}}
						<polyline v-for="line in lines" class="line" v-show="line.visible" @click.left="setSelected(line.user.id)" @click.right.prevent="line.visible = false" :stroke="line.color" :points="points[line.index]" />
					</g>
					<g v-if="selected != 0 && selectedLine.visible" :transform="'translate(0,' + sizeY + ')'">
{{-- выбранный график --}}
						<polyline class="line selected" @click.left="setSelected(selectedLine.user.id)" @click.right.prevent="selectedLine.visible = false" :stroke="selectedLine.color" :points="points[selectedLine.index]" />
						<template v-for="point in chart[selectedLine.index].filter(function(point) { return point.rate !== null })">
							<text v-for="style in ['rate-text-border', 'rate-text']" :class="style" :x="isUp(point.y, point.rate) ? -5 : 5" y="2" :text-anchor="isUp(point.y, point.rate) ? 'end' : 'start'" :transform="'translate(' + point.x + ',' + point.y + ') rotate(90)'">
								@{{ point.rate }}
							</text>
						</template>
					</g>
				</g>
			</svg>
{{--
	конец SVG
--}}
		</div>
	</div>
{{-- общие кнопки управления графиком --}}
	<div v-cloak class="form-inline">
		<div class="btn-group m-1">
			<div class="btn btn-outline-secondary" title="Показать всё" @click="showAll()">
				<i class="far fa-eye"></i>
			</div>
			<div class="btn btn-outline-secondary" title="Инвертировать" @click="invert()">
				<i class="fas fa-exclamation"></i>
			</div>
			<div class="btn btn-outline-secondary" title="Скрыть всё" @click="hideAll()">
				<i class="far fa-eye-slash"></i>
			</div>
		</div>
	</div>
{{-- подробные настройки --}}
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#users">Пользователи</a>
		</li>
		<li class="nav-item">
			<a class="nav-link"	data-toggle="tab" href="#events">События</a>
		</li>
		<li class="nav-item">
			<a class="nav-link"	data-toggle="tab" href="#range">Диапазон</a>
		</li>
	</ul>
	<div v-cloak class="tab-content">
		<div class="tab-pane fade show active" id="users">
{{-- фильтр пользователей --}}
			<div v-for="line in lines" class="btn-group m-1">
				<div class="btn option-btn" :class="'btn-outline-' + (selected == line.user.id ? 'secondary' : 'light')" :style="{color: line.color}" @click="setSelected(line.user.id)">
					@{{ line.user.name }}
				</div>
				<div class="btn" :class="'btn-outline-' + (selected == line.user.id ? 'secondary' : 'light')" :style="{color: line.color}" @click="line.visible = !line.visible">
					<i v-if="line.visible" class="far fa-check-square"></i>
					<i v-else class="far fa-square"></i>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="events">
{{-- фильтр событий --}}
			<p class="mt-1 mb-1">Серии:</p>
			<div v-for="serie in series" class="option" :style="{color: serie.color}" @click="serie.visible = !serie.visible">
				<i v-if="serie.visible" class="far fa-check-square"></i>
				<i v-else class="far fa-square"></i>
				@{{ serie.name }}
			</div>
			<p class="mt-2 mb-1">Категории:</p>
			<div v-for="type in types" class="option" :style="{color: type.color}" @click="type.visible = !type.visible">
				<i v-if="type.visible" class="far fa-check-square"></i>
				<i v-else class="far fa-square"></i>
				@{{ type.name }}
			</div>
			<div class="option mt-2" @click="important_only = !important_only">
				<i v-if="important_only" class="far fa-check-square"></i>
				<i v-else class="far fa-square"></i>
				Только важные
			</div>
		</div>
		<div class="tab-pane fade form-inline pt-2" id="range">
{{-- фильтр диапазона --}}
			от
			<select v-model="start_indent" class="form-control">
				<option v-for="date in dates_full.filter(item => item.id < days_full - end_indent)" :value="date.id">@{{ date.full }}</option>
			</select>
			до
			<select v-model="end_indent" class="form-control">
				<option v-for="date in dates_full.filter(item => item.id >= start_indent)" :value="days_full - date.id - 1">@{{ date.full }}</option>
			</select>
			<div class="btn btn-outline-secondary" @click="start_indent = 0; end_indent = 0">Сброс</div>
		</div>
	</div>
{{-- модальное окно для вывода информации о выбранном событии --}}
	<div v-cloak v-if="selected_event !== null">
		<div class="modal-substrate" @click="selected_event = null"></div>
		<div class="modal-window card">
			<p class="card-header">
				@{{ dateToString(selected_event.date) }}
			</p>
			<div class="card-body" v-html="selected_event.name"></div>
		</div>
	</div>
</div>
@endsection
