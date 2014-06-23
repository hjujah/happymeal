<div id="app-container" class="wrap" ng-controller="galleryController">
	
	<div class="app-main" >
		<div class="container-fluid" >

			<div class="page-header" >
			    <h2>{{$page_title}}</h2>
			</div>

			<div class="">
				<div class="row">

					<div class="col-md-10 col-sm-8">

						<div id="titlediv">
							<div class="form-group">
								<input type="text" class="form-control" id="title" placeholder="Gallery title" ng-model="Gallery.form.name">
							</div>
						</div>

						<div class="">
							<span class="btn btn-default" ng-click="openMediaUploader()">Upload Images</span>
						</div>


						<div mediauploader show='mediaUploaderShown' model="Gallery"></div>

						<div class="gallery-items-list-wrap">
							<div class="row">
								<div class="col-md-2" ng-repeat="item in Gallery.form.items">

									<div class="item-wrap">
										<img ng-src="[%item.url%]" width="100%"/>
										<div class="item-actions">
											<span class="btn btn-default btn-sm pull-right" ng-click="Gallery.deleteItem(item.id)">delete</span>
										</div>
									</div>
								</div>
							</div>
						</div>


					</div><!-- .col-sm-7 -->

				</div>
			</div>

		</div>
	</div>


	<div class="app-sidebar">

	</div>

</div>