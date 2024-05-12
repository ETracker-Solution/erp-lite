<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RMInventoryReportController extends Controller
{
    public function index()
    {
        return view('raw_material_inventory_report.index');
    }

    public function create()
    {
        
    }
}
