<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "template_classes".
 *
 * @property integer $id
 * @property string $name
 *
 * @property TemplateFields[] $templateFields
 */
class TemplateClasses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template_classes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateFields()
    {
        return $this->hasMany(TemplateFields::className(), ['template_class_id' => 'id']);
    }
}
