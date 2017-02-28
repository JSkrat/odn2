<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = Yii::t('frontend', $name);
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode(Yii::t('frontend', $message))) ?>
    </div>

    <p>
        <?= Yii::t('frontend', 'The above error occurred while the Web server was processing your request.') ?>
    </p>
    <p>
        <?= Yii::t('frontend', 'Please contact us if you think this is a server error. Thank you.') ?>
    </p>

</div>
