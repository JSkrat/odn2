<?php

namespace frontend\models;

use Yii;
use frontend\models\ObjectQuery;

/**
 * This is the model class for table "template_fields".
 *
 * @property integer $id
 * @property integer $class_id
 * @property integer $type
 * @property string $name
 * @property string $default_value
 *
 * @property ClassesAllowed[] $classesAllowed
 * @property Classes[] $classes
 * @property Classes $class
 * @property FieldTypes $type0
 * @property ObjectValues[] $templateValues
 */
class ObjectFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'object_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_id', 'type', 'name'], 'required'],
            [['class_id', 'type'], 'integer'],
            [['default_value'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classes::className(), 'targetAttribute' => ['class_id' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => FieldTypes::className(), 'targetAttribute' => ['type' => 'id']],
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
            'type' => 'Type',
            'name' => 'Name',
            'default_value' => 'Default Value',
        ];
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getClassesAllowed()
    {
        return $this->hasMany(ClassesAllowed::className(), ['field_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasses()
    {
        return $this->hasMany(Classes::className(), ['id' => 'class_id'])->viaTable('classes_allowed', ['field_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(Classes::className(), ['id' => 'class_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNamedType()
    {
        return $this->hasOne(FieldTypes::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateValues()
    {
        return $this->hasMany(ObjectValues::className(), ['name' => 'name']);
    }
	
	public function getAllowedObjects() {
		return ObjectQuery::find()->joinWith('classesAllowed')->where(['field_id' => $this->id])->all();
//		return $this->findBySql("select * "
//				. "from " . ClassesAllowed::tableName() . " as ca "
//				. "left join " . Objects::tableName() . " as ob on ca.class_id = ob.class_id "
//				. "left join " . ObjectValues::tableName() . " as ov on ob.id = ov.object_id "
//				. "where ca.field_id = :fieldID "
//				. "group by ov.name", [':fieldID' => $this->id])
//				->all();
	}

}
