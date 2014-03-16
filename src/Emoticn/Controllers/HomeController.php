<?php
namespace Emoticn\Controllers;

use Emoticn\Environment;
use Emoticn\Models\Platform;

class HomeController extends Controller {
	public function actionIndex($params){
		$this->render('index', [
			'platforms'	=> $this->getAllPlatforms(),
		]);
	}

	public function actionContribute($params){
		$this->render('contribute');
	}

	/**
	 * @return Platform[]
	 */
	protected function getAllPlatforms(){
		$platforms	= [];
		foreach(glob($this->env['paths.platforms'].'/*.json') as $path){
			try {
				$id	= basename($path, '.'.pathinfo($path, \PATHINFO_EXTENSION));
				$platforms[$id]	= new Platform($this->env, $id);

			} catch(\Exception $e){
				// Ignore
				continue;
			}
		}

		return $platforms;
	}
};
