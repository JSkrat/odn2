<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
	'modules' => [
		'imagegallery' => [
			'class' => 'dosamigos\gallery\Gallery',			
		],
		'filemanager' => [
			'class' => 'pendalf89\filemanager\Module',
			// Upload routes
			'routes' => [
				// Base absolute path to web directory
				'baseUrl' => '',
				// Base web directory url
				'basePath' => '@frontend/web',
				// Path for uploaded files in web directory
				'uploadPath' => 'uploads',
			],
			// Thumbnails info
			'thumbs' => [
				'small' => [
					'name' => 'Мелкий',
					'size' => [100, 100],
				],
				'medium' => [
					'name' => 'Средний',
					'size' => [300, 200],
				],
				'large' => [
					'name' => 'Большой',
					'size' => [500, 400],
				],
			],
		],
        'utility' => [ 'class' => 'c006\utility\migration\Module', ],
	],
];
