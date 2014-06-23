define([
	'jquery', 
	'underscore', 
	'angular',
	'app/gallery/module'
], function ($, _, angular, Module) {


	Module.directive('gallerymanager', ['$timeout', '$q', function ($timeout, $q) {
		return {
		    restrict: 'A',
		    scope: {
		    	Gallery: '=model'
		    },
		    replace: true, // Replace with the template below
		    link: function(scope, element, attrs) {
		    	
		    },
		    controller: ['$scope', function($scope){
		    	$scope.uploader = false;

		        $scope.mediaUploaderShown = false;
				$scope.openMediaUploader = function() {
					$scope.mediaUploaderShown = true;
				};

				$scope.uploaderDone = function(images){
					var defered = $q.defer();
					if(images.length > 0){
						if(!$scope.Gallery.inited){
							$scope.Gallery.create().then(function(){
								$scope.$emit('gallerymanager.galleryCreated', {gallery: $scope.Gallery});
								$scope.Gallery.addImages(images).then(function(){
									defered.resolve();
								}, function(){
									defered.reject();
								});
							}, function(){
								defered.reject();
							});
						}else{
							$scope.Gallery.addImages(images).then(function(){
								defered.resolve();
							}, function(){
								defered.reject();
							});
						}
					}else{
						defered.resolve();
					}

					return defered.promise;
				}
		    }],
		    templateUrl: CB.admin_assets_url + '/js/app/gallery/templates/gallerymanager-template.html'
		};

	}]);
});