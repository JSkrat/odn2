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
			$record->data[$field->name] = $field->default_value;
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
				$model->value = $this->$fieldname;
				if (! $model->save($runValidation, $attributeNames)) {
					print_r($model->errors); die('field save fault');
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
}