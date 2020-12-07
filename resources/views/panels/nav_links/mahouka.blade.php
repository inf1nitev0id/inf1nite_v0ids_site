@extends('panels.navbar')

@section('links')
<ul class="navbar-nav">
  <li class="nav-item"><a class="nav-link" href="{{ route('mahouka.home') }}">Главная</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('mahouka.top') }}">Рейтинг сервера</a></li>
</ul>
@if (Auth::check())
  <div class="btn-group ml-auto" role="group" aria-label="Управление аккаунтом">
    <a class="btn btn-light disabled">{{ Auth::user()->name }}</a>
    <a class="btn btn-outline-light" href="{{ route('logout') }}">Выйти</a>
  </div>
@else
  <div class="btn-group ml-auto" role="group" aria-label="Вход/Регистрация">
    @if (Request::route()->getName() !== "login")
      <a class="btn btn-outline-light" href="{{ route('mahouka.login') }}">Вход</a>
    @endif
    @if (Request::route()->getName() !== "reg")
      <a class="btn btn-outline-light" href="{{ route('mahouka.reg') }}">Регистрация</a>
    @endif
  </div>
@endif
@endsection
