<?php

namespace frontend\controllers;

use frontend\models\ObjectQuery;
use frontend\models\Pages;
use frontend\models\Tags;
//use frontend\modules\Gallery;

class PageController extends \yii\web\Controller
{
	function __construct($id, $module, $config = array()) {
		parent::__construct($id, $module, $config);
//		$this->page = null;
	}
//	public $defaultAction = 'index';
	
//	private function 
	
    public function actionIndex($uri = 'home')
    {
		// search for children pages if it is a category page
		// query will be executed in template itself, if it really needs them
		$page = Pages::findOne(array('url' => $uri));
		$childPages = Tags::getPages('category:' . $page->id);
//		print_r($page); die();
		$tree = []; // all child items. Key - parent id
		$ids = []; $request = [];
		$objectsByName = [];
		$moduleObjects = []; // for modules
		foreach (ObjectQuery::getPageByURI($uri, true) as $f) {
			$ids[$f->id] = $f;
			if (isset($request[$f->id])) {
				$request[$f->id] = $f; // that is a pointer to some element in the $tree, so we're putting it into $tree
				unset($request[$f->id]); // remove request from query
			}
			// construct one-layer menu (just insert menuitems and objects menuitems linked to to their parents)
			if ('menuitemtemplate' == $f->className) {
				if (isset($ids[$f->link])) {
					// link object already passed and in $ids, just create as usual
					$item = array('menuitem' => $f, 'link' => $ids[$f->link]);
				} else {
					$item = array('menuitem' => $f, 'link' => false);
					// request that link to be filled when we reach that object
					// TODO: will not work if we have two links to the same object
					$request[$f->link] = &$item['link'];
				}
				if (isset($tree[$f->parent])) {
					$tree[$f->parent]['children'][$f->id] = $item;
				} else {
					$tree[$f->parent] = array(['children' => [$f->id => $item]]);
				}
			} elseif ('menuitempage' == $f->className) {
				// find method caches requests, so only one request per page would be executed
				// this is bad: we should get all pages by only one request!
				$item = array('menuitem' => $f, 'link' => Pages::findOne($f->link));
				if (isset($tree[$f->parent])) {
					$tree[$f->parent]['children'][$f->id] = $item;
				} else {
					$tree[$f->parent] = array(['children' => [$f->id => $item]]);
				}
			} elseif ('menu' == $f->className) {
				if (isset($tree[$f->id])) {
					$tree[$f->id]['parent'] = $f;
				} else {
					$tree[$f->id] = array('parent' => $f, 'children' => array());
				}
			} else {
				// for all other objects
				if (isset($f->name)) {
					if (isset($f->module) && ! empty($f->module)) {
						$moduleName = explode(':', $f->module); if (! isset($moduleName[1])) { $moduleName[1] = ''; }
						$moduleFullName = '\frontend\modules\\' . $moduleName[0];
						// TODO: i believe here is wrong usage of creating module, rewrite when we need more than 1 call per page
						$module = new $moduleFullName ($moduleName[0]);
						$f->value = $module->runAction($moduleName[1], ['value' => $f->value]);
					} 
					$objectsByName[$f->name] = $f;
					if (isset($f->value)) {
						$this->view->params[$f->name] = $f->value;
					} else {
						$this->view->params[$f->name] = $f;
					}
				}
			}
		}
		$menus = array();
		foreach ($tree as $menu) {
			$menus[$menu['parent']->name] = $menu;
		}
		// for layout view
		$this->view->params['menus'] = $menus;
		// custom blocks
		// TODO: can i integrate that into one request?
		$this->view->params['lastPages'] = Pages::find()->orderBy('created desc')->limit(5)->all();
		$this->view->params['popularPages'] = Pages::find()->orderBy('views desc')->limit(5)->all();

        return $this->render($page->template->name, [
			'logo' => 'My Pony',
			'objects' => $objectsByName,
			'childPages' => $childPages,
			]);
    }

	public function actionNya() {
		return 'Няя!';
	}
}
