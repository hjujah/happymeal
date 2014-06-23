define([
	'jquery', 
	'underscore',
	'angular',
	'app/core/module',
	'fast-class'
], function ($, _, angular, Module) {

    
	Module
		.factory('BaseModel', ['$http', function($http){

			var model = Function.define({
				constructor: function(){
					this.apiUrl = CB.admin_api_url;
				},
				apiResourceUrl: function(url){
					return this.apiUrl + ((typeof url !== "undefined")?'/'+url:'');
				},
				get: function() { 
					throw new Exception("You need to implement this method");
				}.defineStatic({abstract: true}),

				save: function() {
					throw new Exception("You need to implement this method");
				}.defineStatic({abstract: true}),
			});

			return model;
		}]);

});