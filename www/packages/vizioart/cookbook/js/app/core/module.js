define([
	'angular',
], function (angular) {

    var Module = angular.module('core.module', []);

    Module.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return Module;
});