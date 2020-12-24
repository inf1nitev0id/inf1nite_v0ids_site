@extends('layouts.mahouka')

@section('title') Статистика сервера @endsection

@section('head')
<link rel="stylesheet" href="/css/chart.css" />
<script>
  var dates = [
  @foreach ($dates as $date)
    new Date('{{ $date }}'),
  @endforeach
  ]
  var lines = [
  @foreach ($lines as $key => $line)
    {
			index: {{ $key }},
      user: {
        id: {{ $line['user']['id'] }},
        name: '{{ $line['user']['name'] }}',
        alias: '{{ $line['user']['alias'] }}'
      },
      rating: [@foreach ($line['rating'] as $rate){{ $rate ?? 'null' }}, @endforeach],
      max: 0,
      color: '{{ $line['color'] }}',
      visible: true,
    },
  @endforeach
  ]
	var events = [
	@foreach ($events as $event)
		{
			description: '{{ ($event['series'] !== null ? $event['series'].": " : "").$event['name'] }}',
			date: new Date('{{ $event['date'] }}'),
			type: '{{ $event['type'] }}',
			important: {{ $event['important'] ? 'true' : 'false' }},
			color: '#{{ $event['color'] }}',
		},
	@endforeach
	]
</script>
<script src="/js/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.20/lodash.min.js"></script>
<script src="/js/chart.js"></script>
@endsection

@section('content')
<h5>Статистика сервера <a href="{{ route('mahouka.top.table') }}">Таблица</a></h5>
<div id="chart">
  <p v-if="false">Для работы этой страницы необходим JS, если вы видете эту надпись, значит он не работает в вашем браузере.</p>
  <div v-cloak style="position: relative;">
    <div v-for="y in horizontalDivisions" class="axis-text" :style="{position: 'absolute', top: y.y + 5 + 'px', left: '5px'}">
    	@{{ y.value }}
    </div>
    <div class="overflow">
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
						<template v-for="day in eventsDays">
							<template v-for="(event, index) in day.events">
								<text @click="showEvent(event.id)" class="event" :class="{important: event.important}" :fill="event.color" :x="day.x" :y="11 + index * 10" text-anchor="middle">
									@{{ event.type }}
								</text>
								<line class="event" :class="{important: event.important}" :stroke="event.color" :x1="day.x" :y1="day.events.length * 10 + 4" :x2="day.x" :y2="sizeY" />
							</template>
						</template>
					</g>
	        <g class="chart-grid">
						<line v-for="x in verticalDivisions" :x1="x.x" y1="0" :x2="x.x" :y2="x.y" />
						<line v-for="y in horizontalDivisions" x1="0" :y1="y.y" :x2="sizeX" :y2="y.y" />
						<line x1="0" :y1="sizeY" :x2="sizeX" :y2="sizeY" />
						<line x1="0" :y1="sizeY + 15" :x2="sizeX" :y2="sizeY + 15" />
						<line x1="0" :y1="sizeY + 30" :x2="sizeX" :y2="sizeY + 30" />
						<text v-for="(date, index) in dates" class="axis-text" :x="index * day_width + day_width / 2" :y="sizeY + 13" text-anchor="middle">
							@{{ date.getDate() }}
						</text>
						<text v-for="month in months" class="axis-text" :x="month.x" :y="sizeY + 28" text-anchor="middle">
							@{{ month.text }}
						</text>
	        </g>
					<g :transform="'translate(0,' + sizeY + ')'">
	        	<polyline v-for="line in notSelectedLines" class="line" v-show="line.visible" @click.left="setSelected(line.user.id)" @click.right.prevent="line.visible = false" :stroke="line.color" :points="points[line.index]" />
					</g>
					<g v-if="selected != 0 && selectedLine.visible" :transform="'translate(0,' + sizeY + ')'">
						<polyline class="line selected" @click.left="setSelected(selectedLine.user.id)" @click.right.prevent="selectedLine.visible = false" :stroke="selectedLine.color" :points="points[selectedLine.index]" />
						<template v-for="point in chart[selectedLine.index].filter(function(point) { return point.rate !== null })">
							<text v-for="style in ['rate-text-border', 'rate-text']" :class="style" :x="isUp(point.y, point.rate) ? -5 : 5" y="2" :text-anchor="isUp(point.y, point.rate) ? 'end' : 'start'" :transform="'translate(' + point.x + ',' + point.y + ') rotate(90)'">
								@{{ point.rate }}
							</text>
						</template>
					</g>
				</g>
      </svg>
    </div>
  </div>
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
  <div v-cloak>
    <div v-for="line in lines" class="btn-group m-1">
      <div class="btn" :class="'btn-outline-' + (selected == line.user.id ? 'secondary' : 'light')" :style="{color: line.color}" @click="setSelected(line.user.id)">
        @{{ line.user.name }}
      </div>
      <div class="btn" :class="'btn-outline-' + (selected == line.user.id ? 'secondary' : 'light')" :style="{color: line.color}" @click="line.visible = !line.visible">
        <i v-if="line.visible" class="far fa-check-square"></i>
        <i v-else class="far fa-square"></i>
      </div>
    </div>
  </div>
</div>
@endsection
