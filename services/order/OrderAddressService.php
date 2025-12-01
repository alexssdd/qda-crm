<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\entities\Order;
use app\forms\AddressSelectForm;

/**
 * Order address service
 */
class OrderAddressService
{
    private $_order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    /**
     * @param AddressSelectForm $form
     * @return void
     * @throws Exception
     */
    public function change(AddressSelectForm $form)
    {
        $order = $this->_order;
        $extraFields = $order->extra_fields;

        // Set attributes
        $order->address = $form->address;
        $order->lat = $form->lat;
        $order->lng = $form->lng;

        // Set extra fields
        $extraFields['house'] = $form->house;
        $extraFields['apartment'] = $form->apartment;
        $extraFields['intercom'] = $form->intercom;
        $extraFields['entrance'] = $form->entrance;
        $extraFields['floor'] = $form->floor;
        $extraFields['address_type'] = $form->type;
        $extraFields['address_title'] = $form->title;
        $order->extra_fields = $extraFields;

        // Save
        if (!$order->save()){
            throw new DomainException($order->getErrorMessage());
        }
    }
}