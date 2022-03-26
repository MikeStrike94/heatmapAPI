<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\History;
use App\Models\Type;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function storeVisit(Request $request) {
        // Required data
        request()->validate([
            'customer' => 'required',
            'url' => 'required',
            'type' => 'required'
        ]);

        // Store customer if not exist
        Customer::firstOrCreate([
            'customer' => request('customer')
        ]);

        $requestData = request()->all();
        History::loadData($requestData);
        History::storeData();
    }
}
