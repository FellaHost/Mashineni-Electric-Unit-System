<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;
    //this makes all the sales be able to see all product avilable from the list
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
