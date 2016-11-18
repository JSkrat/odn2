<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property integer $id
 * @property string $created
 * @property string $title
 * @property string $template
 * @property string $url
 *
 * @property PageFields[] $pageFields
 * @property Templates[] $templates
 * @property Tags[] $tags
 */
class Pages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created'], 'safe'],
            [['title', 'template', 'url'], 'required'],
            [['title', 'template', 'url'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'title' => 'Title',
            'template' => 'Template',
            'url' => 'Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageFields()
    {
        return $this->hasMany(PageFields::className(), ['page_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplates()
    {
        return $this->hasMany(Templates::className(), ['page_id' => 'id']);
    }
	
	/**
    * @return \yii\db\ActiveQuery
    */
   public function getTags()
   {
       return $this->hasMany(Tags::className(), ['page_id' => 'id']);
   }
}
