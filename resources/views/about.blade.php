@extends('layouts.main')

@section('title') О сайте @endsection

@section('content')
    <h5>О сайте</h5>
    <p>
        Данный сайт я написал для ознакомления с технологиями для создания веб-сайтов.
        Для создания сайта в том виде, в каком он есть сейчас, были использованы:
    </p>
    <ul>
        <li><i class="fab fa-html5"></i> HTML <small class="text-muted">я знаю, это неожиданно</small></li>
        <li><i class="fab fa-css3"></i> CSS <small class="text-muted">ещё неожиданнее</small></li>
        <li><i class="fab fa-sass"></i> SASS</li>
        <li><i class="fab fa-bootstrap"></i> Bootstrap</li>
        <li><i class="fab fa-php"></i> PHP <small class="text-muted">тоже тот ещё поворот, да?</small></li>
        <li><i class="fas fa-database"></i> MySQL</li>
        <li><i class="fab fa-laravel"></i> Laravel</li>
        <li><i class="fab fa-js"></i> JavaScript</li>
        <li><i class="fab fa-js"></i> JQuery</li>
        <li><i class="fab fa-vuejs"></i> Vue.js</li>
    </ul>
    @if($contacts)
        <h5>Контакты</h5>
        <ul>
            @foreach($contacts as $contact)
                <li>
                    <i class="
                        @switch($contact->type)
                            @case('discord')
                                fab fa-discord
                                @break
                            @default
                                fas fa-envelope
                            @endswitch
                    "></i>
                    @if($contact->link)
                        <a href="{{$contact->link}}">{{$contact->content}}</a>
                    @else
                        {{$contact->content}}
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection
