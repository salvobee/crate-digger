<?php

namespace App\Http\Controllers;

use App\Actions\UpdateReleaseDataAction;
use App\Models\Crate;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CrateController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Crates/Index', [
            'crates' => $request->user()
                ->crates()
                ->with('listing')
                ->get(),
            'selected' => $request->has('selected') ? $request->input('selected') : null
        ]);
    }

    public function store(Request $request, UpdateReleaseDataAction $updateReleaseDataAction): RedirectResponse
    {
        $attributes = $request->validate([
            'listing' => ['required', 'exists:listings,id']
        ]);
        $request->user()->crates()->create(['listing_id' => $attributes['listing']]);
        $listing = Listing::find($attributes['listing']);
        $updateReleaseDataAction->execute($listing->release);
        return redirect()->back();
    }

    public function update(Request $request, Crate $crate): RedirectResponse
    {
        $attributes = $request->validate([
            'is_liked' => ['boolean']
        ]);

        $crate->update($attributes);

        return redirect()->back();
    }

    public function destroy(Request $request, Crate $crate): RedirectResponse
    {
        if ($crate->user_id !== $request->user()->id)
            abort(401);

        $crate->delete();
        return redirect()->back();
    }
}
