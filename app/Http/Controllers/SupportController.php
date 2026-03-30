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
        $request->validate([
            'event_id' => 'required',
        ]);

        $userId = auth()->id(); 
        $eventId = $request->event_id;

        $support = Support::where('user_id', $userId)
        ->where('event_id', $eventId)
        ->first();

        if ($support) {
            $support->delete();
            return response()->json(['message' => 'Support removed']);
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
        
        return response()->json([
            'events' => $events 
        ]);
    }
}
