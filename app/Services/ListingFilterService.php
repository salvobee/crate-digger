<?php

namespace App\Services;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mgussekloo\FacetFilter\Models\Facet;

class ListingFilterService
{
    const OTHER_FILTERS = [
        [
            'is_facet' => false,
            'title' => 'Year Range (from)',
            'fields' => [
                'year_from',
                'year_to'
            ],
            'type' => 'range'
        ]
    ];

    static function getFacetsDefinitions(): Collection
    {
        return collect(Listing::getFacets())
            ->map(function (Facet $facet)  {
                $facet_array = $facet->toArray();
                $facet_array['options'] = $facet->getOptions();
                return $facet_array;
            });
    }


    static function getOtherFiltersDefinitions(): Collection
    {
        return collect(self::OTHER_FILTERS);
    }

    static function getValidFieldsList(): array
    {
        $facets = self::getFacetsDefinitions()
            ->pluck('fieldname');

        $other = collect(self::OTHER_FILTERS)
            ->pluck('fields')
            ->flatten()
            ->values();

        return $facets->merge($other)->toArray();
    }

    static function buildFilterQuery($query, $filters)
    {
        $filter_query = $query;
        foreach ($filters as $field => $values)
        {
            $special_filter_lookup = self::getDefinitionFromFieldName($field);

            // If is a special filter, we use another strategy pattern
            if ($special_filter_lookup) {
                $filter_query = self::buildSpecialFilter($query, $field, $values);
            } else {
                $fields_split = explode('.', $field);

                $filter_query = match (count($fields_split)) {
                    1 => self::buildSimpleFilterQuery($query, $field, $values),
                    2 => self::buildNestedFilterQuery($query, $fields_split, $values),
                    3 => self::buildDoubleNestedFilterQuery($query, $fields_split, $values),
                    default => $query
                };
            }
        }

        return $filter_query;
    }

    static function getDefinitionFromFieldName(string $fieldName)
    {
        return self::getOtherFiltersDefinitions()
            ->filter(fn (array $definition) => collect($definition['fields'])->contains($fieldName))
            ->first();
    }


    private static function buildSpecialFilter($query, string $field, mixed $value): Builder
    {
        return match ($field) {
            'year_from' => $query->whereHas('release',
                fn (Builder $query) => $query->where('year', '>=', $value)
            ),
            'year_to' => $query->whereHas('release',
                fn (Builder $query) => $query->where('year', '<=', $value)
            ),
        };
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
