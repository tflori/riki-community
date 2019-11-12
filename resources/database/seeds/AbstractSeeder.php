<?php

namespace Seeder;

use ORM\EntityManager;

abstract class AbstractSeeder
{
    /** @var EntityManager */
    protected $em;

    /**
     * AbstractSeeder constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    abstract public function sprout();

    protected function seed(string $seederClass)
    {
        if (!class_exists($seederClass)) {
            throw new \Exception(sprintf('Seeder %s not found', $seederClass));
        }

        /** @var AbstractSeeder $seeder */
        $seeder = new $seederClass($this->em);
        $seeder->sprout();
    }
}
