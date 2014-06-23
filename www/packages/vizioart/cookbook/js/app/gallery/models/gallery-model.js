define([
	'jquery', 
	'underscore',
	'angular',
	'app/gallery/module',
	'fast-class',
], function ($, _, angular, Module) {

    
	Module.factory('Gallery', ['$http', '$q', '$rootScope', 'BaseModel', function($http, $q, $rootScope, Base){

			var model = Base.inheritWith(function(base, baseConstructor) {
				return {
					constructor: function(gallery) { 
						baseConstructor.call(this);//calls the baseConstructor
						this.apiUrl = this.apiUrl + '/gallery';
						if(_.isObject(gallery)){
							this.unwrap(gallery);

						}
					},
					protectedAttributes: ['items'],
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
								console.log(data, 'Gallery.get (error)');
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
								console.log(data, 'Gallery.create (error)');
								defered.reject(data);
							});
						
						return defered.promise;
					},
					getItems: function(){
						var self = this;

						var defered = $q.defer();
						if(!self.model){

							// make soft errors in production
							throw new Exception("There need to be model loaded to get model items");
						}

						if(self.itemsFetched){
							defered.resolve(self.form.items);
							return defered.promise;
						}else{
							self.model.items = [];
							self.form.items = [];
						}

						$http.get(self.apiResourceUrl(self.model.id + '/items'))
							.success(function(data){
								self.insertItems(data);
								defered.resolve(data);
							})
							.error(function(data){
								console.log(data, 'Gallery.getItems (error)');
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

						console.log(data, 'Gallery.save - data');

						$http.post(self.apiResourceUrl(), data)
							.success(function(data){
								self.unwrap(data);
								defered.resolve(data);
								$rootScope.$broadcast('Gallery.saved', {model:self, autosave:false});
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
								$rootScope.$broadcast('Gallery.deleted', {model:self});
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
						delete data.itemsFetched;
						delete data.items;

						return data;
					},

					unwrap: function(data){
						var self = this;
						if(self.model){
							if(typeof self.model.items == 'undefined'){
								self.model.items = [];
							}
							if(typeof self.form.items == 'undefined'){
								self.form.items = [];
							}
							if(data.items){
								self.insertItems(data.items);
							}

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

						self.inited = true;
						
					},

					addImages: function(images){
						var self = this;

						var promises = [];
						_.each(images, function(image){
							var promise = self.addImage(image);	
							promises.push(promise);
						});

						return $q.all(promises);
					},

					addImage: function(image){
						var self = this;

						if(!self.model){

							// make soft errors in production
							throw new Exception("There need to be model loaded to add items to it");
						}

						var data = {
							gallery_id: self.model.id,
							type: 'image',
							status: 'publish',
							name: '',
							description: '',
							content: '',
							file_id: image.id,
						};

						return self.addItem(data);
					},

					addItem: function(data){
						var self = this;
						var defered = $q.defer();

						$http.post(self.apiResourceUrl('item'), data)
							.success(function(data){
								self.insertItem(data);
								defered.resolve(data);
								$rootScope.$broadcast('Gallery.addItem', {item:data});
							}).error(function(data){
								defered.reject(data);
							});

						return defered.promise;
					},

					deleteItem: function(id){
						var self = this;
						var defered = $q.defer();

						$http.delete(self.apiResourceUrl('item/' + id))
							.success(function(data){
								if(data.deleted){
									self.removeItem(id);
									defered.resolve(data);
									$rootScope.$broadcast('Gallery.addItem', {item:data});
								}else{
									defered.reject(data);
								}
							}).error(function(data){
								defered.reject(data);
							});

						return defered.promise;
					},

					insertItems: function(items){
						var self = this;

						if(_.isObject(items) && items.length > 0){
							_.each(items, function(item){
								self.insertItem(item);
							});
						}
						
					},

					insertItem: function(item){
						var self = this;

						if(!_.isObject(item) || item == null){
							return;
						}

						if(item.type == 'image'){
							item.url = CB.site_url + '/uploads/test/' + item.file.url;
						}

						var oldItem = _.findWhere(self.model.items, {id: item.id});
						var index = 0;

						if(oldItem){
							index = _.indexOf(self.model.items, oldItem);
							self.model.items.splice(index, 1, item);
							self.form.items.splice(index, 1, angular.copy(item));
						}else{
							if(!self.model.items){
								self.model.items = [];
							}
							self.model.items.push(item);
							if(!self.form.items){
								self.form.items = [];
							}
							self.form.items.push(angular.copy(item));
							index = self.form.items.length - 1;
						}
					},

					removeItem: function(id){
						var self = this;

						var item = _.findWhere(self.model.items, {id: id});
						if(!item){
							return false;
						}

						var index = _.indexOf(self.model.items, item);

						self.model.items.splice(index, 1);
						self.form.items.splice(index, 1);

						return item;
					},
				}
			});


			return model;
		}]);

});




