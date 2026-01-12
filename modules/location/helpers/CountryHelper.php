<?php

namespace app\modules\location\helpers;

class CountryHelper
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    public static function getData(): array
    {
        return [
            [
                'code' => 'kz',
                'name' => 'Kazakhstan',
                'names' => [
                    'ru' => 'Казахстан',
                    'kk' => 'Қазақстан',
                    'uz' => 'Qozogʻiston',
                    'cn' => '哈萨克斯坦'
                ],
                'client_api_url' => 'https://api.goqda.kz',
                'pro_api_url' => 'https://api-pro.goqda.kz',
                'phone_code' => '+7',
                'phone_mask' => '+7 (7##) ###-##-##'
            ],
            [
                'code' => 'uz',
                'name' => 'Uzbekistan',
                'names' => [
                    'ru' => 'Узбекистан',
                    'kk' => 'Өзбекстан',
                    'uz' => 'Oʻzbekiston',
                    'cn' => '乌兹别克斯坦'
                ],
                'client_api_url' => 'https://api-uz.goqda.com',
                'pro_api_url' => 'https://api-pro-uz.goqda.com',
                'phone_code' => '+998',
                'phone_mask' => '+998 (##) ###-##-##'
            ],
            [
                'code' => 'am',
                'name' => 'Armenia',
                'names' => [
                    'ru' => 'Армения',
                    'kk' => 'Армения',
                    'uz' => 'Armaniston',
                    'cn' => '亚美尼亚'
                ],
                'client_api_url' => 'https://api-am.goqda.com',
                'pro_api_url' => 'https://api-pro-am.goqda.com',
                'phone_code' => '+374',
                'phone_mask' => '+374 (##) ###-###'
            ],
            [
                'code' => 'by',
                'name' => 'Belarus',
                'names' => [
                    'ru' => 'Беларусь',
                    'kk' => 'Беларусь',
                    'uz' => 'Belarus',
                    'cn' => '白俄罗斯'
                ],
                'client_api_url' => 'https://api-by.goqda.com',
                'pro_api_url' => 'https://api-pro-by.goqda.com',
                'phone_code' => '+375',
                'phone_mask' => '+375 (##) ###-##-##'
            ],
            [
                'code' => 'ge',
                'name' => 'Georgia',
                'names' => [
                    'ru' => 'Грузия',
                    'kk' => 'Грузия',
                    'uz' => 'Gruziya',
                    'cn' => '格鲁吉亚'
                ],
                'client_api_url' => 'https://api-ge.goqda.com',
                'pro_api_url' => 'https://api-pro-ge.goqda.com',
                'phone_code' => '+995',
                'phone_mask' => '+995 (###) ###-###'
            ],
            [
                'code' => 'kg',
                'name' => 'Kyrgyzstan',
                'names' => [
                    'ru' => 'Кыргызстан',
                    'kk' => 'Қырғызстан',
                    'uz' => 'Qirgʻiziston',
                    'cn' => '吉尔吉斯斯坦'
                ],
                'client_api_url' => 'https://api-kg.goqda.com',
                'pro_api_url' => 'https://api-pro-kg.goqda.com',
                'phone_code' => '+996',
                'phone_mask' => '+996 (###) ###-###'
            ],
            [
                'code' => 'mn',
                'name' => 'Mongolia',
                'names' => [
                    'ru' => 'Монголия',
                    'kk' => 'Моңғолия',
                    'uz' => 'Mongoliya',
                    'cn' => '蒙古'
                ],
                'client_api_url' => 'https://api-mn.goqda.com',
                'pro_api_url' => 'https://api-pro-mn.goqda.com',
                'phone_code' => '+976',
                'phone_mask' => '+976 (##) ##-##-##'
            ],
            [
                'code' => 'ru',
                'name' => 'Russia',
                'names' => [
                    'ru' => 'Россия',
                    'kk' => 'Ресей',
                    'uz' => 'Rossiya',
                    'cn' => '俄罗斯'
                ],
                'client_api_url' => 'https://api-ru.goqda.com',
                'pro_api_url' => 'https://api-pro-ru.goqda.com',
                'phone_code' => '+7',
                'phone_mask' => '+7 (###) ###-##-##'
            ],
            [
                'code' => 'tj',
                'name' => 'Tajikistan',
                'names' => [
                    'ru' => 'Таджикистан',
                    'kk' => 'Тәжікстан',
                    'uz' => 'Tojikiston',
                    'cn' => '塔吉克斯坦'
                ],
                'client_api_url' => 'https://api-tj.goqda.com',
                'pro_api_url' => 'https://api-pro-tj.goqda.com',
                'phone_code' => '+992',
                'phone_mask' => '+992 (##) ###-###'
            ],
        ];
    }
}