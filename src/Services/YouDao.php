<?php
/**
 * +----------------------------------------------------------------------
 * | laravel-translate [ File Description ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-22 Time:20:49
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Services;

use Hongyukeji\LaravelTranslate\Contracts\TranslationService;

class YouDao implements TranslationService
{
    /**
     * Translate a string using the Google Cloud Translate API.
     *
     * @param string $text
     * @param string $target
     * @return string|null
     */
    public function translate(string $text, string $target): ?string
    {
        $translate = new TranslateClient(config('translate.services.baidu'));

        $translation = $translate->translate($text, [
            'target' => $target,
        ]);

        return $translation['text'];
    }
}