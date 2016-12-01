<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "object_values".
 *
 * @property integer $id
 * @property integer $class_id
 * @property integer $type
 * @property integer $object_id
 * @property string $name
 * @property string $value
 *
 * @property ObjectFields $object
 * @property FieldTypes $type0
 */
class ObjectValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'object_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'object_id', 'field_id'], 'integer'],
            [['object_id', 'field_id', 'value'], 'required'],
            [['value'], 'string'],
            [['field_id', 'object_id'], 'unique', 'targetAttribute' => ['field_id', 'object_id'], 'message' => 'The combination of Object ID and Field ID has already been taken.'],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => FieldTypes::className(), 'targetAttribute' => ['type' => 'id']],
            [['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => Objects::className(), 'targetAttribute' => ['object_id' => 'id']],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectFields::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'Object ID',
            'type' => 'Type',
			'field_id' => 'Field ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::className(), ['id' => 'object_id']);
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
    public function getType0()
    {
        return $this->hasOne(FieldTypes::className(), ['id' => 'type']);
    }

    /**
     * @inheritdoc
     * @return FieldTypesQuery the active query used by this AR class.
     */
//    public static function find()
//    {
//        return new FieldTypesQuery(get_called_class());
//    }
	
    /**
     * @inheritdoc
     */
//	public function load($data, $formName = null) {
//		return false;
//		parent::load($data, $formName);
//	}
}
