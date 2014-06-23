/*
Slider View
Author: Filip Arneric
*/
define(['app',
		'pxloader',
		'text!../../templates/hbs/home.hbs', 
		], function(App, PxLoader, Template) {

	App.Views.Map = Backbone.View.extend({
	
		template: Handlebars.compile(Template),
		manage: true,
		
		/*
getFetchURL: function(){
			return absurl + "/api/page/cs/dockpark";
		},
*/
		
		preload: function(callback){
			var self = this;
			
			src = absurl + '/img/blank.gif';
			App.loader.addImage(src);
			
			App.loader.addCompletionListener(function() { 
				callback();
			})
			
			App.loader.start();
	    },	
	    
	    	
		beforeRender: function(){
			//console.log(self.dataCollection[0].page);
		},
		
		afterRender: function(){
			alert(123);
		},
		
		serialize: function() {
		    return {
		      collection: /* App.layout.dataCollection[0].page */ 'smth',
		      test: 'test'
		    };
		}
	});		
	
}); 