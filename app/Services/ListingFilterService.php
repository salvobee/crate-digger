<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class ListingFilterService
{
    static function buildFilterQuery($query, $filters)
    {
        $filter_query = $query;
        foreach ($filters as $field => $values)
        {
            $fields_split = explode('.', $field);

            $filter_query =  match (count($fields_split)) {
                1 => self::buildSimpleFilterQuery($query, $field, $values),
                2 => self::buildNestedFilterQuery($query, $fields_split, $values),
                3 => self::buildDoubleNestedFilterQuery($query, $fields_split, $values),
                default => $query
            };
        }

        return $filter_query;
    }

    private static function buildSimpleFilterQuery(Builder $query, string $field, mixed $values): Builder
    {
        foreach ($values as $idx => $value) {
            if ($idx === 0)
                $query->where($field, '=', $value);
            else {
                $query->orWhere($field, '=', $value);
            }
        }

        return $query;
    }

    private static function buildNestedFilterQuery(Builder $query, array $fields_split, mixed $values): Builder
    {
        $relation = $fields_split[0];
        $related_field = $fields_split[1];

        return $query->whereHas($relation, function (Builder $relation_query) use ($related_field, $query, $values) {
            foreach ($values as $idx => $value) {
                if ($idx === 0)
                    $relation_query->where($related_field, '=', $value);
                else {
                    $relation_query->orWhere($related_field, '=', $value);
                }
            }
        });
    }

    private static function buildDoubleNestedFilterQuery(Builder $query, array $fields_split, mixed $values): Builder
    {
        $relation = $fields_split[0];
        $nested_relation = $fields_split[1];
        $nested_related_field = $fields_split[2];

        return $query->whereHas($relation, function (Builder $relation_query) use ($nested_relation, $nested_related_field, $query, $values) {
            $relation_query->whereHas($nested_relation, function (Builder $nested_relation_query) use ($nested_related_field, $values) {
                foreach ($values as $idx => $value) {
                    if ($idx === 0)
                        $nested_relation_query->where($nested_related_field, '=', $value);
                    else {
                        $nested_relation_query->orWhere($nested_related_field, '=', $value);
                    }
                }
            });
        });
    }
}
