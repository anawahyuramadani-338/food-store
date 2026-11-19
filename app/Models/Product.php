<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * fillable
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'image',
        'title',
        'slug',
        'description',
        'price',
        'weight',
    ];

    /**
     * casts
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id' => 'integer',
        'price'       => 'integer',
        'weight'      => 'integer',
    ];

    /**
     * category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ratings
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * boot
     */
    protected static function boot()
    {
        parent::boot();

        // generate slug before saving
        static::saving(function ($product) {
            if (empty($product->slug) && !empty($product->title)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }
}