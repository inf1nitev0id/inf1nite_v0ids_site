@extends('panels.navbar')

@section('links')
<li class="nav-item"><a class="nav-link" href="{{ route('home-page') }}">Главная</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('about-page') }}">О сайте</a></li>
@endsection
