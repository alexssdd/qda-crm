<?php

namespace app\core\helpers;

use Yii;
use Exception;
use app\entities\Care;
use yii\helpers\ArrayHelper;

/**
 * Care helper
 */
class CareHelper
{
    /** Channels */
    const CHANNEL_ASSISTANT = 10;
    const CHANNEL_WHATSAPP = 11;
    const CHANNEL_TELEGRAM = 12;
    const CHANNEL_CHAT_SITE = 13;
    const CHANNEL_CHAT_APP_MARWIN = 14;
    const CHANNEL_KASPI_SERVICE_DESK = 15;
    const CHANNEL_INSTAGRAM = 16;
    const CHANNEL_LINE = 17;
    const CHANNEL_MAIL = 18;
    const CHANNEL_REVIEW_KASPI_SHOP = 19;
    const CHANNEL_REVIEW_TWO_GIS = 20;
    const CHANNEL_REVIEW_MARWIN = 21;
    const CHANNEL_REVIEW_PLAY_MARKET = 22;
    const CHANNEL_REVIEW_APP_STORE = 23;
    const CHANNEL_REVIEW_WB = 24; // Отзыв wildberries
    const CHANNEL_REVIEW_OZON = 25; // Отзыв Ozon
    const CHANNEL_REVIEW_MAGENTO = 26; // Отзыв Magento
    const CHANNEL_REVIEW_GOOGLE = 27; // Отзыв Google
    const CHANNEL_REVIEW_YANDEX = 28; // Отзыв Яндекс

    /** Types */
    const TYPE_POSITIVE = 10;
    const TYPE_NEGATIVE = 11;

    /** Statuses */
    const STATUS_CREATED = 9;
    const STATUS_NEW = 10;
    const STATUS_ACCEPTED = 11;
    const STATUS_WAITING = 15;
    const STATUS_FINISHED_GOOD = 12;
    const STATUS_FINISHED_BAD = 13;
    const STATUS_COULD_NOT_CALL = 14;

    /** Statuses */
    const CALLBACK_STATUS_ATTEMPT_1 = 10;
    const CALLBACK_STATUS_ATTEMPT_2 = 11;
    const CALLBACK_STATUS_ATTEMPT_3 = 12;
    const CALLBACK_STATUS_FINISHED = 13;

    /** Languages */
    const LANGUAGE_RU = 10;
    const LANGUAGE_KZ = 11;

    /** Count Request */
    const COUNT_REQUEST_1 = 10;
    const COUNT_REQUEST_2 = 11;
    const COUNT_REQUEST_3 = 12;

    /** Count Problem */
    const COUNT_PROBLEM_1 = 10;
    const COUNT_PROBLEM_2 = 11;
    const COUNT_PROBLEM_3 = 12;

    /** Delivery Late */
    const DELIVERY_LATE_1 = 10;
    const DELIVERY_LATE_2 = 11;
    const DELIVERY_LATE_3 = 12;
    const DELIVERY_LATE_4 = 13;

    /** Complaint Object */
    const COMPLAINT_OBJECT_1 = 10;
    const COMPLAINT_OBJECT_2 = 11;
    const COMPLAINT_OBJECT_3 = 12;
    const COMPLAINT_OBJECT_4 = 13;

    /** Complaint validity */
    const COMPLAINT_VALIDITY_YES = 'yes';
    const COMPLAINT_VALIDITY_NO = 'no';

    /** Categories */
    const CATEGORY_DELIVERY = 'delivery';
    const CATEGORY_STORE = 'store';
    const CATEGORY_PRODUCT = 'product';
    const CATEGORY_CARE = 'care';
    const CATEGORY_MOBILE = 'mobile';
    const CATEGORY_SITE = 'site';
    const CATEGORY_KASPI = 'kaspi';
    const CATEGORY_CERTIFICATE = 'certificate';

    /** Final status */
    const FINAL_STATUS_NO_CHANGES = 10;
    const FINAL_STATUS_POSITIVE = 11;
    const FINAL_STATUS_DELETED = 12;

    /**
     * @return string[]
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_POSITIVE => 'Положительный',
            self::TYPE_NEGATIVE => 'Отрицательный'
        ];
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getTypeName($type): ?string
    {
        return ArrayHelper::getValue(static::getTypeArray(), $type);
    }

    /**
     * @param $type
     * @return string
     * @throws Exception
     */
    public static function getTypeKey($type): string
    {
        $keys = [
            self::TYPE_POSITIVE => 'positive',
            self::TYPE_NEGATIVE => 'negative'
        ];

        return ArrayHelper::getValue($keys, $type);
    }

    /**
     * @return string[]
     */
    public static function getChannelArray(): array
    {
        return [
            self::CHANNEL_ASSISTANT => 'Ассистент',
            self::CHANNEL_WHATSAPP => 'Whatsapp',
            self::CHANNEL_TELEGRAM => 'Telegram',
            self::CHANNEL_CHAT_SITE => 'Чат сайта',
            self::CHANNEL_CHAT_APP_MARWIN => 'Чат приложения Меломан',
            self::CHANNEL_KASPI_SERVICE_DESK => 'Kaspi ServiceDesk',
            self::CHANNEL_INSTAGRAM => 'Instagram',
            self::CHANNEL_LINE => 'Прямая линия',
            self::CHANNEL_MAIL => 'Почта',
            self::CHANNEL_REVIEW_KASPI_SHOP => 'Отзыв Kaspi Shop',
            self::CHANNEL_REVIEW_TWO_GIS => 'Отзыв и чат в 2ГИС',
            self::CHANNEL_REVIEW_MARWIN => 'Отзыв Меломан',
            self::CHANNEL_REVIEW_PLAY_MARKET => 'Отзыв Play Market',
            self::CHANNEL_REVIEW_APP_STORE => 'Отзыв App Store',
            self::CHANNEL_REVIEW_WB => 'Отзыв Wildberries',
            self::CHANNEL_REVIEW_OZON => 'Отзыв Ozon',
            self::CHANNEL_REVIEW_MAGENTO => 'Отзыв Magento',
            self::CHANNEL_REVIEW_GOOGLE => 'Отзыв Google',
            self::CHANNEL_REVIEW_YANDEX => 'Отзыв Яндекс',
        ];
    }

    /**
     * @param $channel
     * @return string|null
     * @throws Exception
     */
    public static function getChannelName($channel): ?string
    {
        return ArrayHelper::getValue(static::getChannelArray(), $channel);
    }

    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_CREATED => 'Создан',
            self::STATUS_NEW => 'Новый',
            self::STATUS_ACCEPTED => 'Принят',
            self::STATUS_WAITING => 'В ожидании',
            self::STATUS_FINISHED_GOOD => 'Завершен удовлетворительно',
            self::STATUS_FINISHED_BAD => 'Завершен не удовлетворительно',
            self::STATUS_COULD_NOT_CALL => 'Не удалось дозвониться',
        ];
    }

    /**
     * @param $status
     * @return string|null
     * @throws Exception
     */
    public static function getStatusName($status): ?string
    {
        return ArrayHelper::getValue(self::getStatuses(), $status);
    }

    /**
     * @return string[]
     */
    public static function getLanguages(): array
    {
        return [
            self::LANGUAGE_RU => 'Русский',
            self::LANGUAGE_KZ => 'Казахский'
        ];
    }

    /**
     * @param $language
     * @return string|null
     * @throws Exception
     */
    public static function getLanguage($language): ?string
    {
        return ArrayHelper::getValue(static::getLanguages(), $language);
    }

    /**
     * @return array
     */
    public static function getCountRequestArray(): array
    {
        return [
            self::COUNT_REQUEST_1 => Yii::t('care', 'COUNT_REQUEST_1'),
            self::COUNT_REQUEST_2 => Yii::t('care', 'COUNT_REQUEST_2'),
            self::COUNT_REQUEST_3 => Yii::t('care', 'COUNT_REQUEST_3'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getCountRequest($key)
    {
        return ArrayHelper::getValue(self::getCountRequestArray(), $key);
    }

    /**
     * @return array
     */
    public static function getCountProblemArray(): array
    {
        return [
            self::COUNT_PROBLEM_1 => Yii::t('care', 'COUNT_PROBLEM_1'),
            self::COUNT_PROBLEM_2 => Yii::t('care', 'COUNT_PROBLEM_2'),
            self::COUNT_PROBLEM_3 => Yii::t('care', 'COUNT_PROBLEM_3'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getCountProblem($key)
    {
        return ArrayHelper::getValue(self::getCountProblemArray(), $key);
    }

    /**
     * @return array
     */
    public static function getDeliveryLateArray(): array
    {
        return [
            self::DELIVERY_LATE_1 => Yii::t('care', 'DELIVERY_LATE_1'),
            self::DELIVERY_LATE_2 => Yii::t('care', 'DELIVERY_LATE_2'),
            self::DELIVERY_LATE_3 => Yii::t('care', 'DELIVERY_LATE_3'),
            self::DELIVERY_LATE_4 => Yii::t('care', 'DELIVERY_LATE_4'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getDeliveryLate($key)
    {
        return ArrayHelper::getValue(self::getDeliveryLateArray(), $key);
    }

    /**
     * @return array
     */
    public static function getComplaintObjectArray(): array
    {
        return [
            self::COMPLAINT_OBJECT_1 => Yii::t('care', 'COMPLAINT_OBJECT_1'),
            self::COMPLAINT_OBJECT_2 => Yii::t('care', 'COMPLAINT_OBJECT_2'),
            self::COMPLAINT_OBJECT_3 => Yii::t('care', 'COMPLAINT_OBJECT_3'),
            self::COMPLAINT_OBJECT_4 => Yii::t('care', 'COMPLAINT_OBJECT_4'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getComplaintObject($key)
    {
        return ArrayHelper::getValue(self::getComplaintObjectArray(), $key);
    }

    /**
     * @return array
     */
    public static function getComplaintValidityArray(): array
    {
        return [
            self::COMPLAINT_VALIDITY_YES => Yii::t('care', 'COMPLAINT_VALIDITY_YES'),
            self::COMPLAINT_VALIDITY_NO => Yii::t('care', 'COMPLAINT_VALIDITY_NO')
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getComplaintValidity($key)
    {
        return ArrayHelper::getValue(self::getComplaintValidityArray(), $key);
    }

    /**
     * @return array
     */
    public static function getCategoryArray(): array
    {
        return [
            self::CATEGORY_DELIVERY => Yii::t('care', 'CATEGORY_DELIVERY'),
            self::CATEGORY_STORE => Yii::t('care', 'CATEGORY_STORE'),
            self::CATEGORY_PRODUCT => Yii::t('care', 'CATEGORY_PRODUCT'),
            self::CATEGORY_CARE => Yii::t('care', 'CATEGORY_CARE'),
            self::CATEGORY_MOBILE => Yii::t('care', 'CATEGORY_MOBILE'),
            self::CATEGORY_SITE => Yii::t('care', 'CATEGORY_SITE'),
            self::CATEGORY_KASPI => Yii::t('care', 'CATEGORY_KASPI'),
            self::CATEGORY_CERTIFICATE => Yii::t('care', 'CATEGORY_CERTIFICATE'),
        ];
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getCategory($key)
    {
        return ArrayHelper::getValue(self::getCategoryArray(), $key);
    }

    /**
     * @return array[]
     */
    public static function getComplaintReasons(): array
    {
        return [
            self::CATEGORY_DELIVERY => [
                'Долгая доставка',
                'Грубость курьера',
                'Курьер взял деньги за доставку',
                'Заказ не доставлен',
                'Заказ доставлен частично',
            ],
            self::CATEGORY_STORE => [
                'Качество обслуживание. Грубость, не внимательность.',
                'Не соблюдение графика',
                'Неправильно передан заказ',
                'Неправильная консультация',
                'Долгое подтверждение заказа',
                'Запрос на возврат товара',
                'Чистота и организация работы ТТ',
                'Продажа срокового товара'
            ],
            self::CATEGORY_PRODUCT => [
                'Брак товара',
                'Сроковый товар',
                'Не правильная привязка товара',
                'Не качественный товар',
                'Цена товара',
                'Не подошел товар',
            ],
            self::CATEGORY_CARE => [
                'Качество обслуживание. Грубость, не внимательность',
                'Неправильная консультация',
                'Неправильная обработка заказа',
                'Долгий ответ клиенту',
            ],
            self::CATEGORY_MOBILE => [
                'Не проходит регистрация',
                'Не приходит смс для регистрации',
                'Не оформляется заказ. Ошибка',
                'Не корректно отображается цена',
                'Не начисляются бонусы',
                'Зависает',
                'Не работает карта. Адрес нельзя добавить',
                'Не работает поисковик',
            ],
            self::CATEGORY_SITE => [
                'Не оформляется заказ',
                'Не проходит регистрация',
                'Не корректно отображается цена',
                'Зависает',
                'Не работает карта. Не рассчитывается стоимость доставки',
                'Не работает поисковик',
                'Не корректная информация',
                'Не начисляется бонусы',
                'Не могут провести купон по заказу',
            ],
            self::CATEGORY_KASPI => [
                'Заказ не завершен',
                'Не правильная привязка товара',
                'Не правильная цена',
                'Долгая доставка',
                'Жалоба на курьера',
                'Запрос на возврат. Вины компании нет',
                'Утеря товара',
                'Повреждение товара',
            ],
            self::CATEGORY_CERTIFICATE => [
                'Не пришел номер сертификата',
                'Возврат сертификата',
                'Номер сертификата не верный',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getSolutionMeasureArray(): array
    {
        return [
            self::TYPE_POSITIVE => [
                self::CATEGORY_DELIVERY => [
                    'Быстрая и своевременная доставка'
                ],
                self::CATEGORY_STORE => [
                    'График работы ТТ и удобное расположение',
                    'Ассортимент ТТ',
                    'Качество обслуживание ТТ',
                    'Помощь с выбором',
                ],
                self::CATEGORY_PRODUCT => [
                    'Высокое качество товаров',
                    'Адекватные цены на товары',
                    'Наличие выгодных акции и скидок',
                ],
                self::CATEGORY_CARE => [
                    'Качественное обслуживание',
                    'Оперативное решение жалобы',
                    'Благодарность сотруднику',
                ],
                self::CATEGORY_MOBILE => [
                    'Качественное обслуживание',
                ],
                self::CATEGORY_SITE => [
                    'Качественное обслуживание',
                ],
                self::CATEGORY_KASPI => [
                    'Качественное обслуживание',
                ],
                self::CATEGORY_CERTIFICATE => [
                    'Качество сертификата',
                ]
            ],
            self::TYPE_NEGATIVE => [
                self::CATEGORY_DELIVERY => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_STORE => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_PRODUCT => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_CARE => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_MOBILE => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_SITE => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ],
                self::CATEGORY_KASPI => [
                    'Оплачен через выставленный счет',
                    'Заказ переоформлен',
                    'Заказ не завершен. Нет связи с клиентом',
                    'Запрос на исправление привязки',
                    'Есть компенсации от каспи',
                    'Нет компенсации от каспи',
                ],
                self::CATEGORY_CERTIFICATE => [
                    'Устное замечание',
                    'Объяснительное',
                    'Штраф',
                    'Задача В Битрикс',
                    'Задача супервайзеру ecom',
                    'Нет ответа от админа ТТ',
                    'Возврат оформлен',
                    'Задача логистам',
                    'Возврат товара',
                    'Возврат денег за доставку',
                    'Исправление привязки товара',
                    'Выставление удаленного счета',
                ]
            ]
        ];
    }

    /**
     * @return string[]
     */
    public static function getCompensationArray(): array
    {
        $items = [
            'Без компенсации',
            'Бесплатная доставка на следующий заказ',
            'Бонус 1000 тг',
            'Бонус 2000 тг',
            'Бонус 5000 тг',
            'Бонус 10 000 тг',
            'Сертификат 2000 тг',
            'Сертификат 5000 тг',
            'Сертификат 10 000 тг',
            'Полный возврат средств по заказу',
        ];

        $result = [];
        foreach ($items as $item) {
            $result[$item] = $item;
        }

        return $result;
    }

    /**
     * @param Care $care
     * @return string|null
     */
    public static function getHandlerName(Care $care): ?string
    {
        return $care->handler ? $care->handler->full_name : null;
    }

    /**
     * @param Care $care
     * @return string|null
     * @throws Exception
     */
    public static function getCreated(Care $care): ?string
    {
        return Yii::$app->formatter->asDatetime($care->created_at);
    }

    /**
     * @param Care $care
     * @return array
     * @throws Exception
     */
    public static function getAvailableStatuses(Care $care): array
    {
        switch ($care->status) {
            case self::STATUS_NEW:
                return [
                    self::STATUS_NEW => self::getStatusName(self::STATUS_NEW),
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                    self::STATUS_WAITING => self::getStatusName(self::STATUS_WAITING),
                ];
            case self::STATUS_ACCEPTED:
            case self::STATUS_WAITING:
                return [
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                    self::STATUS_WAITING => self::getStatusName(self::STATUS_WAITING),
                    self::STATUS_FINISHED_GOOD => self::getStatusName(self::STATUS_FINISHED_GOOD),
                    self::STATUS_FINISHED_BAD => self::getStatusName(self::STATUS_FINISHED_BAD),
                    self::STATUS_COULD_NOT_CALL => self::getStatusName(self::STATUS_COULD_NOT_CALL),
                ];
        }
        return [];
    }

    /**
     * @param $status
     * @return bool
     */
    public static function isFinished($status): bool
    {
        return in_array($status, [self::STATUS_FINISHED_BAD, self::STATUS_FINISHED_GOOD, self::STATUS_COULD_NOT_CALL]);
    }

    /**
     * @return string[]
     */
    public static function getFinalStatusArray(): array
    {
        return [
            self::FINAL_STATUS_NO_CHANGES => Yii::t('care', 'FINAL_STATUS_NO_CHANGES'),
            self::FINAL_STATUS_POSITIVE => Yii::t('care', 'FINAL_STATUS_POSITIVE'),
            self::FINAL_STATUS_DELETED => Yii::t('care', 'FINAL_STATUS_DELETED'),
        ];
    }

    /**
     * @param $finalStatus
     * @return string|null
     * @throws Exception
     */
    public static function getFinalStatusName($finalStatus): ?string
    {
        return ArrayHelper::getValue(self::getFinalStatusArray(), $finalStatus);
    }
}