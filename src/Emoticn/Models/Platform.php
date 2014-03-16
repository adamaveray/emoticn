<?php
namespace Emoticn\Models;

use Emoticn\Environment;

class Platform extends Model {
	protected $id;
	protected $name;
	protected $image;
	protected $description;
	protected $company;
	protected $year;
	
	public function __construct(Environment $env, $id){
		parent::__construct($env);
		
		$this->id	= $id;
		
		if(!$this->load()){
			throw new \RuntimeException('Cannot load platform "'.$this->id.'"');
		}
	}
	
	protected function load(){
		$file	= $this->env['paths.platforms'].'/'.$this->id.'.json';
		if(!file_exists($file)){
			return false;
		}
		
		$content	= json_decode(file_get_contents($file), true);
		if(!isset($content)){
			return false;
		}
		
		$this->name			= $content['name'];
		$this->image		= $content['image'];
		$this->description	= $content['description'];
		$this->company		= $content['company'];
		$this->year			= $content['year'];

		$emoticons	= [];
		foreach($content['emoticons'] as $id => $details){
			$emoticons[$id]	= new Emoticon($this->env, $id, $this, $details);
		}
		$this->emoticons	= $emoticons;
		
		return true;
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getDir(){
		return $this->env['paths.platforms'].'/'.$this->id;
	}

	public function getPath(){
		return $this->env['paths.platformIcons'].'/'.$this->image;
	}

	public function getImageURL(){
		return $this->env['urls.platformIcons'].'/'.$this->image;
	}

	public function getEmoticons(){
		return $this->emoticons;
	}
	
	public function getEmoticon($id){
		$emoticons	= $this->getEmoticons();
		if(!isset($emoticons[$id])){
			return null;
		}

		return $emoticons[$id];
	}
	
	public function getEmoticonWithShortcut($shortcut){
		foreach($this->getEmoticons() as $emoticon){
			if($emoticon->hasShortcut($shortcut)){
				return $emoticon;
			}
		}
		
		return null;
	}

	public function getURL(){
		return $this->env['urls.www'].'/'.urlencode($this->getID());
	}

	public function getDescription(){
		return $this->description;
	}

	public function getCompany(){
		return $this->company;
	}

	public function getYear(){
		return $this->year;
	}
};
