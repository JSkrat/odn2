<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "field_types".
 *
 * @property integer $id
 * @property string $type_name
 *
 * @property PageFields[] $pageFields
 * @property TemplateFields[] $templateFields
 * @property TemplateValues[] $templateValues
 */
class FieldTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'field_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name'], 'required'],
            [['type_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_name' => 'Type Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageFields()
    {
        return $this->hasMany(PageFields::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateFields()
    {
        return $this->hasMany(TemplateFields::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateValues()
    {
        return $this->hasMany(TemplateValues::className(), ['type' => 'id']);
    }
}
