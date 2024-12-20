<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mgussekloo\FacetFilter\Traits\Facettable;

class Listing extends Model
{
    /** @use HasFactory<\Database\Factories\ListingFactory> */
    use HasFactory, HasUuids, Facettable;

    public static function facetDefinitions()
    {
        // Return an array of definitions
        return [
            [
                'title' => 'Media Condtion',
                'fieldname' => 'media_condition' // Use dot notation to get the value from related models.
            ],
            [
                'title' => 'Sleeve Condtion',
                'fieldname' => 'sleeve_condition' // Use dot notation to get the value from related models.
            ],
            [
                'title' => 'Year',
                'fieldname' => 'release.year',
                'sort_alphabetically' => true,
            ],
            [
                'title' => 'Label',
                'fieldname' => 'release.label',
                'sort_alphabetically' => true, // Use dot notation to get the value from related models.
            ],
            [
                'title' => 'Genre',
                'fieldname' => 'release.genres.name',
                'sort_alphabetically' => true, // Use dot notation to get the value from related models.
            ],
            [
                'title' => 'Styles',
                'fieldname' => 'release.styles.name',
                'sort_alphabetically' => true, // Use dot notation to get the value from related models.
            ]
        ];
    }

    protected $with = [
        'release',
        'inventory'
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }
}
