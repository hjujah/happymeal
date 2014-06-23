define([
	'jquery', 
	'underscore',
	'angular',
	'app/post/module',
	'fast-class',
], function ($, _, angular, Module) {

    
	Module.factory('Post', ['$http', '$q', '$rootScope', 'BaseModel', 'Language', 'Gallery', 'Attachment', function($http, $q, $rootScope, Base, Language, Gallery, Attachment){

		var model = Base.inheritWith(function(base, baseConstructor) {
			return {
				constructor: function() { 
					baseConstructor.call(this);//calls the baseConstructor
					this.apiUrl = this.apiUrl + '/post';
				},
				protectedAttributes: ['post_contents', 'galleries'],
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
							console.log(data, 'Post.get (error)');
							defered.reject(data);
						});
					
					return defered.promise;
				},
				getAllParents: function(){
					var self = this;
					var defered = $q.defer();
					$http.get(self.apiResourceUrl('parents'))
						.success(function(data){
							defered.resolve(data);
						})
						.error(function(data){
							console.log(data, 'Post.getAllParents (error)');
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
							console.log(data, 'Post.create (error)');
							defered.reject(data);
						});
					
					return defered.promise;
				},
				getContent: function(language_code){
					var self = this;

					var defered = $q.defer();
					if(!Language.getByCode(language_code)){

						// make soft errors in production
						throw new Exception("Invalid language code");
					}
					if(!self.model){

						// make soft errors in production
						throw new Exception("There need to be model loaded to get model content");
					}

					if(typeof self.model.post_contents == 'undefined'){
						self.model.post_contents = [];
					}

					var content = _.find(self.model.post_contents, function(content){

						return content.language_id == Language.getByCode(language_code).id;
					});

					if(content){
						var index = _.indexOf(self.model.post_contents, content);
						self.setActiveContent(index);
						defered.resolve(content);
						return defered.promise;
					}

					$http.get(self.apiResourceUrl(self.model.id + '/content/' + language_code))
						.success(function(data){
							console.log("CONTENT AJAX");
							self.insertContent(data, true);
							defered.resolve(data);
						})
						.error(function(data){
							console.log(data, 'Post.getContent (error)');
							defered.reject(data);
						});
					
					return defered.promise;
				},
				save: function() {
					var self = this;
					var defered = $q.defer();

					var data = {};

					data = self.createSaveData();

					console.log(data, 'Post.save - data');

					$http.post(self.apiResourceUrl(), data)
						.success(function(data){
							self.unwrap(data);
							defered.resolve(data);
							$rootScope.$broadcast('Post.saved', {model:self, autosave:false});
						}).error(function(data){
							defered.reject(data);
						});

					return defered.promise;
				},

				delete: function(id){
					var self = this;
					var defered = $q.defer();

					console.log('Post.delete content ' + id);

					$http.delete(self.apiResourceUrl('content/' + id))
						.success(function(data){
							var content = false;
							if(data.deleted){
								content = self.removeContent(id);
							}
							defered.resolve(data);
							$rootScope.$broadcast('Post.contentDeleted', {model:self, content: content});
						}).error(function(data){
							defered.reject(data);
						});

					return defered.promise;
				},

				attachGallery: function(){
					var self = this;

					var defered = $q.defer();

					if(!self.Gallery || !self.Gallery.inited){
						// make soft errors in production
						throw new Exception("There need to be Gallery inited to attach it");
					}

					$http.get(self.apiResourceUrl(self.model.id + '/attach-gallery/' + self.Gallery.model.id))
						.success(function(data){
							if(data.attached){
								defered.resolve(data);
							}else{
								console.log(data, 'Post.attachGallery (error)');
								defered.reject(data);
							}
						})
						.error(function(data){
							console.log(data, 'Post.attachGallery (error)');
							defered.reject(data);
						});
				},

				attachFeaturedImage: function(){
					var self = this;

					var defered = $q.defer();

					if(!self.FeaturedImage || !self.FeaturedImage.inited){
						// make soft errors in production
						throw new Exception("There need to be Featured Image inited to attach it");
					}

					$http.get(self.apiResourceUrl(self.model.id + '/attach-attachment/' + self.FeaturedImage.model.id))
						.success(function(data){
							if(data.attached){
								defered.resolve(data);
							}else{
								console.log(data, 'Post.attachFeaturedImage (error)');
								defered.reject(data);
							}
						})
						.error(function(data){
							console.log(data, 'Post.attachFeaturedImage (error)');
							defered.reject(data);
						});
				},

				createSaveData: function() {
					var self = this;

					var data = {};
					data = angular.copy(self.form);

					delete data.activeContent;
					delete data.galleries;
					delete data.Gallery;
					delete data.FeaturedImage;

					/*
					if(self.Gallery.inited){
						data.galleries = [self.Gallery.createSaveData()];
					}
					*/

					data.post_contents = [];
					var content = self.createSaveContentData();

					data.post_contents.push(content);

					return data;
				},

				createSaveContentData: function(){
					var self = this;

					var data = self.form.activeContent;

					return data;
				},

				unwrap: function(data){
					var self = this;
					if(self.model){
						if(typeof self.model.post_contents == 'undefined'){
							self.model.post_contents = [];
						}
						if(typeof self.form.post_contents == 'undefined'){
							self.form.post_contents = [];
						}
						if(data.post_contents){
							_.each(data.post_contents, function(content){
								self.insertContent(content, false);
							});
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

					if(data.galleries && data.galleries.length > 0){
						console.log(data.galleries, 'Post galleries');
						var gallery = angular.copy(data.galleries[0]);
						self.Gallery = new Gallery(gallery);
						self.Gallery.getItems().then(function(){});
					}else{
						self.Gallery = new Gallery();
					}

					console.log(self.Gallery, 'Gallery');

					console.log(data, "API DATA");

					if(data.featured_image && data.featured_image.file){
						console.log('HAS FEATURED IMAGE');
						console.log(data.featured_image, 'Post featured image');
						var featured_image = angular.copy(data.featured_image);
						self.FeaturedImage = new Attachment(featured_image);
					}else{
						console.log('NO FEATURED IMAGE')
						self.FeaturedImage = new Attachment();
					}

					console.log(self.FeaturedImage, 'FeaturedImage');
					
				},

				insertContent: function(content, setAsAcitve){
					var self = this;
					var oldContent = _.findWhere(self.model.post_contents, {id: content.id});
					var index = 0;

					var oldActiveContentIndex = _.indexOf(self.form.post_contents, self.form.activeContent);
					if(oldContent){
						index = _.indexOf(self.model.post_contents, oldContent);
						self.model.post_contents.splice(index, 1, content);
						self.form.post_contents.splice(index, 1, angular.copy(content));
					}else{
						if(!self.model.post_contents){
							self.model.post_contents = [];
						}
						self.model.post_contents.push(content);
						if(!self.form.post_contents){
							self.form.post_contents = [];
						}
						self.form.post_contents.push(angular.copy(content));
						index = self.form.post_contents.length - 1;
					}

					if(setAsAcitve){
						self.setActiveContent(index);
					}else{
						self.setActiveContent(oldActiveContentIndex);
					}
					
				},

				removeContent: function(id){
					var self = this;

					var content = _.findWhere(self.model.post_contents, {id: id});
					if(!content){
						return false;
					}

					var index = _.indexOf(self.model.post_contents, content);

					self.model.post_contents.splice(index, 1);
					self.form.post_contents.splice(index, 1);

					return content;
				},

				setActiveContent: function(index){
					var self = this;
					if(index != -1){
						self.form.activeContent = self.form.post_contents[index];
						self.model.activeContent = self.model.post_contents[index];
						self.setParentActiveContent();
						self.createParentUrl();
						self.setUrlProtection();
					}
				},

				setParentActiveContent: function(){
					var self = this;

					if(!self.form.activeContent || !self.model.activeContent){return;}

					if(typeof self.form.parent != 'undefined' && self.form.parent != null){
						self.form.parent.activeContent = _.findWhere(self.form.parent.post_contents, {language_id: self.form.activeContent.language_id});
					}
				},

				createParentUrl: function(){
					var self = this;

					if(!self.form.activeContent || !self.model.activeContent){return;}

					var parentUrl = '';

					if(typeof self.form.parent != 'undefined' && self.form.parent != null && self.form.parent.activeContent.language_id == self.form.activeContent.language_id){
						parentUrl = self.form.parent.activeContent.url + '/';
					}else{
						var lang = Language.getById(self.form.activeContent.language_id);
						parentUrl = lang.code + '/';
					}

					self.form.activeContent.parentUrl = parentUrl;

				},

				setUrlProtection: function(){
					var self = this;
					if(self.model.activeContent.status == 'publish'){
						self.form.activeContent.isUrlCustom = true;
					}
				}
			}
		});


		return model;
	}]);

});




