<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('storage/app/mpdf'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => '',
    'default_font'          => 'notoserifbengali',
    'font_path'       => base_path('storage/fonts/'), // Path to font directory
    'font_data'      => [
        'notoserifbengali' => [
            'R' => 'NotoSerifBengali-Regular.ttf', // Bangla font file
        ],
    ],
    'useOTL'                => true, // Enable OpenType Layout for proper shaping
    'useKashida'            => false, // Disa
];
