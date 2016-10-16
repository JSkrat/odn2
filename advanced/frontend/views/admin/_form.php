<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TemplateValues */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-values-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'template_id')->textInput() ?>
    <?= $form->field($model, 'type')->textInput() ?>
    <?= $form->field($model, 'order')->textInput() ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
