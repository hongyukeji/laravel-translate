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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:41
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Interfaces;


interface RequestHandlerInterface
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public function getMethod(): string;

    public function getPath(): string;

    public function getBody(): array;
}