<?php
declare(strict_types=1);

namespace Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model;

/**
 * Interface ResponseModelInterface
 *
 * @package Hongyukeji\LaravelTranslate\Gateways\DeeplClient\Model
 */
interface ResponseModelInterface
{
    public function hydrate(\stdClass $responseModel): ResponseModelInterface;
}