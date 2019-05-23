<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient;

use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\ResponseModelInterface;
use Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model\TranslationConfigInterface;

/**
 * Class DeeplClientInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient
 */
interface DeeplClientInterface
{
    public function getUsage(): ResponseModelInterface;

    public function getTranslation(TranslationConfigInterface $translation): ResponseModelInterface;

    public function translate(string $text, string $target_language): ResponseModelInterface;

    public static function create(string $apiKey): DeeplClientInterface;
}