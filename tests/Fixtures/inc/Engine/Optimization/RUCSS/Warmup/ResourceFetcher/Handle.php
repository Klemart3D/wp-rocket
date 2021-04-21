<?php

return [

	'vfs_dir' => 'public/',

	'structure' => [

		'css'     => [
			'style1.css'      => '.first{color:red;}',
			'style2.css'      => '.second{color:green;}',
			'style3.css'      => '.third{color:#000000;}',
			'style-empty.css' => '',
			'stylewithimport.css' => '@import "style1.css";.another-class-in-stylewithimport{color: white;}',
			'stylewithimportedmqs.css' => '@import "style3.css" screen;.another-imported-class{color: blue;}',
			'stylewithimport-recursion.css' => '@import "stylewithimport-recursion.css";.another-class-in-stylewithimport-recursion{color: white;}',
			'stylewithrelativepathimport.css' => '@import "./../relativelypathedstyles.css";.some-imported-class{color:pink;}',
		],
		'scripts' => [
			'script1.js' => 'var first = "content 1";',
			'script2.js' => 'var second = "content 2";',
			'script3.js' => 'var third = "content 3";',
		],
		'relativelypathedstyles.css' => '.relatively-pathed-imported-class{color:black;}'
	],

	'test_data' => [
		'shouldBailoutWithNoHTMLContent' => [
			'input'    => [
				'html' => '',
			],
			'expected' => [
				'resources' => [],
			],
		],

		'shouldBailoutWithNoResourcesInHTML' => [
			'input'    => [
				'html' => '<!DOCTYPE html><html><head><title></title></head><body>Content here</body></html>',
			],
			'expected' => [
				'resources' => [],
			],
		],

		'shouldBailoutWithNotFoundResourcesOrEmptyContent' => [
			'input'    => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="http://example.org/css/style-empty.css">' .
						  '<link rel="stylesheet" type="text/css" href="http://example.org/css/style-notfound.css">' .
						  '</head><body>Content here</body></html>',
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/style-empty.css',
						'content' => '*',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/css/style-notfound.css',
						'content' => '*',
						'type'    => 'css',
						'media'   => 'all'
					]
				],
			],
		],

		'shouldQueueResources' => [
			'input'    => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="http://example.org/css/style1.css?ver=123">' .
						  '<link rel="stylesheet" type="text/css" href="http://example.org/css/style2.css">' .

						  '<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">' .
						  '<link href="https://fonts.googleapis.com/css?family=Roboto:wght@100&display=swap" rel="stylesheet">' .
						  '<link href="//fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet">' .
						  '<link href="//fonts.googleapis.com/css?family=Roboto:wght@100&display=swap" rel="stylesheet">' .

						  '<script type="application/ld+json" src="http://example.org/scripts/script1.js"></script>' .
						  '<script src="http://example.org/scripts/script2.js"></script>' .
						  '</head><body>Content here</body></html>',
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/style1.css?ver=123',
						'content' => '.first{color:red;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/css/style2.css',
						'content' => '.second{color:green;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script2.js',
						'content' => 'var second = "content 2";',
						'type'    => 'js'
					]
				],
			],
		],

		'shouldQueueResourcesWithMedias' => [
			'input'    => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="http://example.org/css/style1.css?ver=123" media="all">' .
						  '<link media="print" rel="stylesheet" type="text/css" href="http://example.org/css/style2.css">' .
						  '</head><body>Content here</body></html>',
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/style1.css?ver=123',
						'content' => '.first{color:red;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/css/style2.css',
						'content' => '.second{color:green;}',
						'type'    => 'css',
						'media'   => 'print'
					]

				],
			],
		],

		'shouldQueueResourcesWithoutSchema' => [
			'input'    => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="//example.org/css/style1.css?ver=123">' .
						  '<script type="text/javascript" src="//example.org/scripts/script1.js"></script>' .
						  '</head><body>Content here</body></html>',
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/style1.css?ver=123',
						'content' => '.first{color:red;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script1.js',
						'content' => 'var first = "content 1";',
						'type'    => 'js'
					]
				],
			],
		],

		'shouldFindAndQueueResourcesFoundFromCSSImport' => [
			'input' => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="//example.org/css/stylewithimport.css?ver=123">' .
						  '<script type="text/javascript" src="//example.org/scripts/script1.js"></script>' .
						  '</head><body>Content here</body></html>'
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/stylewithimport.css?ver=123',
						'content' => '.first{color:red;}.another-class-in-stylewithimport{color: white;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script1.js',
						'content' => 'var first = "content 1";',
						'type'    => 'js'
					],
				],
			],
		],

		'shouldFindAndQueueResourcesWithMediaQueryFoundFromCSSImport' => [
			'input' => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="//example.org/css/stylewithimportedmqs.css?ver=123">' .
						  '<script type="text/javascript" src="//example.org/scripts/script1.js"></script>' .
						  '</head><body>Content here</body></html>'
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/stylewithimportedmqs.css?ver=123',
						'content' => '@media screen{.third{color:#000000;}}.another-imported-class{color: blue;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script1.js',
						'content' => 'var first = "content 1";',
						'type'    => 'js'
					],
				],
			],
		],

		'shouldFindAndQueueResourcesWithRelativePathCSSImport' => [
			'input' => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="//example.org/css/stylewithrelativepathimport.css?ver=123">' .
						  '<script type="text/javascript" src="//example.org/scripts/script1.js"></script>' .
						  '</head><body>Content here</body></html>'
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/stylewithrelativepathimport.css?ver=123',
						'content' => '.relatively-pathed-imported-class{color:black;}.some-imported-class{color:pink;}',
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script1.js',
						'content' => 'var first = "content 1";',
						'type'    => 'js'
					],
				],
			],
		],


		'shouldNotRequeueResourcesFoundFromRecursiveCSSImport' => [
			'input' => [
				'html' => '<!DOCTYPE html><html><head><title></title>' .
						  '<link rel="stylesheet" type="text/css" href="//example.org/css/stylewithimport-recursion.css?ver=123">' .
						  '<script type="text/javascript" src="//example.org/scripts/script1.js"></script>' .
						  '</head><body>Content here</body></html>'
			],
			'expected' => [
				'resources' => [
					[
						'url'     => 'http://example.org/css/stylewithimport-recursion.css?ver=123',
						'content' => ".another-class-in-stylewithimport-recursion{color: white;}",
						'type'    => 'css',
						'media'   => 'all'
					],
					[
						'url'     => 'http://example.org/scripts/script1.js',
						'content' => 'var first = "content 1";',
						'type'    => 'js'
					],
				],
			],
		],
	],
];