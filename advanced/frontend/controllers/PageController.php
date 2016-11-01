<?php

namespace frontend\controllers;

use app\models\TemplateQuery;
use frontend\models\Pages;

class PageController extends \yii\web\Controller
{
    public function actionIndex($uri = 'home')
    {
//		$globalPage = TemplateQuery::getPageByURI('');
//		$globalFields = TemplateQuery::findAll(['page_id' => $globalPage->id]);
		$templates = TemplateQuery::getPageByURI($uri);
		$page = Pages::findOne(array('url' => $uri));
//		print_r($page); die();
//		$pageFields = TemplateQuery::findAll(['page_id' => $page->id]);
		$tree = array(); // all child items. Key - parent id
		$ids = array(); $request = array(); $pageRequest = array();
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
			}
			// insert all custom fields into params
			if('customfield' == $f->className) {
				$this->view->params[$f->name] = $f->value;
			}
		}
		$menus = array();
		foreach ($tree as $menu) {
			$menus[$menu['parent']->name] = $menu;
		}
//		print_r($page); die();
		$this->view->params['menus'] = $menus;
//		print_r($templates);
//		print_r($this->view->params); die();
        return $this->render($page->template, [
			'logo' => 'My Pony',
//			'menus' => $menus,
			]);
    }

}
