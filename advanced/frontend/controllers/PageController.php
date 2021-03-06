<?php

namespace frontend\controllers;

use frontend\models\ObjectQuery;
use frontend\models\Pages;
use frontend\models\Tags;
//use frontend\modules\Gallery;
use yii\web\HttpException;

class PageController extends \yii\web\Controller
{
	function __construct($id, $module, $config = array()) {
		parent::__construct($id, $module, $config);
//		$this->page = null;
	}
//	public $defaultAction = 'index';
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
		];
	}
	
    public function actionIndex($uri = 'home')
    {
		// search for children pages if it is a category page
		// query will be executed in template itself, if it really needs them
		$page = Pages::findOne(array('url' => $uri));
//		print_r($page); die();
		$ret = ObjectQuery::constructMenusArrangeObjects(ObjectQuery::getPageByURI($uri, true));
		foreach ($ret['objectsByName'] as $f) {
			if (isset($f->value)) {
				$this->view->params[$f->name] = $f->value;
			} else {
				$this->view->params[$f->name] = $f;
			}
		}
		// for layout view
		$this->view->params['menus'] = $ret['menus'];
		// custom blocks
		// TODO: can i integrate that into one request?
		$this->view->params['lastPages'] = Pages::find()->orderBy('created desc')->limit(5)->all();
		$this->view->params['popularPages'] = Pages::find()->orderBy('views desc')->limit(5)->all();
		if (! $page) {
			throw new HttpException(404, 'Page not found');
		}
		$childPages = Tags::getPages('category:' . $page->id);
        return $this->render($page->template->name, [
			'logo' => 'My Pony',
			'objects' => $ret['objectsByName'],
			'childPages' => $childPages,
			]);
    }

	public function actionNya() {
		return 'Няя!';
	}
}
