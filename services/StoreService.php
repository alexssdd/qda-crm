<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\Store;
use app\forms\StoreCreateForm;
use app\forms\StoreUpdateForm;

/**
 * Store service
 */
class StoreService
{
    /**
     * @param StoreCreateForm $form
     * @return Store
     * @throws Exception
     */
    public function create(StoreCreateForm $form): Store
    {
        $model = new Store();
        $model->merchant_id = $form->merchant_id;
        $model->city_id = $form->city_id;
        $model->type = $form->type;
        $model->name = $form->name;
        $model->number = $form->number;
        $model->address = $form->address;
        $model->lat = $form->lat;
        $model->lng = $form->lng;
        $model->config = [
            'phone' => $form->phone,
            'working_time' => $form->working_time,
            'name_short' => $form->name_short,
            'delivery_number' => $form->delivery_number,
            'two_gis_id' => $form->two_gis_id,
            'yandex_company_id' => $form->yandex_company_id,
            'google_id' => $form->google_id,
            'kaspi_export' => $form->kaspi_export,
            'kaspi_id' => $form->kaspi_id,
            'ozon_export' => $form->ozon_export,
            'ozon_id' => $form->ozon_id,
            'wb_export' => $form->wb_export,
            'wb_id' => $form->wb_id,
            'wolt_export' => $form->wolt_export,
            'wolt_id' => $form->wolt_id,
            'glovo_export' => $form->glovo_export,
            'glovo_id' => $form->glovo_id,
            'ye_export' => $form->ye_export,
            'ye_id' => $form->ye_id,
            'halyk_export' => $form->halyk_export,
            'halyk_id' => $form->halyk_id,
            'jusan_export' => $form->jusan_export,
            'jusan_id' => $form->jusan_id,
            'forte_export' => $form->forte_export,
            'forte_id' => $form->forte_id,
        ];
        $model->status = $form->status;
        $model->created_at = time();
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorMessage());
        }

        return $model;
    }

    /**
     * @param Store $model
     * @param StoreUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(Store $model, StoreUpdateForm $form): void
    {
        $model->name = $form->name;
        $model->address = $form->address;
        $model->lat = $form->lat;
        $model->lng = $form->lng;
        $model->status = $form->status;
        $model->updated_at = time();

        $config = $model->config;
        $config['phone'] = $form->phone;
        $config['working_time'] = $form->working_time;
        $config['name_short'] = $form->name_short;
        $config['delivery_number'] = $form->delivery_number;
        $config['two_gis_id'] = $form->two_gis_id;
        $config['yandex_company_id'] = $form->yandex_company_id;
        $config['google_id'] = $form->google_id;
        $config['kaspi_export'] = $form->kaspi_export;
        $config['kaspi_id'] = $form->kaspi_id;
        $config['ozon_export'] = $form->ozon_export;
        $config['ozon_id'] = $form->ozon_id;
        $config['wb_export'] = $form->wb_export;
        $config['wb_id'] = $form->wb_id;
        $config['wolt_export'] = $form->wolt_export;
        $config['wolt_id'] = $form->wolt_id;
        $config['glovo_export'] = $form->glovo_export;
        $config['glovo_id'] = $form->glovo_id;
        $config['ye_export'] = $form->ye_export;
        $config['ye_id'] = $form->ye_id;
        $config['halyk_export'] = $form->halyk_export;
        $config['halyk_id'] = $form->halyk_id;
        $config['jusan_export'] = $form->jusan_export;
        $config['jusan_id'] = $form->jusan_id;
        $config['forte_export'] = $form->forte_export;
        $config['forte_id'] = $form->forte_id;
        $config['app_id'] = $form->app_id;
        $config['ozon_id_mhv'] = $form->ozon_id_mhv;
        $config['wb_id_express'] = $form->wb_id_express;
        $config['wb_id_pickup'] = $form->wb_id_pickup;
        $model->config = $config;

        if (!$model->save()) {
            throw new DomainException($model->getErrorMessage());
        }
    }
}