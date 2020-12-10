@extends('layouts.mahouka')

@section('title') Статистика сервера @endsection

@section('head')
<link rel="stylesheet" href="/css/chart.css" />
<script src="/js/vue.js"></script>
<script src="/js/chart.js"></script>
@endsection

@section('content')
<h5>Статистика сервера <a href="{{ route('mahouka.top.table') }}">Таблица</a></h5>
<div class="overflow">
  <svg
    version="1.1"
    baseProfile="full"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:ev="http://www.w3.org/2001/xml-events"
    xmlns="http://www.w3.org/2000/svg"
    vievBox="0 0 {{ $x_size + 100 }} {{ $y_size + 100 }}"
    width="{{ $x_size + 100 }}"
    height="{{ $y_size + 100 }}"
    >
    <g transform="translate(0, 0)">
      @foreach ($dates as $i => $date)
        @if ($i > 0)
          <line x1="{{ $i * 20 }}" y1="0" x2="{{ $i * 20 }}" y2="{{ $y_size }}" fill="none" stroke-width="1" stroke="#DDD" />
        @endif
        <text x="10" text-anchor="start" transform="translate({{ $i * 20 + 5 }}, {{ $y_size }}) rotate(90)">{{ $date }}</text>
      @endforeach
      @foreach ($lines as $line)
        <g class="line">
          <polyline fill="none" stroke="{{ $line['color'] }}" points="
            @foreach ($line['points'] as $point)
              {{ $point['x'] }},{{ $point['y'] }}
            @endforeach
          " />
          @foreach ($line['points'] as $point)
          <text class="small" x="-10" text-anchor="end" transform="translate({{ $point['x'] - 2.5 }}, {{ $point['y'] }}) rotate(90)">
            {{ $point['rate'] }}
          </text>
          @endforeach
          <text x="{{ end($line['points'])['x'] + 5 }}" y="{{ end($line['points'])['y'] }}" text-anchor="start">
            {{ $line['user']['alias'] ?? $line['user']['name'] }}
          </text>
        </g>
      @endforeach
    </g>
  </svg>
</div>
<div>
  @foreach ($lines as $line)
    <span class="btn btn-outline-light" style="color: {{ $line['color'] }};">
      {{ $line['user']['name'].($line['user']['alias'] != null ? " (".$line['user']['alias'].")" : "") }}
    </span>
  @endforeach
</div>
@endsection
