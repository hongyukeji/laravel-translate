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

namespace Hongyukeji\LaravelTranslate\Gateways\YouDaoGateway;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Hongyukeji\LaravelTranslate\Gateways\Exceptions\RequestException;
use Hongyukeji\LaravelTranslate\Gateways\Finals\RequestFactory;
use Hongyukeji\LaravelTranslate\Gateways\Finals\Translation;
use Hongyukeji\LaravelTranslate\Gateways\Finals\TranslationConfig;
use Hongyukeji\LaravelTranslate\Gateways\Finals\Usage;
use Hongyukeji\LaravelTranslate\Gateways\GatewayInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestFactoryInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestHandlerInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

class YouDaoGateway implements GatewayInterface
{
    const API_ENDPOINT = 'http://openapi.youdao.com/api';

    private $httpClient;

    private $requestFactory;

    /**
     * YouDaoGateway constructor.
     *
     * @see https://ai.youdao.com/docs/doc-trans-api.s
     *
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return ResponseModelInterface
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsage(): ResponseModelInterface
    {
        return (new Usage())->hydrate(
            $this->executeRequest($this->requestFactory->createUsageRequestHandler())
        );
    }

    /**
     * @param TranslationConfigInterface $translation
     * @return ResponseModelInterface
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface
    {
        return (new Translation())->hydrate($this->executeRequest(
            $this->requestFactory->createTranslationRequestHandler($translation)
        ));
    }

    /**
     * @param string $text
     * @param string $target_language
     * @return ResponseModelInterface
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function translate(string $text, string $target_language): ResponseModelInterface
    {
        $translation = new TranslationConfig($text, $target_language);

        return $this->getTranslation($translation);
    }

    /**
     * @param string|null $appId
     * @param string|null $key
     * @return GatewayInterface
     */
    public static function create(string $appId = null, string $key = null): GatewayInterface
    {
        return new self(
            new \GuzzleHttp\Client(),
            new RequestFactory(self::API_ENDPOINT, $appId, $key)
        );
    }

    /**
     * @param RequestHandlerInterface $requestHandler
     * @return \stdClass
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function executeRequest(RequestHandlerInterface $requestHandler): \stdClass
    {
        try {
            $response = $this->httpClient->request(
                $requestHandler->getMethod(),
                $requestHandler->getPath(),
                $requestHandler->getBody()
            );

            return \GuzzleHttp\json_decode($response->getBody()->getContents());
        } catch (ClientException $exception) {
            throw new RequestException(
                $exception->getCode() .
                ' ' .
                $exception->getResponse()->getBody()->getContents()
            );
        }
    }
}