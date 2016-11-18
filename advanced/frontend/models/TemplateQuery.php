<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use frontend\models\Templates;
use frontend\models\TemplateFields;
use frontend\models\TemplateValues;
use frontend\models\TemplateClassesAllowed;
use frontend\models\TemplateClasses;
//use frontend\models\Pages;
//use frontend\models\PageFields;

class TemplateQuery extends ActiveRecord {
	public $classID; // TODO: create getter instead of public property
	public $template_class_id; // TODO: create getter instead of public property
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
		TemplateQuery::_setClass($this, $classID);
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
			$record->className = TemplateClasses::findOne($record->classID)->name;
		}
		if (! isset($fields)) {
			$fields = TemplateFields::find()->where(['template_class_id' => $record->classID])->all();
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
		$sql = 'SELECT template_fields.name, template_fields.type as default_type, template_fields.default_value, template_fields.id as fields_id, '
				. 'template_values.type, template_values.value, '
				. 'template_classes.id as template_class_id, template_classes.name as class_name,'
				. 'templates.id as template_id, templates.name as template_name '
				. 'FROM `pages` '
				. 'left join page_fields on pages.id = page_fields.page_id '
				. 'left join templates on page_fields.template_id = templates.id '
				. 'left join template_classes on templates.template_class_id = template_classes.id '
				. 'left join template_fields on templates.template_class_id = template_fields.template_class_id '
				. 'left join template_values on (templates.id = template_values.template_id and template_fields.name = template_values.name) '
				. 'WHERE pages.url = :url';
		if ($getGlobal) {
			$sql .= ' or url=""';
		}
		$fields = Yii::$app->getDb()->createCommand($sql, [':url' => $uri])->queryAll(\PDO::FETCH_OBJ);
		// arrange fields to objects
		$objects = array();
		foreach ($fields as $f) {
			$id = $f->template_id;
			if (! isset($objects[$id])) {
				$objects[$id] = [
					'fields' => [],
					'values' => [],
					// it looks like a hack, probably we should rearrange the code or something?
					'row' => ['id' => $f->template_id,
						'template_class_id' => [
							'id' => $f->template_class_id,
							'name' => $f->class_name,
						],
						'name' => $f->template_name,
					],
				];
			}
			$objects[$id]['values'][] = $f;
			$objects[$id]['fields'][] = (object) ['id' => $f->fields_id,
				'default_value' => $f->default_value,
				'name' => $f->name,
				'template_class_id' => $f->template_class_id,
				'type' => $f->default_type,
				];
		}
		// now create objects and populate 'em
		$result = array();
		foreach ($objects as $o) {
			$r = new TemplateQuery();
			$values = (object) $o['values'];
			TemplateQuery::populateRecord($r, $o['row'], $values, $o['fields']);
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
		TemplateQuery::_setClass($record, $row['template_class_id']);
		// populate values
		if (! isset($values)) {
			$values = TemplateValues::find()->where(['template_id' => $id])->all();
		}
		$row = $record->data;
		$row['id'] = $id;
		$row['name'] = $name;
		foreach ($values as $value) {
			$row[$value->name] = $value->value;
			$record->data[$value->name] = $value->value;
			if (! empty($value->type)) { $record->types[$value->name] = $value->type; }
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
        return 'templates';
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
    public function getTemplateClassesAllowed()
    {
        return $this->hasMany(TemplateClassesAllowed::className(), ['template_class_id' => 'template_class_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClass()
    {
        return $this->hasOne(TemplateClasses::className(), ['id' => 'classID']);
    }
	
	/**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->labels;
    }
	
	public function save($runValidation = true, $attributeNames = null) {
		$transaction = TemplateQuery::getDb()->beginTransaction();
		try {
			if (empty($this->id)) {
				$template = new Templates();
				$template->template_class_id = $this->classID;
			} else {
				$template = Templates::findOne($this->id);
			}
			$template->name = $this->name;
			if (! $template->save()) {
				// TODO: i can't set errors, it's read-only property. Google it out
				$this->errors = $template->errors;
				throw new Exception('template save fault');
			}
			$this->id = $template->id;
			foreach ($this->allFields as $fieldname) if ('name' != $fieldname) {
				$model = TemplateValues::findOne(['template_id' => $this->id, 'name' => $fieldname]);
				if (null === $model) {
					$model = new TemplateValues();
					$model->template_id = $this->id;
					$model->name = $fieldname;
				}
				$model->value = $this->$fieldname;
				if (! $model->save($runValidation, $attributeNames)) {
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