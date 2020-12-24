<nav class="navbar navbar-expand-md navbar-dark bg-dark">
	<a class="navbar-brand" href="{{ route('home') }}"><img src="/logo.png" class="logo" /> inf1nite_v0id</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarNav">
		@yield('links')
	</div>
</nav>
