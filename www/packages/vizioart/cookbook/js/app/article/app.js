define([
	'angular',
	'app/article/load'
], function (angular) {
	//console.log('app loaded');

    var App = angular.module("app", ['article.module']);
    App.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return App;
});