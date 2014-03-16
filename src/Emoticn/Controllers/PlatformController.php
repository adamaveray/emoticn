<?php
namespace Emoticn\Controllers;

use Emoticn\Environment;
use Emoticn\Models\Platform;

class PlatformController extends Controller {
	public function actionIndex($params){
		$platform	= new Platform($this->env, $params['platform']);

		$this->render('platform', [
			'platform'	=> $platform,
		]);
	}

	public function actionEmoticon($params){
		$platform	= new Platform($this->env, $params['platform']);
		$emoticon	= $platform->getEmoticonWithShortcut($params['emoticon']);

		$this->render('emoticon', [
			'platform'	=> $platform,
			'emoticon'	=> $emoticon,
		]);
	}
};
