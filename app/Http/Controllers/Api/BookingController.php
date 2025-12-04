<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Models\Booking;
use App\Models\BookingMember;
use App\Models\User;
use App\Models\Wallet;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Tranasaction;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Stripe;


class BookingController extends BaseController
{

    // public function filter_service(Request $request)
    // {
    //     try
    //     {

    //         $booking = Booking::where(['barber_id'=>$request->barber_id,'booking_date'=>$request->date,'booking_time'=> $request->time,'status'=>'accept'])->get();
    //         if($booking)
    //         {
    //           return ServiceTiming::with(['booking' => function ($query) use ($request) {
    //                 $query->where('booking_time', $request->time);
    //             }])->find($request->barber_id);
    //             //User::find($request->barber_id)->join('')

    //         }
    //         return User::with('available_service_timing')->find($request->barber_id);


    //         // Booking::where('barber_id',$request->barber_id)->whereDate('created_at', $request->date)->get();
    //     }
    //     catch(\Exception $e)
    //     {
    //         return response()->json(['success'=>false,'message'=>$e->getMessage()]);
    //     }
    // }

    public function booking(Request $request)
    {
        // print_r($request->all());die;
        try{
            $validator = Validator::make($request->all(), [
                'booking_time' => 'required',
                'booking_date'=> 'required',
            ]);
            if($validator->fails())
            {
                return $this->sendError($validator->errors()->first(),500);
            }
            $fileName = [];
            $profile= '';
            if($request->hasFile('image'))
			{
				$file = request()->file('image');
				$fileName = md5($file->getClientOriginalName() . time()) . "PayMefirst." . $file->getClientOriginalExtension();
				$file->move('uploads/bookings/', $fileName);
				$profile = asset('uploads/bookings/'.$fileName);
			}
			
			$orderNumber = $this->generateOrderNumber();
            // $input = $request->except(['_token'],$request->all());

            $total = $request->input('price') - $request->input('dis_price');
            
            if($request->payment_method == 'stripe')
            {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                Stripe\Charge::create ([
                        "amount" => $request->price * 100,
                        "currency" => "usd",
                        "source" => $request->stripeToken,
                        "description" => "Test payment from Frank Mombe Application"
                ]);
                
                $data = Tranasaction::create([
                        'user_id' => Auth::user()->id,
                        'amount' => $total,
                        //'date' => $request->date,
                        'reason' => 'for barber booking',
                        'type' => 'debit',
                        //'pm_id' => $customer->id,
                        //'status' => 'Pending',
                    ]);

    
                    $data = Booking::create([
                        'member_id' => Auth::user()->id,
                        'barber_id' => $request->input('barber_id'),
                        'service_time_id' => $request->input('service_time_id'),
                        'booking_time' => $request->input('booking_time'),
                        'booking_date'=> $request->input('booking_date'),
                        'price'=> $request->input('price'),
                        'dis_price'=> $request->input('dis_price'),
                        'total_price' => $total,
                        'image' => $profile,
                        'custom_location'=> $request->input('custom_location'),
                        'status' => 'pending',
						'order_no' => $orderNumber,
                    ]);
        
                    foreach($request->service_id as $service)
                    {
                        BookingDetail::create([
                            'booking_id' => $data->id,
                            'service_id' => $service,
                        ]);
                    }
                    $user = User::with('wallet','temporary_address')->find(Auth::user()->id);
                    return response()->json(['success'=>true,'message'=>'Your Booking has been Sent','user_info'=>$user]);
            }
            if($request->payment_method == 'wallet')
            {
                $wallet = Wallet::where('user_id',Auth::user()->id)->first();
                if($wallet->amount < $total)
                {
                    return response()->json(['success'=>false,'message'=>'Insufficient credits please buy some & order again']);
                }
                else
                {
                    $data = Tranasaction::create([
                        'user_id' => Auth::user()->id,
                        'amount' => $total,
                        //'date' => $request->date,
                        'reason' => 'for barber booking',
                        'type' => 'debit',
                        //'pm_id' => $customer->id,
                        //'status' => 'Pending',
                    ]);
    
                    $wallet->amount = $wallet->amount - $total;
                    $wallet->save();
    
                    $data = Booking::create([
                        'member_id' => Auth::user()->id,
                        'barber_id' => $request->input('barber_id'),
                        'service_time_id' => $request->input('service_time_id'),
                        'booking_time' => $request->input('booking_time'),
                        'booking_date'=> $request->input('booking_date'),
                        'price'=> $request->input('price'),
                        'dis_price'=> $request->input('dis_price'),
                        'total_price' => $total,
                        'image' => $profile,
                        'custom_location'=> $request->input('custom_location'),
                        'status' => 'pending',
						'order_no' => $orderNumber,
                    ]);
        
                    foreach($request->service_id as $service)
                    {
                        BookingDetail::create([
                            'booking_id' => $data->id,
                            'service_id' => $service,
                        ]);
                    }
                    $user = User::with('wallet','temporary_address')->find(Auth::user()->id);
                    return response()->json(['success'=>true,'message'=>'Your Booking has been Sent','user_info'=>$user]);
                }
            }

        }
        catch(\Exception $e)
        {
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }
    public function bookinggroup(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [
                'booking_time' => 'required',
                'booking_date' => 'required',
                'event_type' => 'required',
                'event_date' => 'required',
                'number_of_people' => 'required|integer|min:1',
                'staff_preference' => 'required',
                'service_location' => 'required|in:onsite,salon',
                'number_of_staff_member' => 'required|integer|min:1',
                'members' => 'required|array|min:1'
            ]);

            // Member validator mein service_id add karo
            foreach($request->members as $member) {
                $memberValidator = Validator::make($member, [
                    'name' => 'required',
                    'service' => 'required',
                    'service_id' => 'required|integer', // Yahan service_id add karo
                    'any_alergies' => 'sometimes|string',
                    'add_ons' => 'sometimes|string',
                    'reference_image' => 'sometimes|string'
                ]);

                if($memberValidator->fails()) {
                    return $this->sendError('Member validation failed: ' . $memberValidator->errors()->first(), 500);
                }
            }

            $fileName = [];
            $profile = '';
            
            if($request->hasFile('image')) {
                $file = request()->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "PayMefirst." . $file->getClientOriginalExtension();
                $file->move('uploads/bookings/', $fileName);
                $profile = asset('uploads/bookings/'.$fileName);
            }

            $orderNumber = $this->generateOrderNumber();
            $total = $request->input('price') - $request->input('dis_price');

            // Handle payment based on payment method
            if($request->payment_method == 'stripe') {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                Stripe\Charge::create ([
                    "amount" => $request->price * 100,
                    "currency" => "usd",
                    "source" => $request->stripeToken,
                    "description" => "Group booking from Frank Mombe Application"
                ]);

                $transaction = Tranasaction::create([
                    'user_id' => Auth::user()->id,
                    'amount' => $total,
                    'reason' => 'for group barber booking',
                    'type' => 'debit',
                ]);

                $bookingData = $this->createGroupBooking($request, $profile, $orderNumber, $total);
                
                $user = User::with('wallet','temporary_address')->find(Auth::user()->id);
                return response()->json(['success'=>true,'message'=>'Your Group Booking has been Sent','user_info'=>$user]);
            }

            if($request->payment_method == 'wallet') {
                $wallet = Wallet::where('user_id', Auth::user()->id)->first();
                
                if(!$wallet || $wallet->amount < $total) {
                    return response()->json(['success'=>false,'message'=>'Insufficient credits please buy some & order again']);
                }

                $transaction = Tranasaction::create([
                    'user_id' => Auth::user()->id,
                    'amount' => $total,
                    'reason' => 'for group barber booking',
                    'type' => 'debit',
                ]);

                $wallet->amount = $wallet->amount - $total;
                $wallet->save();

                $bookingData = $this->createGroupBooking($request, $profile, $orderNumber, $total);
                
                $user = User::with('wallet','temporary_address')->find(Auth::user()->id);
                return response()->json(['success'=>true,'message'=>'Your Group Booking has been Sent','user_info'=>$user]);
            }

            // If no payment method specified, just create the booking
            $bookingData = $this->createGroupBooking($request, $profile, $orderNumber, $total);
            
            $user = User::with('wallet','temporary_address')->find(Auth::user()->id);
            return response()->json(['success'=>true,'message'=>'Your Group Booking has been Sent','user_info'=>$user]);

        } catch(\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    private function createGroupBooking($request, $profile, $orderNumber, $total) 
    {
        // Create main booking record
        $booking = Booking::create([
            'member_id' => Auth::user()->id,
            'barber_id' => $request->input('barber_id'),
            'service_time_id' => $request->input('service_time_id'),
            'booking_time' => $request->input('booking_time'),
            'booking_date' => $request->input('booking_date'),
            'price' => $request->input('price'),
            'dis_price' => $request->input('dis_price'),
            'total_price' => $total,
            'image' => $profile,
            'custom_location' => $request->input('custom_location'),
            'status' => 'pending',
            'order_no' => $orderNumber,
            // Group booking specific fields
            'event_type' => $request->input('event_type'),
            'event_date' => $request->input('event_date'),
            'number_of_people' => $request->input('number_of_people'),
            'staff_preference' => $request->input('staff_preference'),
            'service_location' => $request->input('service_location'),
            'number_of_staff_member' => $request->input('number_of_staff_member'),
            'parking_instructions' => $request->input('parking_instructions'),
            'add_ons' => $request->input('add_ons'),
            'preferred_stylist_name' => $request->input('preferred_stylist_name'),
            //'booking_type' => 'group',
        ]);

        // Create booking details for main services (agar chahiye to)
        if($request->has('service_id')) {
            foreach($request->service_id as $service) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $service,
                ]);
            }
        }

        // Create member records with service_id
        foreach($request->members as $member) {
            BookingMember::create([
                'booking_id' => $booking->id,
                'name' => $member['name'],
                'service' => $member['service'],
                'service_id' => $member['service_id'], // Yeh add karo
                'any_alergies' => $member['any_alergies'] ?? null,
                'add_ons' => $member['add_ons'] ?? null,
                'reference_image' => $member['reference_image'] ?? null,
            ]);
        }

        return $booking;
    }
	
	private function generateOrderNumber()
	{
		$prefix = 'FM'; // Frank Mombe prefix
		$timestamp = now()->format('YmdHis'); // YearMonthDayHourMinuteSecond
		$random = mt_rand(1000, 9999); // Random 4 digits

		$orderNumber = $prefix . $timestamp . $random;

		// Check if order number already exists (very unlikely but still)
		while (Booking::where('order_no', $orderNumber)->exists()) {
			$random = mt_rand(1000, 9999);
			$orderNumber = $prefix . $timestamp . $random;
		}

		return $orderNumber;
	}
    
	public function booking_list(Request $request)
    {
        if($request->status != 'all')
        {
            $data = Booking::with('review','barber_info','booking_detail','booking_detail.service_info','member_info')->where('status',$request->status)->where('member_id', Auth::user()->id)->get();
        }
        else
        {
            $data = Booking::with('review','barber_info','booking_detail', 'booking_detail.service_info','member_info')->where('member_id',Auth::user()->id)->get();
        }
        return $this->sendResponse($data,'Booking List');
    }
	
    public function cancel_booking($id)
    {
        $booking = Booking::find($id);
        BookingDetail::where('booking_id',$id)->delete();
        if($booking)
        {
            $booking->delete();
            // $booking->save();
        }
        $booking = null;
        return $this->sendResponse($booking,'Booking Delete Successfully');
    }
}
