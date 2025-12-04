<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberService extends Model
{
    use HasFactory;
    protected $guarded =[];
    
    
    public function service_info()
    {
        return $this->hasMany(Service::class,'id','service_id');
    }
	
	public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
	
	public function barber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
	
	public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_id');
    }

    /**
     * Get booking details for this service
     */
    public function booking_details()
    {
        return $this->hasMany(BookingDetail::class, 'service_id');
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for main services
     */
    public function scopeMainServices($query)
    {
        return $query->where('main_service', 1);
    }

    /**
     * Scope for additional services
     */
    public function scopeAdditionalServices($query)
    {
        return $query->where('main_service', 0);
    }

    /**
     * Get formatted price attribute
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get service type attribute
     */
    public function getServiceTypeAttribute()
    {
        return $this->main_service ? 'Main Service' : 'Additional Service';
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
}
