define([
	'angular',
	'app/core/load',
], function (angular) {

    var Module = angular.module('attachment.module', ['core.module']);

    Module.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return Module;
});