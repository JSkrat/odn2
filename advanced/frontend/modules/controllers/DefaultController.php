<?php

namespace frontend\modules\controllers;

use yii\web\Controller;

/**
 * Default controller for the `gallery` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
	 * $value - path to the directory with images to show
     * @return string
     */
    public function actionIndex($value)
    {		
        return $this->render('index');
    }
	
	public function actionImages($value) {
		$path = $value;
		if ('/' != $path[strlen($path)-1]) { $path .= '/'; }
		$ret = [];
		foreach (scandir($path) as $f) {
			$mime = mime_content_type($path . $f);
			$thumb = $path . 'thumbs/' . $f;
			if ('image' != explode('/', $mime)[0]) { continue; }
			$file = [
				'mime'		=> $mime,
				'filename'	=> $f,
				'fullname'	=> $path . $f,
				'thumb'		=> (file_exists($thumb))? $thumb: $path . $f
			];
			$ret[] = $file;
		}
		return $this->render('images', ['list' => $ret]);
	}
}
