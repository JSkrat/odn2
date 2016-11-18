<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tags_allowed".
 *
 * @property integer $id
 * @property string $tag
 * @property integer $template_field_id
 *
 * @property TemplateFields $templateField
 */
class TagsAllowed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags_allowed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag', 'template_field_id'], 'required'],
            [['tag'], 'string'],
            [['template_field_id'], 'integer'],
            [['template_field_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemplateFields::className(), 'targetAttribute' => ['template_field_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag' => 'Tag',
            'template_field_id' => 'Template Field ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateField()
    {
        return $this->hasOne(TemplateFields::className(), ['id' => 'template_field_id']);
    }
}
