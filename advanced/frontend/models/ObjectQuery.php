<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;
use frontend\models\Objects;
use frontend\models\ObjectFields;
use frontend\models\ObjectValues;
use frontend\models\ClassesAllowed;
use frontend\models\Classes;
use frontend\models\Pages;
use frontend\models\PageFields;

class ObjectQuery extends ActiveRecord {
	public $classID; // TODO: create getter instead of public property
	public $class_id; // TODO: create getter instead of public property
	public $className; // TODO: create getter instead of public property
	//public $name;
	public $allFields = array('name');
	public $data = array('name' => '');
	public $types = array('name' => 1); // TODO: create getter instead of public property
	private $labels = array('name' => '[Name]');
	private $required = array();
	private $integer = array();
	private $string = array('name');
	public $id;
	
	/**
	 * define template class to create all fields for instance
	 * @param integer $classID
	 * @return void
	 */
	public function setClass($classID) {
		ObjectQuery::_setClass($this, $classID);
	}

	/**
	 * define template class to create all fields
	 * static function
	 * @param object $record specify TemplateQuery object to set class for
	 * @param integer $classID
	 * @return void
	 */
	public static function _setClass($record, $classID, $fields = null) {
		if (is_array($classID)) {
			// for not to query that again if we queried it before
			$record->classID = $classID['id'];
			$record->className = $classID['name'];
		} else {
			$record->classID = $classID;
			$record->className = Classes::findOne($record->classID)->name;
		}
		if (! isset($fields)) {
			$fields = ObjectFields::find()->where(['class_id' => $record->classID])->all();
		}
		foreach ($fields as $field) {
			$name = $field->name;
			$record->data[$name] = $field->default_value;
//			$record->$name = $field->default_value;
			$record->allFields[] = $field->name;
			$record->types[$field->name] = $field->type;
			$record->labels[$field->name] = $field->name;
			if (is_null($field->default_value)) { $record->required[] = $field->name; }
			if (in_array($field->namedType->type_name, array('text', 'image', 'formatted text'))) { 
				$record->string[] = $field->name;				
			} elseif (in_array($field->namedType->type_name, array('template'))) {
				$record->integer[] = $field->name;
			}
		}
//		print_r($fields);
//		print_r($record->data);
//		print_r($record->required);
//		print_r($record->allFields); die();
	}
	
	public static function getPageByURI($uri, $getGlobal = true) {
		$sql = 'SELECT of.name, of.type as default_type, of.default_value, of.id as fields_id, '
				. 'ov.type, ov.value, '
				. 'cl.id as class_id, cl.name as class_name,'
				. 'ob.id as object_id, ob.name as object_name '
				. 'FROM `' . Pages::tableName() . '` as pa '
				. 'left join ' . PageFields::tableName() . ' as pf on pa.id = pf.page_id '
				. 'left join ' . Objects::tableName() . ' as ob on pf.object_id = ob.id '
				. 'left join ' . Classes::tableName() . ' as cl on ob.class_id = cl.id '
				. 'left join ' . ObjectFields::tableName() . ' as of on ob.class_id = of.class_id '
				. 'left join ' . ObjectValues::tableName() . ' as ov on (ob.id = ov.object_id and of.id = ov.field_id) '
				. 'WHERE pa.url = :url AND NOT of.name IS NULL';
		if ($getGlobal) {
			$sql .= ' or url=""';
		}
		$fields = Yii::$app->getDb()->createCommand($sql, [':url' => $uri])->queryAll(); //\PDO::FETCH_OBJ);
		// arrange fields to objects
		// it's trick for populateRecord
		$objects = array();
		foreach ($fields as $f) {
			$id = $f['object_id'];
			$f['field'] = (object) ['name' => $f['name']];
			if (! isset($objects[$id])) {
				$objects[$id] = [
					'fields' => [],
					'values' => [],
					// it looks like a hack, probably we should rearrange the code or something?
					'row' => ['id' => $f['object_id'],
						'class_id' => [
							'id' => $f['class_id'],
							'name' => $f['class_name'],
						],
						'name' => $f['object_name'],
					],
				];
			}
			$objects[$id]['values'][] = (object) $f;
			$objects[$id]['fields'][] = (object) ['id' => $f['fields_id'],
				'default_value' => $f['default_value'],
				'name' => $f['name'],
				'class_id' => $f['class_id'],
				'type' => $f['default_type'],
				];
		}
		// now create objects and populate 'em
		$result = array();
		foreach ($objects as $o) {
			$r = new ObjectQuery();
			$values = (object) $o['values'];
			ObjectQuery::populateRecord($r, $o['row'], $values, $o['fields']);
			$result[] = $r;
		}
//		print_r($fields); die();
//		print_r($result); die();
		return $result;
	}
	
	public static function populateRecord($record, $row, $values = null, $fields = null) {
//		print_r($row); die();
		// initialize object with fields
		$id = $row['id'];
		$name = $row['name'];
		ObjectQuery::_setClass($record, $row['class_id']);
		// populate values
		if (! isset($values)) {
			$values = ObjectValues::find()->select(ObjectValues::tableName() . '.*, ' . ObjectFields::tableName() . '.name')->joinWith('field')->where(['object_id' => $id])->all();
		}
		$row = $record->data;
		$row['id'] = $id;
		$row['name'] = $name;
		foreach ($values as $value) {
			$name = $value->field->name;
			$row[$name] = $value->value;
			$record->data[$name] = $value->value;
			if (! empty($value->type)) { $record->types[$name] = $value->type; }
		}
		// totally forgot why i put here third argument 0_0
		parent::populateRecord($record, $row, $fields);		
	}
	
	public function attributes() {
//		return parent::attributes();
		return $this->allFields;
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'objects';
    }
	
	public static function primaryKey() {
//		parent::primaryKey();
		return ['id'];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [$this->required, 'required'],
            [$this->string, 'string'],
			[$this->integer, 'integer'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassesAllowed()
    {
//		die('FUCK');
        return $this->hasMany(ClassesAllowed::className(), ['class_id' => 'class_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'classID']);
    }
	
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->labels;
    }
	
	public function save($runValidation = true, $attributeNames = null) {
		$transaction = ObjectQuery::getDb()->beginTransaction();
		try {
			if (empty($this->id)) {
				$object = new Objects();
				$object->class_id = $this->classID;
			} else {
				$object = Objects::findOne($this->id);
			}
			$object->name = $this->name;
			if (! $object->save()) {
				// TODO: i can't set errors, it's read-only property. Google it out
				$this->errors = $object->errors;
				throw new Exception('object save fault');
			}
			// let's get all field id's of that object
			$objectFields = [];
			foreach (ObjectFields::findAll(['class_id' => $this->classID]) as $field) {
				$objectFields[$field->name] = $field->id;
			}
			$this->id = $object->id;
			foreach ($this->allFields as $fieldname) { if ('name' != $fieldname) {
				$model = ObjectValues::findOne(['object_id' => $this->id, 'field_id' => $objectFields[$fieldname]]);
				if (null === $model) {
					$model = new ObjectValues();
					$model->object_id = $this->id;
					$model->field_id = $objectFields[$fieldname];
				}
				$model->value = (string) $this->$fieldname;
				if (! $model->save($runValidation, $attributeNames)) {
					print_r($model->errors); 
					print_r($object);
					print_r($model);
					die('field save fault');
					$this->errors = $model->errors;
					throw new Exception('field save fault');
				}
			} }
			$transaction->commit();
	//		parent::save($runValidation, $attributeNames);
			return true;
		} catch (Exception $e) {
			$transaction->rollBack();
			return false;
//			throw $e;
		}
	}
	
	/**
	 * for import
	 */
	public static function addToPage($objectID, $pageID) {
		$pf = new PageFields();
		$pf->page_id = $pageID;
		$pf->object_id = $objectID;
		$pf->save();
	}
	
	/**
	 * for import too
	 */
	public static function addTag($pageID, $tag) {
		$t = new Tags();
		$t->page_id = $pageID;
		$t->tag = $tag;
		$t->save();
	}
	
	/*
	 * @text article text
	 * @return fixed text
	 */
	public static function resolveTags($newlist, $text, $pageID) {
		$galleries = explode('{gallery}', $text);
		// we don't need text before first gallery
		$value = array_shift($galleries);
		foreach ($galleries as $g) {
			$g = explode('{/gallery}', $g);
			$value .= $g[1];
			$list = clone $newlist;
			$list->value = 'images/' . explode(',', $g[0])[0];
			$list->save(false);
			ObjectQuery::addToPage($list->id, $pageID);
		}
		// cheat: we name urls like they was, so just regexp would be enough to fix links
		return preg_replace('(href="index.php/([^/]+/)*([^"]+)")', 'href="$2"', $value);
	}
	
	/**
	 * construct menus to tree-like structures
	 * need for page show and menu editor in adminpanel
	 * no queries executed here
	 * create modules (wrong way, so it works only once)
	 * @param array $objectList objects like from ObjectQuery::getPageByURI
	 * @return dictionary 'menus' => all menus in tree form, 'objectsByName' => rest objects
	 */
	public static function constructMenusArrangeObjects($objectList) {
		$tree = []; // all child items. Key - parent id
		$ids = []; $request = [];
		$objectsByName = [];
		$moduleObjects = []; // for modules
		foreach ($objectList as $f) {
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
				}
			}
		}
		$menus = array();
		foreach ($tree as $menu) {
			$menus[$menu['parent']->name] = $menu;
		}
		return ['menus' => $menus, 'objectsByName' => $objectsByName];
	}
	
	/**
     * one-use method for import all data from hardcoded joomla database into our database
	 * it's not official import method cause it doesn't touch any menues
     */
	public static function importFromJoomla() {
		ObjectQuery::getDb()->transaction(function () {
			$before = microtime(true);
			// import users
			$sql = 'select id, name, username, email, password, registerDate, sendEmail '
					. 'from db_adminodn_1.jos_users where block = 0';
			// remove all current users
			ObjectQuery::deleteAll(['class_id' => 1]);
			// map old userid => new userid
			$usermap = [];
			$newuser = new ObjectQuery();
			$newuser->setClass(1); // user
			$newuser->status = 'activated';
			foreach (Yii::$app->getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ) as $juser) {
				$user = clone $newuser;
				$user->name = $juser->name;
				$user->login = $juser->username;
				$user->password = $juser->password;
				$user->email = $juser->email;
				$user->created = $juser->registerDate;
				$user->sendmail = $juser->sendEmail;
				$user->save(false);
				$usermap[$juser->id] = $user->id;
			}
			echo "Profiling — users: " . (microtime(true) - $before) . "<br/>\n";
//			echo '<pre>'; print_r($usermap); echo '</pre>';
			$before = microtime(true);
			// import categories
			$sql = 'select id, parent_id, alias, title, description, published, metadesc, created_user_id, created_time '
					. 'from db_adminodn_1.jos_categories';
			Pages::deleteAll('url <> "" and url <> "home"');
			$categorymap = [];
			$newpagemeta = new ObjectQuery();
			$newpagemeta->setClass(2); // page meta
			$newpagemeta->name = 'page';
			$newbigtext = new ObjectQuery();
			$newbigtext->setClass(6); // big text
			$newbigtext->name = 'article';
			$newbigtext->intro = '';
			$queue = [];
			foreach (Yii::$app->getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ) as $cat) {
				$page = new Pages();
				$page->created = $cat->created_time;
				$page->title = $cat->title;
				$page->template_id = 2; // category
				$page->url = $cat->id . '-' . $cat->alias;
				$page->save();
				$categorymap[$cat->id] = $page->id;
				$pagemeta = clone $newpagemeta;
				$pagemeta->meta = $cat->metadesc;
				if (1 != $cat->published) { $pagemeta->status = 'draft'; } else { $pagemeta->status = 'published'; }
				$pagemeta->author = (in_array($cat->created_user_id, $usermap))? $usermap[$cat->created_user_id]: 1;
				$pagemeta->save(false);
				ObjectQuery::addToPage($pagemeta->id, $page->id);
				$desc = clone $newbigtext;
				$desc->value = $cat->description; // hope there are no links, galleries or videos
				$desc->save(false);
				ObjectQuery::addToPage($desc->id, $page->id);
				// tags: for nested categories
				if (isset($queue[$cat->id])) {
					foreach ($queue[$cat->id] as $pageID) {
						ObjectQuery::addTag($pageID, 'category:' . $page->id);
					}
					unset($queue[$cat->id]);
				}
				if (isset($categorymap[$cat->parent_id])) {
					ObjectQuery::addTag($page->id, 'category:' . $categorymap[$cat->parent_id]);
				} else {
					if (! isset($queue[$cat->parent_id])) {
						$queue[$cat->parent_id] = [];
					}
					$queue[$cat->parent_id][] = $page->id;
				}
			}
			echo "Profiling — categories: " . (microtime(true) - $before) . "<br/>\n";
			$before = microtime(true);
			$sql = 'select id, title, alias, introtext, `fulltext`, state, catid, created, created_by, metadesc, hits '
					. 'from db_adminodn_1.jos_content';
			$newpagemeta = new ObjectQuery();
			$newpagemeta->setClass(2); // page meta
			$newpagemeta->name = 'page';
			$newbigtext = new ObjectQuery();
			$newbigtext->setClass(6); // big text
			$newbigtext->name = 'article';
			$newlist = new ObjectQuery();
			$newlist->setClass(5); // customfield
			$newlist->module = 'Gallery:default/images';
			$newlist->name = 'list';
//			$oldPageID = [];
			foreach (Yii::$app->getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ) as $art) {
				$page = new Pages();
				$page->created = $art->created;
				$page->title = $art->title;
				if (count(explode('{gallery}', $art->introtext . $art->fulltext)) > 1) {
					$page->template_id = 3; // gallery
				} else {
					$page->template_id = 1; // article
				}
				$page->url = $art->id . '-' . $art->alias;
				$page->views = $art->hits;
				$page->save();
				$pagemeta = clone $newpagemeta;
				$pagemeta->meta = $art->metadesc;
				if (1 == $art->state) {$pagemeta->status = 'published'; } else {$pagemeta->status = 'draft'; }
				$pagemeta->author = (in_array($art->created_by, $usermap))? $usermap[$art->created_by]: 1;
				$pagemeta->save(false);
				ObjectQuery::addToPage($pagemeta->id, $page->id);
				$cont = clone $newbigtext;
				if (empty($art->fulltext)) {
					$cont->intro = '';
					$cont->value = ObjectQuery::resolveTags($newlist, $art->introtext, $page->id);
				} else {
					$cont->intro = ObjectQuery::resolveTags($newlist, $art->introtext, $page->id);
					$cont->value = ObjectQuery::resolveTags($newlist, $art->fulltext, $page->id);
				}
				$cont->save(false);
				ObjectQuery::addToPage($cont->id, $page->id);
				ObjectQuery::addTag($page->id, 'category:' . $categorymap[$art->catid]);
			}
			echo "Profiling — pages: " . (microtime(true) - $before) . "<br/>\n";
		});
	}

	
	/**
     * one-use method for import all data from hardcoded joomla database into our database
	 * it's not official import method cause it doesn't touch any menues
     */
	public static function createGalleries() {
		$transaction = ObjectQuery::getDb()->transaction(function () {
			$sql = 'select pages.id as page_id, object_values.id as val_id, object_values.value '
					. 'from pages '
					. 'left join page_fields on pages.id = page_fields.page_id '
					. 'left join objects on page_fields.object_id = objects.id '
					. 'left join object_values on object_values.object_id = objects.id '
					. 'left join object_fields on object_values.field_id = object_fields.id '
					. 'where objects.name = "article" and object_fields.name = "value" and object_values.value like "%{gallery}%"';
			$newlist = new ObjectQuery();
			$newlist->setClass(5); // customfield
			$newlist->module = 'Gallery:default/images';
			$newlist->name = 'list';
			foreach (Yii::$app->getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ) as $page) {
				$galleries = explode('{gallery}', $page->value);
				// we don't need text before first gallery
				$value = array_shift($galleries);
				foreach ($galleries as $g) {
					$g = explode('{/gallery}', $g);
					$value .= $g[1];
					$list = clone $newlist;
					$list->value = 'images/' . explode(',', $g[0])[0];
					$list->save(false);
					ObjectQuery::addToPage($list->id, $page->page_id);
				}
				$p = Pages::findOne($page->page_id);
				$p->template_id = 3;
				$p->save(false);
				$obj = ObjectValues::findOne($page->val_id);
				$obj->value = $value;
				$obj->save(false);
			}
		} );
	}
	
}