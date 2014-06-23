define([
    'app',
    'main',
       
    //layouts
    'layouts/page',
    //.....
    
    'router',
], function (App) {
	
    App.Collections.Setup.fetch({
        success: function() {
        	
        	//page list and config
        	App.Setup = App.Collections.Setup.toJSON()[0];
        	App.Routes = App.Setup.routes;
        	App.Translations = App.Setup.translations;
        	App.Languages = App.Setup.languages;
        			    
		    //init main and router
		    App.Main = new App.Views.Main;
		    App.Router = new App.Router;
        	
        }
    });	

});