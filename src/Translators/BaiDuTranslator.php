<?php

namespace Hongyukeji\LaravelTranslate\Translators;

use Hongyukeji\LaravelTranslate\Gateways\BaiDuGateway;
use Hongyukeji\LaravelTranslate\Exceptions\LanguageCodeNotExist;

class BaiDuTranslator implements TranslatorInterface
{
    protected $translator;
    protected $source;
    protected $target;

    public function __construct()
    {
        $appId = config('translate.gateways.baidu.app_id');
        $key = config('translate.gateways.baidu.key');
        $this->translator = new BaiDuGateway();
        if (!empty($appId) && !empty($key)) {
            $this->translator->setAppId($appId);
            $this->translator->setKey($key);
        }
    }

    public function setSource(string $source)
    {
        $this->source = strtoupper($source);

        return $this;
    }

    public function setTarget(string $target)
    {
        $this->target = strtoupper($target);

        return $this;
    }

    public function translate(string $string): string
    {
        try {
            if ($this->source) {
                $this->translator->setSource($this->source);
            }
            if ($this->target) {
                $this->translator->setTarget($this->target);
            }
            return $this->translator->translate($string);
        } catch (\Throwable $th) {
            if ($th->getMessage() === 'Return value of Stichoza\GoogleTranslate\GoogleTranslate::translate() must be of the type string, null returned') {
                throw LanguageCodeNotExist::throw($this->source, $this->target);
            }

            throw $th;
        }
    }
}
