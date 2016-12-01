<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "page_fields".
 *
 * @property integer $page_id
 * @property integer $object_id
 *
 * @property Pages $page
 * @property Objects $template
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
            [['page_id', 'object_id'], 'required'],
            [['page_id', 'object_id'], 'integer'],
            [['page_id', 'object_id'], 'unique', 'targetAttribute' => ['page_id', 'object_id'], 'message' => 'The combination of Page ID and Template ID has already been taken.'],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pages::className(), 'targetAttribute' => ['page_id' => 'id']],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Objects::className(), 'targetAttribute' => ['object_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'Page ID',
            'object_id' => 'Object ID',
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
        return $this->hasOne(Objects::className(), ['id' => 'object_id']);
    }
}
