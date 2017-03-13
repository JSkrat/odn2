<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class='wrap'><div><div class='container'>
	<div class="row">
		<div class="pull-left"><?= Html::img(@$this->params['logo']) ?></div>
		<div class="text-center"><?= Html::img(@$this->params['header']) ?></div>
		<div class="pull-right">search</div>
	</div>
	<div class='row'>
    <?php
    NavBar::begin([
        'brandLabel' => false,
//        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => '',
        ],
    ]);
	$menuItems = array();
	if (isset($this->params['menus'])) foreach ($this->params['menus']['navmenu']['children'] as $item) {
		if (isset($item['link']->url)) {
			$menuItems[] = array(
				'label' => ' ' . $item['menuitem']->caption . ' ',
				'url' => $item['link']->url,
			);
		} else {
			$menuItems[] = array(
				'label' => ' <s>' . $item['menuitem']->caption . '</s> (' . Yii::t('frontend', 'deleted') . ') ',
				'url' => Null,
				
			);
		}
	}
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
		'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>
	</div>
    <div class="row">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
		<div class='col-md-3'>
			<div class=''>
				<header>Встречи ОДН</header>
				<ul>
<?php if (isset($this->params['menus'])) foreach ($this->params['menus']['meetings']['children'] as $item): ?>
<?php if (isset($item['link']->url)): ?>
					<li><a href='<?= $item['link']->url ?>'><?= $item['menuitem']->caption ?></a></li>
<?php else: ?>
					<li><a><s><?= $item['menuitem']->caption ?></s> (<?= Yii::t('frontend', 'deleted') ?>)</a></li>
<?php endif; ?>
<?php endforeach; ?>
				</ul>
			</div>
			<div class=''>
				<header>Последние материалы</header>
				<ul>
<?php if (isset($this->params['lastPages'])) foreach ($this->params['lastPages'] as $item): ?>
					<li><a href='<?= $item->url ?>'><?= $item->title ?></a></li>
<?php endforeach; ?>
				</ul>
			</div>
			<div class=''>
				<header>Самые читаемые</header>
				<ul>
<?php if (isset($this->params['popularPages'])) foreach ($this->params['popularPages'] as $item): ?>
					<li><a href='<?= $item->url ?>'><?= $item->title ?></a></li>
<?php endforeach; ?>
				</ul>
			</div>
			Наши партнёры
		</div>
		<div class='col-md-9'>
			<?= $content ?>
		</div>
    </div>
</div></div></div>
<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; Mintytail <?= date('Y') ?></p>
	</div>
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
