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
 * | Version: v1.0.0  Date:2019-05-23 Time:14:34
 * +----------------------------------------------------------------------
 */

namespace Hongyukeji\LaravelTranslate\Gateways\Finals;

use Hongyukeji\LaravelTranslate\Gateways\Abstracts\AbstractResponseModel;
use Hongyukeji\LaravelTranslate\Gateways\Interfaces\UsageInterface;

final class Usage extends AbstractResponseModel implements UsageInterface
{
    private $characterCount;

    private $characterLimit;

    public function getCharacterCount(): int
    {
        return $this->characterCount;
    }

    public function setCharacterCount(int $characterCount): UsageInterface
    {
        $this->characterCount = $characterCount;

        return $this;
    }

    public function getCharacterLimit(): int
    {
        return $this->characterLimit;
    }

    public function setCharacterLimit(int $characterLimit): UsageInterface
    {
        $this->characterLimit = $characterLimit;

        return $this;
    }
}