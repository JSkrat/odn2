<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $pageModel frontend\models\Pages */

$this->title = $pageModel->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-view">

    <h1><?= Html::encode($this->title) ?></h1>

<?php if (0 != $pageModel->id): ?>
    <p>
        <?= Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $pageModel->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $pageModel->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $pageModel,
        'attributes' => [
            'id',
            'created',
            'title:ntext',
            'template.name:ntext:Template name',
            'url:ntext',
        ],
    ]) ?>
<?php endif; ?>
	<hr>
<?php foreach ($templates as $name => $t): ?>
	<?php if ('integer' == gettype($t)) {
		$t = (object) [
			'className' => '',
			'classID' => $t,
			'id' => 0,
			'value' => '',
		];
		$action = Yii::t('frontend', 'Create');
		$addToUrl = ['name' => $name, 'page' => $pageModel->id];
	} else {
		$action = Yii::t('frontend', 'Edit');
		$addToUrl = [];
	} ?>
	<section class='panel panel-default'>
		<header class='panel-heading panel-title'>
			<?= $name ?>
			(<?= $t->className ?>)
			<a href='<?= Url::to(array_merge(['admin/update', 'class' => $t->classID, 'id' => $t->id, 'block' => 'true'], $addToUrl)) ?>'><?= $action ?></a>
		</header>
		<div class='panel-body'>
			<?= (isset($t->value))? $t->value: '—' ?>
		</div>
	</section>
<?php endforeach; ?>
</div>
