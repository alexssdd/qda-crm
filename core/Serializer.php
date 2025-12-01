<?php

namespace app\core;


class Serializer extends \yii\rest\Serializer
{
    protected function serializePagination($pagination): array
    {
        return [
            'currentPage' => $pagination->getPage() + 1,
            'count' => $pagination->totalCount,
            'pageCount' => $pagination->getPageCount(),
        ];
    }
}