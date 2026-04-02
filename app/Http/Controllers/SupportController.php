<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Support;

class SupportController extends Controller
{
public function toggleSupport(Request $request)
{
    $request->validate(['event_id' => 'required|exists:events,id']);

    $userId = auth()->id(); 
    $eventId = $request->event_id;
    $event = Event::withCount('supports')->findOrFail($eventId);

    $support = Support::where('user_id', $userId)->where('event_id', $eventId)->first();

    if ($support) {
        $support->delete();
        return response()->json(['status' => 'removed', 'message' => 'Support removed']);
    }


    if ($event->supports_count >= $event->nombre_places) {
        return response()->json([
            'status' => 'full',
            'message' => 'Désolé, cet événement est complet !'
        ], 400);
    }

    Support::create([
        'user_id' => $userId,
        'event_id' => $eventId
    ]);

    return response()->json(['status' => 'added', 'message' => 'Soutien ajouté']);
}

    public function getMySupportedEvents() {
        $userId = auth()->id();

$events = Event::with('user')
    ->withCount('supports')
    ->whereHas('supports', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })
    ->withExists([
        'supports as is_supported' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }
    ])
    ->get();

            if($events){
     $events->map(function ($event){
        $event->image_url = $event->image ? asset('storage/'.$event->image) : null ;
        return  $event; 
        });
        }
        
        return response()->json([
            'events' => $events 
        ]);
    }
}
