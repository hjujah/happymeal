/*
Slider View
Author: Filip Arneric
*/
define(['app',
		'pxloader',
		'text!../../templates/hbs/home.hbs', 
		], function(App, PxLoader, Template) {

	App.Views.Home = Backbone.View.extend({
		el: '#main',
		template: Handlebars.compile(Template),
		/* manage: true, */
		
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
			//console.log(self.dataCollection[0].page);
		},
		
		afterRender: function(){
		},
		
		render: function(callback) {
        	var self = this;    
        		
        	//compile template
            self.markup = self.template({
                data: self.dataCollection[0].languages,
                test: 'test',
            }); 
            
           	self.$el.html(self.markup);  
            
            callback && callback();
        },
		
		/*
serialize: function() {
			var self = this;
			console.log(self.dataCollection[0]);
		    return {
		      collection: self.dataCollection[0].languages,
		      test: 'test'
		    };
		}
*/
	});		
	
}); 