<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Templates */

$this->title = Yii::t('frontend', 'Create Templates');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="templates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
