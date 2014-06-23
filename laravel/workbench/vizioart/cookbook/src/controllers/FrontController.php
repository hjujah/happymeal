<?php namespace Vizioart\Cookbook;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelBaseController as BaseController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use Vizioart\Cookbook\Models\PostModel as Post;
use Vizioart\Cookbook\Models\GalleryModel as Gallery;
use Vizioart\Cookbook\Models\MenuModel as Menu;

class FrontController extends BaseController {

    protected $route_segments = array();
    
    protected $locale = false;

    public function __construct(){
        $this->locale = App::getLocale();
        $this->route_segments = Request::segments();
    }

    // -------------------------------------------------------------------------

	public function getIndex(){
        return $this->renderNavigation();
	}

    /**
     *
     */
    public function resolveRoute($route = null){
	    // make sure default route is set
        App::setLocale('cs');

        /**
         * when using route like this : Route::get('{var?}', ...)->where('var', '(.*)');
         * var is passed with first forward slash
         */
        $route = ltrim($route, '/');

        // home page (default locale)
        if (empty($route)){
            return $this->renderHomePage();
        }

        if (!empty($this->route_segments)) { 
            
            // check for locale segment
            if (in_array($this->route_segments[0], $this->get_site_locales())) {
                App::setLocale($this->route_segments[0]);
                $this->locale = $this->route_segments[0];
            } else {
                
                /** 
                 * First segment is reserved for locale
                 * If its not locale string return 404
                 *
                 * @TO_DO try to return more helpful response.
                 */
                App::abort(404);
            }
        }

        if (count($this->route_segments) == 1){
            // home page in requested locale
			return $this->renderHomePage();
        }

        // try to get page by url 
        $model = new Post();
        $post = $model->get_by_url($route);

        
        if ($post){
            return $this->renderPage($post);
        }

        // -----------------------------------------------
        //  Parameterized Pages 
        // -----------------------------------------------

        $route_segments = $this->route_segments;
        $popped_route_segments = array();
        $searched_urls = array();
        $found_url = '';
        $tries = 0;

        while (!$post && count($route_segments)>2 && $tries < 3) {
            $tries++;
            array_push($popped_route_segments, array_pop($route_segments));
            $url = implode('/', $route_segments);
            $searched_urls[] = $url;
            $post = $model->get_by_url($url);

            if ($post){
                $found_url = $url;
            } 
        }
        // get params after found page url
        $page_params = array_reverse($popped_route_segments);

        if ($post){
            return $this->renderPage($post, $page_params);
        }

        // no result, do 404
        // -----------------------------------------------
        App::abort(404);
    }

    // ------------------------------------------------------------------------------------

    protected function renderPage($post, $params = null){
        
        $post_type = $post->type;
        $post_view = $post->view;

        // @CHANGE
        // --------------------------------------
        switch ($post->view) {
            case 'gallery':
                return $this->renderGalleryItemPage($post, $params);
            
            case 'navigator':
                return $this->renderNavigatorPage($post, $params);

        }
        // --------------------------------------

        $view_data = array();

        // meta
        // --------------------------------------
        $meta_data = $this->setMetaData($post);
        $view_data = array_merge($view_data, $meta_data);

        // navigation
        // --------------------------------------
        $view_data['navigation'] = $this->renderNavigation();

        // template
        // --------------------------------------
        $template_data = $this->setPageData($post, $params);

        // page template (handlebars)
        $content = View::make('hbs::'.$post_view, $template_data);
        $view_data['content'] = $content;

        return View::make('layouts.application', $view_data);
    }

    protected function renderNavigation(){

        $model = new Menu();
        $menus = $model->get_all(app::getLocale());

        $template_data = array(
            'data' => json_decode($menus->toJson()),
        );

        return View::make('hbs::navigation', $template_data);

    }

    // ------------------------------------------------------------------------------------

    protected function renderGalleryItemPage($post, $params){
    
        $view_data = array();
        
        // get gallery item
        $model = new Gallery();
        $active_item = $model->get_item_by_id($params[0]);

        // next / prev
        $post->next = $post->galleries[0]->items->filter(function($item) use($active_item) {
            return $item->id > $active_item->id;
        })->first();
        $post->prev = $post->galleries[0]->items->filter(function($item) use($active_item) {
            return $item->id < $active_item->id;
        })->first();
        
        $add_data = array(
            'pageParams' => $params,
            'active_item' => $active_item
        );
                
        // --------------------------------------
        $meta_data = $this->setMetaData($post);
        $view_data = array_merge($view_data, $meta_data);

        // navigation
        // --------------------------------------
        $view_data['navigation'] = $this->renderNavigation();

        // --------------------------------------
        $template_data = $this->setPageData($post, $add_data);

        // page template (handlebars)
        $content = View::make('hbs::photo', $template_data);
        $view_data['content'] = $content;

        return View::make('layouts.application', $view_data);
    }

    protected function renderNavigatorPage($post, $params = array()){


        $view_data = array();

        $add_data = array(
            'pageParams' => $params,
        );
        
        // --------------------------------------
        $meta_data = $this->setMetaData($post);
        $view_data = array_merge($view_data, $meta_data);

        // navigation
        // --------------------------------------
        $view_data['navigation'] = $this->renderNavigation();

        // --------------------------------------
        $template_data = $this->setPageData($post, $add_data);

        // page template (handlebars)
        $content = View::make('hbs::navigator', $template_data);
        $view_data['content'] = $content;

        return View::make('layouts.application', $view_data);
    }

    protected function renderPhase1($post){
    
        $view_data = array();
        
        $add_data = array(
            'phase1' => 'marinaview'
        );
        
        // --------------------------------------
        $meta_data = $this->setMetaData($post);
        $view_data = array_merge($view_data, $meta_data);

        // navigation
        // --------------------------------------
        $view_data['navigation'] = $this->renderNavigation();

        // --------------------------------------
        $template_data = $this->setPageData($post, $add_data);

        // page template (handlebars)
        $content = View::make('hbs::list', $template_data);
        $view_data['content'] = $content;

        return View::make('layouts.application', $view_data);
    }
     
    protected function renderHomePage(){
    
        $view_data = array();
        
        // --------------------------------------
        $meta_data = $this->setMetaData();
        $view_data = array_merge($view_data, $meta_data);

        // navigation
        // --------------------------------------
        $view_data['navigation'] = $this->renderNavigation();

        // --------------------------------------
        $template_data = $this->setPageData();
        
        // page template (handlebars)
        $ie = \BrowserDetect::isIE();
		$ie11 = \BrowserDetect::isIEVersion(11);
        
        $tmpl = ($ie) ? 'hbs::homeIE' : 'hbs::home';
        
        $content = View::make($tmpl, $template_data);
        $view_data['content'] = $content;

        return View::make('layouts.application', $view_data);
    }
    
    
    // ------------------------------------------------------------------------------------

    protected function setMetaData($post = null){

        // default meta
        $default_meta = array(
            'meta_title' => 'Dock by Crestyl',
            'meta_description' => 'Dock by Crestyl',
        );

        $default_og_meta = array(
            'og_title' => 'Dock by Crestyl',
            'og_description' => 'Dock by Crestyl',
            'og_type' => 'website',
            'og_site_name' => 'Dock',
            'og_image' => 'http://www.dock.cz/img/ogLogo.jpg',
            'og_locale' => 'cs_CZ',
        );

        // merge
        $meta_data = array_merge($default_meta, $default_og_meta);

        // helper array
        $og_locale_arr = array(
            'cs' => 'cs_CZ',
            'en' => 'en_EN',
            'ru' => 'ru_RU',
        );

        if ($post){

            $meta_data['meta_title'] = $this->createTitle($post->title);
            $meta_data['og_title'] = $meta_data['meta_title'];

            if (isset($post->meta)){
                if (isset($post->meta['meta_title']) && !empty($post->meta['meta_title'])){
                    $meta_data['meta_title'] = $this->createTitle($post->meta['meta_title']);                    
                }

                if (isset($post->meta['meta_description']) && !empty($post->meta['meta_description'])){
                    $meta_data['meta_description'] = $post->meta['meta_description'];           
                }


                if (isset($post->meta['og_title']) && !empty($post->meta['og_title'])){
                    $meta_data['og_title'] = $this->createTitle($post->meta['og_title']);                    
                } else {
                     $meta_data['og_title'] = $meta_data['meta_title'];
                }

                if (isset($post->meta['og_description']) && !empty($post->meta['og_description'])){
                    $meta_data['og_description'] = $post->meta['og_description'];           
                } else {
                     $meta_data['og_description'] = $meta_data['meta_description'];
                }
            }

            if( array_key_exists($this->locale, $og_locale_arr)){
                $meta_data['og_locale'] =  $og_locale_arr[$this->locale];
            }

            if (!empty($post->featured_image) ){
                $meta_data['og_image'] = url('uploads/md_'.$post->featured_image->url);
            }
        }


        return $meta_data;
    }

    protected function setPageData($post = null, $params = array()){

        $post = ($post) ? json_decode($post->toJson()) : null;
        $postView = ($post) ? $post->view : null;
        
        // meke it global so all partials can access it
        View::share('data', $post);

        // security check 
        // $params
        if (empty($params)){
            $params = array();
        }
        
        $template_data = array(
            'data' => $post,
            'viewName' => $postView,
        );

        $page_data = array_merge($template_data, $params);
        return $page_data;
    }

    // ------------------------------------------------------------------------------------

    private function createTitle($page_title = ''){
        $title = 'Dock by Crestyl';
        if (!empty($page_title)){
            $title = 'Dock - ' . $page_title;
        } 
        return $title;
    }

    // ------------------------------------------------------------------------------------

    /** 
     * Wrapper function to get available locales
     *
     * @TO_DO get site locales dynamicaly
     *
     * @return array - list of available locales
     */
    protected function get_site_locales(){
        return array('cs', 'en', 'ru');
    }

}