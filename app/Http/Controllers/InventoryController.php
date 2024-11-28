<?php

namespace App\Http\Controllers;

use App\Enums\AnalysisType;
use App\Exceptions\InventoryFetchException;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Models\Listing;
use App\Services\DiscogsApiService;
use App\Services\ListingFilterService;
use App\Services\SortingCriteriaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Mgussekloo\FacetFilter\Models\Facet;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Inventories/Index', [ 'inventories' => $request->user()->inventories ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request, DiscogsApiService $discogsApiService)
    {
        $attributes = $request->validated();
        $inventory_data = $discogsApiService->fetchInventoryData($attributes['username']);
        try {
            $inventory = Inventory::storeFromDiscogsData($request->user(), $inventory_data);
        } catch (InventoryFetchException $e) {
            throw ValidationException::withMessages([
                'username' => $e->getMessage(),
            ]);
        }
        $fetch_inventory = array_key_exists('fetch_inventory', $attributes) && $attributes['fetch_inventory'];

        if ($fetch_inventory)
            Analysis::create([
                'type' => AnalysisType::FETCH_INVENTORY->value,
                'resource_id' => $inventory->id,
            ])->spawn();

        return to_route('inventories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return to_route('inventories.index');
    }

    public function show(Request $request, Inventory $inventory)
    {
        $parameters = $request->validate([
            'sort' => [ Rule::in(SortingCriteriaService::sortingCriteriaKeys())],
            'filters' => [
//                Rule::array(
//                    collect(Listing::facetDefinitions())
//                        ->pluck('fieldname')
//                        ->toArray()
//                )
            ]
        ]);


        $query = $inventory->listings()->with('release')->getQuery();

        if (array_key_exists('filters', $parameters))
            ListingFilterService::buildFilterQuery($query, $parameters['filters']);


        $listing_query = SortingCriteriaService::prepareSortingCriteria(
            $query,
            $parameters
       );


        $facets = collect(Listing::getFacets())
            ->map(function (Facet $facet)  {
                $facet_array = $facet->toArray();
                $facet_array['options'] = $facet->getOptions();
                return $facet_array;
            });

        if (!array_key_exists('sort', $parameters))
            $parameters['sort'] = 'default';

        if (!array_key_exists('filters', $parameters))
            $parameters['filters'] = [];

        $props = [
            'criteria' => SortingCriteriaService::SCHEMA,
            'store' => $inventory,
            'facets' => $facets,
            'parameters' => $parameters,
            'listings' => $listing_query
                ->paginate(100)
                ->appends($request->query())
        ];

        return Inertia::render('Inventories/Show', $props);
    }

}
