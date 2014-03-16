<?php
require_once(__DIR__.'/../../vendor/autoload.php');

// Load config
$config	= json_decode(file_get_contents(__DIR__.'/config.json'), true);
array_walk($config['paths'], function($path){
	// Make relative paths absolute
	return (($path === '' || $path[0] !== '/') ? __DIR__.'/' : '').$path;
});

$env	= new Emoticn\Environment($config);

\Emoticn\Controllers\Controller::route($env, rtrim($_SERVER['REQUEST_URI'], '/'), $_SERVER);
