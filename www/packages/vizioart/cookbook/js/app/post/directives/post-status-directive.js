define([
	'jquery', 
	'underscore', 
	'angular',
	'app/post/module'
], function ($, _, angular, Module) {

    
	Module.directive('postStatus', ['$timeout', function($timeout){
		return {
			restrict: 'A',	
			// @MOVE create separate HTML template for this directive			
			templateUrl: CB.admin_assets_url + '/js/app/post/templates/post-status-template.html',
			scope: {
				// post model (whole model - $scope.Post)
				model: '=',

				// post form status (from Post.form)
				status: '=postStatus',

				// post model status (from Post.model)
				oldStatus: '=postModelStatus',

				// list of available statuses (and their labels)
				availableStatuses: '=?'
			},
			link: function(scope, element, attrs){

				// create separate form model for this isolated scope
				scope.form = {
					// new status that is defined in directive
					newStatus: undefined,
					statuses: {
						'publish': 'Published',
						'draft': 'Draft'
					}
				};


				// watch for status changes and update new status accordingly
				scope.$watch('status', function(newValue, oldValue){
					if(typeof oldValue == 'undefined' || (typeof oldValue == 'string' && oldValue.length < 1)){
						scope.form.newStatus = newValue;
					}
				});


				// open status editing mode
				scope.changeStatus = function(){
					scope.editStatus = true;
				};

				// save changes to status
				scope.saveStatusChange = function(){

					if(typeof scope.form.newStatus == 'string' && scope.form.newStatus.length > 0){
						scope.status = angular.copy(scope.form.newStatus);	
					}
					scope.editStatus = false;
				};


				// cancel changes to status
				scope.cancelStatusChange = function(){
					scope.form.newStatus = angular.copy(scope.status);
					scope.editStatus = false;
				};


				// save model, accepts status for save (different buttons will pass different statuses)
				scope.save = function(status){

					// if status is defined it will be set
					if(typeof status == 'string' && status.length > 0){
						scope.status = status;
						
					}

					// instead scope.$apply using $timeout - it will position self in stack for scope.$apply
					$timeout(function(){
						scope.model.save();
					}, 0);
					
				};

				// call delete modal (confirmation)
				scope.callDelete = function(){
					scope.delete();
				}

				// delete model
				scope.delete = function(){
					$timeout(function(){
						scope.model.delete(scope.model.form.activeContent.id);
					}, 0);
				}


				// ------------------------------------

				scope.dateCreated = new Date();

				scope.$watch('model.form.created_at', function(val){
					scope.dateCreated = new Date(val);
				});

				scope.dateCreatedOpen = function($event) {
					$event.preventDefault();
					$event.stopPropagation();
					scope.dateCreatedOpened = true;
				};

				scope.dateCreatedOptions = {
					formatYear: 'yy',
					startingDay: 1
				};

				scope.$watch('dateCreated', function(val){
					console.log(val, 'date created value');
				})


				scope.dateFormats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
				scope.dateCreatedFormat = scope.dateFormats[0];
				
			}
		};
	}]);

});