<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$items = [];
foreach ($list as $item) {
	$items[] = [
		'url' => $item['fullname'],
		'src' => $item['thumb'],
		'options' => [
			'title' => $item['filename'],
		],
	];
}
?>

<?= dosamigos\gallery\Gallery::widget(['items' => $items]); ?>