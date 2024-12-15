<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
