<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "classes".
 *
 * @property integer $id
 * @property string $name
 *
 * @property ObjectFields[] $fields
 */
class Classes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(ObjectFields::className(), ['class_id' => 'id']);
    }
}
