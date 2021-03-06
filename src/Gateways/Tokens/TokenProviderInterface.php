<?php

namespace Hongyukeji\LaravelTranslate\Gateways\Tokens;

/**
 * A nice interface for providing tokens.
 */
interface TokenProviderInterface
{
    /**
     * Generate and return a token.
     *
     * @param string $source Source language
     * @param string $target Target langiage
     * @param string $text Text to translate
     * @param string|null $appId
     * @param string|null $key
     * @param string|null $salt
     * @return string Token
     */
    public function generateToken(string $source, string $target, string $text, string $appId = null, string $key = null, string $salt = null): string;
}
