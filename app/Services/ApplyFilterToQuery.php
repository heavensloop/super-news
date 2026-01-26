<?php

namespace App\Services;

use App\Enum\FilterType;
use Illuminate\Database\Eloquent\Builder;

class ApplyFilterToQuery
{
    function __invoke(Builder $query, FilterType $filterType, array $values)
    {
        foreach ($values as $value) {
            match ($filterType) {
                FilterType::KEYWORD => $this->applyKeywordFilter($query, $value),
                FilterType::CATEGORY, FilterType::SOURCE, FilterType::AUTHOR =>
                $this->applyStandardFilter($query, $filterType->value, $value),
                FilterType::DATE_PUBLISHED =>
                $query->whereDate('published_at', '=', $value),
                FilterType::PUBLISHED_BETWEEN =>
                $query->whereBetween('published_at', [$value['from'], $value['to']]),
            };
        }
    }

    private function applyKeywordFilter(Builder $query, string $keyword): Builder
    {
        return $query->where(function (Builder $subQuery) use ($keyword) {
            $subQuery
                ->where('title', 'LIKE', '%' . $keyword . '%')
                ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                ->orWhere('content', 'LIKE', '%' . $keyword . '%')
                ->orWhere('author', 'LIKE', '%' . $keyword . '%');
        });
    }

    private function applyStandardFilter(Builder $query, string $field, array|string $value): Builder
    {
        if (is_array($value)) {
            return $query->whereIn($field, $value);
        }

        return $query->where($field, '=', $value);
    }
}
