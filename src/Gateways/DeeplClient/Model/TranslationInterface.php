<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model;

/**
 * Interface TranslationInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model
 */
interface TranslationInterface
{
    public function getDetectedSourceLanguage(): string;

    public function setDetectedSourceLanguage(string $detectedSourceLanguage): TranslationInterface;

    public function getText(): string;

    public function setText(string $text): TranslationInterface;
}