define([
	'jquery', 
	'underscore',
	'angular',
	'app/attachment/module',
	'fast-class',
], function ($, _, angular, Module) {

    
	Module.factory('Attachment', ['$http', '$q', '$rootScope', 'BaseModel', function($http, $q, $rootScope, Base){

			var model = Base.inheritWith(function(base, baseConstructor) {
				return {
					constructor: function(attachment) { 
						baseConstructor.call(this);//calls the baseConstructor
						this.apiUrl = this.apiUrl + '/attachment';
						if(_.isObject(attachment)){
							this.unwrap(attachment);

						}
					},
					protectedAttributes: [],
					get: function(id) {//redefines the abstract get function
						var self = this;
						var defered = $q.defer();
						$http.get(self.apiResourceUrl(id))
							.success(function(data){
								if(!data.error){
									self.unwrap(data);
								}
								defered.resolve(data);
							})
							.error(function(data){
								console.log(data, 'Attachment.get (error)');
								defered.reject(data);
							});
						
						return defered.promise;
					},
					create: function() {
						var self = this;
						var defered = $q.defer();
						$http.get(self.apiResourceUrl('create'))
							.success(function(data){
								if(!data.error){
									self.unwrap(data);
								}
								defered.resolve(data);
							})
							.error(function(data){
								console.log(data, 'Attachment.create (error)');
								defered.reject(data);
							});
						
						return defered.promise;
					},
					save: function() {
						var self = this;
						var defered = $q.defer();

						if(!self.model){

							// make soft errors in production
							throw new Exception("There need to be model loaded to save it");
						}

						var data = {};

						data = self.createSaveData();

						console.log(data, 'Attachment.save - data');

						$http.post(self.apiResourceUrl(), data)
							.success(function(data){
								self.unwrap(data);
								defered.resolve(data);
								$rootScope.$broadcast('Attachment.saved', {model:self, autosave:false});
							}).error(function(data){
								defered.reject(data);
							});

						return defered.promise;
					},

					delete: function(){
						var self = this;
						var defered = $q.defer();

						if(!self.model){

							// make soft errors in production
							throw new Exception("There need to be model loaded to delete it");
						}

						$http.delete(self.apiResourceUrl(self.model.id))
							.success(function(data){
								defered.resolve(data);
								self.inited = false;
								$rootScope.$broadcast('Attachment.deleted', {model:self});
								self.model = null;
								self.form = null;
							}).error(function(data){
								defered.reject(data);
							});

						return defered.promise;
					},

					createSaveData: function() {
						var self = this;

						var data = {};
						data = angular.copy(self.form);

						delete data.inited;
						delete data.file;

						return data;
					},

					unwrap: function(data){
						var self = this;
						if(self.model){

							_.each(data, function(val, key){
								if(!_.contains(self.protectedAttributes, key)){
									self.model[key] = val;
									self.form[key] = angular.copy(val);
								}
							});
						}else{
							self.model = data;
							self.form = angular.copy(data);
						}

						if(self.model.file){
							self.url = CB.site_url + '/uploads/test/' + self.model.file.url;
						}

						self.inited = true;
						
					},

					addImage: function(image){
						var self = this;

						if(!self.model){

							// make soft errors in production
							throw new Exception("There need to be model loaded to add image to it");
						}

						self.form.file_id = image.id;

						return self.save();
					},
				}
			});


			return model;
		}]);

});




