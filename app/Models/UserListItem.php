<?php

namespace App\Models;

use App\Enums\UserListItemType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserListItem extends Model
{
    /** @use HasFactory<\Database\Factories\UserListItemFactory> */
    use HasFactory, HasUuids;

    protected $casts = [
        'type' => UserListItemType::class,
    ];
}
