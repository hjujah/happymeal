<?php namespace Vizioart\Cookbook\Models;

use Vizioart\Cookbook\Models\DB\MenuDBModel as MenuDB;
use Vizioart\Cookbook\Models\DB\MenuItemDBModel as MenuItemDB;


class MenuModel extends MenuDB {

	public function get_by_slug($slug, $lang_code = null){
		if(empty($slug)){
			return false;
		}

		if(empty($lang_code)){
			$lang_code = 'cs';
		}

		$query = self::query();
		$query->with(array('items' => function($query) use(&$lang_code){
			$query->where('language_id', '=', self::get_lang_by_code($lang_code));
			$query->orderBy('order', 'asc');
		}));

		$query->where('slug', '=', $slug);

		$menu = $query->first();

		if(empty($menu)){
			return false;
		}else{
			return $menu;
		}
	}

	public function get_all($lang_code = null){
		if(empty($lang_code)){
			$lang_code = 'cs';
		}

		$query = self::query();
		$query->with(array('items' => function($query) use(&$lang_code){
			$query->where('language_id', '=', self::get_lang_by_code($lang_code));
			$query->orderBy('menu_items.order', 'asc');
		}));

		$menus = $query->get();

		if(empty($menus)){
			return false;
		}else{
			return $menus;
		}
	}

	private static function get_lang_by_code($code){
        $language = array(
            'cs' => 1,
            'en' => 2,
            'ru' => 3
        );
        if (array_key_exists($code, $language)){
            return $language[$code];
        } else {
            return 0;
        }
    }
}