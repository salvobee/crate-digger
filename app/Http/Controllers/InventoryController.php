<?php

namespace App\Http\Controllers;

use App\Exceptions\InventoryFetchException;
use App\Http\Requests\StoreInventoryRequest;

use App\Models\Inventory;
use App\Services\DiscogsApiService;
use Illuminate\Http\Request;
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
            Inventory::storeFromDiscogsData($request->user(), $inventory_data);
        } catch (InventoryFetchException $e) {
            throw ValidationException::withMessages([
                'username' => $e->getMessage(),
            ]);
        }

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
}
