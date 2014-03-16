<?php
namespace Emoticn\Controllers;

use Emoticn\Environment;

abstract class Controller {
	/** @var Environment */
	protected $env;
	protected $templateBase;
	
	public function __construct(Environment $env){
		$this->env	= $env;
	}

	protected function render($file, array $params = null){
		$loader	= new \Twig_Loader_Filesystem($this->env['paths.templates']);
		$twig	= new \Twig_Environment($loader, [
			'cache'			=> $this->env['paths.cache'],
			'auto_reload'	=> true,
		]);

		$file	= (isset($this->templateBase) ? $this->templateBase.'/' : '').$file.'.tpl.html';

		echo $twig->render($file, array_merge([
			'env'		=> $this->env,
			'config'	=> $this->env->getConfig(),
			'www'		=> $this->env['urls.root'],
		], (array)$params));
	}


	final public static function route(Environment $env, $uri, $server){
		$router	= (new \Aura\Router\RouterFactory())->newInstance();

		$routes	= json_decode(file_get_contents($env['paths.src'].'/routes.json'));
		foreach($routes as $route => $target){
			$router->add($target, $route);
		}

		if($uri === ''){
			$uri = '/';
		}
		$route	= $router->match($uri, $server);
		if(!$route){
			$controller	= 'Error';
			$action		= '404';
			$params		= [];

		} else {
			$params	= $route->params;

			$controller	= $params['controller'];
			unset($params['controller']);
			$action	= $params['action'];
			unset($params['action']);
		}

		$class	= '\\Emoticn\\Controllers\\'.$controller.'Controller';

		try {
			$instance	= new $class($env);
			return $instance->{'action'.$action}($params);
		} catch(\Exception $e){
			$instance	= new ErrorController($env);
			$instance->action500([
				'error'	=> $e,
			]);
		}
	}
};
