<?php
namespace app\core;

use yii\data\Sort;
use yii\base\BaseObject;
use yii\data\Pagination;
use yii\data\DataProviderInterface;

/**
 * @property int $count
 * @property array $keys
 * @property array $models
 * @property Pagination|false $pagination
 * @property Sort|bool $sort
 * @property int $totalCount
 */
class MapDataProvider extends BaseObject implements DataProviderInterface
{
    private $next;
    private $callback;

    /**
     * MapDataProvider constructor.
     * @param DataProviderInterface $next
     * @param callable $callback
     */
    public function __construct(DataProviderInterface $next, callable $callback)
    {
        $this->next = $next;
        $this->callback = $callback;
        parent::__construct();
    }

    /**
     * @param bool $forcePrepare
     */
    public function prepare($forcePrepare = false): void
    {
        $this->next->prepare($forcePrepare);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->next->getCount();
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->next->getTotalCount();
    }

    /**
     * @return array
     */
    public function getModels(): array
    {
        return array_map($this->callback, $this->next->getModels());
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        return $this->next->getKeys();
    }

    /**
     * @return Sort
     */
    public function getSort()
    {
        return $this->next->getSort();
    }

    /**
     * @return false|Pagination
     */
    public function getPagination()
    {
        return $this->next->getPagination();
    }
}