<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$limit = function ($model, $key, $index, $column) {
	print_r([$model, $key, $index]); die();
	return mb_strcut($data, 0, 128);
};

$this->title = Yii::t('frontend', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pages-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create Pages'), ['update'], ['class' => 'btn btn-success']) ?>
    </p>

<?php
$dataProvider->sort->attributes['template.name'] = [
	'asc' => ['template_id' => SORT_ASC],
	'desc' => ['template_id' => SORT_DESC],
];
?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            //'title:ntext:' . Yii::t('frontend', 'Title'),
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
//            'url:ntext:' . Yii::t('frontend', 'Url'),
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
            // 'views',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
