<?php

namespace app\modules\orders\models;

use yii\data\Pagination;

class OrdersPagination extends Pagination
{
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_PARAM = 'per-page';

    /**
     * Устанавливает св-во pageSize из параметра per-page, если он есть. Иначе берётся значение по умолчанию
     *
     * @param array $params
     * @return void
     */
    public function setPerPage(array $params)
    {
        $this->pageSize = $params[self::PAGE_SIZE_PARAM] ?? self::PAGE_SIZE_DEFAULT;
    }

    /**
     *
     * Возвращает информацию о текущем положении и общем числе записей
     *
     * @return string
     */
    public function getPaginationCounters() :string
    {
        return sprintf('%s to %s of %s', $this->page * $this->pageSize + 1, ($this->page + 1) * $this->pageSize, $this->totalCount);
    }

}