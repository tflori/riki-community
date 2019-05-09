<?php

namespace Community\Model\Concerns;

use Carbon\Carbon;
use DateTime;
use ORM\Entity;

trait WithCreated
{
    /**
     * Get a DateTime object from created
     *
     * @return Carbon|null
     */
    public function getCreated(): ?Carbon
    {
        if (!$this instanceof Entity) {
            return null;
        }

        $col = static::getColumnName('created');

        if (!isset($this->data[$col])) {
            return null;
        }

        return new Carbon($this->data[$col], 'UTC');
    }

    /**
     * Set created to $dt or 'now'
     *
     * @param DateTime|null $dt
     * @return $this
     */
    public function setCreated(DateTime $dt = null)
    {
        if (!$this instanceof Entity) {
            return $this;
        }

        $col = static::getColumnName('created');

        if (isset($this->data[$col])) {
            return $this;
        }

        $dt = $dt ?? Carbon::now('UTC');
        $this->data[$col] = $dt->format('Y-m-d\TH:i:s.u\Z');

        return $this;
    }
}
