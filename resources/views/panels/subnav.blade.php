@section('subnav')
<nav class="navbar navbar-expand-md navbar-light p-0">
  <button class="navbar-toggler mx-auto btn btn-outline-secondary btn-block" type="button" data-toggle="collapse" data-target="#sidebarNav" aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fas fa-caret-down"></i>
  </button>
  <div class="collapse navbar-collapse pt-2" id="sidebarNav">
    <div class="flex-fill mt-xm-2 mt-md-0">
      @if (Auth::check())
        <div class="btn-group btn-block" role="group" aria-label="Управление аккаунтом">
          <a class="btn btn-outline-dark disabled">{{ Auth::user()->name }}</a>
          <a class="btn btn-outline-dark" href="{{ route('logout') }}">Выйти</a>
        </div>
      @else
        <div class="btn-group btn-block" role="group" aria-label="Вход/Регистрация">
          @if (Request::route()->getName() !== "login")
            <a class="btn btn-outline-dark" href="{{ route('login') }}">Вход</a>
          @endif
          @if (Request::route()->getName() !== "reg")
            <a class="btn btn-outline-dark" href="{{ route('reg') }}">Регистрация</a>
          @endif
        </div>
      @endif
    </div>
  </div>
</nav>
