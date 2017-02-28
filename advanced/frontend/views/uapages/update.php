<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Pages */

if ($model->id) {
	$this->title = Yii::t('frontend', 'Create {modelClass}: ', [
		'modelClass' => Yii::t('frontend', 'Pages'),
	]) . $model->title;
} else {
	$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
		'modelClass' => Yii::t('frontend', 'Pages'),
	]) . $model->title;
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Pages'), 'url' => ['index']];
if ($model->id) {
	$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
} else {
	$this->params['breadcrumbs'][] = Yii::t('frontend', 'Create');
}

// let's translate template names!
foreach ($templates as $key => $val) {
	$templates[$key] = Yii::t("frontend", $val);
}
?>
<div class="pages-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<div class="pages-form">
		<?php $form = ActiveForm::begin(); ?>
		<!--?= $form->field($model, 'created')->textInput()->label(Yii::t('frontend', 'Created')) ?-->
		<?= $form->field($model, 'title')->textInput(['data-translit' => 'source'])->label(Yii::t('frontend', 'Title')) ?>
		<?= $form->field($model, 'template_id')->dropDownList($templates)->label(Yii::t('frontend', 'Template')) ?>
		<?= $form->field($model, 'url')->textInput(['data-translit' => 'destination'])->label(Yii::t('frontend', 'Url')) ?>
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
		<?php ActiveForm::end(); ?>

	</div>

</div>
