<?php

namespace app\modules\moderation\services;

final class ModerationMessageBuilder
{
    private const REASON_LABELS = [
        'prohibited_goods' => 'возможны запрещённые товары',
        'hazardous_cargo' => 'возможен опасный груз',
        'contact_bypass' => 'контакты или попытка обхода площадки',
        'suspected_fraud' => 'признаки возможного мошенничества',
        'duplicate_order_spam' => 'повторяющиеся заказы или спам',
        'high_order_frequency' => 'аномально высокая частота заказов',
        'high_cancellation_rate' => 'высокая доля отмен',
        'inconsistent_order_data' => 'противоречия в данных заказа',
        'insufficient_order_data' => 'недостаточно данных для уверенного решения',
        'abusive_content' => 'недопустимый текст',
        'unsafe_service' => 'возможна небезопасная или незаконная услуга',
        'other' => 'другая причина',
    ];

    private const ACTION_LABELS = [
        'none' => 'действие не требуется',
        'manual_review' => 'проверить вручную',
        'hide_comment' => 'рассмотреть скрытие комментария',
        'cancel_order' => 'рассмотреть отмену заказа',
        'request_correction' => 'запросить уточнение или исправление',
    ];

    public function build(array $result): string
    {
        $verdict = $result['verdict'] ?? 'review';
        if ($verdict === 'allow') {
            return 'AI-модерация завершена: нарушений не обнаружено.';
        }

        $prefix = $verdict === 'violation'
            ? 'AI-модерация завершена: выявлено нарушение.'
            : 'AI-модерация завершена: требуется проверка.';

        $reasonLabels = [];
        foreach (array_unique($result['reason_codes'] ?? []) as $reasonCode) {
            $reasonLabels[] = self::REASON_LABELS[$reasonCode] ?? self::REASON_LABELS['other'];
        }

        if ($reasonLabels === []) {
            $reasonLabels[] = 'результат требует ручной оценки';
        }

        $action = self::ACTION_LABELS[$result['recommended_action'] ?? 'manual_review']
            ?? self::ACTION_LABELS['manual_review'];

        return sprintf(
            '%s Причина: %s. Рекомендация: %s.',
            $prefix,
            implode(', ', $reasonLabels),
            $action
        );
    }
}
