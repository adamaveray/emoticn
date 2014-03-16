<?php
namespace Emoticn;

class Environment implements \ArrayAccess {
	protected $config;
	
	public function __construct($config){
		$this->config	= $config;
	}
	

	public function getConfig(){
		return $this->config;
	}


	public function offsetExists($offset){
		return ($this->offsetGet($offset) !== null);
	}

	public function offsetGet($offset){
		$level	= $this->config;
		
		foreach(explode('.', $offset) as $step){
			if(!isset($level[$step])){
				// Not found
				return null;
			}
			
			$level	= $level[$step];
		}
		
		return $level;
	}
	
	public function offsetSet($offset, $value){
		throw new \BadMethodCallException('Cannot modify config');
	}

	public function offsetUnset($offset){
		throw new \BadMethodCallException('Cannot modify config');
	}
};
