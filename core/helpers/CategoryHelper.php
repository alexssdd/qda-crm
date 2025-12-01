<?php

namespace app\core\helpers;

use Exception;
use app\entities\Category;
use yii\helpers\ArrayHelper;

/**
 * Category helper
 */
class CategoryHelper
{
    /**
     * @return array
     * @throws Exception
     */
    public static function getSuperCategories(): array
    {
        /** @var Category[] $categories */
        $categories = Category::find()
            ->indexBy('id')
            ->all();

        $result = [];
        foreach ($categories as $item)
        {
            if (!$item->parent_id){
                $result[$item->id] = $item->name;
                continue;
            }

            $parentId = $item->parent_id;
            while (true){
                /** @var Category $current */
                $current = ArrayHelper::getValue($categories, $parentId);

                if (!$current){
                    $result[$item->id] = null;

                    break;
                }

                if (!$current->parent_id){
                    $result[$item->id] = $current->name;

                    break;
                }

                $parentId = $current->parent_id;
            }
        }

        return $result;
    }
}