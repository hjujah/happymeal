<?php

class BaseController extends Controller {


	protected $layout = 'layouts/application';

	protected function setupLayout() {
		if ( ! is_null($this->layout)) {
			$this->layout = View::make($this->layout);
		}
	}



	/**
	* Catch-all method for requests that can't be matched.
	*
	* @param  string    $method
	* @param  array     $parameters
	* @return Response
	*/
	public function __call($method, $parameters) {
		return Response::error('404');
	}

}