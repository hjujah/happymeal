define([
	'jquery', 
	'underscore',
	'angular',
	'app/page/module',
	'fast-class'
], function ($, _, angular, Module) {

    
	Module.factory('Page', ['$http', 'Post', function($http, Post){

		var model = Post.inheritWith(function(base, baseConstructor) {
			return {
				constructor: function() { 
					baseConstructor.call(this);//calls the baseConstructor
					this.apiUrl = CB.admin_api_url + '/page';
				}
			}
		});

		return model;
	}]);

});