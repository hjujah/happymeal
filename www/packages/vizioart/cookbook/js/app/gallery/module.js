define([
	'angular',
	'app/core/load',
], function (angular) {

    var Module = angular.module('gallery.module', ['core.module']);

    Module.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return Module;
});