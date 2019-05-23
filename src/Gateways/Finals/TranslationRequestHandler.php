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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:48
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;


use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestHandlerInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

final class TranslationRequestHandler implements RequestHandlerInterface
{
    const SEPARATOR = ',';

    private $api_endpoint;
    private $appId;
    private $key;

    private $translation;

    public function __construct(string $api_endpoint = null, string $appId = null, string $key = null, TranslationConfigInterface $translation)
    {
        $this->api_endpoint = $api_endpoint;
        $this->appId = $appId;
        $this->key = $key;
        $this->translation = $translation;
    }

    public function getMethod(): string
    {
        return RequestHandlerInterface::METHOD_POST;
    }

    public function getPath(): string
    {
        return $this->api_endpoint;
    }

    public function getBody(): array
    {
        $salt = rand(10000, 99999);
        $curtime = strtotime("now");
        return [
            'form_params' => array_filter(
                [
                    'q'        => $this->translation->getText(),
                    'from'     => strtolower($this->translation->getSourceLang()),
                    'to'       => strtolower($this->translation->getTargetLang()),
                    'appKey'   => $this->appId,
                    'salt'     => $salt,
                    'sign'     => hash("sha256", $this->appId . $this->truncate($this->translation->getText()) . $salt . $curtime . $this->key),
                    'signType' => 'v3',
                    'curtime'  => $curtime,
                ]
            )
        ];
    }

    function truncate($q)
    {
        $len = strlen($q);
        return $len <= 20 ? $q : (substr($q, 0, 10) . $len . substr($q, $len - 10, $len));
    }
}