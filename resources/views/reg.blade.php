@extends('layouts.main')

@section('title') Регистрация @endsection

@section('head')
<script src="/js/reg.js"></script>
@endsection

@section('content')
@if($errors->any())
  <div class="alert alert-danger mb-2 alert-dismissible">
    <ul>
      @foreach($errors->all() as $error)
          <li>{{$error}}</li>
      @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif
<form id="reg" @submit="checkForm" class="form-login" method="POST" action="{{ route('register') }}">
  <div v-if="errors.length" class="alert alert-danger mb-2">
    <ul>
      <li v-for="error in errors">@{{ error }}</li>
    </ul>
  </div>
  @CSRF
  <div class="form-group">
    <label for="login-input">Имя пользователя</label>
    <input id="login-input" type="text" class="form-control" name="login" v-model="login" placeholder="Введите имя пользователя" required />
  </div>
  <div class="form-group">
    <label for="email-input">Почта</label>
    <input id="email-input" type="email" class="form-control" name="email" placeholder="Введите почту" required />
  </div>
  <div class="form-group">
    <label for="password-input">Пароль</label>
    <input id="password-input" type="password" class="form-control" name="password" v-model="password" placeholder="Введите пароль" required />
  </div>
  <div class="form-group">
    <input id="password-repeat-input" type="password" class="form-control" name="password-repeat" v-model="password_repeat" placeholder="Повторите пароль" required />
  </div>
  <div class="form-group">
    <label for="invite-input">Код приглашения</label>
    <input id="invite-input" type="text" class="form-control" name="invite" v-model="invite" placeholder="Введите код" required />
  </div>
  <div class="form-group">
    <input class="btn btn-dark" type="submit" value="Зарегистрироваться" />
  </div>
</form>
@endsection
