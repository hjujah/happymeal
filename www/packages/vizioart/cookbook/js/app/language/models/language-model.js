define([
	'jquery', 
	'underscore',
	'angular',
	'app/language/module',
	'fast-class',
], function ($, _, angular, Module) {

    
	Module.factory('Language', ['$http', '$q', 'BaseModel', function($http, $q, Base){
		var model = Function.define({
			constructor: function(){

			},
			
		}).defineStatic({
			apiResourceUrl: function(url){
				return CB.admin_api_url + '/language' + ((typeof url !== "undefined")?'/'+url:'');
			},
			get: function(){
				var self = this;

				var defered = $q.defer();
				
				if (self.cache){
					defered.resolve(self.cache);
					return defered.promise;
				}

				$http.get(self.apiResourceUrl())
					.success(function(data){
						self.cache = data;
						defered.resolve(data);
					})
					.error(function(data){
						console.log(data, 'Language.get (error)');
						defered.reject(data);
					});
				
				return defered.promise;
				
			},
			cache: null,
			getByCode: function(code){
				var self = this;
				if(!self.cache){
					return false;
				}
				var lang = _.find(self.cache, function(language){
					return language.code == code;
				});

				return lang;
			},
			getById: function(id){
				var self = this;
				if(!self.cache){
					return false;
				}
				var lang = _.findWhere(self.cache, {id: id});

				return lang;
			}
		});

		return model;
	}]);

});