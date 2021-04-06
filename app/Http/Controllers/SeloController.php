<?php

namespace App\Http\Controllers;

use App\Selo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeloController extends Controller
{
    
    public function selos()
    {
        Log::channel('daily')->debug('SeloController#selos');

        $selos = DB::connection('base_s')->table('selos')->limit(10)->get();

        return response()->json($selos);

    }

}
