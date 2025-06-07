<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    
    //protected static $unguarded = true;

    //this make all registered product to be avilable to whatever sales is made
    public function sale(): HasMany
    {
        return $this-> hasMany(Sale::class);
    }
}
