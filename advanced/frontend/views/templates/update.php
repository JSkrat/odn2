<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Templates */

$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => 'Templates',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="templates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
