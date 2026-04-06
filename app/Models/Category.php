<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        // 'description',
        // 'image',
        'status',
        'icon',
        'sort_order',
        // 'parent_id'

    ];
    protected $hidden = [
        'id',
        'updated_at',
        'status',
    ];

    // protected $appends = [
    //     'image_url',
    //     'parent_id'
    // ];

    public function getImageUrlAttribute()
    {
        return asset($this->image);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
    // parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'id');
    }
}
