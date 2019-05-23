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
 * | Version: v1.0.0  Date:2019-05-23 Time:15:05
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Translators;

use Hongyukeji\LaravelTranslate\Exceptions\LanguageCodeNotExist;
use Hongyukeji\LaravelTranslate\Gateways\Exceptions\RequestException;
use Hongyukeji\LaravelTranslate\Gateways\Finals\TranslationConfig;
use Hongyukeji\LaravelTranslate\Gateways\YouDaoGateway\YouDaoGateway;

class YouDaoTranslator implements TranslatorInterface
{
    protected $translator;
    protected $source;
    protected $target;

    public function __construct()
    {
        $appId = config('translate.gateways.youdao.app_id');
        $key = config('translate.gateways.youdao.key');
        if (!empty($appId) && !empty($key)) {
            $this->translator = YouDaoGateway::create($appId, $key);
        }
    }

    public function setSource(string $source)
    {
        $this->source = strtoupper($source);

        return $this;
    }

    public function setTarget(string $target)
    {
        $this->target = strtoupper($target);

        return $this;
    }

    public function translate(string $string): string
    {
        $translation = new TranslationConfig(
            $string,
            $this->target,
            $this->source
        );

        try {
            return $this->translator->getTranslation($translation)->getText();
        } catch (RequestException $th) {
            if ($th->getMessage() === '400 {"message":"Value for \'target_lang\' is not supported."}') {
                throw LanguageCodeNotExist::throw($this->source, $this->target);
            }

            throw $th;
        }
    }
}