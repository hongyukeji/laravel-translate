<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model;

/**
 * Interface UsageInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model
 */
interface UsageInterface
{
    public function getCharacterCount(): int;

    public function setCharacterCount(int $characterCount): UsageInterface;

    public function getCharacterLimit(): int;

    public function setCharacterLimit(int $characterLimit): UsageInterface;
}