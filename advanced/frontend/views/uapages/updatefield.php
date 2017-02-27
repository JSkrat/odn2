<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\RedactorModule;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model app\models\TemplateValues */

/*$action = 'Create';
if ($model->id) $action = 'Update';
$this->title = Yii::t('frontend', $action . ' {modelClass}: ', [
    'modelClass' => Yii::t('frontend', 'Pages'),
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Pages'), 'url' => ['index']];
if ($model->id) $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;*/


$this->title = Yii::t('frontend', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('frontend', $model->name),
]);
//$this->params['breadcrumbs'][] = ['label' => 'Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Pages'), 'url' => ['uapages/index']];
if (1 == count($ownerPages)) {
	$this->params['breadcrumbs'][] = ['label' => array_flip($ownerPages)[0], 'url' => ['uapages/view', 'id' => array_flip($ownerPages)[0]]];
}
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', $model->name), 'url' => ['index', 'class' => $classModel->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', (empty($model->id))?'Create new field':'Update field');
?>
<div class="template-values-update">
    <h1><?= Html::encode($this->title) ?></h1>
	<div class="template-values-form">
		<!--<pre><?php print_r($model); ?></pre>-->
		<!--<pre><?php print_r($fields); ?></pre>-->
		<?php $form = ActiveForm::begin(); ?>
			<?php echo $form->field($model, 'name')->textInput(['readonly' => $block])->label(Yii::t('frontend', '[Name]')); ?>
			<?php foreach ($fields as $f): 
				switch ($f->namedType->type_name) {
				case 'text':
					echo $form->field($model, $f->name)->textInput()->label(Yii::t('frontend', $f->name));
					break;
				case 'formatted text':
					echo $form->field($model, $f->name)->widget(Redactor::className())->label(Yii::t('frontend', $f->name));
					break;
				case 'object':
					echo $form->field($model, $f->name)->dropDownList($allowedClasses[$f->name])->label(Yii::t('frontend', $f->name));
					break;
				case 'page':
					echo $form->field($model, $f->name)->dropDownList($allPagesList)->label(Yii::t('frontend', $f->name));
					break;
				}
			?>
			<?php endforeach; ?>
			<hr>
			<ul <?php if ($block) echo 'hidden=true'; ?>>
				<?php if ($block) $ro = 'disabled=true'; else $ro = ''; ?>
				<?php foreach ($allPagesCheckboxen as $id => $page): ?>
				<li>
					<label for="page_<?= $id ?>">
						<input type="checkbox" name="pages[<?= $id ?>]" id="page_<?= $id ?>" <?= ($page['checked'])? 'checked': '' ?> <?= $ro ?> >
						<?php if ($block && $page['checked']): // disabled checkboxen doesn't send to server in any state ?>
						<input type='hidden' name='pages[<?= $id ?>]' value='true' >
						<?php endif; ?>
						<?= $page['caption'] ?>
					</label>
				</li>
				<?php endforeach; ?>
			</ul>
			<div class="form-group">
				<?= Html::submitButton(
						Yii::t('frontend', $model->isNewRecord ? 'Create' : 'Update'), 
						['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
				) ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>
