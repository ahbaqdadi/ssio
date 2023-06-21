<?php

namespace App\Message\Query;

class GetListTimeStreamQuery
{
    private int|null $pagination;

    public function __construct(int|null $pagination = null)
    {
        $this->pagination = $pagination;
    }

    public function getPagination(): int
    {
        return $this->pagination;
    }
}