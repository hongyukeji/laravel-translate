<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler;

/**
 * Interface TranslationRequestHandlerInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler
 */
interface DeeplRequestHandlerInterface
{

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public function getMethod(): string;

    public function getPath(): string;

    public function getBody(): array;
}