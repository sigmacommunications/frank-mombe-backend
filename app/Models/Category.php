<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id', 
        'type',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    /**
     * Main categories scope (jo parent nahi hai)
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Sub categories scope (jo main categories ke children hain)
     */
    public function scopeSubCategories($query)
    {
        return $query->whereNotNull('parent_id')
                    ->whereHas('parent', function($q) {
                        $q->whereNull('parent_id');
                    });
    }

    /**
     * Child categories scope (jo sub categories ke children hain)  
     */
    public function scopeChildCategories($query)
    {
        return $query->whereNotNull('parent_id')
                    ->whereHas('parent', function($q) {
                        $q->whereNotNull('parent_id');
                    });
    }

    /**
     * Active categories scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Parent category relationship
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child categories relationship
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Services relationship
     */
    public function services()
    {
        return $this->hasMany(BarberService::class, 'category_id');
    }

    
}