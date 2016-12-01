<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "template_classes".
 *
 * @property integer $id
 * @property integer $template_id
 * @property integer $class_id
 * @property string $name
 *
 * @property Templates $template
 * @property Classes $class
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
            [['template_id', 'class_id', 'name'], 'required'],
            [['template_id', 'class_id'], 'integer'],
            [['name'], 'string'],
            [['template_id', 'class_id'], 'unique', 'targetAttribute' => ['template_id', 'class_id'], 'message' => 'The combination of Template ID and Class ID has already been taken.'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']],
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
            'class_id' => 'Class ID',
            'name' => 'Name',
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
    public function getClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'class_id']);
    }
}
