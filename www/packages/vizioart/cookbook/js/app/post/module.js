define([
	'angular',
	'app/core/load',
	'app/language/load',
	'app/gallery/load',
	'app/attachment/load'
], function (angular) {

    var Module = angular.module('post.module', ['core.module', 'language.module', 'gallery.module', 'attachment.module']);

    Module.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return Module;
});