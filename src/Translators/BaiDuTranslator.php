<?php

namespace Hongyukeji\LaravelTranslate\Translators;

use Hongyukeji\LaravelTranslate\Translates\BaiDuTranslate;
use Hongyukeji\LaravelTranslate\Exceptions\LanguageCodeNotExist;

class BaiDuTranslator implements TranslatorInterface
{
    protected $translator;
    protected $source;
    protected $target;

    public function __construct()
    {
        $this->translator = new BaiDuTranslate();

        if (config('translate.services.baidu')) {
            $this->translator->setAppId(config('translate.services.baidu.app_id'));
            $this->translator->setKey(config('translate.services.baidu.key'));
            //$this->translator->setOptions([]);
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
            return $this->translator->translate($string);
        } catch (\Throwable $th) {
            if ($th->getMessage() === 'Return value of Stichoza\GoogleTranslate\GoogleTranslate::translate() must be of the type string, null returned') {
                throw LanguageCodeNotExist::throw($this->source, $this->target);
            }

            throw $th;
        }
    }
}
