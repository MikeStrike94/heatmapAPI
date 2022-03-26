<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\History;
use App\Models\Type;
use Exception;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function storeVisit()
    {
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
        } catch (Exception $e) {
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

    // Count number of hits of a given page by time interval
    public function countLinkHits()
    {
        $from = request()->get('from', '');
        $to = request()->get('to', '');
        $link = request()->get('link', '');

        try {
            $hits = History::countLinkHits($from, $to, $link);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error at counting link hits',
                    'error' => $e->getMessage()
                ]
            );
        }

        // Success
        return  response()->json(
            [
                'link' => $link,
                'hits' => $hits
            ]
        );
    }

    // Count number of hits for each link type
    public function countTypeHits()
    {
        $from = request()->get('from', '');
        $to = request()->get('to', '');

        try {
            $hits = History::countTypeHits($from, $to);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error at counting type hits',
                    'error' => $e->getMessage()
                ]
            );
        }

        $json_data = array();
        foreach ($hits as $hit) {
            $json_data[] = array(
                'type_name' => $hit['type_name'],
                'count' => $hit['count']
            );
        }

        return json_encode($json_data);
    }
}
