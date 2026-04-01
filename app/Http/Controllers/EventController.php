<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
public function index()
{
    $userId = Auth::guard('sanctum')->id();

   if($userId){
    $events = Event::with('user')
        ->withCount('supports') 
        ->withExists(['supports as is_supported' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->orderBy('created_at', 'desc')
        ->get();
        }
        else{
        $events = Event::with('user')
        ->withCount('supports') 
        ->orderBy('created_at', 'desc')
        ->get();
        }
        
        
    if($events){
     $events->map(function ($event){
        $event->image_url = $event->image ? asset('storage/'.$event->image) : null ;
        return  $event; 
        });
        }
        return response()->json([
            'success' => true,
            'events'  => $events,
            'userId'  => $userId
    ], 200);
     }

    public function store(Request $request){

            $user = Auth::user();

            if($user->role !== 'admin' && $user->role !== 'professor' ){
            return response()->json([
                'message' => 'action non autorisee',
            ],403);
            }

        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|max:255',
            'date_event' => 'required|date|after:today',
            'lieu' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->all();

        if($request->hasFile('image')){
            $path = $request->file('image')->store('events' , 'public');
            $data['image'] = $path;
        };
        $event = Event::create($data);

        return response()->json([
            'message' => 'Event ajouté avec succès',
            'event' => $event
        ], 201);

    }
    public function show($id)
{ 
    $userId = Auth::guard('sanctum')->id();

    $event = Event::with('user')
    ->withCount('supports')
    ->WithExists(['supports as is_supported' => function ($query) use ($userId) {
        $query->where('user_id' , $userId);
    }])->FindOrFail($id);
 

    $event->image_url = $event->image ? asset('storage/' . $event->image) : null;

    return response()->json([
        'success' => true,
        'event' => $event
    ]);
}

public function update(Request $request, $id)
{
    $user = Auth::user();

    if($user->role !== 'admin' && $user->role !== 'professor' ){
    return response()->json([
        'message' => 'action non autorisee',
    ],403);
     }

    $event = Event::findOrFail($id);

    $request->validate([
        'title' => 'required|string|max:150',
        'description' => 'required|max:255',
        'date_event' => 'required|date|after:today',
        'lieu' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
       
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $path = $request->file('image')->store('events', 'public');
        $data['image'] = $path;
    }

    $event->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Event updated successfully!',
        'event' => $event
    ]);
}
public function destroy($id)
{
    $event = Event::findOrFail($id);
    $user = Auth::user();
   
    if ($event->user_id === $user->id || $user->role === "admin") {
    
    if ($event->image) {
        Storage::disk('public')->delete($event->image);
    }

    $event->delete();

    return response()->json([
        'success' => true,
        'message' => 'Événement et image supprimés avec succès'
    ]);
}
      return response()->json(['message' => 'Action non autorisée'], 403);

}

public function myEvents() {
        $userId = auth()->id();

        $events = Auth::user()->events()->withCount("supports")
      ->withExists(['supports as is_supported' => function ($query) use ($userId) {
        $query->where('user_id', $userId);
        }])
        ->with('user')->get();
        
        return response()->json([
            'events' => $events 
        ]);
    }


}
