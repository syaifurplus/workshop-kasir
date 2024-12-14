<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_id',
        'total',
        'note'
    ];

    public function orderDetail(): HasMany
    {
        return $this->HasMany(OrderDetail::class);
    }
}
