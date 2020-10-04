<?php

namespace Seeder;

use App\Cli\Concerns\WritesToConsole;
use ORM\EntityManager;

abstract class AbstractSeeder
{
    use WritesToConsole;

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

    public function getName()
    {
        return substr(static::class, 7);
    }

    abstract public function sprout();

    protected function seed(string $seederClass)
    {
        if (!class_exists($seederClass)) {
            throw new \Exception(sprintf('Seeder %s not found', $seederClass));
        }

        /** @var AbstractSeeder $seeder */
        $seeder = new $seederClass($this->em);
        $this->info('╰ Applying ' . $seeder->getName());
        $seeder->setConsole($this->console);
        $seeder->sprout();
    }
}
