/*
Slider View
Author: Filip Arneric
*/
define(['app',
		'pxloader',
		'text!../../templates/hbs/pageSlider.hbs', 
		], function(App, PxLoader, Template) {

	App.Views.Slider = Backbone.View.extend({
	
		template: Handlebars.compile(Template),
		manage: true,
		
		
		
		beforeRender: function(){
			console.log(App.layout.dataCollection[0].page);
		},
		
		afterRender: function(){
		},
		
		serialize: function() {
		    return {
		      collection: App.layout.dataCollection[0].page,
		      test: 'test'
		    };
		}
	});		
	
}); 