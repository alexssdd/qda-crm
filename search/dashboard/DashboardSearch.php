<?php

namespace app\search\dashboard;

use Yii;
use DateTimeZone;
use DateTimeImmutable;
use yii\base\Model;
use yii\db\Query;
use app\entities\Customer;
use app\modules\location\models\Country;
use app\modules\location\models\Location;
use app\modules\order\models\Order;
use app\modules\order\models\OrderBid;
use app\modules\order\models\Executor;
use app\modules\order\models\ExecutorService;
use app\modules\order\enums\OrderStatus;
use app\modules\order\enums\PriceType;
use app\modules\order\enums\ExecutorStatus;
use app\modules\order\helpers\OrderHelper;

/**
 * Aggregates for the dashboard section: customers, executors, orders, bids.
 */
class DashboardSearch extends Model
{
    const MAX_RANGE_DAYS = 366;
    const TOP_LOCATIONS_LIMIT = 10;
    const WEEKDAY_LABEL_MAX_DAYS = 7;
    const COVERAGE_MIN_EXECUTORS = 3;
    const COVERAGE_NO_LOCATION_KEY = 0;
    const DEFAULT_COUNTRY = 'kz';

    public $date_from;
    public $date_to;
    public $country_code;

    private int $fromTs = 0;
    private int $toTs = 0;
    private int $tzOffset = 0;

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
            ['country_code', 'in', 'range' => array_keys(OrderHelper::getCountries())],
        ];
    }

    /**
     * Loads params, falls back to the last 30 days on invalid input.
     */
    public function prepare(array $params): void
    {
        $this->load($params);

        if (!$this->validate()) {
            foreach (array_keys($this->getErrors()) as $attribute) {
                $this->$attribute = null;
            }
        }

        // Regions are country-scoped, so the dashboard is always filtered by one country.
        if (!$this->country_code) {
            $countries = OrderHelper::getCountries();
            $this->country_code = isset($countries[self::DEFAULT_COUNTRY])
                ? self::DEFAULT_COUNTRY
                : array_key_first($countries);
        }

        $timeZone = new DateTimeZone(Yii::$app->timeZone);
        $today = new DateTimeImmutable('today', $timeZone);

        $to = $this->date_to ? new DateTimeImmutable($this->date_to, $timeZone) : $today;
        $from = $this->date_from ? new DateTimeImmutable($this->date_from, $timeZone) : $to->modify('-29 days');

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        if ($from->diff($to)->days >= self::MAX_RANGE_DAYS) {
            $from = $to->modify('-' . (self::MAX_RANGE_DAYS - 1) . ' days');
        }

        $this->date_from = $from->format('Y-m-d');
        $this->date_to = $to->format('Y-m-d');
        $this->fromTs = $from->getTimestamp();
        $this->toTs = $to->modify('+1 day')->getTimestamp() - 1;
        $this->tzOffset = $timeZone->getOffset($today);
    }

    /**
     * @return array
     */
    public function stats(): array
    {
        return [
            'customers' => $this->customers(),
            'executors' => $this->executors(),
            'orders' => $this->orders(),
            'bids' => $this->bids(),
        ];
    }

    /**
     * @return array
     */
    public function charts(): array
    {
        return [
            'ordersByDay' => $this->ordersByDay(),
            'registrationsByDay' => $this->registrationsByDay(),
            'bidsByDay' => $this->bidsByDay(),
            'ordersByType' => $this->ordersByType(),
            'ordersByStatus' => $this->ordersByStatus(),
            'topLocations' => $this->topLocations(),
            'funnel' => $this->funnel(),
        ];
    }

    /**
     * @return array
     */
    private function customers(): array
    {
        $total = $this->customerQuery()->count();
        $withOrders = $this->customerQuery()->andWhere(['>', 'orders_created', 0])->count();

        return [
            'total' => (int) $total,
            'new' => (int) $this->customerQuery()
                ->andWhere(['between', 'COALESCE(registered_at, created_at)', $this->fromTs, $this->toTs])
                ->count(),
            'active' => (int) $this->customerQuery()
                ->andWhere(['between', 'last_order_at', $this->fromTs, $this->toTs])
                ->count(),
            'withOrders' => (int) $withOrders,
            'withOrdersPercent' => $total > 0 ? round($withOrders / $total * 100) : 0,
        ];
    }

    /**
     * @return array
     */
    private function executors(): array
    {
        $total = $this->executorQuery()->count();
        $verified = $this->executorQuery()->andWhere(['is_verified' => 1])->count();

        return [
            'total' => (int) $total,
            'active' => (int) $this->executorQuery()
                ->andWhere(['status' => ExecutorStatus::ACTIVE->value])
                ->count(),
            'verified' => (int) $verified,
            'verifiedPercent' => $total > 0 ? round($verified / $total * 100) : 0,
            'new' => (int) $this->executorQuery()
                ->andWhere(['between', 'registered_at', $this->fromTs, $this->toTs])
                ->count(),
            'bidders' => (int) $this->bidQuery()->select('executor_id')->distinct()->count(),
        ];
    }

    /**
     * @return array
     */
    private function orders(): array
    {
        $published = $this->publishedOrderQuery()->count();
        $withoutBids = $this->publishedOrderQuery()
            ->andWhere(['not exists', (new Query())
                ->from(OrderBid::tableName() . ' b')
                ->where('b.order_id = o.id')])
            ->count();

        return [
            'created' => (int) $this->orderQuery()->count(),
            'completed' => (int) $this->completedOrderQuery()->count(),
            'cancelled' => (int) $this->orderQuery()
                ->andWhere(['o.status' => OrderStatus::CANCELLED->value])
                ->count(),
            'withoutBids' => (int) $withoutBids,
            'withoutBidsPercent' => $published > 0 ? round($withoutBids / $published * 100) : 0,
            'avgFirstBidMinutes' => $this->avgFirstBidMinutes(),
            'gmv' => $this->gmv(),
        ];
    }

    /**
     * @return array
     */
    private function bids(): array
    {
        $total = $this->bidQuery()->count();
        $published = $this->publishedOrderQuery()->count();

        return [
            'total' => (int) $total,
            'perOrder' => $published > 0 ? round($total / $published, 1) : 0,
        ];
    }

    /**
     * @return int|null Average minutes from order publication to the first bid.
     */
    private function avgFirstBidMinutes(): ?int
    {
        $firstBids = (new Query())
            ->select(['order_id', 'first_at' => 'MIN(source_at)'])
            ->from(OrderBid::tableName())
            ->where(['>=', 'source_at', $this->fromTs])
            ->groupBy('order_id');

        $value = $this->orderQuery()
            ->innerJoin(['fb' => $firstBids], 'fb.order_id = o.id')
            ->andWhere('fb.first_at >= o.created_at')
            ->average('fb.first_at - o.created_at');

        return $value !== null ? (int) round($value / 60) : null;
    }

    /**
     * Completed orders value: chosen bid price, fixed order price as fallback.
     *
     * @return array<string, float> Country code => sum, currencies never mixed.
     */
    private function gmv(): array
    {
        $chosenBids = (new Query())
            ->select(['order_id', 'price' => 'MAX(price)'])
            ->from(OrderBid::tableName())
            ->where(['is_executor' => 1])
            ->andWhere(['<=', 'source_at', $this->toTs])
            ->groupBy('order_id');

        $rows = $this->completedOrderQuery()
            ->select([
                'country_code' => 'o.country_code',
                'total' => 'SUM(COALESCE(cb.price, CASE WHEN o.price_type IN (:pt_fixed_semi, :pt_fixed) THEN o.price END))',
            ])
            ->leftJoin(['cb' => $chosenBids], 'cb.order_id = o.id')
            ->addParams([
                ':pt_fixed_semi' => PriceType::FIXED_SEMI_ID,
                ':pt_fixed' => PriceType::FIXED_ID,
            ])
            ->groupBy('o.country_code')
            ->all();

        $result = [];

        foreach ($rows as $row) {
            if (empty($row['country_code'])) {
                continue;
            }

            $result[$row['country_code']] = (float) ($row['total'] ?? 0);
        }

        return $result;
    }

    /**
     * @return array
     */
    private function ordersByDay(): array
    {
        $rows = $this->orderQuery()
            ->select([
                'd' => "FLOOR((o.created_at + {$this->tzOffset}) / 86400)",
                'total' => 'COUNT(*)',
                'cancelled' => 'SUM(o.status = :st_cancelled)',
            ])
            ->addParams([':st_cancelled' => OrderStatus::CANCELLED->value])
            ->groupBy('d')
            ->all();

        $created = [];
        $cancelled = [];

        foreach ($rows as $row) {
            $created[(int) $row['d']] = (int) $row['total'];
            $cancelled[(int) $row['d']] = (int) $row['cancelled'];
        }

        return $this->fillDays([
            'created' => $created,
            'cancelled' => $cancelled,
            'completed' => $this->countByDay($this->completedOrderQuery(), 'o.completed_at'),
        ]);
    }

    /**
     * @return array
     */
    private function registrationsByDay(): array
    {
        $customers = $this->countByDay(
            $this->customerQuery()
                ->andWhere(['between', 'COALESCE(registered_at, created_at)', $this->fromTs, $this->toTs]),
            'COALESCE(registered_at, created_at)'
        );
        $executors = $this->countByDay(
            $this->executorQuery()
                ->andWhere(['between', 'registered_at', $this->fromTs, $this->toTs]),
            'registered_at'
        );

        return $this->fillDays([
            'customers' => $customers,
            'executors' => $executors,
        ]);
    }

    /**
     * @return array
     */
    private function bidsByDay(): array
    {
        return $this->fillDays([
            'bids' => $this->countByDay($this->bidQuery(), 'source_at'),
        ]);
    }

    /**
     * @return array
     */
    private function ordersByType(): array
    {
        $rows = $this->orderQuery()
            ->select(['type' => 'o.type', 'total' => 'COUNT(*)'])
            ->groupBy('o.type')
            ->orderBy(['total' => SORT_DESC])
            ->all();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = OrderHelper::getTypeName((int) $row['type'])
                ?? Yii::t('app', 'order.type.unknown');
            $values[] = (int) $row['total'];
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array
     */
    private function ordersByStatus(): array
    {
        $rows = $this->orderQuery()
            ->select(['status' => 'o.status', 'total' => 'COUNT(*)'])
            ->groupBy('o.status')
            ->orderBy(['status' => SORT_ASC])
            ->all();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = OrderHelper::getStatusName((int) $row['status']) ?? (string) $row['status'];
            $values[] = (int) $row['total'];
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array
     */
    private function topLocations(): array
    {
        $rows = $this->orderQuery()
            ->select(['name' => 'l.name', 'total' => 'COUNT(*)'])
            ->leftJoin(Location::tableName() . ' l', 'l.id = o.from_location_id')
            ->groupBy(['o.from_location_id', 'l.name'])
            ->orderBy(['total' => SORT_DESC])
            ->limit(self::TOP_LOCATIONS_LIMIT)
            ->all();

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = $row['name'] ?? Yii::t('app', 'dashboard.location.unknown');
            $values[] = (int) $row['total'];
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array
     */
    private function funnel(): array
    {
        $bidExists = (new Query())
            ->from(OrderBid::tableName() . ' b')
            ->where('b.order_id = o.id');
        $executorExists = (new Query())
            ->from(OrderBid::tableName() . ' b')
            ->where('b.order_id = o.id')
            ->andWhere(['b.is_executor' => 1]);

        return [
            'labels' => [
                Yii::t('app', 'dashboard.funnel.published'),
                Yii::t('app', 'dashboard.funnel.with_bid'),
                Yii::t('app', 'dashboard.funnel.with_executor'),
                Yii::t('app', 'dashboard.funnel.completed'),
            ],
            'values' => [
                (int) $this->publishedOrderQuery()->count(),
                (int) $this->publishedOrderQuery()->andWhere(['exists', $bidExists])->count(),
                (int) $this->publishedOrderQuery()->andWhere(['exists', $executorExists])->count(),
                (int) $this->orderQuery()->andWhere(['o.status' => OrderStatus::COMPLETED->value])->count(),
            ],
        ];
    }

    /**
     * Supply vs demand matrix: active executors and period orders per region and service type.
     *
     * @return array{types: array<int, string>, rows: array}
     */
    public function coverage(): array
    {
        $locations = (new Query())
            ->select(['l.id', 'l.parent_id', 'l.name', 'code' => 'c.code'])
            ->from(Location::tableName() . ' l')
            ->leftJoin(Country::tableName() . ' c', 'c.id = l.country_id')
            ->indexBy('id')
            ->all();

        $types = OrderHelper::getTypes();
        $regions = [];

        foreach ($locations as $id => $location) {
            if ($location['parent_id'] !== null) {
                continue;
            }

            if ($this->country_code && $location['code'] !== $this->country_code) {
                continue;
            }

            $regions[$id] = $location['name'];
        }

        // Anything that does not resolve to a listed region (missing location, parent cycle,
        // country mismatch) falls into the "no location" bucket instead of silently vanishing.
        $regionOf = static function (?int $id) use ($locations, $regions): int {
            if ($id === null) {
                return self::COVERAGE_NO_LOCATION_KEY;
            }

            $visited = [];

            while (isset($locations[$id]) && !isset($visited[$id])) {
                $visited[$id] = true;
                $parentId = $locations[$id]['parent_id'];

                if ($parentId === null) {
                    break;
                }

                $id = (int) $parentId;
            }

            return isset($regions[$id]) ? $id : self::COVERAGE_NO_LOCATION_KEY;
        };

        $supplyQuery = (new Query())
            ->select(['location_id' => 'e.location_id', 'type' => 's.type', 'total' => 'COUNT(DISTINCT e.id)'])
            ->from(Executor::tableName() . ' e')
            ->innerJoin(ExecutorService::tableName() . ' s', 's.executor_id = e.id')
            ->where(['e.status' => ExecutorStatus::ACTIVE->value])
            ->groupBy(['e.location_id', 's.type']);

        if ($this->country_code) {
            $supplyQuery->andWhere(['e.country_code' => $this->country_code]);
        }

        $supply = [];

        foreach ($supplyQuery->all() as $row) {
            $region = $regionOf($row['location_id'] !== null ? (int) $row['location_id'] : null);
            $supply[$region][(int) $row['type']] = ($supply[$region][(int) $row['type']] ?? 0) + (int) $row['total'];
        }

        $demand = [];

        $demandRows = $this->publishedOrderQuery()
            ->select(['location_id' => 'o.from_location_id', 'type' => 'o.type', 'total' => 'COUNT(*)'])
            ->groupBy(['o.from_location_id', 'o.type'])
            ->all();

        foreach ($demandRows as $row) {
            $region = $regionOf($row['location_id'] !== null ? (int) $row['location_id'] : null);
            $demand[$region][(int) $row['type']] = ($demand[$region][(int) $row['type']] ?? 0) + (int) $row['total'];
        }

        uksort($regions, static function ($a, $b) use ($demand, $regions) {
            $demandA = array_sum($demand[$a] ?? []);
            $demandB = array_sum($demand[$b] ?? []);

            return $demandB <=> $demandA ?: strcmp($regions[$a], $regions[$b]);
        });

        if (isset($supply[self::COVERAGE_NO_LOCATION_KEY]) || isset($demand[self::COVERAGE_NO_LOCATION_KEY])) {
            $regions[self::COVERAGE_NO_LOCATION_KEY] = Yii::t('app', 'dashboard.coverage.no_location');
        }

        $rows = [];

        foreach ($regions as $regionId => $name) {
            $cells = [];

            foreach (array_keys($types) as $type) {
                $executors = $supply[$regionId][$type] ?? 0;
                $orders = $demand[$regionId][$type] ?? 0;

                $cells[$type] = [
                    'executors' => $executors,
                    'orders' => $orders,
                    'status' => $this->coverageStatus($executors, $orders)?->value,
                ];
            }

            $rows[] = ['name' => $name, 'cells' => $cells];
        }

        return ['types' => $types, 'rows' => $rows];
    }

    /**
     * @return CoverageStatus|null Null when there is neither demand nor supply.
     */
    private function coverageStatus(int $executors, int $orders): ?CoverageStatus
    {
        if ($orders === 0) {
            return $executors >= self::COVERAGE_MIN_EXECUTORS ? CoverageStatus::READY : null;
        }

        if ($executors === 0) {
            return CoverageStatus::CRITICAL;
        }

        if ($executors < self::COVERAGE_MIN_EXECUTORS) {
            return CoverageStatus::LOW;
        }

        return CoverageStatus::OK;
    }

    /**
     * @return Query Orders created within the period.
     */
    private function orderQuery(): Query
    {
        $query = (new Query())
            ->from(Order::tableName() . ' o')
            ->where(['between', 'o.created_at', $this->fromTs, $this->toTs]);

        if ($this->country_code) {
            $query->andWhere(['o.country_code' => $this->country_code]);
        }

        return $query;
    }

    /**
     * @return Query Orders created within the period, excluding drafts.
     */
    private function publishedOrderQuery(): Query
    {
        return $this->orderQuery()->andWhere(['<>', 'o.status', OrderStatus::CREATED->value]);
    }

    /**
     * @return Query Orders completed within the period.
     */
    private function completedOrderQuery(): Query
    {
        $query = (new Query())
            ->from(Order::tableName() . ' o')
            ->where(['o.status' => OrderStatus::COMPLETED->value])
            ->andWhere(['between', 'o.completed_at', $this->fromTs, $this->toTs]);

        if ($this->country_code) {
            $query->andWhere(['o.country_code' => $this->country_code]);
        }

        return $query;
    }

    /**
     * @return Query Bids placed within the period.
     */
    private function bidQuery(): Query
    {
        $query = (new Query())
            ->from(OrderBid::tableName())
            ->where(['between', 'source_at', $this->fromTs, $this->toTs]);

        if ($this->country_code) {
            $query->andWhere(['country_code' => $this->country_code]);
        }

        return $query;
    }

    /**
     * @return Query
     */
    private function customerQuery(): Query
    {
        $query = (new Query())->from(Customer::tableName());

        if ($this->country_code) {
            $query->andWhere(['country_code' => $this->country_code]);
        }

        return $query;
    }

    /**
     * @return Query
     */
    private function executorQuery(): Query
    {
        $query = (new Query())->from(Executor::tableName());

        if ($this->country_code) {
            $query->andWhere(['country_code' => $this->country_code]);
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param string $expression Unix timestamp column or expression.
     * @return array<int, int> Day index => count.
     */
    private function countByDay(Query $query, string $expression): array
    {
        $rows = $query
            ->select(['d' => "FLOOR(($expression + {$this->tzOffset}) / 86400)", 'total' => 'COUNT(*)'])
            ->groupBy('d')
            ->all();

        $result = [];

        foreach ($rows as $row) {
            $result[(int) $row['d']] = (int) $row['total'];
        }

        return $result;
    }

    /**
     * Expands per-day counters into continuous series over the period.
     *
     * @param array<string, array<int, int>> $series
     * @return array
     */
    private function fillDays(array $series): array
    {
        $firstDay = (int) floor(($this->fromTs + $this->tzOffset) / 86400);
        $lastDay = (int) floor(($this->toTs + $this->tzOffset) / 86400);

        $result = ['categories' => []];

        foreach (array_keys($series) as $name) {
            $result[$name] = [];
        }

        $useWeekdays = $lastDay - $firstDay < self::WEEKDAY_LABEL_MAX_DAYS;

        for ($day = $firstDay; $day <= $lastDay; $day++) {
            $result['categories'][] = $useWeekdays
                ? mb_convert_case(Yii::$app->formatter->asDate($day * 86400 - $this->tzOffset, 'EE'), MB_CASE_TITLE)
                : gmdate('d.m', $day * 86400);

            foreach ($series as $name => $counts) {
                $result[$name][] = $counts[$day] ?? 0;
            }
        }

        return $result;
    }

    /**
     * @return string Human-readable period, e.g. "23–29 июля" or "30 июня — 29 июля".
     */
    public function periodLabel(): string
    {
        $formatter = Yii::$app->formatter;
        $from = new DateTimeImmutable($this->date_from, new DateTimeZone(Yii::$app->timeZone));
        $to = new DateTimeImmutable($this->date_to, new DateTimeZone(Yii::$app->timeZone));

        if ($this->date_from === $this->date_to) {
            return $formatter->asDate($to->getTimestamp(), 'd MMMM');
        }

        if ($from->format('Y-m') === $to->format('Y-m')) {
            return $from->format('j') . '–' . $formatter->asDate($to->getTimestamp(), 'd MMMM');
        }

        return $formatter->asDate($from->getTimestamp(), 'd MMMM') . ' — ' . $formatter->asDate($to->getTimestamp(), 'd MMMM');
    }
}
