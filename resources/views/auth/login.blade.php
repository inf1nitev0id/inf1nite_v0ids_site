@extends('layouts.empty')

@section('title') Авторизация @endsection

@section('content')
    <form class="form-login" method="POST" action="{{ route('auth') }}">
        @CSRF
        <input type="hidden" name="from" value="{{ $from }}"/>
        <div class="form-group">
            <label for="login-input">Имя пользователя или почта</label>
            <input id="login-input" type="text" class="form-control" name="login"
                   placeholder="Введите имя пользователя или почту" required/>
        </div>
        <div class="form-group">
            <label for="password-input">Пароль</label>
            <input id="password-input" type="password" class="form-control" name="password" placeholder="Введите пароль"
                   required/>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" id="rememberme-input" type="checkbox" class="form-control"
                       name="rememberme"/>
                <label class="form-check-label" for="rememberme-input">Запомнить меня</label>
            </div>
        </div>
        <div class="form-group">
            <div class="btn-group btn-block ml-auto" role="group" aria-label="Вход/Регистрация">
                <input class="btn btn-dark" type="submit" value="Войти"/>
                <a class="btn btn-outline-dark" href="{{ route(($from !== 'main' ? $from.'.' : '').'reg') }}">Регистрация</a>
            </div>
        </div>
        @if (session('authError'))
            <div class="alert alert-danger mb-2 alert-dismissible">
                {{ session('authError') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </form>
@endsection
