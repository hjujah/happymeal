define([
	'jquery', 
	'underscore', 
	'angular',
	'app/post/module'
], function ($, _, angular, Module) {

    
	Module.directive('postTitle', ['$http', '$timeout', function($http, $timeout){
		return {
			restrict: 'A',
			replace: true,
			// @MOVE to separate HTML template
			templateUrl: CB.admin_assets_url + '/js/app/post/templates/post-title-template.html',
			scope: {
				title: '=postTitle',
				baseUrl: '=',
				parentUrl: '=',
				name: '=?postName',
				oldName: '=?postModelName',
				url: '=?postUrl',
				isCustom: '=',
				placeholder: '@'
			},

			link: function(scope, element, attrs){


				// Sanitize title input for post name
				var sanitizeTitle = function(title){
					//check if title null
					if(typeof title == 'string'){
						title = title.toLowerCase();
						
						// split title in words
						var words = [];
						title.replace(/(\w+)/g, function(match){
							words.push(match);
						});


						// join words with '-'
						return words.join('-');
					}else{

						// if title null
						return '';
					}
		        }

		        

		        var checkUrl = function(){
		        	if(!scope.isCustom){
						if(scope.title != '' && scope.name != '' && scope.name != sanitizeTitle(scope.title)){
							scope.isCustom = true;
						}else{
							scope.name = sanitizeTitle(scope.title);
							scope.url = scope.parentUrl + angular.copy(scope.name);
						}
					}
		        }

				// if url isn't protected (isn't custom) set starting value for name and url
				checkUrl();

		        var oldCustomName = '';

		        scope.urlString = scope.baseUrl + '/' +  scope.url;

		        // if baseUrl changes update the url string
		        scope.$watch('baseUrl', function(newVal, oldVal){
		        	if(newVal != oldVal){
		        		scope.urlString = scope.baseUrl + '/' +  scope.url;
		        	}
		        	checkUrl();
		        });

		        // if parentUrl changes update the url and url string
		        scope.$watch('parentUrl', function(newVal, oldVal){
		        	if(newVal != oldVal){
		        		scope.url = scope.parentUrl + angular.copy(scope.name);
		        		scope.urlString = scope.baseUrl + '/' +  scope.url;
		        		checkUrl();
		        	}
		        	
		        });

		        // watch changes to url and name and update scope.isCustom accordingly
		        scope.$watch('url', function(newVal, oldVal){
		        	if(newVal != oldVal){
			        	checkUrl();
			        }
		        });
		        scope.$watch('name', function(newVal, oldVal){
		        	if(newVal != oldVal){
			        	checkUrl();
			        }
		        });


		        // for throttling ajax call to check if url is unique
		        var checkNameTimeout = null;

		        // status of uniqueness of url (ok, bad, please wait)
		        scope.urlStatus = '';
		        

		        // watch changes to name and check uniqueness on server (throttled)
		        scope.$watch('name', function (val) {
		        	// @CHANGE - urlStatus values are now directly displayed, should be some slugs
		        	scope.urlStatus = 'please wait...';
		        	if(checkNameTimeout){
		        		$timeout.cancel(checkNameTimeout);
		        	}
		        	if(val != scope.oldName && typeof val == 'string' && val != ''){
		        		checkNameTimeout = $timeout(function() {
		        			scope.url = scope.parentUrl + scope.name;
				            checkNameUnique();
				        }, 250); // delay 250 ms
		        	}else{ // do not check if value is empty or not changed
		        		scope.urlStatus = '';
		        	}	        
			    });

			    var checkNameUnique = function(){
					$http.get(CB.admin_api_url + '/page/unique-url/' + scope.url)
						.success(function(data){
							console.log(data, 'unique url response');
							if(typeof data == 'object' && data.isUnique){
								scope.urlStatus = 'ok';
							}else{
								scope.urlStatus = 'bad';
							}
						})
						.error(function(data){
							console.log(data);
							scope.urlStatus = 'bad';
						});
			    };

		        // go to custom editing url
		        scope.editCustomName = function(){
	                oldCustomName = angular.copy(scope.name);
		            scope.customNameMode = 'edit';
		        }
		        
		        // save editing of url
		        scope.saveCustomName = function(){
		        	scope.customNameMode = 'default';
		            if(oldCustomName != scope.name && scope.name != ''){
		            	scope.name = sanitizeTitle(scope.name);
						scope.isCustom = true;
						scope.url = scope.parentUrl + scope.name;
		            }else{
		            	scope.name = angular.copy(oldCustomName);
		            	scope.url = scope.parentUrl + scope.name;
		            }
		            scope.urlString = scope.baseUrl + '/' +  scope.url;
		        }

		        // cancel editing of url
		        scope.cancelCustomName = function(){
		            scope.customNameMode = 'default';
		            scope.name = angular.copy(oldCustomName);
		            scope.url = scope.parentUrl + scope.name;
		        	scope.urlString = scope.baseUrl + '/' + scope.url;
		        }			        

		        // watch changes to title and update name and url if they aren't protected
		        scope.$watch('title', function(value){
		            if(!scope.isCustom){
		            	scope.name = sanitizeTitle(value);
		               	scope.url = scope.parentUrl + scope.name;
		                scope.urlString = scope.baseUrl + '/' + scope.url;
		            }
		        });

		        scope.customNameMode = 'default';
			}
		};
	}]);

});