<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{

    use HasFactory;
    //this makes all the transaction be able to see all categories avilable from the list
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
