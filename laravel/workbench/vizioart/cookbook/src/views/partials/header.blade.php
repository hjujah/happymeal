<header id="masthead" class="navbar navbar-fixed-top" role="banner">
	<div class="container-fluid">
		
		<div class="navbar-header">
			<a href="/admin" class="navbar-brand">CookBook</a>
		</div>

		<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
			
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ url('admin/pages') }}">All Pages</a></li>
						<li><a href="{{ url('admin/pages/add') }}">New Page</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Articles <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ url('admin/articles') }}">All Articles</a></li>
						<li><a href="{{ url('admin/article/new') }}">New Article</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Galleries <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ url('admin/galleries') }}">All Galleries</a></li>
						<li><a href="{{ url('admin/galleries/new') }}">New Gallery</a></li>
					</ul>
				</li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Account <b class="caret"></b></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{{ URL::to('account/logout') }}}">Log out</a></li>
					</ul>
				</li>
			</ul>

		</nav>


	</div>
</header>