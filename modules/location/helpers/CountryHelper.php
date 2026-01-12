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
                'client_api_url' => 'https://api.goqda.kz',
                'pro_api_url' => 'https://api-pro.goqda.kz',
                'phone_code' => '+7',
                'phone_mask' => '+7 (7##) ###-##-##'
            ],
            [
                'code' => 'uz',
                'name' => 'Uzbekistan',
                'client_api_url' => 'https://api-uz.goqda.com',
                'pro_api_url' => 'https://api-pro-uz.goqda.com',
                'phone_code' => '+998',
                'phone_mask' => '+998 (##) ###-##-##'
            ],
            [
                'code' => 'am',
                'name' => 'Armenia',
                'client_api_url' => 'https://api-am.goqda.com',
                'pro_api_url' => 'https://api-pro-am.goqda.com',
                'phone_code' => '+374',
                'phone_mask' => '+374 (##) ###-###'
            ],
            [
                'code' => 'by',
                'name' => 'Belarus',
                'client_api_url' => 'https://api-by.goqda.com',
                'pro_api_url' => 'https://api-pro-by.goqda.com',
                'phone_code' => '+375',
                'phone_mask' => '+375 (##) ###-##-##'
            ],
            [
                'code' => 'ge',
                'name' => 'Georgia',
                'client_api_url' => 'https://api-ge.goqda.com',
                'pro_api_url' => 'https://api-pro-ge.goqda.com',
                'phone_code' => '+995',
                'phone_mask' => '+995 (###) ###-###'
            ],
            [
                'code' => 'kg',
                'name' => 'Kyrgyzstan',
                'client_api_url' => 'https://api-kg.goqda.com',
                'pro_api_url' => 'https://api-pro-kg.goqda.com',
                'phone_code' => '+996',
                'phone_mask' => '+996 (###) ###-###'
            ],
            [
                'code' => 'mn',
                'name' => 'Mongolia',
                'client_api_url' => 'https://api-mn.goqda.com',
                'pro_api_url' => 'https://api-pro-mn.goqda.com',
                'phone_code' => '+976',
                'phone_mask' => '+976 (##) ##-##-##'
            ],
            [
                'code' => 'ru',
                'name' => 'Russia',
                'client_api_url' => 'https://api-ru.goqda.com',
                'pro_api_url' => 'https://api-pro-ru.goqda.com',
                'phone_code' => '+7',
                'phone_mask' => '+7 (###) ###-##-##'
            ],
            [
                'code' => 'tj',
                'name' => 'Tajikistan',
                'client_api_url' => 'https://api-tj.goqda.com',
                'pro_api_url' => 'https://api-pro-tj.goqda.com',
                'phone_code' => '+992',
                'phone_mask' => '+992 (##) ###-###'
            ],
        ];
    }
}