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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:46
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;

use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestFactoryInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestHandlerInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

final class RequestFactory implements RequestFactoryInterface
{
    private $api_endpoint;
    private $appId;
    private $key;

    public function __construct(string $api_endpoint = null, string $appId = null, string $key = null)
    {
        $this->api_endpoint = $api_endpoint;
        $this->appId = $appId;
        $this->key = $key;
    }

    public function createTranslationRequestHandler(TranslationConfigInterface $translation): RequestHandlerInterface
    {
        return new TranslationRequestHandler($this->api_endpoint, $this->appId, $this->key, $translation);
    }

    public function createUsageRequestHandler(): RequestHandlerInterface
    {
        return new UsageRequestHandler($this->api_endpoint, $this->appId, $this->key);
    }
}