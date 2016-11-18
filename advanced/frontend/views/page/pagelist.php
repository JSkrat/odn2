<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$article = '';
if (isset($templates['article'])) $article = $templates['article']->value;

?>
<?= $article ?>

Страницы категории:
<ul>
<?php foreach ($this->params['menus']['pagelist']['children'] as $pagelink): ?>
	<li><a href='<?= $pagelink['link']->url ?>'><?= $pagelink['menuitem']->caption ?></a></li>
<?php endforeach; ?>
</ul>
