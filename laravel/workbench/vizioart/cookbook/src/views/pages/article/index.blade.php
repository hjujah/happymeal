<div id="app-container" class="wrap" ng-controller="articlesController">


	<div class="sub-header">

		<div class="sub-header-left">
			
		</div>
		<div class="sub-header-main">

			<div class="pull-left">
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">[% (activeLanguage.code | uppercase) || "TT" %] <span class="caret"></span></button>
					<ul class="dropdown-menu" role="menu">
						<li ng-repeat="lang in languages">
							<a href="#" class="" ng-click="setActiveLanguage(lang)" >[%lang.description%]</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="view-nav">
				<ul class="nav nav-line">
					<li class="active"><a href="#">All</a></li>
					<li><a href="#">Published</a></li>
					<li><a href="#">Draft</a></li>
					<li><a href="#">Trash</a></li>
				</ul>
			</div>



		</div>

	</div>


	
	<div class="app-main" >

		<div class="container-fluid">
			<div class="row">
			
				<div class="col-lg-8 col-md-10">

					<div class="article-listing">
						<div article-item class="article-item" ng-repeat="article in model.articles" item="article" language="activeLanguage"></div>
					</div>

				</div>

			</div>
		</div>

	</div>


	<div class="app-sidebar">
		
		<div class="main-action ps pt">

			<div class="btn-group btn-lang-options">
				<a href="{{ url('admin/articles/add') }}" class="btn-action btn btn-primary"><span class="glyphicon glyphicon-plus"></span> New Article</a>
				<span class="btn-options btn btn-primary dropdown-toggle" data-toggle="dropdown">
					<span>EN</span>
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</span>
				<ul class="dropdown-menu" role="menu">
					<li><a href="{{ url('admin/articles/add/en') }}">English</a></li>
					<li><a href="{{ url('admin/articles/add/cs') }}">Czech</a></li>
					<li><a href="{{ url('admin/articles/add/ru') }}">Russian</a></li>
					<li class="divider"></li>
					<p class="tip">Make a group of buttons stretch at equal sizes to span the entire</p>
				</ul>
			</div>


		</div>

	</div>
</div>