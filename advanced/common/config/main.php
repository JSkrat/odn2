<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
	'modules' => [
		'redactor' => [
			'class' => 'yii\redactor\RedactorModule',
			'uploadDir' => '@webroot/images',
			'uploadUrl' => '@web/images',
		],
		'imagegallery' => [
			'class' => 'dosamigos\gallery\Gallery',
			
		]
	],
];
