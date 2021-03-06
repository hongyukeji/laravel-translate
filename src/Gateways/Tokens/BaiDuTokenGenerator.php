<?php

namespace Hongyukeji\LaravelTranslate\Gateways\Tokens;

/**
 * A nice interface for providing tokens.
 */
class BaiDuTokenGenerator implements TokenProviderInterface
{
    /**
     * Generate a fake token just as an example.
     *
     * @param string $source Source language
     * @param string $target Target langiage
     * @param string $text Text to translate
     * @param string|null $appId
     * @param string|null $key
     * @param string|null $salt
     * @return string Token
     */
    public function generateToken(string $source, string $target, string $text, string $appId = null, string $key = null, string $salt = null): string
    {
        return md5($appId . $text . $salt . $key);
    }
}
