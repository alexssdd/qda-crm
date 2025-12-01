<?php

namespace app\widgets;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Grid view
 */
class GridView extends \yii\grid\GridView
{
    public $layout = "{items}\n<div class='grid-view__footer'>{pager}{summary}</div>";

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->pager = [
            'options' => [
                'class' => 'pagination',
            ],
            'linkContainerOptions' => ['class' => 'pagination__item'],
            'linkOptions' => ['class' => 'pagination__link'],
            'disabledListItemSubTagOptions' => [
                'tag' => 'a',
                'class' => 'pagination__link pagination__link--disabled'
            ],
            'prevPageLabel' => Yii::t('app', 'Keep off'),
            'nextPageLabel' => Yii::t('app', 'Forward'),
            'firstPageLabel' => '<i class="icon-first_page pagination__strong"></i>',
            'lastPageLabel' => '<i class="icon-last_page pagination__strong"></i>'
        ];
    }

    /**
     * @return string
     */
    public function renderTableBody(): string
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr class='empty-row'><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }

        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }
}