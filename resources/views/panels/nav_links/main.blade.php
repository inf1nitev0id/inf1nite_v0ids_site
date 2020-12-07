@extends('panels.navbar')

@section('links')
<li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Главная</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('about') }}">О сайте</a></li>
@endsection
