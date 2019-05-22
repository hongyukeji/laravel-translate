<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Driver
    |--------------------------------------------------------------------------
    |
    | This option controls which service you would like to use to obtain
    | translations from. You may set this to one of the options below.
    |
    | Supported: "google"
    |
    */
    'driver' => 'baidu',

    /*
    |--------------------------------------------------------------------------
    | Translation Service Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the services which can be used for translation
    | along with their associated settings. These settings will be passed
    | to the translation service as per their own requirements.
    |
    */
    'services' => [
        'google' => [
            'key' => env('GOOGLE_TRANSLATE_API_KEY')
        ],
        'baidu' => [
            'appid' => env('BAIDU_TRANSLATE_API_APPID'),
            'key'   => env('BAIDU_TRANSLATE_API_KEY')
        ],
        'youdao' => [
            'appid' => env('YOUDAO_TRANSLATE_API_APPID'),
            'key'   => env('YOUDAO_TRANSLATE_API_KEY')
        ]
    ],

    /*
     * Here you can specify the source language code.
     */
    'source_language' => 'en',

    /*
     * Here you can specify the target language code(s). This can be a string or an array.
     */
    'target_language' => ['de'],

    /*
     * Specify the path to the translation files.
     */
    'path' => realpath(base_path('resources/lang')),

    /*
     * This is the translator used to translate the source language files. You can also specify your own here if you wish. It has to implement \Ben182\AutoTranslate\Translators\TranslatorInterface.
     */
    'translator' => \Hongyukeji\LaravelTranslate\Translators\SimpleGoogleTranslator::class,

    'simple_google_translator' => [

        // The translator will wait between these numbers between each request.
        'sleep_between_requests' => [1, 3],

        // If you want to proxy the requests, you can specify a proxy server here.
        'proxy' => '',
    ],

    'deepl' => [

        // Your DeepL API Key. See https://www.deepl.com/pro.html#developer
        'api_key' => '',
    ],
];
