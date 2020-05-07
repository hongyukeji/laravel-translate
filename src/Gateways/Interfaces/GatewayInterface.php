<?php

namespace Hongyukeji\LaravelTranslate\Gateways\Interfaces;

use Hongyukeji\LaravelTranslate\Gateways\Interfaces\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

interface GatewayInterface
{
    /**
     * @return ResponseModelInterface
     */
    public function getUsage(): ResponseModelInterface;

    /**
     * @param TranslationConfigInterface $translation
     * @return ResponseModelInterface
     */
    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface;

    /**
     * @param string $text
     * @param string $target_language
     * @return ResponseModelInterface
     */
    public function translate(string $text, string $target_language): ResponseModelInterface;

    /**
     * @param string|null $appId
     * @param string|null $key
     * @return GatewayInterface
     */
    public static function create(string $appId = null, string $key = null): GatewayInterface;
}