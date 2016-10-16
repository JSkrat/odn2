<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "templates".
 *
 * @property integer $id
 * @property integer $template_class_id
 * @property string $name
 * @property integer $group_id
 *
 * @property TemplateValues[] $templateValues
 * @property TemplateFields[] $names
 * @property TemplateClasses $templateClass
 */
class Templates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_class_id'], 'required'],
            [['template_class_id', 'group_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['template_class_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateClasses::className(), 'targetAttribute' => ['template_class_id' => 'id']],
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
            'name' => 'Name',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateValues()
    {
        return $this->hasMany(TemplateValues::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNames()
    {
        return $this->hasMany(TemplateFields::className(), ['name' => 'name'])->viaTable('template_values', ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClass()
    {
        return $this->hasOne(TemplateClasses::className(), ['id' => 'template_class_id']);
    }
}
