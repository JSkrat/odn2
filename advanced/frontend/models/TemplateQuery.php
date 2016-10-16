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

class TemplateQuery extends ActiveRecord {
	public $classID;
	public $template_class_id;
	public $className;
	public $allFields = array();
	public $data = array();
	private $types = array();
	private $labels = array();
	private $required = array();
	private $integer = array();
	private $string = array();
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
	public static function _setClass($record, $classID) {
		$record->classID = $classID;
		$record->className = TemplateClasses::findOne($record->classID)->name;
		foreach (TemplateFields::find()->where(['template_class_id' => $record->classID])->all() as $field) {
			$record->data[$field->name] = $field->default_value;
			$record->allFields[] = $field->name;
			$record->types[$field->name] = $field->type;
			$record->labels[$field->name] = $field->name;
			if (empty($field->default_value)) { $record->required[] = $field->name; }
			if (in_array($field->namedType->type_name, array('text', 'image', 'formatted text'))) { 
				$record->string[] = $field->name;				
			} elseif (in_array($field->namedType->type_name, array('template'))) {
				$record->integer[] = $field->name;
			}
		}
	}
	
	public static function populateRecord($record, $row) {
		// initialize object with fields
		$id = $row['id'];
		TemplateQuery::_setClass($record, $row['template_class_id']);
		// populate values
		$fields = TemplateValues::find()->where(['template_id' => $id])->all();
		$row = $record->data;
		$row['id'] = $id;
		foreach ($fields as $field) {
			$row[$field->name] = $field->value;
			$record->data[$field->name] = $field->value;
			if (! empty($field->type)) { $record->types[$field->name] = $field->type; }
		}
		parent::populateRecord($record, $row);		
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
				if (! $template->save()) {
					$this->errors = $template->errors;
					throw new Exception('template save fault');
				}
				$this->id = $template->id;
			}
			foreach ($this->allFields as $fieldname) {
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