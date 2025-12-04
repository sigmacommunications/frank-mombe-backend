<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'name',
        'service_id',
        'any_allergies',
        'add_ons',
        'reference_image',
    ];

    public function service_info()
    {
        return $this->hasOne(Category::class, 'id', 'service_id');
    }
}
