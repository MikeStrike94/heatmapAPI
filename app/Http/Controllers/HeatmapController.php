<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function index() {
        echo 'hei';
    }

    public function storeVisit() {
        request()->validate([
            'user' => 'required',
            'url' => 'required',
            'type' => 'required',
        ]);
    }
}
