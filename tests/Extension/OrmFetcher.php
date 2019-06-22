<?php

namespace Test\Extension;

use Mockery as m;
use ORM\Entity;
use PDO;
use PDOStatement;

trait OrmFetcher
{
    protected $fetcherResults = [];

    /** @var m\Mock|PDO */
    protected $pdo;

    protected function initFetcher()
    {
        $this->pdo->shouldReceive('query')->with(m::pattern('/^SELECT DISTINCT t0\.\* FROM /'))
            ->andReturnUsing(function ($query) {
                return $this->buildStatementMock($query);
            })->byDefault();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^SELECT COUNT\(DISTINCT t0\.\*\) FROM /'))
            ->andReturnUsing(function ($query) {
                $results = $this->getResultsForQuery($query);

                $statement = m::mock(PDOStatement::class);
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
                return array_map(function (Entity $entity) {
                    return $entity->getData();
                }, $fetcherResult['entities']);
            }
        }

        return array_map(function (Entity $entity) {
            return $entity->getData();
        }, $unconditional);
    }

    protected function addFetcherResult(string $class, array $conditions, Entity ...$entities)
    {
        /** @var Entity|string $class */
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

    protected function expectFetch(string $class, array $conditions, Entity ...$entities)
    {
        $this->addFetcherResult($class, $conditions, ...$entities);

        $this->pdo->shouldReceive('query')->withArgs(function ($query) use ($class, $conditions) {
            if (!preg_match('/^SELECT DISTINCT t0\.\* FROM (.*?) AS t0/', $query, $match)) {
                return false;
            }

            /** @var Entity|string $class */
            $table = str_replace('"', '', $match[1]);
            if ($class::getTableName() !== $table) {
                return false;
            }

            return $this->queryMatchesConditions($query, $conditions);
        })->atLeast()->once()->andReturnUsing(function ($query) {
            return $this->buildStatementMock($query);
        });
    }

    protected function buildStatementMock($query)
    {
        $results = $this->getResultsForQuery($query);
        array_push($results, false);

        $statement = m::mock(PDOStatement::class);
        $statement->shouldReceive('fetch')->with(PDO::FETCH_ASSOC)
            ->andReturn(...$results)
            ->atLeast()->once();

        return $statement;
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
