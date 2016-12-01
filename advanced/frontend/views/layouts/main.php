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
<div class='wrap'><div class='container-fluid'>
	<div class="row">
		<div class="pull-left"><?= $this->params['logo'] ?></div>
		<div class="center-block"><?= $this->params['header'] ?></div>
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
	foreach ($this->params['menus']['navmenu']['children'] as $item) {
		$menuItems[] = array(
			'label' => ' ' . $item['menuitem']->caption . ' ',
			'url' => $item['link']->url,
		);
	}
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
	</div>
    <div class="container row">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
		<div class='col-md-3'>
			<div class='container'>
				<header>Встречи ОДН</header>
				<ul>
<?php foreach ($this->params['menus']['meetings']['children'] as $item): ?>
					<li><a href='<?= $item['link']->url ?>'><?= $item['menuitem']->caption ?></a></li>
<?php endforeach; ?>
				</ul>
			</div>
			<div class='container'>Последние материалы</div>
			<div class='container'>Самые читаемые</div>
			Наши партнёры
		</div>
		<div class='col-md-9'>
			<?= $content ?>
		</div>
    </div>
</div></div>
<footer class="footer">
	<div class="container">
		<p class="pull-left">&copy; My Company <?= date('Y') ?></p>
	</div>
</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
