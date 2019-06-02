<?php

namespace Test\Extension;

use Mockery as m;
use ORM\Entity;

trait OrmFetcher
{
    protected $fetcherResults = [];

    protected function initFetcher()
    {
        $this->mocks['pdo']->shouldReceive('query')->with(m::pattern('/^SELECT DISTINCT t0\.\* FROM /'))
            ->andReturnUsing(function ($query) {
                $results = $this->getResultsForQuery($query);
                array_push($results, false);

                $statement = m::mock(\PDOStatement::class);
                $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)
                    ->andReturn(...$results)
                    ->atLeast()->once();

                return $statement;
            })->byDefault();
        $this->mocks['pdo']->shouldReceive('query')->with(m::pattern('/^SELECT COUNT\(DISTINCT t0\.\*\) FROM /'))
            ->andReturnUsing(function ($query) {
                $results = $this->getResultsForQuery($query);

                $statement = m::mock(\PDOStatement::class);
                $statement->shouldReceive('fetchColumn')->with()
                    ->andReturn(count($results))
                    ->once();

                return $statement;
            })->byDefault();
    }

    protected function getResultsForQuery(string $query): array
    {
        if (!preg_match('/FROM (.*?) AS t0/', $query, $match)) {
            return [];
        }

        $table = str_replace('"', '', $match[1]);
        if (!isset($this->fetcherResults[$table])) {
            return [];
        }

        $unconditional = [];
        foreach ($this->fetcherResults[$table] as $fetcherResult) {
            if (empty($fetcherResult['conditions'])) {
                $unconditional = $fetcherResult['entities'];
                continue;
            }

            if ($this->queryMatchesConditions($query, $fetcherResult['conditions'])) {
                return $fetcherResult['entities'];
            }
        }

        return $unconditional;
    }

    protected function addFetcherResult(string $class, array $conditions, Entity ...$entities)
    {
        $table = $class::getTableName();

        // complete the primary keys
        foreach ($entities as $entity) {
            foreach ($entity::getPrimaryKeyVars() as $attribute) {
                if (!$entity->$attribute) {
                    $entity->$attribute = mt_rand(1000000, 2000000);
                }
            }
        }

        if (!isset($this->fetcherResults[$table])) {
            $this->fetcherResults[$table] = [[
                'conditions' => $conditions,
                'entities' => $entities
            ]];
            return;
        }

        foreach ($this->fetcherResults[$table] as $fetcherResult) {
            if ($fetcherResult['condition'] == $conditions) {
                $fetcherResult['entities'] = $entities;
                return;
            }
        }

        $fetcherResult[$table][] = [
            'conditions' => $conditions,
            'entities' => $entities
        ];
    }

    protected function queryMatchesConditions(string $query, array $conditions): bool
    {
        foreach ($conditions as $condition) {
            if (!preg_match($condition, $query)) {
                return false;
            }
        }

        return true;
    }
}
