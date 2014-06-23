define([
	'angular',
	'fast-class',
	'ui-utils',
	'ui-tinymce',
	'app/language/load',
	'app/article/load',
	'app/articles/directives/articles-directive'
], function (angular) {

    var App = angular.module("app", ['language.module', 'article.module', 'articles.module']);
    App.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return App;
});