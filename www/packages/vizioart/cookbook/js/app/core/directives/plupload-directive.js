define([
	'jquery', 
	'underscore', 
	'angular',
	'app/core/module',
	'plupload'
], function ($, _, angular, Module) {


	Module.directive('plupload', function () {
		return {
			restrict: 'A',
			scope: {
				'params': '=?',
				'multiParams': '=?',
				'instance': '=?',
				'uploaderId': '=?',
				'meta': '=?'
			},
			link: function (scope, iElement, iAttrs) {

				scope.randomString = function(len, charSet) {
				    charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				    var randomString = '';
				    for (var i = 0; i < len; i++) {
				    	var randomPoz = Math.floor(Math.random() * charSet.length);
				    	randomString += charSet.substring(randomPoz,randomPoz+1);
				    }
				    return randomString;
				}

				if(!iAttrs.id){
					var randomValue = scope.randomString(5);
					iAttrs.$set('id',randomValue);	
				}

				/**
				 * @TO_DO :
				 * 1. change default url, so that it does not depend on the global CB (CookBook) object
				 */
				var defaultParams = {
					runtimes: 'html5',
					browse_button: iAttrs.id,
					multi_selection: true,
					multipart: true,
					multiple_queues: true,
					max_file_count: 0,
					//container : 'abc',
					max_file_size : '8mb',
					url : CB.admin_api_url + '/file/upload',
					filters : [{title: "Image files", extensions: "jpg,jpeg,gif,png,tiff"}]
				};

				if(!scope.params){
					scope.params = {};
				}

				var params = {};
				angular.extend(params, defaultParams, scope.params);

				if(scope.multiParams){
					params.multipart_params = scope.multiParams;
				}

				var uploader = new plupload.Uploader(params);

				if(scope.multiParams){
					params.multipart_params.uploader_id = uploader.id;
				}


				uploader.bind('Init', function(up, params) {
					
					var dropAreaElement = $('#plupload-dd-area'),
						ddClassOn = false,
						applyClass = function(){
							if(ddClassOn){return;}
							dropAreaElement.addClass('dragover');
							ddClassOn = true;
						}

			
					if ( up.features.dragdrop && dropAreaElement.length) {
						
						dropAreaElement.addClass('drag-drop')
							.bind('dragover.cb-uploader', function(){
								applyClass();
							})
							.bind('dragleave.cb-uploader, drop.cb-uploader', function(){
								dropAreaElement.removeClass('dragover');
								ddClassOn = false;
							});

					} else {
						dropAreaElement.removeClass('drag-drop')
							.unbind('.cb-uploader');
					}
				});

				uploader.init();
				uploader.settings.multipart_params = params.multipart_params;

				if(scope.uploaderId){
					uploader.id = scope.uploaderId;
				}else{
					scope.uploaderId = uploader.id;
				}
				uploader.settings.file_data_name = 'plupload_file';

				uploader.bind('Error', function(up, err) {
					scope.$emit('PLUPLOAD.error', {uploader: up, error: err, meta:scope.meta});
		        	up.refresh(); // Reposition Flash/Silverlight
			 	});
				
				uploader.bind('FilesAdded',function(up,files){

					if(typeof(scope.filesModel) != 'object' || scope.filesModel == null){
						scope.filesModel = [];
					}

					var maxCountError = false;
					var addedFiles = [];

					files.reverse();

					angular.forEach(files, function(file){

						var i = uploader.files.length;

						if(uploader.settings.max_file_count && i > uploader.settings.max_file_count){
							uploader.removeFile(file);
							i = uploader.files.length;
						}else{
							scope.filesModel.push(file);
							addedFiles.push(file);
						}
					});

					if(addedFiles.length > 0){
						addedFiles.reverse();
						if(!iAttrs.autoUpload || iAttrs.autoUpload=="true"){
							uploader.start();
						}

						scope.$emit('PLUPLOAD.files.added', {uploader: up, files: addedFiles, meta:scope.meta});
					}

					
    			});

    			uploader.bind('UploadFile', function(up, file){
    				scope.$emit('PLUPLOAD.files.upload', {uploader: up, file: file, meta:scope.meta});
    			});

				uploader.bind('FileUploaded', function(up, file, res) {

					res = angular.fromJson(res.response);

					angular.forEach(scope.filesModel, function(file,key){
						scope.allUploaded = false;
						if(file.percent==100){
							scope.allUploaded = true;
						}
					});

					if(scope.allUploaded) {
						scope.$emit('PLUPLOAD.files.uploaded.all', {uploader: up, file: file, result: res, meta:scope.meta});
					}

					scope.$emit('PLUPLOAD.files.uploaded', {uploader: up, file: file, result: res, meta:scope.meta});
    			});

				uploader.bind('UploadProgress',function(up,file){
					
					var sum = 0;
					angular.forEach(scope.filesModel, function(file, key){
						sum += file.percent;
					});

					sum = Math.round(sum / scope.filesModel.length);					

					scope.$emit('PLUPLOAD.files.progress', {uploader: up, file: file, progress: file.percent, summary: sum, meta:scope.meta});
    				
    			});

				scope.instance = uploader;
				
			}
		};
	})
	.directive('dropareaPlupload', function(){
		return {
			restrict: 'EA',
			scope: {
				'params': '=?',
				'multiParams': '=?',
				'instance': '=?',
				'uploaderId': '=?',
				'meta': '=?',
				'infoText': '=?'
			},
			template: 	'<div class="plupload-inner">' +
							'<div class="vCenter">' +
								'<div class="vCenterKid">' +
									'<p class="plupload-info-text">[%infoText%]</p>' +
									'<p class="plupload-info-or">- or -</p>' +
									'<span class="btn btn-default button" plupload params="params" multi-params="multiParams" instance="instance" uploader-id="uploaderId" meta="meta">Click to open the file browser</span>' +
								'</div>' +
							'</div>' +
						'</div>',

			controller: function($scope, $element){

			},
			compile: function(tElement, tAttrs){

				var randomString = function(len, charSet) {
				    charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				    var randomString = '';
				    for (var i = 0; i < len; i++) {
				    	var randomPoz = Math.floor(Math.random() * charSet.length);
				    	randomString += charSet.substring(randomPoz,randomPoz+1);
				    }
				    return randomString;
				}

				tAttrs.defaultParams = {};

				var dropAreaElement = false;
				if ( _.isString(tAttrs.dropareaid) ){
					var elem = $('#'+tAttrs.dropareaid);
					if (elem.length){
						dropAreaElement = elem;
					}
				} else {
					var children = tElement.children();
					angular.forEach(children, function(elem){
						elem = angular.element(elem);
						if(elem.hasClass('plupload-droparea')){
							dropAreaElement = elem;
						}
					});
				}

				if(dropAreaElement){
					tAttrs.defaultParams.drop_element = dropAreaElement.get(0);
				}

				return {
					pre: function preLink(scope, iElement, iAttrs, controller) {
						if(!scope.infoText){
							scope.infoText = "Drag images here";
						}

						if(!scope.params){
							scope.params = {};
						}
						var params = {};
						angular.extend(params, iAttrs.defaultParams, scope.params);

						scope.params = params;
					},
					post: function postLink(scope, iElement, iAttrs, controller) {

					}
				}
			},
			
		};
	});

});