define([
	'jquery', 
	'underscore',
	'angular',
	'app/article/module',
	'fast-class'
], function ($, _, angular, Module) {

    
	Module.factory('Article', ['$http', '$q', 'Post', function($http, $q, Post){

		var model = Post.inheritWith(function(base, baseConstructor) {
			return {
				constructor: function() { 
					baseConstructor.call(this);//calls the baseConstructor
					this.apiUrl = CB.admin_api_url + '/article';
				},

				getAll: function(){
					var self = this;

					var defered = $q.defer();

					$http.get(self.apiResourceUrl())
						.success(function(data){
							defered.resolve(data);
						})
						.error(function(data){
							defered.reject(data);
						});

					return defered.promise;
				}
			}
		});

		return model;
	}]);

});