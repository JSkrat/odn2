<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "template_classes_allowed".
 *
 * @property integer $template_field_id
 * @property integer $template_class_id
 * 
 * @property TemplateFields $templateField 
 * @property TemplateClasses $templateClass 
 */
class TemplateClassesAllowed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_classes_allowed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_field_id', 'template_class_id'], 'required'],
            [['template_field_id', 'template_class_id'], 'integer'],
            [['template_field_id', 'template_class_id'], 'unique', 'targetAttribute' => ['template_field_id', 'template_class_id'], 'message' => 'The combination of Template Field ID and Template Class ID has already been taken.'],
            [['template_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateFields::className(), 'targetAttribute' => ['template_field_id' => 'id']], 
            [['template_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateClasses::className(), 'targetAttribute' => ['template_class_id' => 'id']], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'template_field_id' => 'Template Field ID',
            'template_class_id' => 'Template Class ID',
        ];
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTemplateField()
	{
		return $this->hasOne(TemplateFields::className(), ['id' => 'template_field_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTemplateClass()
	{
		return $this->hasOne(TemplateClasses::className(), ['id' => 'template_class_id']);
	}

}
