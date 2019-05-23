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
 * | Version: v1.0.0  Date:2019-05-23 Time:13:27
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\YouDao;

use Hongyukeji\LaravelTranslate\Gateways\GatewayInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

class YouDaoGateway implements GatewayInterface
{

    public function getUsage(): ResponseModelInterface
    {
        // TODO: Implement getUsage() method.
    }

    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface
    {
        // TODO: Implement getTranslation() method.
    }

    public function translate(string $text, string $target_language): ResponseModelInterface
    {
        // TODO: Implement translate() method.
    }

    public static function create(string $apiKey): GatewayInterface
    {
        // TODO: Implement create() method.
    }
}