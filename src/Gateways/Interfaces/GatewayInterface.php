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
 * | Version: v1.0.0  Date:2019-05-23 Time:13:28
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways;

use Hongyukeji\LaravelTranslate\Gateways\Interfaces\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

interface GatewayInterface
{
    public function getUsage(): ResponseModelInterface;

    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface;

    public function translate(string $text, string $target_language): ResponseModelInterface;

    public static function create(string $apiKey): GatewayInterface;
}