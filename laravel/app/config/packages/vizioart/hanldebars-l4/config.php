<?php

return array(

	// A cache directory for compiled templates. Mustache will not cache templates unless this is set.
    //'cache' => storage_path() . '/cache/views/handlebars',

    // A Mustache loader instance for partials. If none is specified, defaults to an ArrayLoader for the supplied
    // partials option, if present, and falls back to the specified template loader.
    'partials_loader' => App::make('Vizioart\HandlebarsL4\FilesystemLoader'),

    // An array of 'helpers'. Helpers can be global variables or objects, closures (e.g. for higher order sections), 
    // or any other valid Mustache context value. They will be prepended to the context stack, 
    // so they will be available in any template loaded by this Mustache instance.
    'helpers' => array(
        'lang' => function($template, $context, $args, $source){
            //return App::getLocale();
            return 'cs';
        },
        'baseUrl' => function($template, $context, $args, $source){
            return url();
        },
        'getProp' => function($template, $context, $args, $source){

            preg_match("/(.*?)\s+(?:(?:\"|\')(.*?)(?:\"|\'))/", $args, $m);
            
            $data_key = $m[1];
            $data_prop = $m[2];

            // get data
            $data = $context->get($data_key);
            if ($data){
                $prop_key = $data_prop . '_en';
                if (isset($data[$prop_key])){
                    return $data[$prop_key];
                }
            }
        },
    ),
    
    // An 'escape' callback, responsible for escaping double-mustache variables. Defaults to htmlspecialchars.
    // 'escape' => function($value) {
    //     return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    // },    

);