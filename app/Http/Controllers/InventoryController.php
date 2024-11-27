<?php

namespace App\Http\Controllers;

use App\Enums\AnalysisType;
use App\Exceptions\InventoryFetchException;
use App\Http\Requests\StoreInventoryRequest;

use App\Jobs\FetchInventoryPageJob;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Services\DiscogsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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

    public function show(Inventory $inventory)
    {
        return Inertia::render('Inventories/Show', [
            'store' => $inventory,
            'listings' => $inventory->listings()->with('release')->paginate()
        ]);
    }
}
