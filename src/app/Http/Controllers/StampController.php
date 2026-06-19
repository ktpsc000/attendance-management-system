<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorrectionRequest;

class StampController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $tab = $request->query('tab', 'pending');
        $query = CorrectionRequest::query()->with('user')
        ->where('user_id', $user->id);

        if($tab === 'approved'){
            $query->where('status', CorrectionRequest::STATUS_APPROVED);
        }else{
            $query->where('status', CorrectionRequest::STATUS_PENDING);
        }

        $requests = $query->get();

        return view('stamp_correction_request.index', compact('requests','tab'));
    }
}
