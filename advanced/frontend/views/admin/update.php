<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\RedactorModule;

/* @var $this yii\web\View */
/* @var $model app\models\TemplateValues */

$this->title = 'Update Template: ';
$this->params['breadcrumbs'][] = ['label' => 'Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $classModel->name, 'url' => ['index', 'class' => $classModel->id]];
$this->params['breadcrumbs'][] = (empty($model->id))?'Create new template':'Update template';
?>
<div class="template-values-update">
    <h1><?= Html::encode($this->title) ?></h1>
	<div class="template-values-form">
		<!--<pre><?php print_r($model); ?></pre>-->
		<!--<pre><?php print_r($fields); ?></pre>-->
		<?php $form = ActiveForm::begin(); ?>
			<?php echo $form->field($model, 'name')->textInput(['readonly' => $block]); ?>
			<?php foreach ($fields as $f): 
				switch ($f->namedType->type_name) {
				case 'text':
					echo $form->field($model, $f->name)->textInput();
					break;
				case 'formatted text':
					echo $form->field($model, $f->name)->widget(\yii\redactor\widgets\Redactor::className());
					break;
				case 'object':
					echo $form->field($model, $f->name)->dropDownList($allowedClasses[$f->name]);
					break;
				case 'page':
					echo $form->field($model, $f->name)->dropDownList($allPagesList);
					break;
				}
			?>
			<?php endforeach; ?>
			<hr>
			<ul>
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
						$model->isNewRecord ? 'Create' : 'Update', 
						['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
				) ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>

</div>
