@section('footer')
    <footer class="container-fluid mt-3 py-2 border-top">
        <div class="row">
            <div class="col-12 col-md">
                <small class="text-muted">© 2020-{{ (new DateTime())->format('Y') }}</small>
            </div>
        </div>
    </footer>
@endsection