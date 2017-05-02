<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\redactor\widgets\Redactor;
use pendalf89\filemanager\widgets\TinyMCE;
use pendalf89\filemanager\widgets\FileInput;

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
//$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', $model->name), 'url' => ['index', 'class' => $classModel->id]];
if (1 == count($ownerPages)) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Page') . ' ' . array_flip($ownerPages)[0], 'url' => ['uapages/view', 'id' => array_flip($ownerPages)[0]]];
}
$this->params['breadcrumbs'][] = Yii::t('frontend', (empty($model->id))?'Create new field':'Update field');
?>
<div class="template-values-update">
    <h1><?= Html::encode($this->title) ?></h1>
	<div class="template-values-form">
		<!--<pre><?php print_r($model); ?></pre>-->
		<!--<pre><?php print_r($fields); ?></pre>-->
		<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
			<span <?= ($block)? 'class="hidden"': '' ?>>
				<?= $form->field($model, 'name')->textInput(['readonly' => $block])->label(Yii::t('frontend', '[Name]')) ?>
			</span>
			<?php foreach ($fields as $f): 
				switch ($model->types[$f->name]) {
				case 1: // text
					echo $form->field($model, $f->name)->textInput()->label(Yii::t('frontend', $f->name));
					break;
				case 3: // file
					echo Html::img($model->{$f->name}, ['class' => 'img-thumbnail']);
//					echo $form->field($model, $f->name)->fileInput()->label(Yii::t('frontend', $f->name));
					echo $form->field($model, $f->name)->widget(FileInput::className(), [
//						'imageContainer' => '.img',
//						'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>{image}{img}{src}{url}',
						]);
					break;
				case 4: // formatted text
//					echo $form->field($model, $f->name)->widget(Redactor::className())->label(Yii::t('frontend', $f->name));
					echo $form->field($model, $f->name)->widget(TinyMce::className(), [
						'clientOptions' => [
							'language' => 'ru',
							'menubar' => true,
							'height' => 500,
							'image_dimensions' => false,
							'image_advtab' => true,
							'plugins' => [
								'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code contextmenu table media',
							],
							'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code',
//							'setup' => 'setupTinymce',
						],
					]);
					/*echo FileInput::widget([
						'name' => '',
						'options' => ['id' => 'iframe-tinymce'],
						'callbackBeforeInsert' => 'function(e, data) {
							console.log( data );
						}',
//						'imageContainer' => '.img',
//						'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>{image}{img}{src}{url}',
						]);*/
					break;
				case 5: // object
					echo $form->field($model, $f->name)->dropDownList($allowedClasses[$f->name])->label(Yii::t('frontend', $f->name));
					break;
				case 7: // page
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
