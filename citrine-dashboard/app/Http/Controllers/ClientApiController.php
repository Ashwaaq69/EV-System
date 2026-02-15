<?php

namespace App\Http\Controllers;

use App\Models\ChargingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientApiController extends Controller
{
    /**
     * Get paginated charging sessions for the authenticated user.
     */
    public function sessions(Request $request)
    {
        $user = Auth::user();
        
        // If no user (e.g. standalone mode without session), return some mock data
        // but normally this will be protected by auth middleware
        
        $query = ChargingSession::with('chargePoint.location')
            ->where('user_id', $user->id)
            ->orderBy('start_time', 'desc');

        $perPage = $request->input('per_page', 5);
        $sessionsPaginator = $query->paginate($perPage);

        return response()->json($sessionsPaginator);
    }
}
