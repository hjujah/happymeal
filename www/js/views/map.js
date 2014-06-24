/*
Slider View
Author: Filip Arneric
*/
define(['app',
		'pxloader',
		'text!../../templates/hbs/map.hbs', 
		], function(App, PxLoader, Template) {

	App.Views.Map = Backbone.View.extend({
		el: '#main',
		manage: true,
		template: Handlebars.compile(Template),
		
		getFetchURL: function(){
			return absurl + "/api/setup";
		},
		
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
			
		},
		
		afterRender: function(){
			this.delegateEvents();
		},
			
		serialize: function() {
			var self = this;
		    return {
		      data: self.dataCollection[0].languages,
		      test: 'test'
		    };
		}
	});		
	
}); 