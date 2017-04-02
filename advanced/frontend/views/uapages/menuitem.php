<?php
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

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