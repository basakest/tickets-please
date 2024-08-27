<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

abstract class QueryFilter
{
    protected array $sortable = [];

    protected Builder $builder;

    public function __construct(protected Request $request)
    {}

    protected function sort(string $sortValue): Builder
    {
        // DB::listen(function (QueryExecuted $query) {
        //     logger($query->toRawSql());
        // });
        $sortColumns = explode(',', $sortValue);
        foreach ($sortColumns as $sortColumn) {
            $direction = 'asc';
            if (str_starts_with($sortColumn, '-')) {
                $direction = 'desc';
                $sortColumn = substr($sortColumn, 1);
            }
            $skipFlag = ! in_array($sortColumn, $this->sortable) && ! array_key_exists($sortColumn, $this->sortable);
            if ($skipFlag) {
                continue;
            }
            $sortColumn = $this->sortable[$sortColumn] ?? $sortColumn;
            $this->builder->orderBy($sortColumn, $direction);
        }

        return $this->builder;
    }

    protected function filter($arr): Builder
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }
}