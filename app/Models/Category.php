<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    //protected static $unguarded = true;

    //this make all registered categories to be avilable to whatever transaction is made
    public function transaction(): HasMany
    {
        return $this-> hasMany(Transaction::class);
    }
}
