<?php

namespace frontend\models;

use Yii;
use frontend\models\TemplateQuery;

/**
 * This is the model class for table "template_fields".
 *
 * @property integer $id
 * @property integer $template_class_id
 * @property integer $type
 * @property string $name
 * @property string $default_value
 *
 * @property TemplateClassesAllowed[] $templateClassesAllowed
 * @property TemplateClasses[] $templateClasses
 * @property TemplateClasses $templateClass
 * @property FieldTypes $type0
 * @property TemplateValues[] $templateValues
 */
class TemplateFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_class_id', 'type', 'name'], 'required'],
            [['template_class_id', 'type'], 'integer'],
            [['default_value'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['template_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateClasses::className(), 'targetAttribute' => ['template_class_id' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => FieldTypes::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_class_id' => 'Template Class ID',
            'type' => 'Type',
            'name' => 'Name',
            'default_value' => 'Default Value',
        ];
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClassesAllowed()
    {
        return $this->hasMany(TemplateClassesAllowed::className(), ['template_field_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClasses()
    {
        return $this->hasMany(TemplateClasses::className(), ['id' => 'template_class_id'])->viaTable('template_classes_allowed', ['template_field_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClass()
    {
        return $this->hasOne(TemplateClasses::className(), ['id' => 'template_class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNamedType()
    {
        return $this->hasOne(FieldTypes::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateValues()
    {
        return $this->hasMany(TemplateValues::className(), ['name' => 'name']);
    }
	
	public function getAllowedObjects() {
		return TemplateQuery::find()->joinWith('templateClassesAllowed')->where(['template_field_id' => $this->id])->all();
		return $this->findBySql("select * "
				. "from template_classes_allowed as tca "
				. "left join templates as t on tca.template_class_id = t.template_class_id "
				. "left join template_values as tv on t.id = tv.template_id "
				. "where tca.template_field_id = :fieldID "
				. "group by tv.name", [':fieldID' => $this->id])
				->all();
	}

}
