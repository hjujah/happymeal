//handlebars helpers
define([
    'app'
], function (App) {	
		    	            
    Handlebars.registerHelper('if_eq', function(context, options) {
        if (context == options.hash.compare) return options.fn(this);
        return options.inverse(this);
    });

    Handlebars.registerHelper('unless_eq', function(context, options) {
        if (context == options.hash.compare) return options.inverse(this);
        return options.fn(this);
    });

    Handlebars.registerHelper('times', function(n, block) {
        var accum = '';
        for (var i = 1; i <= n; ++i)
        accum += block.fn(i);
        return accum;
    });
    
    Handlebars.registerHelper('include', function(templatename, options){ 
        var partial = Handlebars.partials[templatename];
        var additional_data = options.hash || {};
        var context = $.extend({}, this, additional_data);
        return new Handlebars.SafeString(partial(context));
    });
    
    Handlebars.registerHelper('plusone', function(context){
     	return ++context;
    });

    Handlebars.registerHelper('locale', function() {
        return App.options.locale
    });
    
    Handlebars.registerHelper('getProp', function(context, options){
      if (context) return context[options + '_' + App.lang];
    });   
    

    Handlebars.registerHelper('formatDate', function(context){
        return context && context.replace(/-/g, "—");
    });
    
    Handlebars.registerHelper('translation', function(context){
        return new Handlebars.SafeString(App.Translations[App.lang][context]);
    });
                
    Handlebars.registerHelper('baseUrl', function() {
        return absurl
    });

});