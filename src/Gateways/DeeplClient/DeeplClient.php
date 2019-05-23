<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Exception\RequestException;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler\DeeplRequestFactoryInterface;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler\DeeplRequestHandlerInterface;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\Translation;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\TranslationConfig;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\TranslationConfigInterface;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\Usage;

/**
 * Class DeeplClient
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient
 */
class DeeplClient implements DeeplClientInterface
{
    private $httpClient;

    private $requestFactory;

    public function __construct(ClientInterface $httpClient, DeeplRequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Return Usage of API- Key
     * Possible Return:
     *
     * Usage
     *      -> characterCount 123
     *      -> characterLimit 5647
     *
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsage(): ResponseModelInterface
    {
        return (new Usage())->hydrate(
            $this->executeRequest($this->requestFactory->createDeeplUsageRequestHandler())
        );
    }

    /**
     * Return TranslationConfig from given TranslationConfig Object
     * Possible Return:
     *
     * Translation
     *      -> detectedSourceLanguage EN
     *                -> text some translated text
     *
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface
    {
        return (new Translation())->hydrate($this->executeRequest(
            $this->requestFactory->createDeeplTranslationRequestHandler($translation)
        ));
    }

    public static function create($apiKey): DeeplClientInterface
    {
        return new DeeplClient(
            new \GuzzleHttp\Client(),
            new Handler\DeeplRequestFactory($apiKey)
        );
    }

    /**
     * Return TranslationConfig for given Text / Target Language with Default TranslationConfig Configuration
     *
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function translate(string $text, string $target_language): ResponseModelInterface
    {
        $translation = new TranslationConfig($text, $target_language);

        return $this->getTranslation($translation);
    }

    /**
     * Execute given RequestHandler Request and returns decoded Json Object or throws Exception with Error Code
     * and maybe given Error Message
     *
     * @throws RequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function executeRequest(DeeplRequestHandlerInterface $requestHandler): \stdClass
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
