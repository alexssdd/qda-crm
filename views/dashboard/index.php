<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use app\assets\DashboardAsset;
use app\helpers\PriceFormatter;
use app\search\dashboard\DashboardSearch;
use app\modules\order\helpers\OrderHelper;

/* @var $this View */
/* @var $searchModel DashboardSearch */
/* @var $stats array */
/* @var $charts array */

$this->title = Yii::t('app', 'dashboard.title');
$this->params['breadcrumbs'][] = $this->title;

DashboardAsset::register($this);

$formatMinutes = static function (?int $minutes): string {
    if ($minutes === null) {
        return '—';
    }

    if ($minutes < 60) {
        return Yii::t('app', 'dashboard.duration.minutes', ['value' => $minutes]);
    }

    if ($minutes < 1440) {
        return Yii::t('app', 'dashboard.duration.hours', [
            'hours' => intdiv($minutes, 60),
            'minutes' => $minutes % 60,
        ]);
    }

    return Yii::t('app', 'dashboard.duration.days', ['value' => round($minutes / 1440, 1)]);
};

$gmvLabels = [];

foreach ($stats['orders']['gmv'] as $countryCode => $amount) {
    if ($amount > 0) {
        $gmvLabels[] = PriceFormatter::short($amount, $countryCode);
    }
}

$kpiGroups = [
    [
        'title' => Yii::t('app', 'dashboard.customers.title'),
        'cards' => [
            [
                'label' => Yii::t('app', 'dashboard.customers.total'),
                'value' => $stats['customers']['total'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.customers.new'),
                'value' => $stats['customers']['new'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.customers.active'),
                'value' => $stats['customers']['active'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.customers.with_orders'),
                'value' => $stats['customers']['withOrders'],
                'hint' => $stats['customers']['withOrdersPercent'] . '%',
            ],
        ],
    ],
    [
        'title' => Yii::t('app', 'dashboard.executors.title'),
        'cards' => [
            [
                'label' => Yii::t('app', 'dashboard.executors.total'),
                'value' => $stats['executors']['total'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.executors.active'),
                'value' => $stats['executors']['active'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.executors.verified'),
                'value' => $stats['executors']['verified'],
                'hint' => $stats['executors']['verifiedPercent'] . '%',
            ],
            [
                'label' => Yii::t('app', 'dashboard.executors.new'),
                'value' => $stats['executors']['new'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.executors.bidders'),
                'value' => $stats['executors']['bidders'],
            ],
        ],
    ],
    [
        'title' => Yii::t('app', 'dashboard.orders.title'),
        'cards' => [
            [
                'label' => Yii::t('app', 'dashboard.orders.created'),
                'value' => $stats['orders']['created'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.orders.completed'),
                'value' => $stats['orders']['completed'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.orders.cancelled'),
                'value' => $stats['orders']['cancelled'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.orders.without_bids'),
                'value' => $stats['orders']['withoutBids'],
                'hint' => $stats['orders']['withoutBidsPercent'] . '%',
            ],
            [
                'label' => Yii::t('app', 'dashboard.orders.first_bid'),
                'value' => $formatMinutes($stats['orders']['avgFirstBidMinutes']),
            ],
            [
                'label' => Yii::t('app', 'dashboard.orders.gmv'),
                'value' => $gmvLabels ? implode(' + ', $gmvLabels) : '—',
            ],
        ],
    ],
    [
        'title' => Yii::t('app', 'dashboard.bids.title'),
        'cards' => [
            [
                'label' => Yii::t('app', 'dashboard.bids.total'),
                'value' => $stats['bids']['total'],
            ],
            [
                'label' => Yii::t('app', 'dashboard.bids.per_order'),
                'value' => $stats['bids']['perOrder'],
            ],
        ],
    ],
];

$presets = [
    7 => Yii::t('app', 'dashboard.filter.period_7'),
    30 => Yii::t('app', 'dashboard.filter.period_30'),
    90 => Yii::t('app', 'dashboard.filter.period_90'),
];

$activeDays = null;

foreach (array_keys($presets) as $days) {
    if ($searchModel->date_to === date('Y-m-d')
        && $searchModel->date_from === date('Y-m-d', strtotime('-' . ($days - 1) . ' days'))) {
        $activeDays = $days;
        break;
    }
}

$payload = [
    'labels' => [
        'created' => Yii::t('app', 'dashboard.series.created'),
        'completed' => Yii::t('app', 'dashboard.series.completed'),
        'customers' => Yii::t('app', 'dashboard.customers.title'),
        'executors' => Yii::t('app', 'dashboard.executors.title'),
        'bids' => Yii::t('app', 'dashboard.bids.title'),
        'orders' => Yii::t('app', 'dashboard.orders.title'),
        'noData' => Yii::t('app', 'dashboard.no_data'),
    ],
    'charts' => $charts,
];

?>
<div class="page dashboard">
    <div class="page__header">
        <h1 class="page__title"><?= Html::encode($this->title) ?></h1>
    </div>
    <form class="dashboard-filter" method="get" action="<?= Url::to(['/dashboard/index']) ?>">
        <div class="dashboard-filter__presets">
            <?php foreach ($presets as $days => $label) : ?>
                <?= Html::a(
                    $label,
                    [
                        '/dashboard/index',
                        'date_from' => date('Y-m-d', strtotime('-' . ($days - 1) . ' days')),
                        'date_to' => date('Y-m-d'),
                        'country_code' => $searchModel->country_code,
                    ],
                    ['class' => 'dashboard-filter__preset' . ($activeDays === $days ? ' dashboard-filter__preset--active' : '')]
                ) ?>
            <?php endforeach; ?>
        </div>
        <div class="dashboard-filter__controls">
            <?= Html::input('date', 'date_from', $searchModel->date_from, ['class' => 'form-control dashboard-filter__input']) ?>
            <span class="dashboard-filter__dash">—</span>
            <?= Html::input('date', 'date_to', $searchModel->date_to, ['class' => 'form-control dashboard-filter__input']) ?>
            <?= Html::dropDownList('country_code', $searchModel->country_code, OrderHelper::getCountries(), [
                'class' => 'form-control dashboard-filter__input',
                'prompt' => Yii::t('app', 'dashboard.filter.country_all'),
            ]) ?>
            <?= Html::submitButton(Yii::t('app', 'dashboard.filter.apply'), ['class' => 'btn btn--primary btn--sm']) ?>
        </div>
    </form>
    <?php foreach ($kpiGroups as $group) : ?>
        <div class="dashboard-section">
            <div class="dashboard-section__title"><?= Html::encode($group['title']) ?></div>
            <div class="dashboard-kpi">
                <?php foreach ($group['cards'] as $card) : ?>
                    <div class="kpi-card">
                        <div class="kpi-card__value">
                            <?= Html::encode($card['value']) ?>
                            <?php if (!empty($card['hint'])) : ?>
                                <span class="kpi-card__hint"><?= Html::encode($card['hint']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="kpi-card__label"><?= Html::encode($card['label']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="dashboard-charts">
        <div class="chart-card chart-card--wide">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.orders_by_day')) ?></div>
            <div class="chart-card__body" id="dashboard-orders-by-day"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.registrations_by_day')) ?></div>
            <div class="chart-card__body" id="dashboard-registrations-by-day"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.bids_by_day')) ?></div>
            <div class="chart-card__body" id="dashboard-bids-by-day"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.funnel')) ?></div>
            <div class="chart-card__body" id="dashboard-funnel"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.orders_by_type')) ?></div>
            <div class="chart-card__body" id="dashboard-orders-by-type"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.orders_by_status')) ?></div>
            <div class="chart-card__body" id="dashboard-orders-by-status"></div>
        </div>
        <div class="chart-card">
            <div class="chart-card__title"><?= Html::encode(Yii::t('app', 'dashboard.chart.top_locations')) ?></div>
            <div class="chart-card__body" id="dashboard-top-locations"></div>
        </div>
    </div>
</div>
<?php

$this->registerJs('Dashboard.init(' . Json::htmlEncode($payload) . ');');
