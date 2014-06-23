define([
	'jquery',
	'angular',
    'app/articles/app'
], function ($, angular, App) {

    App.controller('articlesController', ['$scope', 'Language','Article',  function($scope, Language, Article){

        $scope.baseUrl = CB.site_url;
        $scope.languages = [];
        $scope.activeLanguage = {};

        Language.get().then(
            function(response){
                $scope.languages = response;
                $scope.setActiveLanguage(Language.getByCode('cs'));
            },
            function(response){
                console.log(response, 'articlesController - languages not loaded');
            }
        );

        $scope.setActiveLanguage = function(lang){
            $scope.activeLanguage = lang;
        };
        
        $scope.model = {};

        var ArticleModel = new Article();
        ArticleModel.getAll().then(function(data){
            $scope.model.articles = data;
        }, function(data){
            console.log(data, 'error');
        });

    }]);

    


});