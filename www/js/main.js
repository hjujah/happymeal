/*
Dock - Main View
Copyright Kitchen S.R.O. May 2013. 
Author: Filip Arneric
*/

define(['app', 
		'pxloader', 
		'tweenmax',
		'smartresize',
		'imgLiquid',
		'fittext', 
		'fastclick', 
		'helpers'], function(App, PxLoader) {
	
	 App.Views.Main = Backbone.View.extend({   
	    el: 'body',
	    
	    events: {
	        'click .links' : 'changeRoute',
	        'click .langLink' : 'changeLang'
	    }, 
	    
	    setSizes: function(){
	    	var self = this;    		    	
		    App.width = $(window).width();
		    App.height = window.innerHeight || $(window).height();
		    if(self.view)  self.view.handleResize && self.view.handleResize();
		},	
		       
        destroy_view: function() {     	
		    this.view.undelegateEvents();
		    this.view.$el.removeData().unbind();	    
		    App.Common.destroy_view();
		    this.view.destroy_view && this.view.destroy_view();	    		    
	    },

		render: function(){
			var self = this;
			var	$navbar = $(".navbar-collapse"),
				angle = (self.view && self.view.angle) ? self.view.angle + 90 : '90';

			self.$el.removeAttr("class").addClass(App.options.lang);
			//App.Views.navigation.setActiveNav();
	        //$navbar.hasClass("in") && $navbar.collapse('hide');
	        
			self.view && self.destroy_view();
			self.define_fetch();
			   
		}, 
		
		define_fetch: function(){
			var self = this;
			
			//destroy the previous view
			App.view && self.destroy_view();
			
			//define view and collection then render
			App.view = /* App.Views[App.options.view]  */new App.Views.Map;

			 //fetch the data if needed if not render page
		    if(App.view.getFetchURL){
		    		    	
			  	App.Collections[App.options.view] = Backbone.Collection.extend({
			        url: /* App.options.fetchUrl */ App.view.getFetchURL()
			    });	    
			      
			    App.view.collection = new App.Collections[App.options.view];	
				App.view.collection.fetch({
	                reset: true,
	                success: function() {
	                	App.view.dataCollection = App.view.collection.toJSON();
	                    self.renderPage();
	                }
	            });
	            
		    }else{
			    self.renderPage();
		    }
		   			
		},
		
		renderPage: function(){
	    	var self = this;
	    
	    	//preload and render view
		  	App.loader = new PxLoader();
		  	
		  	App.view.preload(function(){
		  		App.view.render();
		  			
		  	});
		 
		},

   
	    changeRoute: function(e) {
	    	e.preventDefault();
	    	var self = this,
    			target = $(e.currentTarget),
    			href = target.attr("href").replace(absurl,'');
			_gaq.push(['_trackPageview', escape(href)]);
        	App.Router.navigate(href, true);
	    },
	    
	    changeLang: function(e){
	    	e.preventDefault();
	    	var lang = $(e.currentTarget).data("lang"),
	    		cur = (window.location.pathname),
	    		newPage = cur.replace(App.options.lang, lang),
	    		href = (_.filter(App.Routes, function(data){ return data.url == href.substr(1) })[0]) ? href 
	    			: lang + "/" + App.pageName;

	    	App.Router.navigate(href, true);	  
	    	App.Views.navigation.render(); 	    	
	    },
	    
	    initialize: function(){	    	
	    	var self = this;
	    	
	    	self.setSizes();
	    	
	    	
	    	//handle resize
            $(window).on('smartresize', function() {
            	self.setSizes();
            });
            
            //secret msg
            $( window ).konami({
		        cheat: function() {
		        	alert("hjuston master");
		        }
		    });
	  
	    }
	
	});		
	
})