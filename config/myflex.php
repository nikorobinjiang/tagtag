<?php

return [
    'api' => [
        'token_name' => 'self',
        'prefix' => 'api',
        'middleware'    => ['api', 'nv:api'],
    ],
    'sign_key' => env('MF_SIGN_KEY', 'myflex'),
    'superusers' => ['root'],
    'permission' => [],
    'token' => [
        'name' => 'Token Name'
    ],
    'operation_log'   => [
        'enable' => false,
        'except' => []
    ],
    'upload'           => [
        'folders'      => [
            'docs'   => [
                'doc', 'docx', 'docm', 'dotx', 'dotm',
                'xls', 'xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam',
                'ppt', 'pptx', 'pptm', 'ppsx', 'ppsm', 'potx', 'potm', 'ppam'
            ],
            'images' => ['jpeg', 'jpg', 'png', 'gif'],
            'videos' => ['mp4'],
            'files'  => ['txt', 'zip'],
            'others' => ['others'],
        ],
        'unzip_folder' => 'unzip',
    ],
];
