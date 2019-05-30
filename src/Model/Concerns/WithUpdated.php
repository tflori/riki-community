<?php

namespace Community\Model\Concerns;

use Carbon\Carbon;
use ORM\Entity;

trait WithUpdated
{
    /**
     * Get a DateTime object from created
     *
     * @return Carbon|null
     */
    public function getUpdated(): ?Carbon
    {
        if (!$this instanceof Entity) {
            return null;
        }

        $col = static::getColumnName('updated');

        if (!isset($this->data[$col])) {
            return null;
        }

        return new Carbon($this->data[$col], 'UTC');
    }

    /**
     * Set created to $dt or 'now'
     *
     * @param Carbon|null $dt
     * @return $this
     */
    public function setUpdated(Carbon $dt = null)
    {
        if (!$this instanceof Entity) {
            return $this;
        }

        $col = static::getColumnName('updated');

        $dt = $dt ?? Carbon::now();
        $this->data[$col] = $dt->setTimezone('UTC')->format('Y-m-d\TH:i:s.u\Z');

        return $this;
    }

    public function preUpdate()
    {
        $this->setUpdated();

        if (is_callable('parent::preUpdate')) {
            parent::preUpdate();
        }
    }
}
