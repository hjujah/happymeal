define([
	'jquery',
	'angular',
	'app/article/module',
    'app/article/models/article-model'
], function ($, angular, Module) {

    Module.controller('postController', ['$scope', 'Language', 'Article',  function($scope, Language, Article){

        $scope.baseUrl = CB.site_url;

        // languages
        // --------------------------------
        $scope.languages = [];

        $scope.Article = new Article();

        Language.get().then(
            function(response){
                $scope.languages = response;
                setLanguagesStatus();
                loadingObjects.languages = true;
                checkIfReadyForInit();
                //$scope.setActiveLanguage();
            },
            function(response){
                console.log(response, 'ArticleCtrl - lang not loaded');
            }
        );

        $scope.setActiveLanguage = function(lang){
            if(typeof lang !== "undefined"){
                if(lang.content_status == 'locked'){
                    return false;
                }
                $scope.activeLanguage = lang;    
            } else if ($scope.languages.length>0) {
                $scope.activeLanguage = $scope.languages[0];
            } else {
                $scope.activeLanguage = null;
            }
            setActiveContent();
        }


        // Post
        // --------------------------------
        $scope.activeContent = {};
        $scope.post = {};

        $scope.mod = $('#hidden_mod').val(),
        $scope.post_id = $('#hidden_post_id').val();
        if ($scope.mod == 'edit' && $scope.post_id != 0){
            $scope.Article.get($scope.post_id).then(
                function(response){
                    $scope.post = response;
                    loadingObjects.post = true;
                    checkIfReadyForInit();
                },
                function(response){
                    console.log(response, 'ArticleCtrl - Article not loaded');
                }
            );
        }else{
            $scope.Article.create().then(
                function(response){
                    $scope.post = response;
                    loadingObjects.post = true;
                    checkIfReadyForInit();
                },
                function(response){
                    console.log(response, 'ArticleCtrl - Article not loaded');
                }
            );
        }

        // Parents
        // ------------------------------------
        $scope.parents = [];
        $scope.Article.getAllParents().then(function(data){
            $scope.parents = data;
            loadingObjects.parents = true;
            setLanguagesStatus();
            checkIfReadyForInit();
        }, function(){});



        var setActiveContent = function(){
            if($scope.activeLanguage){
                $scope.Article.getContent($scope.activeLanguage.code).then(function(){
                    //$scope.Article.save();
                    setLanguagesStatus();
                });
            }
        };

        var loadingObjects = {
            languages: false,
            post: false,
            parents: false
        };

        var checkIfReadyForInit = function(){
            var obj = null;
            var loaded = true;
            for( obj in loadingObjects){
                if(!loadingObjects[obj]){
                    loaded = false;
                    break;
                }
            }

            if(loaded){
                init();
            }
        };

        var init = function(){
            $scope.setActiveLanguage();
            $scope.$broadcast('postinit');
            if($scope.Article.model.parent_id){
                setParent($scope.Article.model.parent_id);
            }
        };



        // TinyMCE
    	$scope.tinymceOptions = {
    		menubar:false,
            theme:'modern',
            skin:'light'
		};

        // url
        // --------------------------------
        $scope.url = ""

        // --------------------------------
        // functions
        // --------------------------------

        $scope.$watch('Article.form.parent_id', function(newVal, oldVal){

            if(newVal != oldVal && newVal > 0){
                setParent(newVal);
            }else{
                if($scope.Article && $scope.Article.form){
                    $scope.Article.form.parent = null;
                    $scope.Article.setParentActiveContent();
                    $scope.Article.createParentUrl();
                } 
                
            }
        });

        var setParent = function(parent_id){
            $scope.Article.form.parent = _.findWhere($scope.parents, {id: parent_id});

            if(!$scope.Article.form.parent){
                return;
            }
            $scope.Article.setParentActiveContent();
            $scope.Article.createParentUrl();

            setLanguagesStatus();
        }

        var setLanguagesStatus = function(){
            var lockedLanguages = [];

            // current post contents
            var contents = $scope.Article.form.post_contents;
            // parent contents
            var parentContents = false;

            // set parent contents if parent is set
            if(typeof $scope.Article.form.parent != 'undefined' && $scope.Article.form.parent){
                parentContents = $scope.Article.form.parent.post_contents;
            }


            angular.forEach($scope.languages, function (language) {

                // is language locked
                var locked = false;
                if(parentContents){
                    var parentContent = _.findWhere(parentContents, {language_id: language.id});
                    if(!parentContent || parentContent.status != 'publish'){

                        locked = true;
                    }
                    
                }

                // language status ('none', 'draft', 'publish', 'locked')
                var status = 'none';
                if(!locked){
                    
                    var language_content = _.findWhere(contents, {language_id: language.id});
                    if(language_content){
                        switch(language_content.status){
                            case 'publish':
                                status = 'publish';
                                break;
                            case 'draft':
                                status = 'draft';
                                break;
                            case 'auto-draft':
                            default:
                                status = 'none';
                                break;
                        }
                    }
                }else{
                    status = 'locked';
                }

                language.content_status = status;
                
            });     
        };


        

        $scope.$on('Post.saved', function(e, data){
            if(!data.autosave){
                if($scope.mod == 'new'){
                    if(window.history.replaceState) {
                        window.history.replaceState({}, "Edit Article", "edit/" + data.model.model.id);
                    }
                }
            }
            setLanguagesStatus();
        });

        $scope.$on('Post.contentDeleted', function(e, data){
            setLanguagesStatus();
            var languageSet = false;
            if(data.content){
                var language_id = data.content.language_id;
                if(data.model){

                    _.each(data.model.form.post_contents, function(content){
                        var lang = _.findWhere($scope.languages, {id: content.language_id});
                        if(lang && lang.status != 'locked'){
                            $scope.setActiveLanguage(lang);
                            languageSet = true;
                        }
                    })
                }
            }

            if(!languageSet){
                _.each($scope.languages, function(lang){
                    if(!languageSet){
                        if(lang.status != 'locked'){
                            $scope.setActiveLanguage(lang);
                            languageSet = true;
                            return;
                        }
                    }else{
                        return;
                    }
                })
            }

        });

        $scope.$on('gallerymanager.galleryCreated', function(e, data){
            console.log('gallerymanager.galleryCreated handled');
            $scope.Article.attachGallery();
        });

        $scope.$on('featuredimage.attachmentCreated', function(e, data){
            console.log('featuredimage.attachmentCreated handled');
            $scope.Article.attachFeaturedImage();
        });


        $scope.availableStatuses = {
            'publish': 'Published',
            'draft': 'Draft',
            'auto-draft': 'Auto Draft',
            'trash': 'Trashed'
        };

        $scope.archiveTypes = [
            // {
            //     type: '',
            //     label: 'none'
            // },
            {
                type: 'post',
                label: 'Articles'
            }
        ];

    }]);

    


});