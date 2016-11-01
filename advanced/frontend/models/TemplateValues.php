<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "template_values".
 *
 * @property integer $id
 * @property integer $template_class_id
 * @property integer $type
 * @property integer $template_id
 * @property string $name
 * @property string $value
 *
 * @property TemplateFields $template
 * @property FieldTypes $type0
 */
class TemplateValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id'], 'required'],
            [['type', 'template_id'], 'integer'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => FieldTypes::className(), 'targetAttribute' => ['type' => 'id']],
//			[['name'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateFields::className(), 'targetAttribute' => ['name' => 'name', 'template_class_id' => 'templates.template_class_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'type' => 'Type',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Templates::className(), ['id' => 'template_id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getTemplateField()
//    {
//        return $this->hasOne(TemplateFields::className(), ['name' => 'name', 'template_class_id' => 'template_class_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(FieldTypes::className(), ['id' => 'type']);
    }

    /**
     * @inheritdoc
     * @return FieldTypesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FieldTypesQuery(get_called_class());
    }
	
    /**
     * @inheritdoc
     */
	public function load($data, $formName = null) {
		return false;
		parent::load($data, $formName);
	}
}
