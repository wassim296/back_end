<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Support;

class SupportController extends Controller
{
    public function toggleSupport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ]);

        $existing = Support::where('user_id', $request->user_id)
        ->where('event_id', $request->event_id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed', 'message' => 'Soutien retiré']);
        }

        Support::create([
            'user_id' => $request->user_id,
            'event_id' => $request->event_id
        ]);

        return response()->json(['status' => 'added', 'message' => 'Soutien ajouté']);
    }
}
