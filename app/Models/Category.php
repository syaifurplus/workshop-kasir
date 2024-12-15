<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status'
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    public static function generateUniqueSlug(String $name): String
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while(self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
