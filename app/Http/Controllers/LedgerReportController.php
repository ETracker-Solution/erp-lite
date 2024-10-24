<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LedgerReportController extends Controller
{
    public function index()
    {
        return view('ledger_report.index');
    }
}
