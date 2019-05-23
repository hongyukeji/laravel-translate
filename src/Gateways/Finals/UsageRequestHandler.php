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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:50
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;

use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestHandlerInterface;

final class UsageRequestHandler implements RequestHandlerInterface
{
    const API_ENDPOINT = 'https://api.deepl.com/v2/usage';

    private $authKey;

    public function __construct(string $authKey)
    {
        $this->authKey = $authKey;
    }

    public function getMethod(): string
    {
        return RequestHandlerInterface::METHOD_GET;
    }

    public function getPath(): string
    {
        return static::API_ENDPOINT;
    }

    public function getBody(): array
    {
        return [
            'form_params' => array_filter(
                [
                    'auth_key' => $this->authKey
                ]
            )
        ];
    }
}