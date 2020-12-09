@extends('layouts.mahouka')

@section('title') Загрузка рейтинга @endsection

@section('content')
<h5>Проверка данных рейтинга</h5>
<div class="row">
  <div class="col-xs-12 col-md-6">
    <img src="{{ $url }}" width="100%" />
    <p>
      Дата: {{ $date }}<br />
      Время: {{ $time ? 'Вечер' : 'Утро' }}
    </p>
    <form method="post" action="{{ route('mahouka.top.write-rate') }}">
      @csrf
      <input type="hidden" name="date" value="{{ $date }}" />
      <input type="hidden" name="time" value="{{ $time }}" />
      <table>
        <tr>
          <th colspan="2">Готово к записи</th>
        </tr>
        @foreach ($users as $user)
          <tr>
            <td>
              {{ $user['name'] }}
            </td>
            <td>
              <input type="text" readonly name="{{ $user['id'] }}" value="{{ $user['rate'] }}" />
            </td>
          </tr>
        @endforeach
        @if ($users)
          <tr>
            <th colspan="2">
              <input type="submit" value="Записать рейтинг" />
            </th>
          </tr>
        @endif
      </table>
    </form>
  </div>
  <form class="col-xs-12 col-md-6" method="post" action="{{ route('mahouka.top.load-hashes') }}">
    @csrf
    <table>
      <tr>
        <th colspan="2">Неопознанные имена</th>
      </tr>
      @foreach ($unknown_names as $hash => $name)
        <tr>
          <td>
            <svg vievBox="0 0 {{ count($name) }} {{ $char_height }}" width="{{ count($name) / 2 }}" height="{{ $char_height / 2 }}" xmlns="http://www.w3.org/2000/svg">
              @foreach ($name as $x => $line)
                @foreach ($line as $y => $pixel)
                  @if ($pixel)
                    <rect fill="#000" x="{{ $x / 2 }}" y="{{ $y / 2 }}" width="0.5" height="0.5" />
                  @endif
                @endforeach
              @endforeach
            </svg>
          </td>
          <td>
            <select class="form-control" name="name_{{ $hash }}">
              <option value="-1"></option>
              @foreach ($usernames as $user)
                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
              @endforeach
            </select>
          </td>
        </tr>
      @endforeach
      <tr>
        <th colspan="2">Неопознанные цифры</th>
      </tr>
      @foreach ($unknown_numbers as $hash => $number)
        <tr>
          <td>
            <svg vievBox="0 0 {{ count($number) }} {{ $char_height }}" width="{{ count($number) }}" height="{{ $char_height }}" xmlns="http://www.w3.org/2000/svg">
              @foreach ($number as $x => $line)
                @foreach ($line as $y => $pixel)
                  @if ($pixel)
                    <rect fill="#000" x="{{ $x }}" y="{{ $y }}" width="1" height="1" />
                  @endif
                @endforeach
              @endforeach
            </svg>
          </td>
          <td>
            <select class="form-control" name="number_{{ $hash }}">
              <option value="-1"></option>
              @for ($i = 0; $i < 10; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </td>
        </tr>
      @endforeach
      <tr>
        <th colspan="2">
          <input type="submit" value="Записать значения" />
        </th>
      </tr>
    </table>
  </form>
</div>
@endsection
