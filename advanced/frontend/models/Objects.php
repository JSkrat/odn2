<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "objects".
 *
 * @property integer $id
 * @property integer $class_id
 * @property string $name
 * @property integer $group_id
 *
 * @property ObjectValues[] $values
 * @property ObjectFields[] $names
 * @property Pages $pages
 * @property Classes $class
 */
class Objects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'objects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_id'], 'required'],
            [['class_id', 'group_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
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
            'class_id' => 'Class ID',
            'name' => 'Name',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateValues()
    {
        return $this->hasMany(ObjectValues::className(), ['object_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNames()
    {
        return $this->hasMany(ObjectFields::className(), ['name' => 'name'])->viaTable('values', ['object_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'class_id']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
//	public function getPage() {
//		return $this->hasOne(Templates::className(), ['id' => 'page_id']);
//	}
}
