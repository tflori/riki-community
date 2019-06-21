<?php

namespace App\Environment;

use App\Environment;

/**
 * The development environment defines things that are specific for development environment but not configurations.
 *
 * @codeCoverageIgnore Environment will not be loaded in tests
 */
class Development extends Environment
{
    public function canCacheConfig(): bool
    {
        return false;
    }

    public function canShowErrors()
    {
        return true;
    }
}
