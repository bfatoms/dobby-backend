<?php

namespace App\Http\Controllers;

class ChartOfAccountTypeController extends Controller
{

    public function index()
    {
        return response()->json([
            'data' => config('accounting.types'),
            'message' => 'ACCOUNT_TYPES'
        ], 200);
    }
}
