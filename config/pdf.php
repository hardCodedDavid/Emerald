<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),
    'font_path' => base_path('resources/fonts/'),
    'font_data' => [
        'satisfy' => [
            'R'  => 'Satisfy-Regular.ttf',    // regular font
        ],
        'akayatelivigala' => [
            'R'  => 'AkayaTelivigala-Regular.ttf',    // regular font
        ]
    ]
];
