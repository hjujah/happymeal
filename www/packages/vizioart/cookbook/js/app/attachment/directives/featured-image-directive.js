define([
	'jquery', 
	'underscore', 
	'angular',
	'app/attachment/module'
], function ($, _, angular, Module) {


	Module.directive('featuredImage', ['$timeout', '$q', function ($timeout, $q) {
		return {
		    restrict: 'A',
		    scope: {
		    	Attachment: '=model'
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
						if(!$scope.Attachment.inited){
							$scope.Attachment.create().then(function(){
								$scope.$emit('featuredimage.attachmentCreated', {attachment: $scope.Attachment});
								$scope.Attachment.addImage(images[0]).then(function(){
									defered.resolve();
								}, function(){
									defered.reject();
								});
							}, function(){
								defered.reject();
							});
						}else{
							defered.reject();
						}
					}else{
						defered.resolve();
					}

					return defered.promise;
				}

				$scope.deleteImage = function(){
					$scope.Attachment.delete();
				}
		    }],
		    templateUrl: CB.admin_assets_url + '/js/app/attachment/templates/featured-image-template.html'
		};

	}]);
});