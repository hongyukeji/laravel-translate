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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:42
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;

use Hongyukeji\LaravelTranslate\Gateways\Abstracts\AbstractResponseModel;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationInterface;

final class Translation extends AbstractResponseModel implements TranslationInterface
{

    private $detectedSourceLanguage;

    private $text;

    public function getDetectedSourceLanguage(): string
    {
        return $this->detectedSourceLanguage;
    }

    public function setDetectedSourceLanguage(string $detectedSourceLanguage): TranslationInterface
    {
        $this->detectedSourceLanguage = $detectedSourceLanguage;

        return $this;
    }

    public function getText(): string
    {
        return $this->text ?? '';
    }

    public function setText(string $text): TranslationInterface
    {
        $this->text = $text;

        return $this;
    }
}