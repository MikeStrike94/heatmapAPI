<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\History;
use App\Models\Type;
use Exception;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function storeVisit(Request $request) {
        // Required fields
        request()->validate([
            'customer' => 'required',
            'url' => 'required',
            'type' => 'required'
        ]);

        // Store customer if not exists
        try {
            Customer::firstOrCreate([
                'customer' => request('customer')
            ]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error at inserting Customer Data',
                    'error' => $e->getMessage()
                ]
            );
        }
        
        // Load History Data
        $requestData = request()->all();
        History::loadData($requestData);

        try {
            History::storeData();
        } catch(Exception $e) {
            return response()->json(
                [
                    'message' => 'Error at inserting History Data',
                    'error' => $e->getMessage()
                ]
            );
        }

        // Return Success
        return  response()->json(
            [
                'message' => 'Data inserted successfully'
            ]
        );
    }
}
