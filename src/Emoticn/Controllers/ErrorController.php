<?php
namespace Emoticn\Controllers;

use Emoticn\Environment;

class ErrorController extends Controller {
	public function action404($params){
		header('HTTP/1.0 404 Not Found');

		$this->render('404', $params);
	}

	public function action500($params){
		header('HTTP/1.0 500 Server Error');

		$this->render('500', $params);
	}
};
