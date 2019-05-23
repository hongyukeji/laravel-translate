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

namespace Hongyukeji\LaravelTranslate\Gateways\Abstracts;

use Hongyukeji\LaravelTranslate\Gateways\Interfaces\ResponseModelInterface;

abstract class AbstractResponseModel implements ResponseModelInterface
{
    const SETTER_PREFIX = 'set';
    const MODEL_SETTER = 'setText';

    public function hydrate(\stdClass $responseModel): ResponseModelInterface
    {
        $modelSetter = self::MODEL_SETTER;
        if (!empty($responseModel->translation) && method_exists($this, $modelSetter)) {
            $value = $responseModel->translation[0];
            $this->$modelSetter($value);
        } else {
            dump($responseModel);
        }

        /*foreach ($responseModel as $key => $value) {

            if (is_array($value)) {
                $this->hydrate(current($value));
            }

            $modelSetter = $this->getModelSetter($key);

            if (method_exists($this, $modelSetter)) {
                $this->$modelSetter($value);
            }

        }*/

        return $this;
    }

    private function getModelSetter(string $key): string
    {
        return self::SETTER_PREFIX .
            str_replace(
                ' ',
                '',
                ucwords(
                    str_replace('_', ' ', $key)
                )
            );
    }
}