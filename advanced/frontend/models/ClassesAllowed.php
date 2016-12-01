<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "classes_allowed".
 *
 * @property integer $field_id
 * @property integer $class_id
 * 
 * @property ObjectFields $field 
 * @property Classes $class 
 */
class ClassesAllowed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classes_allowed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['field_id', 'class_id'], 'required'],
            [['field_id', 'class_id'], 'integer'],
            [['field_id', 'class_id'], 'unique', 'targetAttribute' => ['field_id', 'class_id'], 'message' => 'The combination of Field ID and Class ID has already been taken.'],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectFields::className(), 'targetAttribute' => ['field_id' => 'id']], 
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']], 
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'field_id' => 'Field ID',
            'class_id' => 'Class ID',
        ];
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getField()
	{
		return $this->hasOne(ObjectFields::className(), ['id' => 'field_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getClass()
	{
		return $this->hasOne(Classes::className(), ['id' => 'class_id']);
	}

}
