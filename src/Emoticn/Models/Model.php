<?php
namespace Emoticn\Models;

use Emoticn\Environment;

abstract class Model {
	/** @var Environment */
	protected $env;
	
	public function __construct(Environment $env){
		$this->env	= $env;
	}
};
