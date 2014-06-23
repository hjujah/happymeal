<div id="app-container" class="wrap" ng-controller="postController">

	<div class="sub-header">

		<div class="sub-header-left">
			
		</div>
		<div class="sub-header-main">


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



	<div class="app-main">



		<div class="container-fluid">
			<div class="row">
			
				<div class="col-lg-10 col-md-12">
					
					<div class="panel panel-default">
						<div class="panel-heading">Panel heading without title</div>
						<div class="panel-body">
							<div class="dt-view-nav">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#">All <span class="badge view-item-count">42</span></a></li>
									<li><a href="#">Published <span class="badge view-item-count">40</span></a></li>
									<li><a href="#">Draft <span class="badge view-item-count">2</span></a></li>
								</ul>
							</div>
							<table cellpadding="0" cellspacing="0" border="0" id="cb_table_pages" class="datatable table table-striped table-hover"></table>

						</div>
					</div>


				</div>
			</div>
		</div>

	</div>


	<div class="app-sidebar">
		
		<div class="main-action ps pt">

			<div class="btn-group btn-lang-options">
				<a href="{{ url('admin/pages/add') }}" class="btn-action btn btn-primary"><span class="glyphicon glyphicon-plus"></span> New Page</a>
				<span class="btn-options btn btn-primary dropdown-toggle" data-toggle="dropdown">
					<span>EN</span>
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</span>
				<ul class="dropdown-menu" role="menu">
					<li><a href="{{ url('admin/pages/add/en') }}">English</a></li>
					<li><a href="{{ url('admin/pages/add/cs') }}">Czech</a></li>
					<li><a href="{{ url('admin/pages/add/ru') }}">Russian</a></li>
					<li class="divider"></li>
					<p class="tip">Make a group of buttons stretch at equal sizes to span the entire</p>
				</ul>
			</div>


		</div>

	</div>
</div>