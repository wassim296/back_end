<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
    $events = Event::with('user')->orderBy('created_at','desc')->get();
    return $events->map(function ($event){
        $event->image_url = $event->image ? asset('storege/'.$event->image) : null ;
        return $event;
    });
    }

    public function store(Request $request){
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required',
            'date_event'=>'required|date',
            'lieu'=>'required|string',
            'user_id'=>'required|exists:user,id',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
}
