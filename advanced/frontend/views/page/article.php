<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$article = '<i>no text here</i>';
if (isset($templates['article'])) $article = $templates['article']->value;

?>
<?= $article ?>