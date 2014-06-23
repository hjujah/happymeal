define([
	'jquery', 
	'underscore',
	'angular',
	'app/core/module',
	'fast-class'
], function ($, _, angular, Module) {

    
	Module
		// Filter parents : have only parents that have needed languages and display adequate content (title) for each parent
		.filter('availableParents', function () {
			// filter needs Model to check its post_contents and Language (activeLanguage) to set parent content in active Language
			return function (items, Model, Language) {
				var filtered = [];

				var usedLanguages = [];

				// if something is missing return empty array
				if(!Model || !Model.form || !Model.form.post_contents || !Language){
					return filtered;
				}

				// set needed languages for parents
				_.each(Model.form.post_contents, function(content){
					if(content.status != 'auto-draft'){
						usedLanguages.push(content.language_id);
					}
				});

				// also include current language(activeLanguage) in needed languages 
				if(!_.contains(usedLanguages, Language.id)){
					usedLanguages.push(Language.id);
				}

				// check each parent
				angular.forEach(items, function (item) {
					// if its post that is currently edited dont include it
					if(Model.form.id == item.id){
						return;
					}

					// is item valid
					var valid = true;

					// what is active content for this parent
					var activeContent = false;

					// go through needed languages
					_.each(usedLanguages, function(lang_id){
						var c = _.findWhere(item.post_contents, {language_id: lang_id});

						// if some needed language is missing in this parent set it as invalid
						if(!c){
							valid = false;
						}else if(c.language_id == Language.id || !activeContent){ // set active content for parent if its current language
							activeContent = c;
						}
					});

					// if its valid parent create custom object for it and add it to filtered parents array
					if(valid){

						// set custom title depending on parent level
						// so it will look like tree structure

						// parent 1
						// - parent 2 (child of parent 1)
						// - - parent 3 (child of parent 2)
						// parent 4

						var title = '';
						for(var i = 0; i < item.level; i++){
							title += '- ';
						}

						// set custom object
						var obj = {
							id: item.id,
							level: item.level,
							title: title + activeContent.title,
							url: activeContent.url
						};
						
						// add it to array
						filtered.push(obj);
					}
					
				});
				
				// return filtered parents array
				return filtered;        
			}
		})
		
		// Filter for Languages selector: 
		// sets languages status for each language depending on current post_content statuses and available parent contents
		.filter('availableLanguages', function () {
			// it needs Model to see is parent selected and its contents and to see current post content statuses
			return function (items, Model) {
				var filtered = [];
				var lockedLanguages = [];

				// if model is not set return empty array
				if(!Model || !Model.form || !Model.form.post_contents){
					return [];
				}

				// current post contents
				var contents = Model.form.post_contents;
				// parent contents
				var parentContents = false;

				// set parent contents if parent is set
				if(typeof Model.form.parent != 'undefined' && Model.form.parent){
					parentContents = Model.form.parent.post_contents;
				}


				// if there are parent contents set locked languages
				if(typeof parentContents != 'object' || parentContents == null || !parentContents){
					lockedLanguages = [];
				}else{

					// if there isnt available parent content for language set it as locked
					_.each(items, function(item){
						if(!_.findWhere(parentContents, {language_id: item.id})){
							lockedLanguages.push(item.id);
						}
						
					});
				}

				angular.forEach(items, function (item) {

					// is language locked
					var locked = true;
					_.each(lockedLanguages, function(lang_id){
						if(item.id == lang_id){
							locked = false;
						}
					});

					// language status ('none', 'draft', 'publish', 'locked')
					var status = 'none';
					if(locked){
						
						var language_content = _.findWhere(contents, {language_id: item.id});
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

					item.content_status = status;
					
					filtered.push(item);
					
				});

				return filtered;        
			}
		});
});