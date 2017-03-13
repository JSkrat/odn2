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

<?php if (0 != $pageModel->id && 1 != $pageModel->id): ?>
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
<?php if ($menus): ?>
<!-- Beware! That code will not work for recursive menus! -->
	<p>
		<?= Html::a(Yii::t('frontend', 'Create menu'), ['updatefield', 'class' => 4, 'page' => $pageModel->id, 'gobackid' => $pageModel->id], ['class' => 'btn btn-primary hidden']) ?>
		<?= Html::a(Yii::t('frontend', 'Create menuitem'), ['updatefield', 'class' => 7, 'page' => $pageModel->id, 'gobackid' => $pageModel->id], ['class' => 'btn btn-primary']) ?>
	</p>
<?php foreach ($menus as $menu): ?>
	<section class="panel panel-default">
		<header class="panel-heading panel-title">
			<span class="pull-right"><?= Html::a(Yii::t('frontend', 'Edit'), ['updatefield', 'id' => $menu['parent']->id, 'gobackid' => $pageModel->id], ['class' => 'hidden']) ?></span>
			<?= Yii::t('frontend', $menu['parent']->name) ?> (<?= $menu['parent']->name ?>)
		</header>
		<!--div class="panel-body">
		</div-->
		<ul class="list-group">
	<?php foreach ($menu['children'] as $item): ?>
			<li class="list-group-item">
				<span class="pull-right"><?= Html::a(Yii::t('frontend', 'Edit'), ['updatefield', 'id' => $item['menuitem']->id, 'gobackid' => $pageModel->id]) ?></span>
		<?php if ('menuitempage' == $item['menuitem']->className): ?>
				<?= $item['menuitem']->caption ?>
				<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
			<?php if ($item['link']): ?>
				<a href="/<?=$item['link']->url ?>"><?= $item['link']->title ?></a>
			<?php else: ?>
				<em><?= Yii::t('frontend', 'deleted') ?></em> (<?= $item['menuitem']->link ?>)
			<?php endif; ?>
		<?php else: ?>
				<em><?= Yii::t('frontend', 'not implemented yet') ?></em>
		<?php endif; ?>
			</li>
	<?php endforeach; ?>
		</ul>
	</section>
<?php endforeach; ?>
	<hr>
<?php endif; // ($menus) ?>
<?php foreach ($objects as $name => $t): ?>
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
			<span class="pull-right"><?= Html::a($action, array_merge(['updatefield', 'class' => $t->classID, 'id' => $t->id, 'gobackid' => $pageModel->id], $addToUrl)) ?></span>
			<?= Yii::t('frontend', $name) ?>
			(<?= Yii::t('frontend', $t->className) ?>)
		</header>
		<div class='panel-body'>
<?php switch (@$t->types['value']): ?>
<?php	case 3: // file ?>
			<?php
			$file = pathInfo($t->value);
			?>
			<!?= Html::img($file['dirname'] . '/' . $file['filename'] . '-fm.' . $file['extension']) ?>
			<?= dosamigos\gallery\Gallery::widget(['items' => [['url' => $t->value, 'src' => $file['dirname'] . '/' . $file['filename'] . '-fm.' . $file['extension']]]]) ?>
<?php	break; ?>
<?php	default: ?>
			<div class="truncate-vertical">
				<?= (isset($t->value))? $t->value: '—' ?>
			</div>
			<div class="fadeout"></div>
<?php endswitch; ?>
			<hr>
			<!--pre><?php print_r($t); ?></pre-->
		</div>
	</section>
<?php endforeach; ?>
</div>
	
