<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler;

use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\TranslationConfigInterface;

/**
 * Interface DeeplRequestFactoryInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Handler
 */
interface DeeplRequestFactoryInterface
{

    public function createDeeplTranslationRequestHandler(TranslationConfigInterface $translation
    ): DeeplRequestHandlerInterface;

    public function createDeeplUsageRequestHandler(): DeeplRequestHandlerInterface;
}