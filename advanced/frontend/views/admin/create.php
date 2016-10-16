<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TemplateValues */

$this->title = 'Create Template Values';
$this->params['breadcrumbs'][] = ['label' => 'Template Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-values-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
