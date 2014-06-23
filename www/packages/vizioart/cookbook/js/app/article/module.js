define([
	'angular',
	'ui-bootstrap',
	'ui-utils',
	'ui-tinymce',
	'app/post/load',
], function (angular) {
	//console.log('app loaded');

    var Module = angular.module("article.module", ['ui.utils', 'ui.tinymce', 'ui.bootstrap', 'post.module']);
    Module.config(function($interpolateProvider){
		$interpolateProvider.startSymbol('[%').endSymbol('%]');
	});

	return Module;
});