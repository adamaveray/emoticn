<?php
namespace Emoticn\Models;

use Emoticn\Environment;

class Emoticon extends Model {
	protected $id;
	protected $platform;
	protected $name;
	
	public function __construct(Environment $env, $id, Platform $platform, array $details){
		parent::__construct($env);
		
		$this->id		= $id;
		$this->platform	= $platform;
		
		$this->name			= $details['name'];
		$this->image		= $details['image'];
		$this->shortcuts	= $details['shortcuts'];
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getPlatform(){
		return $this->platform;
	}

	public function getName(){
		return $this->name;
	}

	public function getPath(){
		return $this->env['paths.platformIcons'].'/'.$this->platform->getID().'/'.$this->image;
	}
	
	public function hasShortcut($shortcut){
		return in_array($shortcut, $this->getShortcuts());
	}

	public function getShortcuts(){
		return $this->shortcuts;
	}

	public function getURL(){
		// Manual URL encode
		$replace	= [
			'#'	=> '%23',
		];

		$shortcut	= current($this->getShortcuts());
		$slug		= str_replace(array_keys($replace), array_values($replace), $shortcut);

		return $this->getPlatform()->getURL().'/'.($slug);
	}

	public function getImageURL(){
		return $this->env['urls.platformIcons'].'/'.$this->platform->getID().'/'.$this->image;
	}

	public function getDominantColor($stripColor = true, $granularity = null){
		return current($this->getDominantColors(1, $stripColor));
	}

	public function getDominantColors($count, $stripColor = true, $granularity = null){
		if(!isset($granularity)){
			$granularity	= 5;
		}
		if($stripColor === true){
			$stripColor	= [
				[
					'red'	=> 255,
					'green'	=> 255,
					'blue'	=> 255,
				],
				[
					'red'	=> 0,
					'green'	=> 0,
					'blue'	=> 0,
				],
			];
		}

		// Granularity must be at least 1
		$granularity	= max(1, abs((int)$granularity));

		$content	= file_get_contents($this->getPath());
		if(!$content){
			throw new \RuntimeException('Cannot read image '.$this->getPath());
		}
		$img		= @imagecreatefromstring($content);
		if($img === false){
			throw new \RuntimeException('Cannot load image');
		}

		$size	= @getimagesize($this->getPath());
		if($size === false){
			throw new \RuntimeException('Cannot get image size');
			return false;
		}

		$colors	= [];
		for($x = 0; $x < $size[0]; $x += $granularity){
			for($y = 0; $y < $size[1]; $y += $granularity){
				$color	= @imagecolorat($img, $x, $y);
				if($color === false){
					continue;
				}

				$rgb	= imagecolorsforindex($img, $color);

				array_walk($rgb, function($color){
					$value	= 0x33;
					return round(round($color / $value) * $value);
				});

				if($stripColor){
					foreach($stripColor as $strippableColor){
						if($rgb['red'] === $strippableColor['red']
						   && $rgb['green'] === $strippableColor['green']
						   && $rgb['blue'] === $strippableColor['blue']){
							// Discard ignored color
							continue 2;
						}
					}
				}

				// Convert to string
				$rgb	= sprintf('%02X%02X%02X', $rgb['red'], $rgb['green'], $rgb['blue']);

				if(isset($colors[$rgb])){
					$colors[$rgb]++;
				} else {
					$colors[$rgb] = 1;
				}
			}
		}

		// Sort by most used
		arsort($colors);

		return array_slice(array_keys($colors), 0, $count);
	}

	public function isLightColor(){
		$color	= $this->getDominantColor();

		// Parse hex
		$r	= hexdec(substr($color,0,2));
		$g	= hexdec(substr($color,2,2));
		$b	= hexdec(substr($color,4,2));

		$yiq	= (($r*299) + ($g*587) + ($b*114)) / 1000;
		return ($yiq >= 128);
	}

	public function isDarkColor(){
		return !$this->isLightColor();
	}
};
