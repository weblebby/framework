<?php

namespace Feadmin\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Collection;

class Paginator
{
    public static function fromArray(
        array $items,
        int $perPage = 15,
        int $page = null,
        array $options = []
    ): LengthAwarePaginator {
        return self::fromCollection(collect($items), $perPage, $page, $options);
    }

    public static function fromCollection(
        Collection $items,
        int $perPage = 15,
        int $page = null,
        array $options = []
    ): LengthAwarePaginator {
        $page ??= PaginationPaginator::resolveCurrentPage(1);

        if (!isset($options['path'])) {
            $options['path'] = PaginationPaginator::resolveCurrentPath();
        }

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options,
        );
    }
}
