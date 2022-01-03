@section('subnav')
    <?php
        $menu = \App\Http\Controllers\Controller::getSubmenu();
    ?>
@if($menu)
    <nav class="navbar navbar-expand-md navbar-light p-0">
        <button class="navbar-toggler mx-auto btn btn-outline-secondary btn-block" type="button" data-toggle="collapse"
                data-target="#sidebarNav" aria-controls="sidebarNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <i class="fas fa-caret-down"></i>
        </button>
        <div class="collapse navbar-collapse pt-2" id="sidebarNav">
        @foreach(\App\Http\Controllers\Controller::getSubmenu() as $item)
            <div class="flex-fill mt-xm-2 mt-md-0">
                <a class="btn btn-outline-secondary btn-block" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
            </div>
        @endforeach
        </div>
    </nav>
@endif
