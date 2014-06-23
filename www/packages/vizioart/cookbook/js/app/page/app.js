define([
	'angular',
	'app/page/load'
], function (angular) {
	//console.log('app loaded');

    var App = angular.module("app", ['page.module']);
    App.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return App;
});