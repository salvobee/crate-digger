<?php

namespace App\Models;

use App\Exceptions\InventoryFetchException;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'rating' => 'float',
        'stars' => 'float',
        'total_feedbacks' => 'float',
        'min_order_total' => 'float',
        'total_listings_count' => 'integer',
        'total_listings_count_updated_at' => 'datetime',
    ];

    public static function storeFromDiscogsData(User $user, array $inventoryData)
    {
        // Assicurati che i dati contengano almeno una listing
        if (empty($inventoryData['listings'])) {
            throw new InventoryFetchException("This user has no active listings");
        }

        // Estrarre i dati del venditore dalla prima listing
        $sellerData = $inventoryData['listings'][0]['seller'];

        // Creare l'inventario
        return self::create([
            'user_id' => $user->id, // O specifica un altro metodo per il legame utente
            'seller_id' => $sellerData['id'],
            'seller_username' => $sellerData['username'],
            'html_url' => $sellerData['html_url'],
            'avatar_url' => $sellerData['avatar_url'],

            'rating' => $sellerData['stats']['rating'] ?? null,
            'stars' => $sellerData['stats']['stars'] ?? null,
            'total_feedbacks' => $sellerData['stats']['total'] ?? null,
            'min_order_total' => $sellerData['min_order_total'] ?? null,
            'total_listings_count' => $inventoryData['pagination']['items'],
            'total_listings_count_updated_at' => now(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
