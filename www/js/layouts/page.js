/*
Dock Page Layout
Author: Filip Arneric
*/

define(['app',
		'pxloader',
		'text!../../templates/hbs/page.hbs', 
		'views/slider',
		], function(App, PxLoader, Template) {
	
    App.Layouts.Page = Backbone.Layout.extend({
        el: '#main',
        template: Handlebars.compile(Template),
       
		views: {
			"#slideshow": new App.Views.Slider
		},
		
		getFetchURL: function(){
			return absurl + "/api/page/cs/dockpark";
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
		
		initialize: function() {
            var self = this;
            _.bindAll(this, 'render');
        }
		
    });

}); 