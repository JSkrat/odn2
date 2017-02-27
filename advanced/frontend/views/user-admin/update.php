<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Pages */
/* @var $form yii\widgets\ActiveForm */

$action = 'Create';
if ($model->id) $action = 'Update';
$this->title = Yii::t('frontend', $action . ' {modelClass}: ', [
    'modelClass' => Yii::t('frontend', 'Pages'),
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Pages'), 'url' => ['index']];
if ($model->id) $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-update">
    <h1><?= Html::encode($this->title) ?></h1>
	<div class="pages-form">
		<?php $form = ActiveForm::begin(); ?>
		<?= $form->field($model, 'created')->textInput(['readonly' => 'readonly']) ?>
		<?= $form->field($model, 'title')->textInput() ?>
		<?= $form->field($model, 'template_id')->textInput() ?>
		<?= $form->field($model, 'url')->textInput() ?>
		<?= $form->field($model, 'views')->textInput(['readonly' => 'readonly']) ?>
		<div class="form-group">
			<?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
	<div class="objects-form">
		<?php foreach ($objects as $item): ?>
		<div class="object-form panel panel-default">
			<header class="panel-heading" title="<?= $item->name ?> (<?= $item->className ?>)">
				<?= Yii::t('frontend', $item->name) ?>
			</header>
			<div class="panel-body">
				<?php foreach ($item->allFields as $field): if ('name' != $field): ?>
				<div class="field-form input-group">
				<?php switch ($item->types[$field]):
				case 4: // formatted text ?>
					<label for="id-<?= $field ?>"><?= Yii::t('frontend', $field) ?></label>
					<textarea class="form-control" rows="25" cols="160" id="id-<?= $field ?>" name="<?= $field ?>"><?= $item->$field ?></textarea>
				<?php break;
				default: ?>
					<span class="input-group-addon"><?= Yii::t('frontend', $field) ?></span>
					<input type="text" class="form-control" placeholder="" name="<?= $field ?>" value="<?= $item->$field ?>" >
				<?php endswitch; ?>
				</div>
				<br/>
				<?php endif; endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<pre><?php print_r($objects); ?></pre>
</div>
