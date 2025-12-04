<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded =[];
	
	protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->order_no)) {
                $booking->order_no = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $prefix = 'FM'; // Frank Mombe prefix
        $timestamp = now()->format('YmdHis'); // YearMonthDayHourMinuteSecond
        $random = mt_rand(1000, 9999); // Random 4 digits
        
        $orderNumber = $prefix . $timestamp . $random;
        
        // Check if order number already exists
        while (static::where('order_no', $orderNumber)->exists()) {
            $random = mt_rand(1000, 9999);
            $orderNumber = $prefix . $timestamp . $random;
        }
        
        return $orderNumber;
    }
	
    public function barber_info()
    {
        return $this->hasOne(\App\Models\User::class,"id","barber_id");
    }

    public function service_info()
    {
        return $this->hasOne(\App\Models\Service::class,"id","barber_id");
    }

    public function group_members()
    {
        return $this->hasMany(BookingMember::class, 'booking_id');
    }

    public function group_members_with_service()
    {
        return $this->hasMany(BookingMember::class, 'booking_id')
                    ->with('service_info'); // Agar service_info relationship banao to
    }

    public function booking_detail()
    {
        return $this->hasMany(\App\Models\BookingDetail::class, "booking_id","id");
    }
    public function member_info()
    {
        return $this->hasOne(\App\Models\User::class,"id","member_id");
    }

    public function review()
    {
        return $this->hasOne(\App\Models\Review::class,"booking_id","id");
    }
    
    public function booking_review()
    {
        return $this->hasOne(\App\Models\BookingReview::class,"booking_id","id");
    }
}
