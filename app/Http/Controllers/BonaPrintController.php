<?php

namespace App\Http\Controllers;

use App\Models\BonRequest;
use Illuminate\Http\Request;

class BonaPrintController extends Controller
{
    public function print($id)
    {
        // Pastikan model BonRequest dan relasinya ter-load
        $bonRequest = BonRequest::with(['items', 'user'])->findOrFail($id);
        
        return view('bona.print-pengambilan', compact('bonRequest'));
    }
}
