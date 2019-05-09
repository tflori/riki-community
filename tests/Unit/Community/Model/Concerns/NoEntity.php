<?php

namespace Test\Unit\Community\Model\Concerns;

use Community\Model\Concerns\WithCreated;
use Community\Model\Concerns\WithUpdated;

class NoEntity
{
    use WithCreated, WithUpdated;
}
