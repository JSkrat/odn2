<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Pages'), ['update'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            'title:ntext',
			[
				'attribute' => 'title',
				'label' => Yii::t('frontend', 'Title'),
				'value' => function ($model) {
					$dots = '';
					if (mb_strlen($model->title) > 50) $dots = '...';
					return mb_substr($model->title, 0, 50) . $dots;
				},
			],
            'template.name:ntext:' . Yii::t('frontend', 'Template'),
//            'url:ntext',
			[
				'attribute' => 'url',
				'label' => Yii::t('frontend', 'Url'),
				'value' => function ($model) {
					$dots = '';
					if (mb_strlen($model->url) > 50) $dots = '...';
					return mb_substr($model->url, 0, 50) . $dots;
				},
			],
            'created:datetime:' . Yii::t('frontend', 'Created'),
            [
				'class' => 'yii\grid\ActionColumn',
				'visibleButtons' => [
					'delete' => function ($model, $key, $index) {
						return $model->id != 1;
					},
					'update' => function ($model, $key, $index) {
						return $model->id != 1;
					}
				]
			],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
