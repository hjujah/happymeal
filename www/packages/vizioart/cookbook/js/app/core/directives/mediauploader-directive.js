define([
	'jquery', 
	'underscore', 
	'angular',
	'app/core/module',
	'plupload',
	'app/core/directives/plupload-directive',
], function ($, _, angular, Module) {


	Module.directive('mediauploader',['$timeout', '$http', '$q', function ($timeout, $http, $q) {
		return {
		    restrict: 'A',
		    scope: {
		    	// propertie used as switch for show/hide modal
		    	show: '=',

				// callback to be called when uploader is done
				// @params array of uploaded images
		    	doneCallback: '&',
		    },
		    replace: true, // Replace with the template below
		    transclude: true, // we want to insert custom content inside the directive
		    
		    compile: function(tElement, tAttrs){


		    	return {
					pre: function preLink(scope, iElement, iAttrs, controller) {
						if(!iAttrs.single || iAttrs.single.toLowerCase() == 'false'){
				    		scope.single = false;
				    	}else{
				    		scope.single = true;
				    		scope.uploaderParams = {};
				    		scope.uploaderParams.multi_selection = false;
				    		scope.uploaderParams.multiple_queues = false;
				    		scope.uploaderParams.max_file_count = 1;
				    	}

				    	scope.uploader = null;
					},
					post: function postLink(scope, iElement, iAttrs, controller) {

					}
				}
		    },
		    controller: function($scope){

		    	// set default values
		    	$scope.status = 'default';
		    	$scope.files = [];

		    	// @TO-DO implement plupload cancel file upload / remove from file list
		    	$scope.cancelUpload = function(item){
		    		// to be implemented
		    		console.warn('cancelUpload() - to be implemented');
		    	};


		    	// delete file from database and filesystem via ajax
		    	// @MOVE - to some File model or something
		    	$scope.deleteFile = function(item){
		    		
		    		var defered = $q.defer();

		    		// check if item is already uploaded and existing
		    		if(_.isUndefined(item) || _.isNull(item) || item.status != 'uploaded'){
		    			defered.reject('Invalid params');
		    			return defered.promise;
		    		}

		    		// set status for DOM changes (it should show some kind of loader)
		    		item.status = 'deleting';

		    		// make request for delete
					$http.delete(CB.admin_api_url + '/file/' + item.imageId)
						.success(function(data){
							// remove file from list and resolve promise
							var index = _.indexOf($scope.files, item);
							$scope.files.splice(index, 1);
							defered.resolve(data);
						}).error(function(data){
							// Error
							// @TO-DO - make DOM changes so user is aware of error
							item.status = 'error';
							item.error = 'Failed to delete file.';
							defered.reject(data);
						});

					return defered.promise;
		    	}

		    	// cancel dialog function
		    	$scope.cancel = function(){
		    		// set directive to busy
		    		// @TO-DO make DOM changes so some loader is shown while proccessing files
		    		$scope.busy = true;

		    		// delete/remove all files in list
		    		// returns merged promise for all actions
		    		deleteAllFiles().then(function(data){
		    			_resetModal();
		    		}, function(data){
		    			// some error occured
		    			// @TO-DO some kind of exit strategy
		    			$scope.busy = false;
		    		});
		    	};



		    	// delete/remove all files from list
		    	var deleteAllFiles = function(){

		    		// sort files by their status for delete or for remove
		    		var filesForDelete = [];
		    		var filesForRemove = [];

		    		_.each($scope.files, function(file){
		    			if(file.status == 'uploaded'){
		    				filesForDelete.push(file);
		    			}else{
		    				filesForRemove.push(file);
		    			}
		    		});

		    		// @TO-DO remove/cancel file upload for each file in filesForRemove

	    			// delete all uploaded files
	    			var promises = [];
	    			_.each(filesForDelete, function(file){
	    				// stack promises for one merged promise
	    				var promise = $scope.deleteFile(file);
	    				promises.push(promise);
	    			});

	    			// create merged promse
	    			// waits for all promises in array
	    			var result = $q.all(promises);

		    		return result;
		    	};

		    	// done dialog function (calls $scope.doneCallback function)
		    	$scope.done = function(){

		    		// set directive to busy
		    		// @TO-DO make DOM changes so some loader is shown while proccessing files
		    		$scope.busy = true;

		    		// sort files by their status for save or for remove
		    		var filesForSave = [];
		    		var filesForRemove = [];

		    		_.each($scope.files, function(file){
		    			if(file.status == 'uploaded'){
		    				filesForSave.push(file.image);
		    			}else{
		    				filesForRemove.push(file);
		    			}
		    		});

		    		// @TO-DO remove/cancel file upload for each file in filesForRemove

		    		var callback = $scope.doneCallback();
		    		callback(filesForSave).then(function(data){
		    			_resetModal();
		    		}, function(data){
		    			// some error occured
		    			// @TO-DO some kind of exit strategy
		    			$scope.busy = false;
		    		});

		    	};

		    	// reset modal properties and close the modal
		    	var _resetModal = function(){
		    		// set parameters to default values for next use and 
	    			// close the modal
	    			var files = angular.copy($scope.uploader.files);
	    			_.each(files, function(file){
	    				$scope.uploader.removeFile(file);
	    			});
	    			$scope.show = false;
	    			$scope.busy = false;
	    			$scope.status = 'default';
	    			$scope.files = [];
		    	}

		    	// handle PLUPLOAD files added
		    	$scope.$on('PLUPLOAD.files.added', function(e, data){
		    		// check if event is triggered by uploader of this directive
		    		if(data.uploader.id == $scope.uploader.id){
		    			// create item for each file that is added and push it to files list
		    			_.each(data.files, function(file){
		    				var item = {
		    					status: 'added',
		    					fileId: file.id,
		    					file: file,
		    					item: null
		    				};
		    				$scope.files.push(item);
		    			});
		    		}
		    	});

		    	// handle PLUPLOAD files progress
		    	$scope.$on('PLUPLOAD.files.progress', function(e, data){
		    		// check if event is triggered by uploader of this directive
		    		if(data.uploader.id == $scope.uploader.id){
		    			// update progress of a file
		    			var item = _.findWhere($scope.files, {fileId: data.file.id});
		    			item.progress = data.progress;
		    		}
		    	});

		    	// handle PLUPLOAD files uploaded
		    	$scope.$on('PLUPLOAD.files.uploaded', function(e, data){
		    		// check if event is triggered by uploader of this directive
		    		if(data.uploader.id == $scope.uploader.id){
		    			// load image model in corresponding item
		    			$timeout(function(){
			    			var item = _.findWhere($scope.files, {fileId: data.file.id});
			    			item.status = 'uploaded';
			    			item.image = data.result.result.file;
			    			item.imageId = data.result.result.file.id;
			    			item.url = CB.site_url + '/uploads/test/' + data.result.result.file.url;
			    		}, 0);
		    		}
		    	});

		    	// handle PLUPLOAD files upload start
		    	$scope.$on('PLUPLOAD.files.upload', function(e, data){
		    		// check if event is triggered by uploader of this directive
		    		if(data.uploader.id == $scope.uploader.id){
		    			// update status for item when upload starts
			    		$timeout(function(){
			    			$scope.status = 'list';
			    			var item = _.findWhere($scope.files, {fileId: data.file.id});
			    			item.status = 'uploading';
			    		}, 0);
		    		}
		    	});
		    },
		    templateUrl: CB.admin_assets_url + '/js/app/core/templates/mediauploader-template.html'
		}
	}]);
});

