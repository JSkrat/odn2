<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "templates".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Pages[] $pages
 * @property TemplateClasses[] $templateClasses
 * @property Classes[] $classes
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
            [['name'], 'required'],
            [['name'], 'string'],
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
    public function getPages()
    {
        return $this->hasMany(Pages::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClasses()
    {
        return $this->hasMany(TemplateClasses::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasses()
    {
        return $this->hasMany(Classes::className(), ['id' => 'class_id'])->viaTable('template_classes', ['template_id' => 'id']);
    }
}
