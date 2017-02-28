<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property integer $id
 * @property string $created
 * @property string $title
 * @property integer $template_id
 * @property string $url
 * @property integer $views
 *
 * @property PageFields[] $pageFields
 * @property Objects[] $objects
 * @property Templates $template
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
            [['title', 'url'], 'required'],
            [['title', 'url'], 'string'],
			[['url'], 'unique'],
            [['template_id', 'views'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Templates::className(), 'targetAttribute' => ['template_id' => 'id']],
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
            'template_id' => 'Template ID',
            'url' => 'Url',
			'views' => 'Views',
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
    public function getObjects()
    {
        return $this->hasMany(Objects::className(), ['id' => 'object_id'])->viaTable('page_fields', ['page_id' => 'id']);
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
    public function getTags()
    {
        return $this->hasMany(Tags::className(), ['page_id' => 'id']);
    }
}
