<?php

namespace frontend\controllers;

use frontend\models\ObjectQuery;
use frontend\models\Pages;
use frontend\models\Tags;

class PageController extends \yii\web\Controller
{
//	public $defaultAction = 'index';
	
    public function actionIndex($uri = 'home')
    {
		$templates = ObjectQuery::getPageByURI($uri);
		$page = Pages::findOne(array('url' => $uri));
		// search for children pages if it is a category page
		// query will be executed in template itself, if it really needs them
		$childPages = Tags::getPages('category:' . $page->id);
//		print_r($page); die();
		$tree = array(); // all child items. Key - parent id
		$ids = array(); $request = array(); $pageRequest = array();
		$templatesByName = array();
		foreach ($templates as $f) {
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
				// for all other templates
				if (isset($f->name)) {
					$templatesByName[$f->name] = $f;
					$this->view->params[$f->name] = $f->value;
				}
			}
		}
		$menus = array();
		foreach ($tree as $menu) {
			$menus[$menu['parent']->name] = $menu;
		}
//		print_r($page); die();
		// for layout view
		$this->view->params['menus'] = $menus;
//		print_r($templates);
//		print_r($page->template0); die();
//		print_r($this->view->params); die();
        return $this->render($page->template->name, [
			'logo' => 'My Pony',
			'templates' => $templatesByName,
			'childPages' => $childPages,
			]);
    }

	public function actionNya() {
		return 'Няя!';
	}
}
