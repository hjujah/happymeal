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
        'if_eq' => function($template, $context, $args, $source){

            $_args = explode(' ', $args);

            $data_key = trim($_args[0]);
            $compare_arg = $_args[1];
            $compare_to = false;

            if (strpos($compare_arg, '\'') || strpos($compare_arg, '\'')){
                preg_match("/(.*?)=(?:(?:\"|\')(.*?)(?:\"|\'))/", $compare_arg, $m);
                $compare_to = $m[2];
            } else {
                $_compare_args = explode('=', $compare_arg);
                $compare_to = $_compare_args[1];

                if ('@index' == $compare_to){
                    $compare_to = $context->lastIndex();
                } elseif ('@key' == $compare_to) {
                    $compare_to = $this->lastKey();
                } elseif ($d = $context->get($compare_to)) {
                    $compare_to = $d;
                }
            }

            $condition = ($context->get($data_key) == $compare_to);

            if ($condition) {
                $template->setStopToken('else');
                $buffer = $template->render($context);
                $template->setStopToken(false);
            } else {
                $template->setStopToken('else');
                $template->discard();
                $template->setStopToken(false);
                $buffer = $template->render($context);
            }
            return $buffer;
        },

        'unless_eq' => function($template, $context, $args, $source){
            
            $_args = explode(' ', $args);

            $data_key = trim($_args[0]);
            $compare_arg = $_args[1];
            $compare_to = false;

            if (strpos($compare_arg, '\'') || strpos($compare_arg, '\'')){
                preg_match("/(.*?)=(?:(?:\"|\')(.*?)(?:\"|\'))/", $compare_arg, $m);
                $compare_to = $m[2];
            } else {
                $_compare_args = explode('=', $compare_arg);
                $compare_to = $_compare_args[1];

                if ('@index' == $compare_to){
                    $compare_to = $context->lastIndex();
                } elseif ('@key' == $compare_to) {
                    $compare_to = $this->lastKey();
                } elseif ($d = $context->get($compare_to)) {
                    $compare_to = $d;
                }
            }

            $condition = ($context->get($data_key) == $compare_to);

            if (!$condition) {
                $template->setStopToken('else');
                $buffer = $template->render($context);
                $template->setStopToken(false);
            } else {
                $template->setStopToken('else');
                $template->discard();
                $template->setStopToken(false);
                $buffer = $template->render($context);
            }
            return $buffer;
        },

        'lang' => function($template, $context, $args, $source){
            return App::getLocale();
        },
        'baseUrl' => function($template, $context, $args, $source){
            return url();
        },
        'plusOne' => function($template, $context, $args, $source){
            // preg_match("/(.*?)\s+(?:(?:\"|\')(.*?)(?:\"|\'))/", $args, $m);
            // $data_key = $m[1];
            $data = $context->get($args);
            if ($data && intval($data)){
                return ++$data;
            }
        },
        'formatDate' => function($template, $context, $args, $source){

            $date_str = trim($args);

            if ($d = $context->get($args)) {
                $date_str = $d;
            }
            
            if ($date = strtotime($date_str)){
                $formated = sprintf('%1$s&ndash;%2$s&ndash;%3$s',
                    date('d', $date),
                    date('m', $date),
                    date('Y', $date)
                );
                return $formated;
            }
            //return 'Whoops, invalid date';
        },
        'translation' => function($template, $context, $args, $source){
            $str = trim($args, " \'\"");
            if (!empty($str)){
                $key = 'dock.'.$str;
                $translation = Lang::get($key);
                return $translation;
            }
        },
        'getProp' => function($template, $context, $args, $source){

            preg_match("/(.*?)\s+(?:(?:\"|\')(.*?)(?:\"|\'))/", $args, $m);
            $data_key = $m[1];
            $data_prop = $m[2];
            // get data
            $data = $context->get($data_key);
            if ($data){
                $prop_key = $data_prop . '_' . App::getLocale();

                if (is_object($data) && property_exists($data, $prop_key)){
                    return $data->$prop_key;
                } elseif (is_array($data) && isset($data[$prop_key])){
                    return $data[$prop_key];
                }
            }
        },
        'makeAllSides' => function($template, $context, $args, $source){
            $html = '<div class="left-pane side setSize hiddenBackface"></div><div class="right-pane side hiddenBackface setSize"></div><div class="back-pane side setSize hiddenBackface"></div>';
            if (BrowserDetect::isIE()){
                $html = '<div class="back-pane hiddenBackface side setSize"></div>';   
            }
            return $html;
        },
        'makeSides' => function($template, $context, $args, $source){
            $html =  '<div class="left-pane side setSize hiddenBackface"></div><div class="right-pane side setSize hiddenBackface"></div>';
            if (BrowserDetect::isIE()){
                $html = '<div class="back-pane side setSize"></div>';  
            }
            return $html;
        },
        'include' => function($template, $context, $args, $source){
            
            $parial = false;
            $partial_data = array();

            if(!empty($args)){
                $_args = explode(' ', $args);
                $partial = array_shift($_args);
                $partial = trim($partial, " \'\"");

                if (!empty($_args)){
                    foreach ($_args as $arg) {
                        if (strpos($arg,'=')){

                            $re = '/(\w+)=["\']([^"\'<>]+)["\']/';
                            if (preg_match($re, $arg, $m)) {
                                $partial_data[$m[1]] = $m[2];
                            }

                        } else if ($d = $context->get($arg)){
                            $partial_data[$arg] = $d;
                        }
                    }
                }
            }

            if ($partial){
                
               
                if ($data = $context->get('data')){
                    $partial_data['data'] = $data;
                }
                
                $view = View::make('hbs::'.$partial, $partial_data); 
                return $view;  
            }
            return '';
        },
        'times' => function($template, $context, $args, $source){
            $buffer = $template->render($context);
            return str_repeat($buffer, intval($args));
        },

    ),
    
    // An 'escape' callback, responsible for escaping double-mustache variables. Defaults to htmlspecialchars.
    // 'escape' => function($value) {
    //     return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    // },    

);