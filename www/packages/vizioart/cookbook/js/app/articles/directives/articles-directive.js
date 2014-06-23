define([
	'jquery', 
	'underscore', 
	'angular',
	'app/language/models/language-model',
	'app/article/models/article-model',
], function ($, _, angular) {


angular.module('articles.module', [])
	.directive('articleItem', ['Language', 'Article', function(Language, Article){
		return {
		    restrict: 'A',
		    scope: {
		      item: '=',
		      language: '='
		    },
		    controller: function($scope){

		    	$scope.getEditLink = function(){
		    		var url = CB.admin_url + '/articles/edit/' + $scope.item.id;
		    		return url;
		    	}

		    },
		    replace: true, // Replace with the template below
		    link: function(scope, element, attrs) {
		    	
		    	scope.content = {};
		    	scope.datePublished = function(){
		    		
	    			var created_at = moment(scope.item.created_at),
						updated_at = moment(scope.item.updated_at),
						now = moment(),
						old = now.diff(created_at, 'days') > 1 ? true : false,
						display = '';

					if (status=='publish'){
						display = old ? created_at.format("YYYY-MM-DD HH:mm") : created_at.fromNow();
					} else {
						display = old ? updated_at.format("YYYY-MM-DD HH:mm") : updated_at.fromNow();
					}
					return display;
		    	};


		    	var getPostContentByLang = function(lang){

		    		var lang_id = lang.id;
		    		var post_content = _.find(scope.item.post_contents, function(post_content){ 
		    			return post_content.language_id == lang_id; 
		    		});

		    		if (_.isUndefined(post_content)){
		    			// set default lang

		    			// if still undefined, set first lang

		    		}

		    		return post_content;
		    	};

				scope.$watch('language', function(newValue, oldValue) {
					scope.content = getPostContentByLang(newValue);
					console.log(scope.content);
				});


				scope.content.image = CB.site_url + '/img/blank.gif';
				if(_.isObject(scope.item.featured_image)){
					scope.content.image = CB.site_url + '/uploads/md_' + scope.item.featured_image.url;
				}



		    },
		    templateUrl: CB.admin_assets_url + '/js/app/templates/article-item.html'
		}
	}]);
});

