<?php
/**
 * +----------------------------------------------------------------------
 * | laravel-translate [ File Description ]
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015~2019 http://www.wmt.ltd All rights reserved.
 * +----------------------------------------------------------------------
 * | 版权所有：贵州鸿宇叁柒柒科技有限公司
 * +----------------------------------------------------------------------
 * | Author: shadow <admin@hongyuvip.com>  QQ: 1527200768
 * +----------------------------------------------------------------------
 * | Version: v1.0.0  Date:2019-05-23 Time:14:48
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;


use Hongyukeji\LaravelTranslate\Gateways\Interfaces\RequestHandlerInterface;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

final class TranslationRequestHandler implements RequestHandlerInterface
{

    const SEPARATOR = ',';
    const API_ENDPOINT = 'https://api.deepl.com/v2/translate';

    private $authKey;

    private $translation;

    public function __construct(string $authKey, TranslationConfigInterface $translation)
    {
        $this->authKey = $authKey;
        $this->translation = $translation;
    }

    public function getMethod(): string
    {
        return RequestHandlerInterface::METHOD_POST;
    }

    public function getPath(): string
    {
        return static::API_ENDPOINT;
    }

    public function getBody(): array
    {
        return [
            'form_params' => array_filter(
                [
                    'text' => $this->translation->getText(),
                    'target_lang' => $this->translation->getTargetLang(),
                    'source_lang' => $this->translation->getSourceLang(),
                    'tag_handling' => implode(static::SEPARATOR,
                        (array)$this->translation->getTagHandling()),
                    'non_splitting_tags' => implode(static::SEPARATOR,
                        (array)$this->translation->getNonSplittingTags()),
                    'ignore_tags' => implode(static::SEPARATOR, (array)$this->translation->getIgnoreTags()),
                    'split_sentences' => (string)$this->translation->getSplitSentences(),
                    'preserve_formatting' => $this->translation->getPreserveFormatting(),
                    'auth_key' => $this->authKey,
                ]
            )
        ];
    }
}