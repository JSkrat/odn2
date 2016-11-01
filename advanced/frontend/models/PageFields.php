<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "page_fields".
 *
 * @property integer $page_id
 * @property integer $template_id
 *
 * @property Pages $page
 * @property Templates $template
 */
class PageFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'template_id'], 'required'],
            [['page_id', 'template_id'], 'integer'],
            [['page_id', 'template_id'], 'unique', 'targetAttribute' => ['page_id', 'template_id'], 'message' => 'The combination of Page ID and Template ID has already been taken.'],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pages::className(), 'targetAttribute' => ['page_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'Page ID',
            'template_id' => 'Template ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Pages::className(), ['id' => 'page_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Templates::className(), ['id' => 'template_id']);
    }
}
