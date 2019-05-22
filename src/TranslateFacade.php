<?php

namespace Hongyukeji\LaravelTranslate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hongyukeji\LaravelTranslate\Skeleton\SkeletonClass
 */
class TranslateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'translate';
    }
}
