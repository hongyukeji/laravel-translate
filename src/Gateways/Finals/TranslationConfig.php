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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:44
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;


use Hongyukeji\LaravelTranslate\Gateways\Interfaces\TranslationConfigInterface;

final class TranslationConfig implements TranslationConfigInterface
{

    private $text;

    private $targetLang;

    private $sourceLang;

    private $tagHandling;

    private $nonSplittingTags;

    private $ignoreTags;

    private $splitSentences;

    private $preserveFormatting;

    public function __construct(
        string $text,
        string $targetLang,
        string $sourceLang = '',
        array $tagHandling = [],
        array $nonSplittingTags = [],
        array $ignoreTags = [],
        bool $splitSentences = true,
        bool $preserveFormatting = false
    )
    {
        $this->setText($text);
        $this->setTargetLang($targetLang);
        $this->setSourceLang($sourceLang);
        $this->setTagHandling($tagHandling);
        $this->setNonSplittingTags($nonSplittingTags);
        $this->setIgnoreTags($ignoreTags);
        $this->setSplitSentences($splitSentences);
        $this->setPreserveFormatting($preserveFormatting);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TranslationConfigInterface
    {
        $this->text = $text;

        return $this;
    }

    public function getTargetLang(): string
    {
        return $this->targetLang;
    }

    public function setTargetLang(string $targetLang): TranslationConfigInterface
    {
        $this->targetLang = $targetLang;

        return $this;
    }

    public function getSourceLang(): string
    {
        return $this->sourceLang;
    }

    public function setSourceLang(string $sourceLang): TranslationConfigInterface
    {
        $this->sourceLang = $sourceLang;

        return $this;
    }

    public function getTagHandling(): array
    {
        return $this->tagHandling;
    }

    public function setTagHandling(array $tagHandling): TranslationConfigInterface
    {
        $this->tagHandling = $tagHandling;

        return $this;
    }

    public function getNonSplittingTags(): array
    {
        return $this->nonSplittingTags;
    }

    public function setNonSplittingTags(array $nonSplittingTags): TranslationConfigInterface
    {
        $this->nonSplittingTags = $nonSplittingTags;

        return $this;
    }

    public function getIgnoreTags(): array
    {
        return $this->ignoreTags;
    }

    public function setIgnoreTags(array $ignoreTags): TranslationConfigInterface
    {
        $this->ignoreTags = $ignoreTags;

        return $this;
    }

    public function getSplitSentences(): bool
    {
        return $this->splitSentences;
    }

    public function setSplitSentences(bool $splitSentences): TranslationConfigInterface
    {
        $this->splitSentences = $splitSentences;

        return $this;
    }

    public function getPreserveFormatting(): bool
    {
        return $this->preserveFormatting;
    }

    public function setPreserveFormatting(bool $preserveFormatting): TranslationConfigInterface
    {
        $this->preserveFormatting = $preserveFormatting;

        return $this;
    }
}