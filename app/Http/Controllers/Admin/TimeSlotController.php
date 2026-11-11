<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditUserRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Country;
use App\Models\User;
use App\Traits\General;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
// use DB;
use Auth;

use App\Models\TimeSlot;
class TimeSlotController extends Controller
{
    use General;

    public function index()
    {
        $timeSlots = TimeSlot::orderBy('start_time')
            ->paginate(20);

        return view('admin.time_slots.index', compact('timeSlots'));
    }

    public function indexApi(Request $request)
    {
        $date = $request->query('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required'], 400);
        }
        
        try {
            $carbonDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }
        
        $timeSlots = TimeSlot::where('date', $carbonDate->format('Y-m-d'))
            ->where('is_active', true)
            ->get()
            ->map(function ($slot) {
                $startTime = Carbon::parse($slot->start_time);
                $endTime = Carbon::parse($slot->end_time);
                
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'formatted_time' => $startTime->format('h:i A'), // Format as "2:00 PM"
                    'available' => $slot->is_active
                ];
            });
        
        return response()->json($timeSlots);
    }


    public function create()
    {
        return view('admin.time_slots.create');
    }

      public function store(Request $request)
    {
        $validated = $request->validate([
            // 'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',

            'end_time' => [
            'required',
            'date_format:H:i',
            'after:start_time',
            function ($attribute, $value, $fail) use ($request) {
                $exists = TimeSlot::where('start_time', $request->start_time)
                    ->where('end_time', $value)
                    ->exists();
                
                if ($exists) {
                    $fail('A time slot with this exact time range already exists.');
                }
            },
            // function ($attribute, $value, $fail) use ($request) {
            //     $overlapping = TimeSlot::where('date', $request->date)
            //         ->where(function ($query) use ($request, $value) {
            //             $query->whereBetween('start_time', [$request->start_time, $value])
            //                   ->orWhereBetween('end_time', [$request->start_time, $value])
            //                   ->orWhere(function ($query) use ($request, $value) {
            //                       $query->where('start_time', '<', $request->start_time)
            //                             ->where('end_time', '>', $value);
            //                   });
            //         })
            //         ->exists();
                
            //     if ($overlapping) {
            //         $fail('This time slot overlaps with an existing one.');
            //     }
            // },
        ],
            'is_active' => 'sometimes|boolean' 
            // 'is_active' => 'required|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        // $date = Carbon::parse($validated['date']);
        $weeks = 1;   // Means Single Slot
      
        for ($i = 0; $i < $weeks; $i++) {
            // $currentDate = $date->copy()->addWeeks($i);
            TimeSlot::create([
                // 'date' => $currentDate,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'is_active' => $validated['is_active']
            ]);
        }

        $this->showToastrMessage('success', __('Time slot created successfully'));
        return redirect()->route('time_slots.index');

        // return redirect()->route('admin.time-slots.index')
        //     ->with('success', 'Time slot created successfully');
    }

    public function edit($timeSlot_id)
    {
        $timeSlot = TimeSlot::where('id',$timeSlot_id)->first();
     
        return view('admin.time_slots.edit', compact('timeSlot'));
    }

    public function update(Request $request, $timeSlot_id)
    {
   
        $timeSlot = TimeSlot::where('id',$timeSlot_id)->first();
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) use ($request, $timeSlot_id) {
                    $exists = TimeSlot::where('start_time', $request->start_time)
                        ->where('end_time', $value)
                        ->where('id', '!=', $timeSlot_id) // Ignore current slot
                        ->exists();
                    
                    if ($exists) {
                        $fail('Another time slot with this exact time range already exists.');
                    }
                },
                // function ($attribute, $value, $fail) use ($request) {
                //     $overlapping = TimeSlot::where('date', $request->date)
                //         ->where('id', '!=', $timeSlot->id) // Exclude current slot
                //         ->where(function ($query) use ($request, $value) {
                //             $query->whereBetween('start_time', [$request->start_time, $value])
                //                   ->orWhereBetween('end_time', [$request->start_time, $value])
                //                   ->orWhere(function ($query) use ($request, $value) {
                //                       $query->where('start_time', '<', $request->start_time)
                //                             ->where('end_time', '>', $value);
                //                   });
                //         })
                //         ->exists();
                    
                //     if ($overlapping) {
                //         $fail('This time slot overlaps with an existing one.');
                //     }
                // },
            ],

            'is_active' => 'sometimes|boolean' 
            // 'is_active' => 'required|boolean'
        ]);
        $validated['is_active'] = $request->has('is_active');
        $timeSlot->update($validated);

        $this->showToastrMessage('success', __('Time slot updated successfully'));
        return redirect()->route('time_slots.index');

        // return redirect()->route('admin.time-slots.index')
        //     ->with('success', 'Time slot updated successfully');
    }

    public function destroy($timeSlot_id)
    {
     
        $timeSlot = TimeSlot::where('id',$timeSlot_id)->first();
        DB::transaction(function () use ($timeSlot) {
            // Delete bookings first
            $timeSlot->bookings()->delete();
            
            // Then delete the time slot
            $timeSlot->delete();
        });
    

        $this->showToastrMessage('success', __('Time slot and all associated bookings deleted successfully'));
        return redirect()->route('time_slots.index');

    }

    public function toggleStatus($timeSlot_id)
    {
      
        $timeSlot = TimeSlot::where('id',$timeSlot_id)->first();
        $timeSlot->update(['is_active' => !$timeSlot->is_active]);
        
        $this->showToastrMessage('success', __('Time slot status updated'));
        return redirect()->back();
        // return redirect()->route('time_slots.index');
    }


    public function bulkCreate()
    {
        return view('admin.time_slots.bulk_create');
    }




    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'in:0,1,2,3,4,5,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'required|boolean'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysOfWeek = $validated['days_of_week'];

        $createdCount = 0;

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if (in_array($date->dayOfWeek, $daysOfWeek)) {
                TimeSlot::create([
                    'date' => $date,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'is_active' => $validated['is_active']
                ]);
                $createdCount++;
            }
        }

        $this->showToastrMessage('success', __("Created $createdCount time slots"));
        return redirect()->route('time_slots.index');
    }

}
