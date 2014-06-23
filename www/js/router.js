/*
Router
Copyright Kitchen S.R.O. September 2013. 
Author: Filip Arneric
*/
define(['app'], function(App) {
			
	//Router
	App.Router = Backbone.Router.extend({
		routes: {
	      "*actions": "render"
   		},
   	 	
   	 	render: function(url){
   	 		var self = this;
   	 		
   	 		//define segments
	   	 	var segments = (url) ? url.split("/") : [];
	   	 	$.each(segments,function(key, value){
		   	 	App.segments[key] = value;
	   	 	});
	   	 	
	   	 	//define curent page
	   	 	self.current_page = _.filter(App.Routes, function(data){ return data.url == url })[0];
	   	 	
	   	 	//define locale
	   	 	var locale =  _.filter(App.Languages, function(data){ return data.code == App.segments[0]});
	   	 	App.options.locale = locale.code || language;
	   	 	
	   	 	//define view
	   	 	App.options.view = ((!App.segments[0] || !App.segments[1]) && locale.length) ? 'home'
	   	 					 : (!self.currentPage) ? 'notFound'
	   	 					 : self.current_page.view;
	   	 	
	   	 	//now render
		    App.Main.render();
	   	 	
   	 	},
		
		initialize: function(){
			var self = this;
			Backbone.history = Backbone.history || new Backbone.History({});
			var root = "/";

			Backbone.history.start({
				pushState: Modernizr.history,
				root: root,
				silent: !Modernizr.history
			}); 
			
			// handle history for old internet explorer + normal behaviour
			if(!Modernizr.history) {
				var rootLength = Backbone.history.options.root.length;
				var fragment = window.location.pathname.substr(rootLength) || 'cs';
				self.navigate(fragment, { trigger: true });
			}
		}
	    
	});

});