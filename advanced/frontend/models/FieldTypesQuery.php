<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[TemplateValues]].
 *
 * @see TemplateValues
 */
class FieldTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TemplateValues[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TemplateValues|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
