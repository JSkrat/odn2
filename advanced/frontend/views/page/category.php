<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// even category is just a page, so why it can't have a little text?
$article = '';
if (isset($objects['article'])) $article = $objects['article']->value;

?>
<?= $article ?>

Страницы категории:
<ul>
<?php foreach ($childPages->all() as $page): ?>
	<li><a href='<?= $page->url ?>'><?= $page->title ?></a></li>
<?php endforeach; ?>
</ul>
