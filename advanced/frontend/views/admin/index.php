<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (isset($classModel)) {
	$this->title = $classModel->name;
	$this->params['breadcrumbs'][] = ['label' => 'Classes', 'url' => ['index']];
} else {
	$this->title = 'Classes';
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-values-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php if (isset($classModel)): ?>
    <p>
        <?= Html::a('Create ' . $classModel->name, ['update', 'class' => $classModel->id], ['class' => 'btn btn-success']) ?>
    </p>
	<?php endif; ?>
	<?php
		array_unshift($fields, ['class' => 'yii\grid\SerialColumn']);
		$buttons = ['class' => 'yii\grid\ActionColumn'];
		if (! isset($classModel)) {
			$buttons['visibleButtons'] = ['update' => false, 'delete' => false];
			$buttons['urlCreator'] = function ($action, $model, $key, $index) {
				if ('view' == $action) {
					return '?class=' . $key;
				}
			};
		}
		array_push($fields, $buttons);
	?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $fields,
    ]); ?>
</div>
