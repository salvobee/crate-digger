<?php

namespace App\Services;

use App\Models\Release;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SortingCriteriaService

{
    const SCHEMA = [
        [
            'key' => 'default',
            'description' => 'Default (added desc)',
            'field' => 'created_at',
            'order' => 'desc'
        ],
        [
            'key' => 'community-rating-desc',
            'description' => 'Global community rating (9-0)',
            'field' => 'release.year',
            'order' => 'desc'
        ],
        [
            'key' => 'year-asc',
            'description' => 'Year (0-9)',
            'field' => 'release.year',
            'order' => 'asc'
        ],
        [
            'key' => 'year-desc',
            'description' => 'Year (9-0)',
            'field' => 'release.year',
            'order' => 'desc'
        ],
        [
            'key' => 'label-asc',
            'description' => 'Label (A-Z)',
            'field' => 'release.label',
            'order' => 'asc'
        ],
        [
            'key' => 'label-desc',
            'description' => 'Label (Z-A)',
            'field' => 'release.label',
            'order' => 'desc'
        ],
        [
            'key' => 'want-asc',
            'description' => 'Want (0-9)',
            'field' => 'release.want',
            'order' => 'asc'
        ],
        [
            'key' => 'want-desc',
            'description' => 'Want (9-0)',
            'field' => 'release.want',
            'order' => 'desc'
        ],
        [
            'key' => 'have-asc',
            'description' => 'Have (0-9)',
            'field' => 'release.have',
            'order' => 'asc'
        ],
        [
            'key' => 'have-desc',
            'description' => 'Have (9-0)',
            'field' => 'release.have',
            'order' => 'desc'
        ],

        [
            'key' => 'on-sale-asc',
            'description' => 'Num for Sales (0-9)',
            'field' => 'release.num_for_sale:',
            'order' => 'asc'
        ],
        [
            'key' => 'on-sale-desc',
            'description' => 'Num for Sales (9-0)',
            'field' => 'release.num_for_sale:',
            'order' => 'desc'
        ],

        [
            'key' => 'rating-count-asc',
            'description' => 'Rating Count (0-9)',
            'field' => 'release.rating_count',
            'order' => 'asc'
        ],
        [
            'key' => 'rating-count-desc',
            'description' => 'Rating Count (9-0)',
            'field' => 'release.rating_count',
            'order' => 'desc'
        ],
        [
            'key' => 'rating-average-asc',
            'description' => 'Rating Average (0-9)',
            'field' => 'release.rating_average',
            'order' => 'asc'
        ],
        [
            'key' => 'rating-average-desc',
            'description' => 'Rating Average (9-0)',
            'field' => 'release.rating_average',
            'order' => 'desc'
        ],
        [
            'key' => 'price-asc',
            'description' => 'Price (0-9)',
            'field' => 'price_value',
            'order' => 'asc'
        ],

        [
            'key' => 'price-desc',
            'description' => 'Price (9-0)',
            'field' => 'price_value',
            'order' => 'desc'
        ],
    ];

    public static function getCriteriaByKey(string $sort): array
    {
        $criteria = collect(self::SCHEMA)->where('key','=', $sort)->first();
        return [$criteria['field'], $criteria['order']];
    }

    public static function sortingCriteriaKeys(): array
    {
        return collect(self::SCHEMA)
            ->map(fn ($criteria) => $criteria['key'])
            ->values()
            ->toArray();
    }

    public static function prepareSortingCriteria(Builder $query, array $parameters): Builder
    {
        if (!array_key_exists('sort', $parameters))
            return $query->orderBy('listed_at', 'DESC');

        if ($parameters['sort'] === 'community-rating-desc') {

            [$field, $order] = SortingCriteriaService::getCriteriaByKey($parameters['sort']);
            return $query->orderBy(
                Release::select(
                    DB::raw('
            ((COALESCE(releases.have, 0) * 1) + (COALESCE(releases.want, 0) * 2))
            *
            ((COALESCE(releases.rating_average, 0) * 2) * COALESCE(releases.rating_count, 0))'
                    )
                )->whereColumn("releases.id", "listings.release_id"),
                $order
            );
        }

        [$field, $order] = SortingCriteriaService::getCriteriaByKey($parameters['sort']);

        // Ordering is applied to a field in the main table
        if (!Str::contains($field, '.'))
            return $query->orderBy($field, $order);

        // Ordering is applied to a field in a related table
        [$relation_name, $related_field] = explode('.', $field);

        $owner_model = $query->getModel();
        $owner_table = $owner_model->getTable();
        $relation = $query->getRelation($relation_name);

        $related_model = $relation->getModel();
        $related_table = $related_model->getTable();
        $owner_key = $relation->getOwnerKeyName();
        $foreign_key = $relation->getForeignKeyName();

        return $query->orderBy(
            $related_model::select($related_field)->whereColumn("$related_table.$owner_key", "$owner_table.$foreign_key",),
            $order
        );
    }
}
