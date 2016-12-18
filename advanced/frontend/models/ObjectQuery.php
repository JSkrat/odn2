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
			foreach ($this->allFields as $fieldname) if ('name' != $fieldname) {
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
			}
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
				$page->url = $cat->alias . '-' . $cat->id;
				$page->save();
				$categorymap[$cat->id] = $page->id;
				$pagemeta = clone $newpagemeta;
				$pagemeta->meta = $cat->metadesc;
				if (1 != $cat->published) { $pagemeta->status = 'draft'; } else { $pagemeta->status = 'published'; }
				$pagemeta->author = (in_array($cat->created_user_id, $usermap))? $usermap[$cat->created_user_id]: 1;
				$pagemeta->save(false);
				ObjectQuery::addToPage($pagemeta->id, $page->id);
				$desc = clone $newbigtext;
				$desc->value = $cat->description;
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
			foreach (Yii::$app->getDb()->createCommand($sql)->queryAll(\PDO::FETCH_OBJ) as $art) {
				$page = new Pages();
				$page->created = $art->created;
				$page->title = $art->title;
				$page->template_id = 1; // article
				$page->url = $art->alias . '-' . $art->id;
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
					$cont->value = $art->introtext;
					$cont->intro = '';
				} else {
					$cont->intro = $art->introtext;
					$cont->value = $art->fulltext;
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
			
		} );
	}
	
}